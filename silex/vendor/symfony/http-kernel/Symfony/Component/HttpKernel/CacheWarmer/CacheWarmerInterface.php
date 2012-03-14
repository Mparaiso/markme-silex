<?php










namespace Symfony\Component\HttpKernel\CacheWarmer;






interface CacheWarmerInterface extends WarmableInterface
{
    









    function isOptional();
}
