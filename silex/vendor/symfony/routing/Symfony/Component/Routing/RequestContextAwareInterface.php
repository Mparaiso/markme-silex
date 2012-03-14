<?php










namespace Symfony\Component\Routing;




interface RequestContextAwareInterface
{
    






    function setContext(RequestContext $context);

    






    function getContext();
}
