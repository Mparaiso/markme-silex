<?php










namespace Symfony\Component\Routing\Generator;

use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\Exception\InvalidParameterException;
use Symfony\Component\Routing\Exception\RouteNotFoundException;
use Symfony\Component\Routing\Exception\MissingMandatoryParametersException;








class UrlGenerator implements UrlGeneratorInterface
{
protected $context;
protected $decodedChars = array(

 '%2F' => '/',
);

protected $routes;









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






public function generate($name, $parameters = array(), $absolute = false)
{
if (null === $route = $this->routes->get($name)) {
throw new RouteNotFoundException(sprintf('Route "%s" does not exist.', $name));
}


 $compiledRoute = $route->compile();

return $this->doGenerate($compiledRoute->getVariables(), $route->getDefaults(), $route->getRequirements(), $compiledRoute->getTokens(), $parameters, $name, $absolute);
}





protected function doGenerate($variables, $defaults, $requirements, $tokens, $parameters, $name, $absolute)
{
$variables = array_flip($variables);

$originParameters = $parameters;
$parameters = array_replace($this->context->getParameters(), $parameters);
$tparams = array_replace($defaults, $parameters);


 if ($diff = array_diff_key($variables, $tparams)) {
throw new MissingMandatoryParametersException(sprintf('The "%s" route has some missing mandatory parameters ("%s").', $name, implode('", "', array_keys($diff))));
}

$url = '';
$optional = true;
foreach ($tokens as $token) {
if ('variable' === $token[0]) {
if (false === $optional || !array_key_exists($token[3], $defaults) || (isset($parameters[$token[3]]) && (string) $parameters[$token[3]] != (string) $defaults[$token[3]])) {
if (!$isEmpty = in_array($tparams[$token[3]], array(null, '', false), true)) {

 if ($tparams[$token[3]] && !preg_match('#^'.$token[2].'$#', $tparams[$token[3]])) {
throw new InvalidParameterException(sprintf('Parameter "%s" for route "%s" must match "%s" ("%s" given).', $token[3], $name, $token[2], $tparams[$token[3]]));
}
}

if (!$isEmpty || !$optional) {
$url = $token[1].strtr(rawurlencode($tparams[$token[3]]), $this->decodedChars).$url;
}

$optional = false;
}
} elseif ('text' === $token[0]) {
$url = $token[1].$url;
$optional = false;
}
}

if (!$url) {
$url = '/';
}


 $extra = array_diff_key($originParameters, $variables, $defaults);
if ($extra && $query = http_build_query($extra, '', '&')) {
$url .= '?'.$query;
}

$url = $this->context->getBaseUrl().$url;

if ($this->context->getHost()) {
$scheme = $this->context->getScheme();
if (isset($requirements['_scheme']) && ($req = strtolower($requirements['_scheme'])) && $scheme != $req) {
$absolute = true;
$scheme = $req;
}

if ($absolute) {
$port = '';
if ('http' === $scheme && 80 != $this->context->getHttpPort()) {
$port = ':'.$this->context->getHttpPort();
} elseif ('https' === $scheme && 443 != $this->context->getHttpsPort()) {
$port = ':'.$this->context->getHttpsPort();
}

$url = $scheme.'://'.$this->context->getHost().$port.$url;
}
}

return $url;
}
}
