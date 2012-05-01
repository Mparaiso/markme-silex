<?php










namespace Symfony\Component\HttpFoundation\Session\Storage\Proxy;








class NativeProxy extends AbstractProxy
{



public function __construct()
{

 $this->saveHandlerName = ini_get('session.save_handler');
}






public function isWrapper()
{
return false;
}
}
