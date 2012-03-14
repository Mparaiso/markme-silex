<?php










namespace Symfony\Component\HttpFoundation\Session\Storage;






class MemcacheSessionStorage extends AbstractSessionStorage implements \SessionHandlerInterface
{
    




    private $memcache;

    




    private $memcacheOptions;

    




    private $prefix;

    








    public function __construct(\Memcache $memcache, array $memcacheOptions = array(), array $options = array())
    {
        $this->memcache = $memcache;

        
        if (!isset($memcacheOptions['serverpool'])) {
            $memcacheOptions['serverpool'] = array(array(
                'host' => '127.0.0.1',
                'port' => 11211,
                'timeout' => 1,
                'persistent' => false,
                'weight' => 1,
                'retry_interval' => 15,
            ));
        }

        $memcacheOptions['expiretime'] = isset($memcacheOptions['expiretime']) ? (int)$memcacheOptions['expiretime'] : 86400;
        $this->prefix = isset($memcacheOptions['prefix']) ? $memcacheOptions['prefix'] : 'sf2s';

        $this->memcacheOptions = $memcacheOptions;

        parent::__construct($options);
    }

    protected function addServer(array $server)
    {
        if (!array_key_exists('host', $server)) {
            throw new \InvalidArgumentException('host key must be set');
        }

        $server['port'] = isset($server['port']) ? (int)$server['port'] : 11211;
        $server['timeout'] = isset($server['timeout']) ? (int)$server['timeout'] : 1;
        $server['persistent'] = isset($server['persistent']) ? (bool)$server['persistent'] : false;
        $server['weight'] = isset($server['weight']) ? (int)$server['weight'] : 1;
        $server['retry_interval'] = isset($server['retry_interval']) ? (int)$server['retry_interval'] : 15;

        $this->memcache->addserver($server['host'], $server['port'], $server['persistent'],$server['weight'],$server['timeout'],$server['retry_interval']);

    }

    


    public function open($savePath, $sessionName)
    {
        foreach ($this->memcacheOptions['serverpool'] as $server) {
            $this->addServer($server);
        }

        return true;
    }

    


    public function close()
    {
        return $this->memcache->close();
    }

    


    public function read($sessionId)
    {
        return $this->memcache->get($this->prefix.$sessionId) ?: '';
    }

    


    public function write($sessionId, $data)
    {
        return $this->memcache->set($this->prefix.$sessionId, $data, 0, $this->memcacheOptions['expiretime']);
    }

    


    public function destroy($sessionId)
    {
        return $this->memcache->delete($this->prefix.$sessionId);
    }

    


    public function gc($lifetime)
    {
        
        return true;
    }
}
