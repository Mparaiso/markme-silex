<?php










namespace Symfony\Component\HttpFoundation\Session\Storage;

use Symfony\Component\HttpFoundation\Session\SessionBagInterface;








class MetadataBag implements SessionBagInterface
{
const CREATED = 'c';
const UPDATED = 'u';
const LIFETIME = 'l';




private $name = '__metadata';




private $storageKey;




protected $meta = array();






private $lastUsed;






public function __construct($storageKey = '_sf2_meta')
{
$this->storageKey = $storageKey;
$this->meta = array(self::CREATED => 0, self::UPDATED => 0, self::LIFETIME => 0);
}




public function initialize(array &$array)
{
$this->meta = &$array;

if (isset($array[self::CREATED])) {
$this->lastUsed = $this->meta[self::UPDATED];
$this->meta[self::UPDATED] = time();
} else {
$this->stampCreated();
}
}






public function getLifetime()
{
return $this->meta[self::LIFETIME];
}









public function stampNew($lifetime = null)
{
$this->stampCreated($lifetime);
}




public function getStorageKey()
{
return $this->storageKey;
}






public function getCreated()
{
return $this->meta[self::CREATED];
}






public function getLastUsed()
{
return $this->lastUsed;
}




public function clear()
{

 }




public function getName()
{
return $this->name;
}






public function setName($name)
{
$this->name = $name;
}

private function stampCreated($lifetime = null)
{
$timeStamp = time();
$this->meta[self::CREATED] = $this->meta[self::UPDATED] = $this->lastUsed = $timeStamp;
$this->meta[self::LIFETIME] = (null === $lifetime) ? ini_get('session.cookie_lifetime') : $lifetime;
}
}
