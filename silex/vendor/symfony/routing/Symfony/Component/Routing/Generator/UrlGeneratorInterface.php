<?php










namespace Symfony\Component\Routing\Generator;

use Symfony\Component\Routing\RequestContextAwareInterface;
use Symfony\Component\Routing\Exception\RouteNotFoundException;








interface UrlGeneratorInterface extends RequestContextAwareInterface
{
    















    function generate($name, $parameters = array(), $absolute = false);
}
