<?php










namespace Symfony\Component\HttpKernel\EventListener;

use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;






class ResponseListener implements EventSubscriberInterface
{
private $charset;

public function __construct($charset)
{
$this->charset = $charset;
}






public function onKernelResponse(FilterResponseEvent $event)
{
if (HttpKernelInterface::MASTER_REQUEST !== $event->getRequestType()) {
return;
}

$response = $event->getResponse();

if (null === $response->getCharset()) {
$response->setCharset($this->charset);
}

$response->prepare($event->getRequest());
}

static public function getSubscribedEvents()
{
return array(
KernelEvents::RESPONSE => 'onKernelResponse',
);
}
}
