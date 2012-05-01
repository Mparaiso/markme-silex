<?php










namespace Symfony\Component\HttpFoundation\Session\Storage\Handler;










class NullSessionHandler implements \SessionHandlerInterface
{



public function open($savePath, $sessionName)
{
return true;
}




public function close()
{
return true;
}




public function read($sessionId)
{
return '';
}




public function write($sessionId, $data)
{
return true;
}




public function destroy($sessionId)
{
return true;
}




public function gc($lifetime)
{
return true;
}
}
