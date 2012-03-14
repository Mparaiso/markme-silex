<?php










namespace Symfony\Component\Routing\Loader;

use Symfony\Component\Config\Loader\Loader;










class ClosureLoader extends Loader
{
    







    public function load($closure, $type = null)
    {
        return call_user_func($closure);
    }

    









    public function supports($resource, $type = null)
    {
        return $resource instanceof \Closure && (!$type || 'closure' === $type);
    }
}
