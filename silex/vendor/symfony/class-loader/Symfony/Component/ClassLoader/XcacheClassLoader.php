<?php










namespace Symfony\Component\ClassLoader;






























class XcacheClassLoader
{
private $prefix;
private $classFinder;









public function __construct($prefix, $classFinder)
{
if (!extension_loaded('Xcache')) {
throw new \RuntimeException('Unable to use XcacheClassLoader as Xcache is not enabled.');
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
if (xcache_isset($this->prefix.$class)) {
$file = xcache_get($this->prefix.$class);
} else {
xcache_set($this->prefix.$class, $file = $this->classFinder->findFile($class));
}

return $file;
}
}
