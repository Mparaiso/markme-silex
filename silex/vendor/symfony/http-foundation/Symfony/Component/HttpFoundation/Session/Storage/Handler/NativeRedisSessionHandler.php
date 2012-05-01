<?php










namespace Symfony\Component\HttpFoundation\Session\Storage\Handler;










class NativeRedisSessionHandler extends NativeSessionHandler
{





public function __construct($savePath = 'tcp://127.0.0.1:6379?persistent=0')
{
if (!extension_loaded('redis')) {
throw new \RuntimeException('PHP does not have "redis" session module registered');
}

if (null === $savePath) {
$savePath = ini_get('session.save_path');
}

ini_set('session.save_handler', 'redis');
ini_set('session.save_path', $savePath);
}
}
