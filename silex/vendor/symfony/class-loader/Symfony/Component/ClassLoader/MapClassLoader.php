<?php










namespace Symfony\Component\ClassLoader;






class MapClassLoader
{
private $map = array();






public function __construct(array $map)
{
$this->map = $map;
}






public function register($prepend = false)
{
spl_autoload_register(array($this, 'loadClass'), true, $prepend);
}






public function loadClass($class)
{
if ('\\' === $class[0]) {
$class = substr($class, 1);
}

if (isset($this->map[$class])) {
require $this->map[$class];
}
}








public function findFile($class)
{
if ('\\' === $class[0]) {
$class = substr($class, 1);
}

if (isset($this->map[$class])) {
return $this->map[$class];
}
}
}
