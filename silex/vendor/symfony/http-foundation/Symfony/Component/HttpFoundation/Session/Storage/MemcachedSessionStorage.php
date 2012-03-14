<?php










namespace Symfony\Component\HttpFoundation\Session\Storage;






class MemcachedSessionStorage extends AbstractSessionStorage implements \SessionHandlerInterface
{
    




    private $memcached;

    




    private $memcachedOptions;

    








    public function __construct(\Memcached $memcached, array $memcachedOptions = array(), array $options = array())
    {
        $this->memcached = $memcached;

        
        if (!isset($memcachedOptions['serverpool'])) {
            $memcachedOptions['serverpool'][] = array(
                'host' => '127.0.0.1',
                'port' => 11211,
                'weight' => 1);
        }

        $memcachedOptions['expiretime'] = isset($memcachedOptions['expiretime']) ? (int)$memcachedOptions['expiretime'] : 86400;

        $this->memcached->setOption(\Memcached::OPT_PREFIX_KEY, isset($memcachedOptions['prefix']) ? $memcachedOptions['prefix'] : 'sf2s');

        $this->memcachedOptions = $memcachedOptions;

        parent::__construct($options);
    }

    


    public function open($savePath, $sessionName)
    {
        return $this->memcached->addServers($this->memcachedOptions['serverpool']);
    }

    


    public function close()
    {
        return true;
    }

    


    public function read($sessionId)
    {
        return $this->memcached->get($sessionId) ?: '';
    }

    


    public function write($sessionId, $data)
    {
        return $this->memcached->set($sessionId, $data, $this->memcachedOptions['expiretime']);
    }

    


    public function destroy($sessionId)
    {
        return $this->memcached->delete($sessionId);
    }

    


    public function gc($lifetime)
    {
        
        return true;
    }

    




    protected function addServer(array $server)
    {
        if (array_key_exists('host', $server)) {
            throw new \InvalidArgumentException('host key must be set');
        }
        $server['port'] = isset($server['port']) ? (int)$server['port'] : 11211;
        $server['timeout'] = isset($server['timeout']) ? (int)$server['timeout'] : 1;
        $server['presistent'] = isset($server['presistent']) ? (bool)$server['presistent'] : false;
        $server['weight'] = isset($server['weight']) ? (bool)$server['weight'] : 1;
    }
}
