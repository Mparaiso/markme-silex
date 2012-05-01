<?php










namespace Symfony\Component\Routing;

use Symfony\Component\Config\Resource\ResourceInterface;












class RouteCollection implements \IteratorAggregate
{
private $routes;
private $resources;
private $prefix;
private $parent;






public function __construct()
{
$this->routes = array();
$this->resources = array();
$this->prefix = '';
}

public function __clone()
{
foreach ($this->routes as $name => $route) {
$this->routes[$name] = clone $route;
if ($route instanceof RouteCollection) {
$this->routes[$name]->setParent($this);
}
}
}






public function getParent()
{
return $this->parent;
}






public function getRoot()
{
$parent = $this;
while ($parent->getParent()) {
$parent = $parent->getParent();
}

return $parent;
}






public function getIterator()
{
return new \ArrayIterator($this->routes);
}











public function add($name, Route $route)
{
if (!preg_match('/^[a-z0-9A-Z_.]+$/', $name)) {
throw new \InvalidArgumentException(sprintf('The provided route name "%s" contains non valid characters. A route name must only contain digits (0-9), letters (a-z and A-Z), underscores (_) and dots (.).', $name));
}

$this->remove($name);

$this->routes[$name] = $route;
}






public function all()
{
$routes = array();
foreach ($this->routes as $name => $route) {
if ($route instanceof RouteCollection) {
$routes = array_merge($routes, $route->all());
} else {
$routes[$name] = $route;
}
}

return $routes;
}








public function get($name)
{
if (isset($this->routes[$name])) {
return $this->routes[$name] instanceof RouteCollection ? null : $this->routes[$name];
}

foreach ($this->routes as $routes) {
if ($routes instanceof RouteCollection && null !== $route = $routes->get($name)) {
return $route;
}
}

return null;
}







public function remove($name)
{
$root = $this->getRoot();

foreach ((array) $name as $n) {
$root->removeRecursively($n);
}
}














public function addCollection(RouteCollection $collection, $prefix = '', $defaults = array(), $requirements = array(), $options = array())
{

 $root = $this->getRoot();
if ($root === $collection || $root->hasCollection($collection)) {
throw new \InvalidArgumentException('The RouteCollection already exists in the tree.');
}


 $this->remove(array_keys($collection->all()));

$collection->setParent($this);

 
 $collection->addPrefix($this->getPrefix() . $prefix, $defaults, $requirements, $options);
$this->routes[] = $collection;
}











public function addPrefix($prefix, $defaults = array(), $requirements = array(), $options = array())
{

 $prefix = rtrim($prefix, '/');

if ('' === $prefix && empty($defaults) && empty($requirements) && empty($options)) {
return;
}


 if ('' !== $prefix && '/' !== $prefix[0]) {
$prefix = '/'.$prefix;
}

$this->prefix = $prefix.$this->prefix;

foreach ($this->routes as $route) {
if ($route instanceof RouteCollection) {
$route->addPrefix($prefix, $defaults, $requirements, $options);
} else {
$route->setPattern($prefix.$route->getPattern());
$route->addDefaults($defaults);
$route->addRequirements($requirements);
$route->addOptions($options);
}
}
}






public function getPrefix()
{
return $this->prefix;
}






public function getResources()
{
$resources = $this->resources;
foreach ($this as $routes) {
if ($routes instanceof RouteCollection) {
$resources = array_merge($resources, $routes->getResources());
}
}

return array_unique($resources);
}






public function addResource(ResourceInterface $resource)
{
$this->resources[] = $resource;
}







private function setParent(RouteCollection $parent)
{
$this->parent = $parent;
}








private function removeRecursively($name)
{

 
 
 if (isset($this->routes[$name])) {
unset($this->routes[$name]);

return true;
}

foreach ($this->routes as $routes) {
if ($routes instanceof RouteCollection && $routes->removeRecursively($name)) {
return true;
}
}

return false;
}








private function hasCollection(RouteCollection $collection)
{
foreach ($this->routes as $routes) {
if ($routes === $collection || $routes instanceof RouteCollection && $routes->hasCollection($collection)) {
return true;
}
}

return false;
}
}
