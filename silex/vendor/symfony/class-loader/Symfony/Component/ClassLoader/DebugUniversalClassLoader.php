<?php










namespace Symfony\Component\ClassLoader;






class DebugUniversalClassLoader extends UniversalClassLoader
{



static public function enable()
{
if (!is_array($functions = spl_autoload_functions())) {
return;
}

foreach ($functions as $function) {
spl_autoload_unregister($function);
}

foreach ($functions as $function) {
if (is_array($function) && $function[0] instanceof UniversalClassLoader) {
$loader = new static();
$loader->registerNamespaceFallbacks($function[0]->getNamespaceFallbacks());
$loader->registerPrefixFallbacks($function[0]->getPrefixFallbacks());
$loader->registerNamespaces($function[0]->getNamespaces());
$loader->registerPrefixes($function[0]->getPrefixes());
$loader->useIncludePath($function[0]->getUseIncludePath());

$function[0] = $loader;
}

spl_autoload_register($function);
}
}




public function loadClass($class)
{
if ($file = $this->findFile($class)) {
require $file;

if (!class_exists($class, false) && !interface_exists($class, false) && (!function_exists('trait_exists') || !trait_exists($class, false))) {
throw new \RuntimeException(sprintf('The autoloader expected class "%s" to be defined in file "%s". The file was found but the class was not in it, the class name or namespace probably has a typo.', $class, $file));
}
}
}
}
