<?php










namespace Symfony\Component\HttpFoundation\Session\Storage;












class MockFileSessionStorage extends MockArraySessionStorage
{



private $savePath;

private $sessionData;







public function __construct($savePath = null, $name = 'MOCKSESSID')
{
if (null === $savePath) {
$savePath = sys_get_temp_dir();
}

if (!is_dir($savePath)) {
mkdir($savePath, 0777, true);
}

$this->savePath = $savePath;

parent::__construct($name);
}




public function start()
{
if ($this->started) {
return true;
}

if (!$this->id) {
$this->id = $this->generateId();
}

$this->read();

$this->started = true;

return true;
}




public function regenerate($destroy = false, $lifetime = null)
{
if (!$this->started) {
$this->start();
}

if ($destroy) {
$this->destroy();
}

return parent::regenerate($destroy, $lifetime);
}




public function save()
{
file_put_contents($this->getFilePath(), serialize($this->data));
}





private function destroy()
{
if (is_file($this->getFilePath())) {
unlink($this->getFilePath());
}
}






private function getFilePath()
{
return $this->savePath.'/'.$this->id.'.mocksess';
}




private function read()
{
$filePath = $this->getFilePath();
$this->data = is_readable($filePath) && is_file($filePath) ? unserialize(file_get_contents($filePath)) : array();

$this->loadSession();
}
}
