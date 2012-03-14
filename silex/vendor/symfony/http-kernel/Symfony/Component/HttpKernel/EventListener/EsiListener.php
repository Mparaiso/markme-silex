<?php










namespace Symfony\Component\HttpKernel\EventListener;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\HttpCache\Esi;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;






class EsiListener implements EventSubscriberInterface
{
    private $i;
    private $esi;

    




    public function __construct(Esi $esi = null)
    {
        $this->esi = $esi;
    }

    




    public function onKernelResponse(FilterResponseEvent $event)
    {
        if (HttpKernelInterface::MASTER_REQUEST !== $event->getRequestType() || null === $this->esi) {
            return;
        }

        $this->esi->addSurrogateControl($event->getResponse());
    }

    static public function getSubscribedEvents()
    {
        return array(
            KernelEvents::RESPONSE => 'onKernelResponse',
        );
    }
}
