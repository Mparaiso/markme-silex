<?php










namespace Symfony\Component\HttpFoundation\Session\Flash;






class FlashBag implements FlashBagInterface
{
    private $name = 'flashes';

    




    private $flashes = array();

    




    private $storageKey;

    




    public function __construct($storageKey = '_sf2_flashes')
    {
        $this->storageKey = $storageKey;
    }

    


    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    


    public function initialize(array &$flashes)
    {
        $this->flashes = &$flashes;
    }

    


    public function peek($type, $default = null)
    {
        return $this->has($type) ? $this->flashes[$type] : $default;
    }

    


    public function peekAll()
    {
        return $this->flashes;
    }

    


    public function get($type, $default = null)
    {
        if (!$this->has($type)) {
            return $default;
        }

        $return = $this->flashes[$type];

        unset($this->flashes[$type]);

        return $return;
    }

    


    public function all()
    {
        $return = $this->peekAll();
        $this->flashes = array();

        return $return;
    }

    


    public function set($type, $message)
    {
        $this->flashes[$type] = $message;
    }

    


    public function setAll(array $messages)
    {
        $this->flashes = $messages;
    }

    


    public function has($type)
    {
        return array_key_exists($type, $this->flashes);
    }

    


    public function keys()
    {
        return array_keys($this->flashes);
    }

    


    public function getStorageKey()
    {
        return $this->storageKey;
    }

    


    public function clear()
    {
        return $this->all();
    }
}
