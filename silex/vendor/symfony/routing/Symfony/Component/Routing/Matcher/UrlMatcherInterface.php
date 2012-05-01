<?php










namespace Symfony\Component\Routing\Matcher;

use Symfony\Component\Routing\RequestContextAwareInterface;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\Exception\MethodNotAllowedException;








interface UrlMatcherInterface extends RequestContextAwareInterface
{















function match($pathinfo);
}
