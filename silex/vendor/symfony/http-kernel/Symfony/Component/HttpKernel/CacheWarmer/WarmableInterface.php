<?php










namespace Symfony\Component\HttpKernel\CacheWarmer;






interface WarmableInterface
{
    




    function warmUp($cacheDir);
}
