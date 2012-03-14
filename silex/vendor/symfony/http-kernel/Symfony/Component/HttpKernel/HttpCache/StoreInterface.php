<?php













namespace Symfony\Component\HttpKernel\HttpCache;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;






interface StoreInterface
{
    






    function lookup(Request $request);

    










    function write(Request $request, Response $response);

    




    function invalidate(Request $request);

    






    function lock(Request $request);

    




    function unlock(Request $request);

    






    function purge($url);

    


    function cleanup();
}
