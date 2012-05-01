<?php










namespace Symfony\Component\HttpFoundation\Session\Storage\Handler;










class NativeMemcacheSessionHandler extends NativeSessionHandler
{






public function __construct($savePath = 'tcp://127.0.0.1:11211?persistent=0', array $options = array())
{
if (!extension_loaded('memcache')) {
throw new \RuntimeException('PHP does not have "memcache" session module registered');
}

if (null === $savePath) {
$savePath = ini_get('session.save_path');
}

ini_set('session.save_handler', 'memcache');
ini_set('session.save_path', $savePath);

$this->setOptions($options);
}






protected function setOptions(array $options)
{
$validOptions = array_flip(array(
'memcache.allow_failover', 'memcache.max_failover_attempts',
'memcache.chunk_size', 'memcache.default_port', 'memcache.hash_strategy',
'memcache.hash_function', 'memcache.protocol', 'memcache.redundancy',
'memcache.session_redundancy', 'memcache.compress_threshold',
'memcache.lock_timeout',
));

foreach ($options as $key => $value) {
if (isset($validOptions[$key])) {
ini_set($key, $value);
}
}
}
}
