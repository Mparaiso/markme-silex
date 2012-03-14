<?php































class Pimple implements ArrayAccess
{
    private $values;

    






    function __construct (array $values = array())
    {
        $this->values = $values;
    }

    











    function offsetSet($id, $value)
    {
        $this->values[$id] = $value;
    }

    








    function offsetGet($id)
    {
        if (!array_key_exists($id, $this->values)) {
            throw new InvalidArgumentException(sprintf('Identifier "%s" is not defined.', $id));
        }

        return $this->values[$id] instanceof Closure ? $this->values[$id]($this) : $this->values[$id];
    }

    






    function offsetExists($id)
    {
        return isset($this->values[$id]);
    }

    




    function offsetUnset($id)
    {
        unset($this->values[$id]);
    }

    







    function share(Closure $callable)
    {
        return function ($c) use ($callable) {
            static $object;

            if (is_null($object)) {
                $object = $callable($c);
            }

            return $object;
        };
    }

    








    function protect(Closure $callable)
    {
        return function ($c) use ($callable) {
            return $callable;
        };
    }

    








    function raw($id)
    {
        if (!array_key_exists($id, $this->values)) {
            throw new InvalidArgumentException(sprintf('Identifier "%s" is not defined.', $id));
        }

        return $this->values[$id];
    }
}
