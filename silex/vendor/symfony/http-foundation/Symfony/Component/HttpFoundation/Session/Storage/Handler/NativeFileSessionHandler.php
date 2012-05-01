<?php










namespace Symfony\Component\HttpFoundation\Session\Storage\Handler;








class NativeFileSessionHandler extends NativeSessionHandler
{





public function __construct($savePath = null)
{
if (null === $savePath) {
$savePath = ini_get('session.save_path');
}

if ($savePath && !is_dir($savePath)) {
mkdir($savePath, 0777, true);
}

ini_set('session.save_handler', 'files');
ini_set('session.save_path', $savePath);
}
}
