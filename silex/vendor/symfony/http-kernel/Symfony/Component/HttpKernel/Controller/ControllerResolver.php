<?php










namespace Symfony\Component\HttpKernel\Controller;

use Symfony\Component\HttpKernel\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;












class ControllerResolver implements ControllerResolverInterface
{
private $logger;






public function __construct(LoggerInterface $logger = null)
{
$this->logger = $logger;
}
















public function getController(Request $request)
{
if (!$controller = $request->attributes->get('_controller')) {
if (null !== $this->logger) {
$this->logger->warn('Unable to look for the controller as the "_controller" parameter is missing');
}

return false;
}

if (is_array($controller) || (is_object($controller) && method_exists($controller, '__invoke'))) {
return $controller;
}

if (false === strpos($controller, ':')) {
if (method_exists($controller, '__invoke')) {
return new $controller;
} elseif (function_exists($controller)) {
return $controller;
}
}

list($controller, $method) = $this->createController($controller);

if (!method_exists($controller, $method)) {
throw new \InvalidArgumentException(sprintf('Method "%s::%s" does not exist.', get_class($controller), $method));
}

return array($controller, $method);
}











public function getArguments(Request $request, $controller)
{
if (is_array($controller)) {
$r = new \ReflectionMethod($controller[0], $controller[1]);
} elseif (is_object($controller) && !$controller instanceof \Closure) {
$r = new \ReflectionObject($controller);
$r = $r->getMethod('__invoke');
} else {
$r = new \ReflectionFunction($controller);
}

return $this->doGetArguments($request, $controller, $r->getParameters());
}

protected function doGetArguments(Request $request, $controller, array $parameters)
{
$attributes = $request->attributes->all();
$arguments = array();
foreach ($parameters as $param) {
if (array_key_exists($param->getName(), $attributes)) {
$arguments[] = $attributes[$param->getName()];
} elseif ($param->getClass() && $param->getClass()->isInstance($request)) {
$arguments[] = $request;
} elseif ($param->isDefaultValueAvailable()) {
$arguments[] = $param->getDefaultValue();
} else {
if (is_array($controller)) {
$repr = sprintf('%s::%s()', get_class($controller[0]), $controller[1]);
} elseif (is_object($controller)) {
$repr = get_class($controller);
} else {
$repr = $controller;
}

throw new \RuntimeException(sprintf('Controller "%s" requires that you provide a value for the "$%s" argument (because there is no default value or because there is a non optional argument after this one).', $repr, $param->getName()));
}
}

return $arguments;
}








protected function createController($controller)
{
if (false === strpos($controller, '::')) {
throw new \InvalidArgumentException(sprintf('Unable to find controller "%s".', $controller));
}

list($class, $method) = explode('::', $controller, 2);

if (!class_exists($class)) {
throw new \InvalidArgumentException(sprintf('Class "%s" does not exist.', $class));
}

return array(new $class(), $method);
}
}
