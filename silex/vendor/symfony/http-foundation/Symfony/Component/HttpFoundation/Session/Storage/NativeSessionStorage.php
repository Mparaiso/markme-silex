<?php










namespace Symfony\Component\HttpFoundation\Session\Storage;

use Symfony\Component\HttpFoundation\Session\SessionBagInterface;
use Symfony\Component\HttpFoundation\Session\Storage\MetadataBag;
use Symfony\Component\HttpFoundation\Session\Storage\Proxy\NativeProxy;
use Symfony\Component\HttpFoundation\Session\Storage\Proxy\AbstractProxy;
use Symfony\Component\HttpFoundation\Session\Storage\Proxy\SessionHandlerProxy;






class NativeSessionStorage implements SessionStorageInterface
{





protected $bags;




protected $started = false;




protected $closed = false;




protected $saveHandler;




protected $metadataBag;











































public function __construct(array $options = array(), $handler = null, MetadataBag $metaBag = null)
{

 ini_set('session.auto_start', 0); 
 ini_set('session.cache_limiter', ''); 
 ini_set('session.use_cookies', 1);

if (version_compare(phpversion(), '5.4.0', '>=')) {
session_register_shutdown();
} else {
register_shutdown_function('session_write_close');
}

$this->setMetadataBag($metaBag);
$this->setOptions($options);
$this->setSaveHandler($handler);
}






public function getSaveHandler()
{
return $this->saveHandler;
}




public function start()
{
if ($this->started && !$this->closed) {
return true;
}


 if (!$this->started && !$this->closed && $this->saveHandler->isActive()
&& $this->saveHandler->isSessionHandlerInterface()) {
$this->loadSession();

return true;
}

if (ini_get('session.use_cookies') && headers_sent()) {
throw new \RuntimeException('Failed to start the session because headers have already been sent.');
}


 if (!session_start()) {
throw new \RuntimeException('Failed to start the session');
}

$this->loadSession();

if (!$this->saveHandler->isWrapper() && !$this->saveHandler->isSessionHandlerInterface()) {
$this->saveHandler->setActive(false);
}

return true;
}




public function getId()
{
if (!$this->started) {
return ''; 
 }

return $this->saveHandler->getId();
}




public function setId($id)
{
return $this->saveHandler->setId($id);
}




public function getName()
{
return $this->saveHandler->getName();
}




public function setName($name)
{
$this->saveHandler->setName($name);
}




public function regenerate($destroy = false, $lifetime = null)
{
if (null !== $lifetime) {
ini_set('session.cookie_lifetime', $lifetime);
}

if ($destroy) {
$this->metadataBag->stampNew();
}

return session_regenerate_id($destroy);
}




public function save()
{
session_write_close();

if (!$this->saveHandler->isWrapper() && !$this->getSaveHandler()->isSessionHandlerInterface()) {
$this->saveHandler->setActive(false);
}

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

if (ini_get('session.auto_start') && !$this->started) {
$this->start();
} elseif ($this->saveHandler->isActive() && !$this->started) {
$this->loadSession();
}

return $this->bags[$name];
}






public function setMetadataBag(MetadataBag $metaBag = null)
{
if (null === $metaBag) {
$metaBag = new MetadataBag();
}

$this->metadataBag = $metaBag;
}






public function getMetadataBag()
{
return $this->metadataBag;
}











public function setOptions(array $options)
{
$validOptions = array_flip(array(
'auto_start', 'cache_limiter', 'cookie_domain', 'cookie_httponly',
'cookie_lifetime', 'cookie_path', 'cookie_secure',
'entropy_file', 'entropy_length', 'gc_divisor',
'gc_maxlifetime', 'gc_probability', 'hash_bits_per_character',
'hash_function', 'name', 'referer_check',
'serialize_handler', 'use_cookies',
'use_only_cookies', 'use_trans_sid', 'upload_progress.enabled',
'upload_progress.cleanup', 'upload_progress.prefix', 'upload_progress.name',
'upload_progress.freq', 'upload_progress.min-freq', 'url_rewriter.tags',
));

foreach ($options as $key => $value) {
if (isset($validOptions[$key])) {
ini_set('session.'.$key, $value);
}
}
}
















public function setSaveHandler($saveHandler = null)
{

 if (!$saveHandler instanceof AbstractProxy && $saveHandler instanceof \SessionHandlerInterface) {
$saveHandler = new SessionHandlerProxy($saveHandler);
} elseif (!$saveHandler instanceof AbstractProxy) {
$saveHandler = new NativeProxy($saveHandler);
}

$this->saveHandler = $saveHandler;

if ($this->saveHandler instanceof \SessionHandlerInterface) {
if (version_compare(phpversion(), '5.4.0', '>=')) {
session_set_save_handler($this->saveHandler, false);
} else {
session_set_save_handler(
array($this->saveHandler, 'open'),
array($this->saveHandler, 'close'),
array($this->saveHandler, 'read'),
array($this->saveHandler, 'write'),
array($this->saveHandler, 'destroy'),
array($this->saveHandler, 'gc')
);
}
}
}











protected function loadSession(array &$session = null)
{
if (null === $session) {
$session = &$_SESSION;
}

$bags = array_merge($this->bags, array($this->metadataBag));

foreach ($bags as $bag) {
$key = $bag->getStorageKey();
$session[$key] = isset($session[$key]) ? $session[$key] : array();
$bag->initialize($session[$key]);
}

$this->started = true;
$this->closed = false;
}
}
