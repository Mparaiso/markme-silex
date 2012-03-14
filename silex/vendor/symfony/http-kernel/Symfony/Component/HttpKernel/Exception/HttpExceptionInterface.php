<?php










namespace Symfony\Component\HttpKernel\Exception;






interface HttpExceptionInterface
{
    




    function getStatusCode();

    




    function getHeaders();
}
