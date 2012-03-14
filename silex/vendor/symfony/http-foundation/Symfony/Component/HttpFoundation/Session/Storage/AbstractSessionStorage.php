<?php










namespace Symfony\Component\HttpFoundation\Session\Storage;

use Symfony\Component\HttpFoundation\Session\SessionBagInterface;






abstract class AbstractSessionStorage implements SessionStorageInterface
{
    




    protected $bags;

    


    protected $options = array();

    


    protected $started = false;

    


    protected $closed = false;

    








































    public function __construct(array $options = array())
    {
        $this->setOptions($options);
        $this->registerSaveHandlers();
        $this->registerShutdownFunction();
    }

    


    public function start()
    {
        if ($this->started && !$this->closed) {
            return true;
        }

        if ($this->options['use_cookies'] && headers_sent()) {
            throw new \RuntimeException('Failed to start the session because header have already been sent.');
        }

        
        if (!session_start()) {
            throw new \RuntimeException('Failed to start the session');
        }

        $this->loadSession();

        $this->started = true;
        $this->closed = false;

        return true;
    }

    


    public function getId()
    {
        if (!$this->started) {
            return ''; 
        }

        return session_id();
    }

    


    public function regenerate($destroy = false)
    {
        return session_regenerate_id($destroy);
    }

    


    public function save()
    {
        session_write_close();
        $this->closed = true;
    }

    


    public function clear()
    {
        
        foreach ($this->bags as $bag) {
            $bag->clear();
        }

        
        $_SESSION = array();

        
        $this->loadSession();
    }

    


    public function registerBag(SessionBagInterface $bag)
    {
        $this->bags[$bag->getName()] = $bag;
    }

    


    public function getBag($name)
    {
        if (!isset($this->bags[$name])) {
            throw new \InvalidArgumentException(sprintf('The SessionBagInterface %s is not registered.', $name));
        }

        if ($this->options['auto_start'] && !$this->started) {
            $this->start();
        }

        return $this->bags[$name];
    }

    











    protected function setOptions(array $options)
    {
        $cookieDefaults = session_get_cookie_params();
        $this->options = array_merge(array(
            'cookie_lifetime' => $cookieDefaults['lifetime'],
            'cookie_path' => $cookieDefaults['path'],
            'cookie_domain' => $cookieDefaults['domain'],
            'cookie_secure' => $cookieDefaults['secure'],
            'cookie_httponly' => isset($cookieDefaults['httponly']) ? $cookieDefaults['httponly'] : false,
            ), $options);

        
        
        if (!isset($this->options['cache_limiter'])) {
            $this->options['cache_limiter'] = false;
        }

        if (!isset($this->options['auto_start'])) {
            $this->options['auto_start'] = 0;
        }

        if (!isset($this->options['use_cookies'])) {
            $this->options['use_cookies'] = 1;
        }

        foreach ($this->options as $key => $value) {
            if (in_array($key, array(
                'auto_start', 'cache_limiter', 'cookie_domain', 'cookie_httponly',
                'cookie_lifetime', 'cookie_path', 'cookie_secure',
                'entropy_file', 'entropy_length', 'gc_divisor',
                'gc_maxlifetime', 'gc_probability', 'hash_bits_per_character',
                'hash_function', 'name', 'referer_check',
                'save_path', 'serialize_handler', 'use_cookies',
                'use_only_cookies', 'use_trans_sid', 'upload_progress.enabled',
                'upload_progress.cleanup', 'upload_progress.prefix', 'upload_progress.name',
                'upload_progress.freq', 'upload_progress.min-freq', 'url_rewriter.tags'))) {
                ini_set('session.'.$key, $value);
            }
        }
    }

    


































    protected function registerSaveHandlers()
    {
        
        
        if ($this instanceof \SessionHandlerInterface) {
            session_set_save_handler(
                array($this, 'open'),
                array($this, 'close'),
                array($this, 'read'),
                array($this, 'write'),
                array($this, 'destroy'),
                array($this, 'gc')
            );
        }
    }

    





    protected function registerShutdownFunction()
    {
        register_shutdown_function('session_write_close');
    }

    









    protected function loadSession(array &$session = null)
    {
        if (null === $session) {
            $session = &$_SESSION;
        }

        foreach ($this->bags as $bag) {
            $key = $bag->getStorageKey();
            $session[$key] = isset($session[$key]) ? $session[$key] : array();
            $bag->initialize($session[$key]);
        }
    }
}
