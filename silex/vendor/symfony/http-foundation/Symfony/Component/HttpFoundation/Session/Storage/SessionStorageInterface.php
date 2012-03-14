<?php










namespace Symfony\Component\HttpFoundation\Session\Storage;

use Symfony\Component\HttpFoundation\Session\SessionBagInterface;









interface SessionStorageInterface
{
    








    function start();

    






    function getId();

    















    function regenerate($destroy = false);

    







    function save();

    


    function clear();

    








    function getBag($name);

    




    function registerBag(SessionBagInterface $bag);
}
