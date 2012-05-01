<?php










namespace Silex;

use Silex\Exception\ControllerFrozenException;

use Symfony\Component\Routing\Route;






class Controller
{
private $route;
private $routeName;
private $isFrozen = false;






public function __construct(Route $route)
{
$this->route = $route;
}






public function getRoute()
{
return $this->route;
}






public function getRouteName()
{
return $this->routeName;
}







public function bind($routeName)
{
if ($this->isFrozen) {
throw new ControllerFrozenException(sprintf('Calling %s on frozen %s instance.', __METHOD__, __CLASS__));
}

$this->routeName = $routeName;

return $this;
}








public function assert($variable, $regexp)
{
$this->route->setRequirement($variable, $regexp);

return $this;
}








public function value($variable, $default)
{
$this->route->setDefault($variable, $default);

return $this;
}








public function convert($variable, $callback)
{
$converters = $this->route->getOption('_converters');
$converters[$variable] = $callback;
$this->route->setOption('_converters', $converters);

return $this;
}







public function method($method)
{
$this->route->setRequirement('_method', $method);

return $this;
}






public function requireHttp()
{
$this->route->setRequirement('_scheme', 'http');

return $this;
}






public function requireHttps()
{
$this->route->setRequirement('_scheme', 'https');

return $this;
}








public function middleware($callback)
{
$middlewareCallbacks = $this->route->getDefault('_middlewares');
$middlewareCallbacks[] = $callback;
$this->route->setDefault('_middlewares', $middlewareCallbacks);

return $this;
}






public function freeze()
{
$this->isFrozen = true;
}

public function bindDefaultRouteName($prefix)
{
$requirements = $this->route->getRequirements();
$method = isset($requirements['_method']) ? $requirements['_method'] : '';

$routeName = $prefix.$method.$this->route->getPattern();
$routeName = str_replace(array('/', ':', '|', '-'), '_', $routeName);
$routeName = preg_replace('/[^a-z0-9A-Z_.]+/', '', $routeName);

$this->routeName = $routeName;
}
}
