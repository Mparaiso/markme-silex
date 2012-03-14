<?php










namespace Symfony\Component\HttpFoundation\Session;






interface SessionInterface
{
    








    function start();

    









    function invalidate();

    









    function migrate($destroy = false);

    






    function save();

    








    function has($name);

    









    function get($name, $default = null);

    







    function set($name, $value);

    






    function all();

    




    function replace(array $attributes);

    








    function remove($name);

    




    function clear();
}
