<?php














namespace Symfony\Component\HttpKernel\HttpCache;

use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\TerminableInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;








class HttpCache implements HttpKernelInterface, TerminableInterface
{
    private $kernel;
    private $store;
    private $request;
    private $esi;
    private $esiCacheStrategy;
    private $traces;

    







































    public function __construct(HttpKernelInterface $kernel, StoreInterface $store, Esi $esi = null, array $options = array())
    {
        $this->store = $store;
        $this->kernel = $kernel;

        
        register_shutdown_function(array($this->store, 'cleanup'));

        $this->options = array_merge(array(
            'debug'                  => false,
            'default_ttl'            => 0,
            'private_headers'        => array('Authorization', 'Cookie'),
            'allow_reload'           => false,
            'allow_revalidate'       => false,
            'stale_while_revalidate' => 2,
            'stale_if_error'         => 60,
        ), $options);
        $this->esi = $esi;
        $this->traces = array();
    }

    




    public function getStore()
    {
        return $this->store;
    }

    




    public function getTraces()
    {
        return $this->traces;
    }

    




    public function getLog()
    {
        $log = array();
        foreach ($this->traces as $request => $traces) {
            $log[] = sprintf('%s: %s', $request, implode(', ', $traces));
        }

        return implode('; ', $log);
    }

    




    public function getRequest()
    {
        return $this->request;
    }

    




    public function getKernel()
    {
        return $this->kernel;
    }


    




    public function getEsi()
    {
        return $this->esi;
    }

    




    public function handle(Request $request, $type = HttpKernelInterface::MASTER_REQUEST, $catch = true)
    {
        
        if (HttpKernelInterface::MASTER_REQUEST === $type) {
            $this->traces = array();
            $this->request = $request;
            if (null !== $this->esi) {
                $this->esiCacheStrategy = $this->esi->createCacheStrategy();
            }
        }

        $path = $request->getPathInfo();
        if ($qs = $request->getQueryString()) {
            $path .= '?'.$qs;
        }
        $this->traces[$request->getMethod().' '.$path] = array();

        if (!$request->isMethodSafe()) {
            $response = $this->invalidate($request, $catch);
        } elseif ($request->headers->has('expect')) {
            $response = $this->pass($request, $catch);
        } else {
            $response = $this->lookup($request, $catch);
        }

        $response->isNotModified($request);

        $this->restoreResponseBody($request, $response);

        $response->setDate(new \DateTime(null, new \DateTimeZone('UTC')));

        if (HttpKernelInterface::MASTER_REQUEST === $type && $this->options['debug']) {
            $response->headers->set('X-Symfony-Cache', $this->getLog());
        }

        if (null !== $this->esi) {
            $this->esiCacheStrategy->add($response);

            if (HttpKernelInterface::MASTER_REQUEST === $type) {
                $this->esiCacheStrategy->update($response);
            }
        }

        $response->prepare($request);

        return $response;
    }

    




    public function terminate(Request $request, Response $response)
    {
        if ($this->getKernel() instanceof TerminableInterface) {
            $this->getKernel()->terminate($request, $response);
        }
    }

    







    protected function pass(Request $request, $catch = false)
    {
        $this->record($request, 'pass');

        return $this->forward($request, $catch);
    }

    









    protected function invalidate(Request $request, $catch = false)
    {
        $response = $this->pass($request, $catch);

        
        if ($response->isSuccessful() || $response->isRedirect()) {
            try {
                $this->store->invalidate($request, $catch);

                $this->record($request, 'invalidate');
            } catch (\Exception $e) {
                $this->record($request, 'invalidate-failed');

                if ($this->options['debug']) {
                    throw $e;
                }
            }
        }

        return $response;
    }

    













    protected function lookup(Request $request, $catch = false)
    {
        
        if ($this->options['allow_reload'] && $request->isNoCache()) {
            $this->record($request, 'reload');

            return $this->fetch($request);
        }

        try {
            $entry = $this->store->lookup($request);
        } catch (\Exception $e) {
            $this->record($request, 'lookup-failed');

            if ($this->options['debug']) {
                throw $e;
            }

            return $this->pass($request, $catch);
        }

        if (null === $entry) {
            $this->record($request, 'miss');

            return $this->fetch($request, $catch);
        }

        if (!$this->isFreshEnough($request, $entry)) {
            $this->record($request, 'stale');

            return $this->validate($request, $entry, $catch);
        }

        $this->record($request, 'fresh');

        $entry->headers->set('Age', $entry->getAge());

        return $entry;
    }

    











    protected function validate(Request $request, Response $entry, $catch = false)
    {
        $subRequest = clone $request;

        
        $subRequest->setMethod('GET');

        
        $subRequest->headers->set('if_modified_since', $entry->headers->get('Last-Modified'));

        
        
        
        $cachedEtags = $entry->getEtag() ? array($entry->getEtag()) : array();
        $requestEtags = $request->getEtags();
        if ($etags = array_unique(array_merge($cachedEtags, $requestEtags))) {
            $subRequest->headers->set('if_none_match', implode(', ', $etags));
        }

        $response = $this->forward($subRequest, $catch, $entry);

        if (304 == $response->getStatusCode()) {
            $this->record($request, 'valid');

            
            $etag = $response->getEtag();
            if ($etag && in_array($etag, $requestEtags) && !in_array($etag, $cachedEtags)) {
                return $response;
            }

            $entry = clone $entry;
            $entry->headers->remove('Date');

            foreach (array('Date', 'Expires', 'Cache-Control', 'ETag', 'Last-Modified') as $name) {
                if ($response->headers->has($name)) {
                    $entry->headers->set($name, $response->headers->get($name));
                }
            }

            $response = $entry;
        } else {
            $this->record($request, 'invalid');
        }

        if ($response->isCacheable()) {
            $this->store($request, $response);
        }

        return $response;
    }

    









    protected function fetch(Request $request, $catch = false)
    {
        $subRequest = clone $request;

        
        $subRequest->setMethod('GET');

        
        $subRequest->headers->remove('if_modified_since');
        $subRequest->headers->remove('if_none_match');

        $response = $this->forward($subRequest, $catch);

        if ($this->isPrivateRequest($request) && !$response->headers->hasCacheControlDirective('public')) {
            $response->setPrivate(true);
        } elseif ($this->options['default_ttl'] > 0 && null === $response->getTtl() && !$response->headers->getCacheControlDirective('must-revalidate')) {
            $response->setTtl($this->options['default_ttl']);
        }

        if ($response->isCacheable()) {
            $this->store($request, $response);
        }

        return $response;
    }

    








    protected function forward(Request $request, $catch = false, Response $entry = null)
    {
        if ($this->esi) {
            $this->esi->addSurrogateEsiCapability($request);
        }

        
        $response = $this->kernel->handle($request, HttpKernelInterface::MASTER_REQUEST, $catch);
        

        
        if (null !== $entry && in_array($response->getStatusCode(), array(500, 502, 503, 504))) {
            if (null === $age = $entry->headers->getCacheControlDirective('stale-if-error')) {
                $age = $this->options['stale_if_error'];
            }

            if (abs($entry->getTtl()) < $age) {
                $this->record($request, 'stale-if-error');

                return $entry;
            }
        }

        $this->processResponseBody($request, $response);

        return $response;
    }

    







    protected function isFreshEnough(Request $request, Response $entry)
    {
        if (!$entry->isFresh()) {
            return $this->lock($request, $entry);
        }

        if ($this->options['allow_revalidate'] && null !== $maxAge = $request->headers->getCacheControlDirective('max-age')) {
            return $maxAge > 0 && $maxAge >= $entry->getAge();
        }

        return true;
    }

    







    protected function lock(Request $request, Response $entry)
    {
        
        $lock = $this->store->lock($request, $entry);

        
        if (true !== $lock) {
            
            if (null === $age = $entry->headers->getCacheControlDirective('stale-while-revalidate')) {
                $age = $this->options['stale_while_revalidate'];
            }

            if (abs($entry->getTtl()) < $age) {
                $this->record($request, 'stale-while-revalidate');

                
                return true;
            }

            
            $wait = 0;
            while (is_file($lock) && $wait < 5000000) {
                usleep($wait += 50000);
            }

            if ($wait < 2000000) {
                
                $new = $this->lookup($request);
                $entry->headers = $new->headers;
                $entry->setContent($new->getContent());
                $entry->setStatusCode($new->getStatusCode());
                $entry->setProtocolVersion($new->getProtocolVersion());
                foreach ($new->headers->getCookies() as $cookie) {
                    $entry->headers->setCookie($cookie);
                }
            } else {
                
                $entry->setStatusCode(503);
                $entry->setContent('503 Service Unavailable');
                $entry->headers->set('Retry-After', 10);
            }

            return true;
        }

        
        return false;
    }

    





    protected function store(Request $request, Response $response)
    {
        try {
            $this->store->write($request, $response);

            $this->record($request, 'store');

            $response->headers->set('Age', $response->getAge());
        } catch (\Exception $e) {
            $this->record($request, 'store-failed');

            if ($this->options['debug']) {
                throw $e;
            }
        }

        
        $this->store->unlock($request);
    }

    







    private function restoreResponseBody(Request $request, Response $response)
    {
        if ('HEAD' === $request->getMethod() || 304 === $response->getStatusCode()) {
            $response->setContent('');
            $response->headers->remove('X-Body-Eval');
            $response->headers->remove('X-Body-File');

            return;
        }

        if ($response->headers->has('X-Body-Eval')) {
            ob_start();

            if ($response->headers->has('X-Body-File')) {
                include $response->headers->get('X-Body-File');
            } else {
                eval('; ?>'.$response->getContent().'<?php ;');
            }

            $response->setContent(ob_get_clean());
            $response->headers->remove('X-Body-Eval');
            if (!$response->headers->has('Transfer-Encoding')) {
                $response->headers->set('Content-Length', strlen($response->getContent()));
            }
        } elseif ($response->headers->has('X-Body-File')) {
            $response->setContent(file_get_contents($response->headers->get('X-Body-File')));
        } else {
            return;
        }

        $response->headers->remove('X-Body-File');
    }

    protected function processResponseBody(Request $request, Response $response)
    {
        if (null !== $this->esi && $this->esi->needsEsiParsing($response)) {
            $this->esi->process($request, $response);
        }
    }

    







    private function isPrivateRequest(Request $request)
    {
        foreach ($this->options['private_headers'] as $key) {
            $key = strtolower(str_replace('HTTP_', '', $key));

            if ('cookie' === $key) {
                if (count($request->cookies->all())) {
                    return true;
                }
            } elseif ($request->headers->has($key)) {
                return true;
            }
        }

        return false;
    }

    





    private function record(Request $request, $event)
    {
        $path = $request->getPathInfo();
        if ($qs = $request->getQueryString()) {
            $path .= '?'.$qs;
        }
        $this->traces[$request->getMethod().' '.$path][] = $event;
    }
}
