<?php










namespace Symfony\Component\HttpKernel\Profiler;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Profiler\ProfilerStorageInterface;
use Symfony\Component\HttpKernel\DataCollector\DataCollectorInterface;
use Symfony\Component\HttpKernel\Log\LoggerInterface;






class Profiler
{
    private $storage;
    private $collectors;
    private $logger;
    private $enabled;

    





    public function __construct(ProfilerStorageInterface $storage, LoggerInterface $logger = null)
    {
        $this->storage = $storage;
        $this->logger = $logger;
        $this->collectors = array();
        $this->enabled = true;
    }

    


    public function disable()
    {
        $this->enabled = false;
    }

    






    public function loadProfileFromResponse(Response $response)
    {
        if (!$token = $response->headers->get('X-Debug-Token')) {
            return false;
        }

        return $this->loadProfile($token);
    }

    






    public function loadProfile($token)
    {
        return $this->storage->read($token);
    }

    






    public function saveProfile(Profile $profile)
    {
        if (!($ret = $this->storage->write($profile)) && null !== $this->logger) {
            $this->logger->warn('Unable to store the profiler information.');
        }

        return $ret;
    }

    


    public function purge()
    {
        $this->storage->purge();
    }

    






    public function export(Profile $profile)
    {
        return base64_encode(serialize($profile));
    }

    






    public function import($data)
    {
        $profile = unserialize(base64_decode($data));

        if ($this->storage->read($profile->getToken())) {
            return false;
        }

        $this->saveProfile($profile);

        return $profile;
    }

    









    public function find($ip, $url, $limit, $method)
    {
        return $this->storage->find($ip, $url, $limit, $method);
    }

    








    public function collect(Request $request, Response $response, \Exception $exception = null)
    {
        if (false === $this->enabled) {
            return;
        }

        $profile = new Profile(uniqid());
        $profile->setTime(time());
        $profile->setUrl($request->getUri());
        $profile->setIp($request->server->get('REMOTE_ADDR'));
        $profile->setMethod($request->getMethod());

        $response->headers->set('X-Debug-Token', $profile->getToken());

        foreach ($this->collectors as $collector) {
            $collector->collect($request, $response, $exception);

            
            $profile->addCollector(unserialize(serialize($collector)));
        }

        return $profile;
    }

    




    public function all()
    {
        return $this->collectors;
    }

    




    public function set(array $collectors = array())
    {
        $this->collectors = array();
        foreach ($collectors as $collector) {
            $this->add($collector);
        }
    }

    




    public function add(DataCollectorInterface $collector)
    {
        $this->collectors[$collector->getName()] = $collector;
    }

    






    public function has($name)
    {
        return isset($this->collectors[$name]);
    }

    








    public function get($name)
    {
        if (!isset($this->collectors[$name])) {
            throw new \InvalidArgumentException(sprintf('Collector "%s" does not exist.', $name));
        }

        return $this->collectors[$name];
    }
}
