<?php










namespace Symfony\Component\EventDispatcher;










interface EventDispatcherInterface
{
    










    function dispatch($eventName, Event $event = null);

    









    function addListener($eventName, $listener, $priority = 0);

    







    function addSubscriber(EventSubscriberInterface $subscriber);

    





    function removeListener($eventName, $listener);

    




    function removeSubscriber(EventSubscriberInterface $subscriber);

    







    function getListeners($eventName = null);

    







    function hasListeners($eventName = null);
}
