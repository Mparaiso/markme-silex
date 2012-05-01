<?php










namespace Silex;

use Silex\Application;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Debug\ExceptionHandler as DebugExceptionHandler;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;






class ExceptionHandler implements EventSubscriberInterface
{
public function onSilexError(GetResponseForErrorEvent $event)
{
$app = $event->getKernel();
$handler = new DebugExceptionHandler($app['debug']);

$event->setResponse($handler->createResponse($event->getException()));
}




static public function getSubscribedEvents()
{
return array(SilexEvents::ERROR => array('onSilexError', -255));
}
}
