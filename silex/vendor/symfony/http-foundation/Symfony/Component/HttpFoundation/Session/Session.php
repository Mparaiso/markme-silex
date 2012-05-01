<?php










namespace Symfony\Component\HttpFoundation\Session;

use Symfony\Component\HttpFoundation\Session\Storage\SessionStorageInterface;
use Symfony\Component\HttpFoundation\Session\Attribute\AttributeBag;
use Symfony\Component\HttpFoundation\Session\Attribute\AttributeBagInterface;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBag;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\HttpFoundation\Session\SessionBagInterface;
use Symfony\Component\HttpFoundation\Session\Storage\NativeSessionStorage;









class Session implements SessionInterface, \IteratorAggregate, \Countable
{





protected $storage;




private $flashName;




private $attributeName;








public function __construct(SessionStorageInterface $storage = null, AttributeBagInterface $attributes = null, FlashBagInterface $flashes = null)
{
$this->storage = $storage ?: new NativeSessionStorage();

$attributes = $attributes ?: new AttributeBag();
$this->attributeName = $attributes->getName();
$this->registerBag($attributes);

$flashes = $flashes ?: new FlashBag();
$this->flashName = $flashes->getName();
$this->registerBag($flashes);
}




public function start()
{
return $this->storage->start();
}




public function has($name)
{
return $this->storage->getBag($this->attributeName)->has($name);
}




public function get($name, $default = null)
{
return $this->storage->getBag($this->attributeName)->get($name, $default);
}




public function set($name, $value)
{
$this->storage->getBag($this->attributeName)->set($name, $value);
}




public function all()
{
return $this->storage->getBag($this->attributeName)->all();
}




public function replace(array $attributes)
{
$this->storage->getBag($this->attributeName)->replace($attributes);
}




public function remove($name)
{
return $this->storage->getBag($this->attributeName)->remove($name);
}




public function clear()
{
$this->storage->getBag($this->attributeName)->clear();
}






public function getIterator()
{
return new \ArrayIterator($this->storage->getBag($this->attributeName)->all());
}






public function count()
{
return count($this->storage->getBag($this->attributeName)->all());
}




public function invalidate($lifetime = null)
{
$this->storage->clear();

return $this->migrate(true, $lifetime);
}




public function migrate($destroy = false, $lifetime = null)
{
return $this->storage->regenerate($destroy, $lifetime);
}




public function save()
{
$this->storage->save();
}




public function getId()
{
return $this->storage->getId();
}




public function setId($id)
{
$this->storage->setId($id);
}




public function getName()
{
return $this->storage->getName();
}




public function setName($name)
{
$this->storage->setName($name);
}




public function getMetadataBag()
{
return $this->storage->getMetadataBag();
}




public function registerBag(SessionBagInterface $bag)
{
$this->storage->registerBag($bag);
}




public function getBag($name)
{
return $this->storage->getBag($name);
}






public function getFlashBag()
{
return $this->getBag($this->flashName);
}








public function getFlashes()
{
$all = $this->getBag($this->flashName)->all();

$return = array();
if ($all) {
foreach ($all as $name => $array) {
if (is_numeric(key($array))) {
$return[$name] = reset($array);
} else {
$return[$name] = $array;
}
}
}

return $return;
}






public function setFlashes($values)
{
foreach ($values as $name => $value) {
$this->getBag($this->flashName)->set($name, $value);
}
}









public function getFlash($name, $default = null)
{
$return = $this->getBag($this->flashName)->get($name);

return empty($return) ? $default : reset($return);
}







public function setFlash($name, $value)
{
$this->getBag($this->flashName)->set($name, $value);
}








public function hasFlash($name)
{
return $this->getBag($this->flashName)->has($name);
}






public function removeFlash($name)
{
$this->getBag($this->flashName)->get($name);
}






public function clearFlashes()
{
return $this->getBag($this->flashName)->clear();
}
}
