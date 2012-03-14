<?php










namespace Symfony\Component\HttpFoundation\Session;

use Symfony\Component\HttpFoundation\Session\Storage\SessionStorageInterface;
use Symfony\Component\HttpFoundation\Session\Attribute\AttributeBag;
use Symfony\Component\HttpFoundation\Session\Attribute\AttributeBagInterface;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBag;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\HttpFoundation\Session\SessionBagInterface;









class Session implements SessionInterface
{
    




    protected $storage;

    






    public function __construct(SessionStorageInterface $storage, AttributeBagInterface $attributes = null, FlashBagInterface $flashes = null)
    {
        $this->storage = $storage;
        $this->registerBag($attributes ?: new AttributeBag());
        $this->registerBag($flashes ?: new FlashBag());
    }

    


    public function start()
    {
        return $this->storage->start();
    }

    


    public function has($name)
    {
        return $this->storage->getBag('attributes')->has($name);
    }

    


    public function get($name, $default = null)
    {
        return $this->storage->getBag('attributes')->get($name, $default);
    }

    


    public function set($name, $value)
    {
        $this->storage->getBag('attributes')->set($name, $value);
    }

    


    public function all()
    {
        return $this->storage->getBag('attributes')->all();
    }

    


    public function replace(array $attributes)
    {
        $this->storage->getBag('attributes')->replace($attributes);
    }

    


    public function remove($name)
    {
        return $this->storage->getBag('attributes')->remove($name);
    }

    


    public function clear()
    {
        $this->storage->getBag('attributes')->clear();
    }

    


    public function invalidate()
    {
        $this->storage->clear();

        return $this->storage->regenerate(true);
    }

    


    public function migrate($destroy = false)
    {
        return $this->storage->regenerate($destroy);
    }

    


    public function save()
    {
        $this->storage->save();
    }

    






    public function getId()
    {
        return $this->storage->getId();
    }

    




    public function registerBag(SessionBagInterface $bag)
    {
        $this->storage->registerBag($bag);
    }

    






    public function getBag($name)
    {
        return $this->storage->getBag($name);
    }

    




    public function getFlashBag()
    {
        return $this->getBag('flashes');
    }

    

    




    public function getFlashes()
    {
        return $this->getBag('flashes')->all();
    }

    




    public function setFlashes($values)
    {
       $this->getBag('flashes')->setAll($values);
    }

    







    public function getFlash($name, $default = null)
    {
       return $this->getBag('flashes')->get($name, $default);
    }

    





    public function setFlash($name, $value)
    {
       $this->getBag('flashes')->set($name, $value);
    }

    






    public function hasFlash($name)
    {
       return $this->getBag('flashes')->has($name);
    }

    




    public function removeFlash($name)
    {
       $this->getBag('flashes')->get($name);
    }

    




    public function clearFlashes()
    {
       return $this->getBag('flashes')->clear();
    }
}
