<?php










namespace Silex;

use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpFoundation\Request;








class GetResponseForErrorEvent extends GetResponseForExceptionEvent
{
    public function setStringResponse($response)
    {
        $converter = new StringResponseConverter();
        $this->setResponse($converter->convert($response));
    }
}
