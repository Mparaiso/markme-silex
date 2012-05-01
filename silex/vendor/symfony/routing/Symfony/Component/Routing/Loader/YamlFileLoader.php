<?php










namespace Symfony\Component\Routing\Loader;

use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\Route;
use Symfony\Component\Config\Resource\FileResource;
use Symfony\Component\Yaml\Yaml;
use Symfony\Component\Config\Loader\FileLoader;








class YamlFileLoader extends FileLoader
{
private static $availableKeys = array(
'type', 'resource', 'prefix', 'pattern', 'options', 'defaults', 'requirements'
);













public function load($file, $type = null)
{
$path = $this->locator->locate($file);

$config = Yaml::parse($path);

$collection = new RouteCollection();
$collection->addResource(new FileResource($path));


 if (null === $config) {
$config = array();
}


 if (!is_array($config)) {
throw new \InvalidArgumentException(sprintf('The file "%s" must contain a YAML array.', $file));
}

foreach ($config as $name => $config) {
$config = $this->normalizeRouteConfig($config);

if (isset($config['resource'])) {
$type = isset($config['type']) ? $config['type'] : null;
$prefix = isset($config['prefix']) ? $config['prefix'] : null;
$defaults = isset($config['defaults']) ? $config['defaults'] : array();
$requirements = isset($config['requirements']) ? $config['requirements'] : array();
$options = isset($config['options']) ? $config['options'] : array();

$this->setCurrentDir(dirname($path));
$collection->addCollection($this->import($config['resource'], $type, false, $file), $prefix, $defaults, $requirements, $options);
} else {
$this->parseRoute($collection, $name, $config, $path);
}
}

return $collection;
}











public function supports($resource, $type = null)
{
return is_string($resource) && 'yml' === pathinfo($resource, PATHINFO_EXTENSION) && (!$type || 'yaml' === $type);
}











protected function parseRoute(RouteCollection $collection, $name, $config, $file)
{
$defaults = isset($config['defaults']) ? $config['defaults'] : array();
$requirements = isset($config['requirements']) ? $config['requirements'] : array();
$options = isset($config['options']) ? $config['options'] : array();

if (!isset($config['pattern'])) {
throw new \InvalidArgumentException(sprintf('You must define a "pattern" for the "%s" route.', $name));
}

$route = new Route($config['pattern'], $defaults, $requirements, $options);

$collection->add($name, $route);
}










private function normalizeRouteConfig(array $config)
{
foreach ($config as $key => $value) {
if (!in_array($key, self::$availableKeys)) {
throw new \InvalidArgumentException(sprintf(
'Yaml routing loader does not support given key: "%s". Expected one of the (%s).',
$key, implode(', ', self::$availableKeys)
));
}
}

return $config;
}
}
