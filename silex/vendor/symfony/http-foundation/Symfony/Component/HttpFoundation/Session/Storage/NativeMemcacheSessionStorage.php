<?php










namespace Symfony\Component\HttpFoundation\Session\Storage;








class NativeMemcacheSessionStorage extends AbstractSessionStorage
{
    


    private $savePath;

    







    public function __construct($savePath = 'tcp://127.0.0.1:11211?persistent=0', array $options = array())
    {
        if (!extension_loaded('memcache')) {
            throw new \RuntimeException('PHP does not have "memcache" session module registered');
        }

        $this->savePath = $savePath;
        parent::__construct($options);
    }

    


    protected function registerSaveHandlers()
    {
        ini_set('session.save_handler', 'memcache');
        ini_set('session.save_path', $this->savePath);
    }

    






    protected function setOptions(array $options)
    {
        foreach ($options as $key => $value) {
            if (in_array($key, array(
                'memcache.allow_failover', 'memcache.max_failover_attempts',
                'memcache.chunk_size', 'memcache.default_port', 'memcache.hash_strategy',
                'memcache.hash_function', 'memcache.protocol', 'memcache.redundancy',
                'memcache.session_redundancy', 'memcache.compress_threshold',
                'memcache.lock_timeout'))) {
                ini_set($key, $value);
            }
        }

        parent::setOptions($options);
    }
}
