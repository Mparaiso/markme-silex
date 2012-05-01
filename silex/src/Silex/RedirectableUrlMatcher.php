<?php










namespace Silex;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Matcher\RedirectableUrlMatcher as BaseRedirectableUrlMatcher;
use Symfony\Component\Routing\Matcher\RedirectableUrlMatcherInterface;






class RedirectableUrlMatcher extends BaseRedirectableUrlMatcher
{



public function redirect($path, $route, $scheme = null)
{
$url = $this->context->getBaseUrl().$path;

if ($this->context->getHost()) {
if ($scheme) {
$port = '';
if ('http' === $scheme && 80 != $this->context->getHttpPort()) {
$port = ':'.$this->context->getHttpPort();
} elseif ('https' === $scheme && 443 != $this->context->getHttpsPort()) {
$port = ':'.$this->context->getHttpsPort();
}

$url = $scheme.'://'.$this->context->getHost().$port.$url;
}
}

return array(
'_controller' => function ($url) { return new RedirectResponse($url, 301); },
'url' => $url,
);
}
}
