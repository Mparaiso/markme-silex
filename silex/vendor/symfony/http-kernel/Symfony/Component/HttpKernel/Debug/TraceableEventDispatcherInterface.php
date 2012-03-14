<?php










namespace Symfony\Component\HttpKernel\Debug;




interface TraceableEventDispatcherInterface
{
    




    function getCalledListeners();

    




    function getNotCalledListeners();
}
