<?php










namespace Symfony\Component\HttpFoundation\Session\Storage\Proxy;






class SessionHandlerProxy extends AbstractProxy implements \SessionHandlerInterface
{



protected $handler;






public function __construct(\SessionHandlerInterface $handler)
{
$this->handler = $handler;
$this->wrapper = ($handler instanceof \SessionHandler);
$this->saveHandlerName = $this->wrapper ? ini_get('session.save_handler') : 'user';
}






public function open($savePath, $sessionName)
{
$return = (bool)$this->handler->open($savePath, $sessionName);

if (true === $return) {
$this->active = true;
}

return $return;
}




public function close()
{
$this->active = false;

return (bool) $this->handler->close();
}




public function read($id)
{
return (string) $this->handler->read($id);
}




public function write($id, $data)
{
return (bool) $this->handler->write($id, $data);
}




public function destroy($id)
{
return (bool) $this->handler->destroy($id);
}




public function gc($maxlifetime)
{
return (bool) $this->handler->gc($maxlifetime);
}
}
