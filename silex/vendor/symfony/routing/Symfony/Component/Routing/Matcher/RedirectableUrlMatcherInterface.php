<?php










namespace Symfony\Component\Routing\Matcher;








interface RedirectableUrlMatcherInterface
{
    










    function redirect($path, $route, $scheme = null);
}
