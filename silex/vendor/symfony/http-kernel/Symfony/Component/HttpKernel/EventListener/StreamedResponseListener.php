<?php










namespace Symfony\Component\HttpKernel\EventListener;

use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;







class StreamedResponseListener implements EventSubscriberInterface
{





public function onKernelResponse(FilterResponseEvent $event)
{
if (HttpKernelInterface::MASTER_REQUEST !== $event->getRequestType()) {
return;
}

$response = $event->getResponse();

if ($response instanceof StreamedResponse) {
$response->send();
}
}

static public function getSubscribedEvents()
{
return array(
KernelEvents::RESPONSE => array('onKernelResponse', -1024),
);
}
}
