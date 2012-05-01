<?php










namespace Symfony\Component\Routing\Matcher;

use Symfony\Component\Routing\Exception\MethodNotAllowedException;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\Route;








class UrlMatcher implements UrlMatcherInterface
{
const REQUIREMENT_MATCH = 0;
const REQUIREMENT_MISMATCH = 1;
const ROUTE_MATCH = 2;

protected $context;
protected $allow;

private $routes;









public function __construct(RouteCollection $routes, RequestContext $context)
{
$this->routes = $routes;
$this->context = $context;
}








public function setContext(RequestContext $context)
{
$this->context = $context;
}






public function getContext()
{
return $this->context;
}













public function match($pathinfo)
{
$this->allow = array();

if ($ret = $this->matchCollection(rawurldecode($pathinfo), $this->routes)) {
return $ret;
}

throw 0 < count($this->allow)
? new MethodNotAllowedException(array_unique(array_map('strtoupper', $this->allow)))
: new ResourceNotFoundException();
}












protected function matchCollection($pathinfo, RouteCollection $routes)
{
foreach ($routes as $name => $route) {
if ($route instanceof RouteCollection) {
if (false === strpos($route->getPrefix(), '{') && $route->getPrefix() !== substr($pathinfo, 0, strlen($route->getPrefix()))) {
continue;
}

if (!$ret = $this->matchCollection($pathinfo, $route)) {
continue;
}

return $ret;
}

$compiledRoute = $route->compile();


 if ('' !== $compiledRoute->getStaticPrefix() && 0 !== strpos($pathinfo, $compiledRoute->getStaticPrefix())) {
continue;
}

if (!preg_match($compiledRoute->getRegex(), $pathinfo, $matches)) {
continue;
}


 if ($req = $route->getRequirement('_method')) {

 if ('HEAD' === $method = $this->context->getMethod()) {
$method = 'GET';
}

if (!in_array($method, $req = explode('|', strtoupper($req)))) {
$this->allow = array_merge($this->allow, $req);

continue;
}
}

$status = $this->handleRouteRequirements($pathinfo, $name, $route);

if (self::ROUTE_MATCH === $status[0]) {
return $status[1];
}

if (self::REQUIREMENT_MISMATCH === $status[0]) {
continue;
}

return array_merge($this->mergeDefaults($matches, $route->getDefaults()), array('_route' => $name));
}
}










protected function handleRouteRequirements($pathinfo, $name, Route $route)
{

 $scheme = $route->getRequirement('_scheme');
$status = $scheme && $scheme !== $this->context->getScheme() ? self::REQUIREMENT_MISMATCH : self::REQUIREMENT_MATCH;

return array($status, null);
}

protected function mergeDefaults($params, $defaults)
{
$parameters = $defaults;
foreach ($params as $key => $value) {
if (!is_int($key)) {
$parameters[$key] = $value;
}
}

return $parameters;
}
}
