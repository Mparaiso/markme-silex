<?php










namespace Symfony\Component\HttpFoundation\Session\Storage\Handler;








class NativeSqliteSessionHandler extends NativeSessionHandler
{






public function __construct($savePath, array $options = array())
{
if (!extension_loaded('sqlite')) {
throw new \RuntimeException('PHP does not have "sqlite" session module registered');
}

if (null === $savePath) {
$savePath = ini_get('session.save_path');
}

ini_set('session.save_handler', 'sqlite');
ini_set('session.save_path', $savePath);

$this->setOptions($options);
}






protected function setOptions(array $options)
{
foreach ($options as $key => $value) {
if (in_array($key, array('sqlite.assoc_case'))) {
ini_set($key, $value);
}
}
}
}
