<?php










namespace Symfony\Component\EventDispatcher\Debug;




interface TraceableEventDispatcherInterface
{





function getCalledListeners();






function getNotCalledListeners();
}
