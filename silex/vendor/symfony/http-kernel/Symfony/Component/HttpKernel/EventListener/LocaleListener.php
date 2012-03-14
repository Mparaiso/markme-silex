<?php










namespace Symfony\Component\HttpKernel\EventListener;

use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;






class LocaleListener implements EventSubscriberInterface
{
    private $router;
    private $defaultLocale;

    public function __construct($defaultLocale = 'en', RouterInterface $router = null)
    {
        $this->defaultLocale = $defaultLocale;
        $this->router = $router;
    }

    public function onKernelRequest(GetResponseEvent $event)
    {
        $request = $event->getRequest();

        if ($request->hasPreviousSession()) {
            $request->setDefaultLocale($request->getSession()->get('_locale', $this->defaultLocale));
        } else {
            $request->setDefaultLocale($this->defaultLocale);
        }

        if ($locale = $request->attributes->get('_locale')) {
            $request->setLocale($locale);

            if ($request->hasPreviousSession()) {
                $request->getSession()->set('_locale', $request->getLocale());
            }
        }

        if (null !== $this->router) {
            $this->router->getContext()->setParameter('_locale', $request->getLocale());
        }
    }

    static public function getSubscribedEvents()
    {
        return array(
            
            KernelEvents::REQUEST => array(array('onKernelRequest', 16)),
        );
    }
}
