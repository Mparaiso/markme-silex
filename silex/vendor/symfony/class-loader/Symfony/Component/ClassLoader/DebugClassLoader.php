<?php










namespace Symfony\Component\ClassLoader;













class DebugClassLoader
{
private $classFinder;








public function __construct($classFinder)
{
$this->classFinder = $classFinder;
}




static public function enable()
{
if (!is_array($functions = spl_autoload_functions())) {
return;
}

foreach ($functions as $function) {
spl_autoload_unregister($function);
}

foreach ($functions as $function) {
if (is_array($function) && method_exists($function[0], 'findFile')) {
$function = array(new static($function[0]), 'loadClass');
}

spl_autoload_register($function);
}
}




public function unregister()
{
spl_autoload_unregister(array($this, 'loadClass'));
}








public function loadClass($class)
{
if ($file = $this->classFinder->findFile($class)) {
require $file;

if (!class_exists($class, false) && !interface_exists($class, false) && (!function_exists('trait_exists') || !trait_exists($class, false))) {
throw new \RuntimeException(sprintf('The autoloader expected class "%s" to be defined in file "%s". The file was found but the class was not in it, the class name or namespace probably has a typo.', $class, $file));
}

return true;
}
}
}
