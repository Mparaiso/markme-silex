<?php










namespace Silex;

use Symfony\Component\HttpKernel\HttpKernel;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\Event\KernelEvent;
use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\EventListener\ResponseListener;
use Symfony\Component\HttpKernel\EventListener\RouterListener;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\Exception\ExceptionInterface as RoutingException;
use Symfony\Component\Routing\Exception\MethodNotAllowedException;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\ClassLoader\UniversalClassLoader;
use Silex\RedirectableUrlMatcher;
use Silex\ControllerResolver;






class Application extends \Pimple implements HttpKernelInterface, EventSubscriberInterface
{
    const VERSION = '39184eb 2012-03-11 18:03:32 +0100';

    


    public function __construct()
    {
        $app = $this;

        $this['autoloader'] = $this->share(function () {
            $loader = new UniversalClassLoader();
            $loader->register();

            return $loader;
        });

        $this['routes'] = $this->share(function () {
            return new RouteCollection();
        });

        $this['controllers'] = $this->share(function () use ($app) {
            return new ControllerCollection();
        });

        $this['exception_handler'] = $this->share(function () {
            return new ExceptionHandler();
        });

        $this['dispatcher'] = $this->share(function () use ($app) {
            $dispatcher = new EventDispatcher();
            $dispatcher->addSubscriber($app);

            $urlMatcher = new LazyUrlMatcher(function () use ($app) {
                return $app['url_matcher'];
            });
            $dispatcher->addSubscriber(new RouterListener($urlMatcher));

            return $dispatcher;
        });

        $this['resolver'] = $this->share(function () use ($app) {
            return new ControllerResolver($app);
        });

        $this['kernel'] = $this->share(function () use ($app) {
            return new HttpKernel($app['dispatcher'], $app['resolver']);
        });

        $this['request_context'] = $this->share(function () use ($app) {
            $context = new RequestContext();

            $context->setHttpPort($app['request.http_port']);
            $context->setHttpsPort($app['request.https_port']);

            return $context;
        });

        $this['url_matcher'] = $this->share(function () use ($app) {
            return new RedirectableUrlMatcher($app['routes'], $app['request_context']);
        });

        $this['route_middlewares_trigger'] = $this->protect(function (KernelEvent $event) use ($app) {
            foreach ($event->getRequest()->attributes->get('_middlewares', array()) as $callback) {
                $ret = call_user_func($callback, $event->getRequest());
                if ($ret instanceof Response) {
                    $event->setResponse($ret);
                    return;
                } elseif (null !== $ret) {
                    throw new \RuntimeException('Middleware for route "'.$event->getRequest()->attributes->get('_route').'" returned an invalid response value. Must return null or an instance of Response.');
                }
            }
        });

        $this['request.default_locale'] = 'en';

        $this['request'] = function () {
            throw new \RuntimeException('Accessed request service outside of request scope. Try moving that call to a before handler or controller.');
        };

        $this['request.http_port'] = 80;
        $this['request.https_port'] = 443;
        $this['debug'] = false;
        $this['charset'] = 'UTF-8';
    }

    





    public function register(ServiceProviderInterface $provider, array $values = array())
    {
        foreach ($values as $key => $value) {
            $this[$key] = $value;
        }

        $provider->register($this);
    }

    









    public function match($pattern, $to)
    {
        return $this['controllers']->match($pattern, $to);
    }

    







    public function get($pattern, $to)
    {
        return $this['controllers']->get($pattern, $to);
    }

    







    public function post($pattern, $to)
    {
        return $this['controllers']->post($pattern, $to);
    }

    







    public function put($pattern, $to)
    {
        return $this['controllers']->put($pattern, $to);
    }

    







    public function delete($pattern, $to)
    {
        return $this['controllers']->delete($pattern, $to);
    }

    








    public function before($callback, $priority = 0)
    {
        $this['dispatcher']->addListener(SilexEvents::BEFORE, function (GetResponseEvent $event) use ($callback) {
            $ret = call_user_func($callback, $event->getRequest());

            if ($ret instanceof Response) {
                $event->setResponse($ret);
            }
        }, $priority);
    }

    








    public function after($callback, $priority = 0)
    {
        $this['dispatcher']->addListener(SilexEvents::AFTER, function (FilterResponseEvent $event) use ($callback) {
            call_user_func($callback, $event->getRequest(), $event->getResponse());
        }, $priority);
    }

    






    public function abort($statusCode, $message = '', array $headers = array())
    {
        throw new HttpException($statusCode, $message, null, $headers);
    }

    
















    public function error($callback, $priority = 0)
    {
        $this['dispatcher']->addListener(SilexEvents::ERROR, function (GetResponseForErrorEvent $event) use ($callback) {
            $exception = $event->getException();
            $code = $exception instanceof HttpExceptionInterface ? $exception->getStatusCode() : 500;

            $result = call_user_func($callback, $exception, $code);

            if (null !== $result) {
                $event->setStringResponse($result);
            }
        }, $priority);
    }

    




    public function flush($prefix = '')
    {
        $this['routes']->addCollection($this['controllers']->flush($prefix), $prefix);
    }

    







    public function redirect($url, $status = 302)
    {
        return new RedirectResponse($url, $status);
    }

    








    public function stream($callback = null, $status = 200, $headers = array())
    {
        return new StreamedResponse($callback, $status, $headers);
    }

    









    public function escape($text, $flags = ENT_COMPAT, $charset = 'UTF-8', $doubleEncode = true)
    {
        return htmlspecialchars($text, $flags, $charset, $doubleEncode);
    }

    





    public function mount($prefix, $app)
    {
        if ($app instanceof ControllerProviderInterface) {
            $app = $app->connect($this);
        }

        if (!$app instanceof ControllerCollection) {
            throw new \LogicException('The "mount" method takes either a ControllerCollection or a ControllerProviderInterface instance.');
        }

        $this['routes']->addCollection($app->flush($prefix), $prefix);
    }

    




    public function run(Request $request = null)
    {
        if (null === $request) {
            $request = Request::createFromGlobals();
        }

        $this->handle($request)->send();
    }

    


    public function handle(Request $request, $type = HttpKernelInterface::MASTER_REQUEST, $catch = true)
    {
        $this->beforeDispatched = false;

        $this['request'] = $request;
        $this['request']->setDefaultLocale($this['request.default_locale']);

        $this->flush();

        return $this['kernel']->handle($request, $type, $catch);
    }

    




    public function onEarlyKernelRequest(KernelEvent $event)
    {
        if (HttpKernelInterface::MASTER_REQUEST === $event->getRequestType()) {
            if (isset($this['exception_handler'])) {
                $this['dispatcher']->addSubscriber($this['exception_handler']);
            }
            $this['dispatcher']->addSubscriber(new ResponseListener($this['charset']));
        }
    }

    




    public function onKernelRequest(KernelEvent $event)
    {
        if (HttpKernelInterface::MASTER_REQUEST === $event->getRequestType()) {
            $this->beforeDispatched = true;
            $this['dispatcher']->dispatch(SilexEvents::BEFORE, $event);
            $this['route_middlewares_trigger']($event);
        }
    }

    




    public function onKernelController(FilterControllerEvent $event)
    {
        $request = $event->getRequest();
        $route = $this['routes']->get($request->attributes->get('_route'));
        if ($route && $converters = $route->getOption('_converters')) {
            foreach ($converters as $name => $callback) {
                $request->attributes->set($name, call_user_func($callback, $request->attributes->get($name, null), $request));
            }
        }
    }

    




    public function onKernelView(GetResponseForControllerResultEvent $event)
    {
        $response = $event->getControllerResult();
        $converter = new StringResponseConverter();
        $event->setResponse($converter->convert($response));
    }

    




    public function onKernelResponse(Event $event)
    {
        if (HttpKernelInterface::MASTER_REQUEST === $event->getRequestType()) {
            $this['dispatcher']->dispatch(SilexEvents::AFTER, $event);
        }
    }

    







    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        if (!$this->beforeDispatched) {
            $this->beforeDispatched = true;
            $this['dispatcher']->dispatch(SilexEvents::BEFORE, $event);
        }

        $errorEvent = new GetResponseForErrorEvent($this, $event->getRequest(), $event->getRequestType(), $event->getException());
        $this['dispatcher']->dispatch(SilexEvents::ERROR, $errorEvent);

        if ($errorEvent->hasResponse()) {
            $event->setResponse($errorEvent->getResponse());
        }
    }

    


    static public function getSubscribedEvents()
    {
        return array(
            KernelEvents::REQUEST    => array(
                array('onEarlyKernelRequest', 256),
                array('onKernelRequest')
            ),
            KernelEvents::CONTROLLER => 'onKernelController',
            KernelEvents::RESPONSE   => 'onKernelResponse',
            KernelEvents::EXCEPTION  => 'onKernelException',
            KernelEvents::VIEW       => array('onKernelView', -10),
        );
    }
}
