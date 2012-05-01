<?php










namespace Symfony\Component\Routing\Matcher;

use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\Route;






abstract class RedirectableUrlMatcher extends UrlMatcher implements RedirectableUrlMatcherInterface
{





public function match($pathinfo)
{
try {
$parameters = parent::match($pathinfo);
} catch (ResourceNotFoundException $e) {
if ('/' === substr($pathinfo, -1)) {
throw $e;
}

try {
parent::match($pathinfo.'/');

return $this->redirect($pathinfo.'/', null);
} catch (ResourceNotFoundException $e2) {
throw $e;
}
}

return $parameters;
}




protected function handleRouteRequirements($pathinfo, $name, Route $route)
{

 $scheme = $route->getRequirement('_scheme');
if ($scheme && $this->context->getScheme() !== $scheme) {
return array(self::ROUTE_MATCH, $this->redirect($pathinfo, $name, $scheme));
}

return array(self::REQUIREMENT_MATCH, null);
}
}
