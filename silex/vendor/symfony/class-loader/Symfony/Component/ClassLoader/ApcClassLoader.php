<?php










namespace Symfony\Component\ClassLoader;





























class ApcClassLoader
{
private $prefix;
private $classFinder;









public function __construct($prefix, $classFinder)
{
if (!extension_loaded('apc')) {
throw new \RuntimeException('Unable to use ApcUniversalClassLoader as APC is not enabled.');
}

if (!method_exists($classFinder, 'findFile')) {
throw new \InvalidArgumentException('The class finder must implement a "findFile" method.');
}

$this->prefix = $prefix;
$this->classFinder = $classFinder;
}






public function register($prepend = false)
{
spl_autoload_register(array($this, 'loadClass'), true, $prepend);
}




public function unregister()
{
spl_autoload_unregister(array($this, 'loadClass'));
}








public function loadClass($class)
{
if ($file = $this->findFile($class)) {
require $file;

return true;
}
}








public function findFile($class)
{
if (false === $file = apc_fetch($this->prefix.$class)) {
apc_store($this->prefix.$class, $file = $this->classFinder->findFile($class));
}

return $file;
}
}
