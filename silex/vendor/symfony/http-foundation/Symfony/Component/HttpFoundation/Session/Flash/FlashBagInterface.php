<?php










namespace Symfony\Component\HttpFoundation\Session\Flash;

use Symfony\Component\HttpFoundation\Session\SessionBagInterface;






interface FlashBagInterface extends SessionBagInterface
{
    





    function set($type, $message);

    







    function peek($type, $default = null);

    




    function peekAll();

    







    function get($type, $default = null);

    




    function all();

    


    function setAll(array $messages);

    






    function has($type);

    




    function keys();
}
