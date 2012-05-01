<?php










namespace Symfony\Component\HttpKernel\Profiler;

class MongoDbProfilerStorage implements ProfilerStorageInterface
{
protected $dsn;
protected $lifetime;
private $mongo;









public function __construct($dsn, $username = '', $password = '', $lifetime = 86400)
{
$this->dsn = $dsn;
$this->lifetime = (int) $lifetime;
}











public function find($ip, $url, $limit, $method)
{
$cursor = $this->getMongo()->find($this->buildQuery($ip, $url, $method), array('_id', 'parent', 'ip', 'method', 'url', 'time'))->sort(array('time' => -1))->limit($limit);

$tokens = array();
foreach ($cursor as $profile) {
$tokens[] = $this->getData($profile);
}

return $tokens;
}




public function purge()
{
$this->getMongo()->remove(array());
}










public function read($token)
{
$profile = $this->getMongo()->findOne(array('_id' => $token, 'data' => array('$exists' => true)));

if (null !== $profile) {
$profile = $this->createProfileFromData($this->getData($profile));
}

return $profile;
}








public function write(Profile $profile)
{
$this->cleanup();

$record = array(
'_id' => $profile->getToken(),
'parent' => $profile->getParentToken(),
'data' => serialize($profile->getCollectors()),
'ip' => $profile->getIp(),
'method' => $profile->getMethod(),
'url' => $profile->getUrl(),
'time' => $profile->getTime()
);

return $this->getMongo()->update(array('_id' => $profile->getToken()), array_filter($record, function ($v) { return !empty($v); }), array('upsert' => true));
}






protected function getMongo()
{
if ($this->mongo === null) {
if (preg_match('#^(mongodb://.*)/(.*)/(.*)$#', $this->dsn, $matches)) {
$mongo = new \Mongo($matches[1]);
$database = $matches[2];
$collection = $matches[3];
$this->mongo = $mongo->selectCollection($database, $collection);
} else {
throw new \RuntimeException(sprintf('Please check your configuration. You are trying to use MongoDB with an invalid dsn "%s". The expected format is "mongodb://user:pass@location/database/collection"', $this->dsn));
}
}

return $this->mongo;
}





protected function createProfileFromData(array $data)
{
$profile = $this->getProfile($data);

if ($data['parent']) {
$parent = $this->getMongo()->findOne(array('_id' => $data['parent'], 'data' => array('$exists' => true)));
if ($parent) {
$profile->setParent($this->getProfile($this->getData($parent)));
}
}

$profile->setChildren($this->readChildren($data['token']));

return $profile;
}





protected function readChildren($token)
{
$profiles = array();

$cursor = $this->getMongo()->find(array('parent' => $token, 'data' => array('$exists' => true)));
foreach ($cursor as $d) {
$profiles[] = $this->getProfile($this->getData($d));
}

return $profiles;
}

protected function cleanup()
{
$this->getMongo()->remove(array('time' => array('$lt' => time() - $this->lifetime)));
}







private function buildQuery($ip, $url, $method)
{
$query = array();

if (!empty($ip)) {
$query['ip'] = $ip;
}

if (!empty($url)) {
$query['url'] = $url;
}

if (!empty($method)) {
$query['method'] = $method;
}

return $query;
}





private function getData(array $data)
{
return array(
'token' => $data['_id'],
'parent' => isset($data['parent']) ? $data['parent'] : null,
'ip' => isset($data['ip']) ? $data['ip'] : null,
'method' => isset($data['method']) ? $data['method'] : null,
'url' => isset($data['url']) ? $data['url'] : null,
'time' => isset($data['time']) ? $data['time'] : null,
'data' => isset($data['data']) ? $data['data'] : null,
);
}





private function getProfile(array $data)
{
$profile = new Profile($data['token']);
$profile->setIp($data['ip']);
$profile->setMethod($data['method']);
$profile->setUrl($data['url']);
$profile->setTime($data['time']);
$profile->setCollectors(unserialize($data['data']));

return $profile;
}
}
