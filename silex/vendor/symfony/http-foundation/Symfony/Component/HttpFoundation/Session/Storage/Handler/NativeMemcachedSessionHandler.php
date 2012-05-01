<?php










namespace Symfony\Component\HttpFoundation\Session\Storage\Handler;










class NativeMemcachedSessionHandler extends NativeSessionHandler
{






public function __construct($savePath = '127.0.0.1:11211', array $options = array())
{
if (!extension_loaded('memcached')) {
throw new \RuntimeException('PHP does not have "memcached" session module registered');
}

if (null === $savePath) {
$savePath = ini_get('session.save_path');
}

ini_set('session.save_handler', 'memcached');
ini_set('session.save_path', $savePath);

$this->setOptions($options);
}






protected function setOptions(array $options)
{
$validOptions = array_flip(array(
'memcached.sess_locking', 'memcached.sess_lock_wait',
'memcached.sess_prefix', 'memcached.compression_type',
'memcached.compression_factor', 'memcached.compression_threshold',
'memcached.serializer',
));

foreach ($options as $key => $value) {
if (isset($validOptions[$key])) {
ini_set($key, $value);
}
}
}
}
