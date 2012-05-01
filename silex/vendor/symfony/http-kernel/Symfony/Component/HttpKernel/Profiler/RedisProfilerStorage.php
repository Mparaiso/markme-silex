<?php










namespace Symfony\Component\HttpKernel\Profiler;

use Redis;






class RedisProfilerStorage implements ProfilerStorageInterface
{
const TOKEN_PREFIX = 'sf_profiler_';

protected $dsn;
protected $lifetime;




private $redis;









public function __construct($dsn, $username = '', $password = '', $lifetime = 86400)
{
$this->dsn = $dsn;
$this->lifetime = (int) $lifetime;
}




public function find($ip, $url, $limit, $method)
{
$indexName = $this->getIndexName();

if (!$indexContent = $this->getValue($indexName, Redis::SERIALIZER_NONE)) {
return array();
}

$profileList = explode("\n", $indexContent);
$result = array();

foreach ($profileList as $item) {
if ($limit === 0) {
break;
}

if ($item == '') {
continue;
}

list($itemToken, $itemIp, $itemMethod, $itemUrl, $itemTime, $itemParent) = explode("\t", $item, 6);

if ($ip && false === strpos($itemIp, $ip) || $url && false === strpos($itemUrl, $url) || $method && false === strpos($itemMethod, $method)) {
continue;
}

$result[$itemToken] = array(
'token' => $itemToken,
'ip' => $itemIp,
'method' => $itemMethod,
'url' => $itemUrl,
'time' => $itemTime,
'parent' => $itemParent,
);
--$limit;
}

usort($result, function($a, $b) {
if ($a['time'] === $b['time']) {
return 0;
}

return $a['time'] > $b['time'] ? -1 : 1;
});

return $result;
}




public function purge()
{

 $indexName = $this->getIndexName();

$indexContent = $this->getValue($indexName, Redis::SERIALIZER_NONE);

if (!$indexContent) {
return false;
}

$profileList = explode("\n", $indexContent);

$result = array();

foreach ($profileList as $item) {
if ($item == '') {
continue;
}

if (false !== $pos = strpos($item, "\t")) {
$result[] = $this->getItemName(substr($item, 0, $pos));
}
}

$result[] = $indexName;

return $this->delete($result);
}




public function read($token)
{
if (empty($token)) {
return false;
}

$profile = $this->getValue($this->getItemName($token), Redis::SERIALIZER_PHP);

if (false !== $profile) {
$profile = $this->createProfileFromData($token, $profile);
}

return $profile;
}




public function write(Profile $profile)
{
$data = array(
'token' => $profile->getToken(),
'parent' => $profile->getParentToken(),
'children' => array_map(function ($p) { return $p->getToken(); }, $profile->getChildren()),
'data' => $profile->getCollectors(),
'ip' => $profile->getIp(),
'method' => $profile->getMethod(),
'url' => $profile->getUrl(),
'time' => $profile->getTime(),
);

if ($this->setValue($this->getItemName($profile->getToken()), $data, $this->lifetime, Redis::SERIALIZER_PHP)) {

 $indexName = $this->getIndexName();

$indexRow = implode("\t", array(
$profile->getToken(),
$profile->getIp(),
$profile->getMethod(),
$profile->getUrl(),
$profile->getTime(),
$profile->getParentToken(),
))."\n";

return $this->appendValue($indexName, $indexRow, $this->lifetime);
}

return false;
}






protected function getRedis()
{
if (null === $this->redis) {
if (!preg_match('#^redis://(?(?=\[.*\])\[(.*)\]|(.*)):(.*)$#', $this->dsn, $matches)) {
throw new \RuntimeException(sprintf('Please check your configuration. You are trying to use Redis with an invalid dsn "%s". The expected format is "redis://[host]:port".', $this->dsn));
}

$host = $matches[1] ?: $matches[2];
$port = $matches[3];

if (!extension_loaded('redis')) {
throw new \RuntimeException('RedisProfilerStorage requires that the redis extension is loaded.');
}

$redis = new Redis;
$redis->connect($host, $port);

$redis->setOption(Redis::OPT_PREFIX, self::TOKEN_PREFIX);

$this->redis = $redis;
}

return $this->redis;
}

private function createProfileFromData($token, $data, $parent = null)
{
$profile = new Profile($token);
$profile->setIp($data['ip']);
$profile->setMethod($data['method']);
$profile->setUrl($data['url']);
$profile->setTime($data['time']);
$profile->setCollectors($data['data']);

if (!$parent && $data['parent']) {
$parent = $this->read($data['parent']);
}

if ($parent) {
$profile->setParent($parent);
}

foreach ($data['children'] as $token) {
if (!$token) {
continue;
}

if (!$childProfileData = $this->getValue($this->getItemName($token), Redis::SERIALIZER_PHP)) {
continue;
}

$profile->addChild($this->createProfileFromData($token, $childProfileData, $profile));
}

return $profile;
}








private function getItemName($token)
{
$name = $token;

if ($this->isItemNameValid($name)) {
return $name;
}

return false;
}






private function getIndexName()
{
$name = 'index';

if ($this->isItemNameValid($name)) {
return $name;
}

return false;
}

private function isItemNameValid($name)
{
$length = strlen($name);

if ($length > 2147483648) {
throw new \RuntimeException(sprintf('The Redis item key "%s" is too long (%s bytes). Allowed maximum size is 2^31 bytes.', $name, $length));
}

return true;
}









private function getValue($key, $serializer = Redis::SERIALIZER_NONE)
{
$redis = $this->getRedis();
$redis->setOption(Redis::OPT_SERIALIZER, $serializer);

return $redis->get($key);
}











private function setValue($key, $value, $expiration = 0, $serializer = Redis::SERIALIZER_NONE)
{
$redis = $this->getRedis();
$redis->setOption(Redis::OPT_SERIALIZER, $serializer);

return $redis->setex($key, $expiration, $value);
}










private function appendValue($key, $value, $expiration = 0)
{
$redis = $this->getRedis();
$redis->setOption(Redis::OPT_SERIALIZER, Redis::SERIALIZER_NONE);

if ($redis->exists($key)) {
$redis->append($key, $value);

return $redis->setTimeout($key, $expiration);
}

return $redis->setex($key, $expiration, $value);
}








private function delete(array $keys)
{
return (bool) $this->getRedis()->delete($keys);
}
}
