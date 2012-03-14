<?php










namespace Silex;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Matcher\RedirectableUrlMatcher as BaseRedirectableUrlMatcher;
use Symfony\Component\Routing\Matcher\RedirectableUrlMatcherInterface;






class RedirectableUrlMatcher extends BaseRedirectableUrlMatcher
{
    


    public function redirect($path, $route, $scheme = null)
    {
        return array(
            '_controller' => function ($url) { return new RedirectResponse($url, 301); },
            'url' => $this->context->getBaseUrl().$path,
        );
    }
}
