<?php










namespace Symfony\Component\HttpFoundation\Session\Storage;








class NativeMemcachedSessionStorage extends AbstractSessionStorage
{
    


    private $savePath;

    







    public function __construct($savePath = '127.0.0.1:11211', array $options = array())
    {
        if (!extension_loaded('memcached')) {
            throw new \RuntimeException('PHP does not have "memcached" session module registered');
        }

        $this->savePath = $savePath;
        parent::__construct($options);
    }

    


    protected function registerSaveHandlers()
    {
        ini_set('session.save_handler', 'memcached');
        ini_set('session.save_path', $this->savePath);
    }

    






    protected function setOptions(array $options)
    {
        foreach ($options as $key => $value) {
            if (in_array($key, array(
                'memcached.sess_locking', 'memcached.sess_lock_wait',
                'memcached.sess_prefix', 'memcached.compression_type',
                'memcached.compression_factor', 'memcached.compression_threshold',
                'memcached.serializer'))) {
                ini_set($key, $value);
            }
        }

        parent::setOptions($options);
    }
}
