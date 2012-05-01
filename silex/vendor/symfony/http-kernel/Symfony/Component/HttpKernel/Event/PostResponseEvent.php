<?php










namespace Symfony\Component\HttpKernel\Event;

use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;






class PostResponseEvent extends Event
{




private $kernel;

private $request;

private $response;

public function __construct(HttpKernelInterface $kernel, Request $request, Response $response)
{
$this->kernel = $kernel;
$this->request = $request;
$this->response = $response;
}






public function getKernel()
{
return $this->kernel;
}






public function getRequest()
{
return $this->request;
}






public function getResponse()
{
return $this->response;
}
}
