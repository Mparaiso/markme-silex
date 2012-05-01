<?php










namespace Symfony\Component\HttpFoundation\Session\Storage\Handler;






class MongoDbSessionHandler implements \SessionHandlerInterface
{



private $mongo;




private $collection;




private $options;









public function __construct(\Mongo $mongo, array $options)
{
if (!isset($options['database']) || !isset($options['collection'])) {
throw new \InvalidArgumentException('You must provide the "database" and "collection" option for MongoDBSessionHandler');
}

$this->mongo = $mongo;

$this->options = array_merge(array(
'id_field' => 'sess_id',
'data_field' => 'sess_data',
'time_field' => 'sess_time',
), $options);
}




public function open($savePath, $sessionName)
{
return true;
}




public function close()
{
return true;
}




public function destroy($sessionId)
{
$this->getCollection()->remove(
array($this->options['id_field'] => $sessionId),
array('justOne' => true)
);

return true;
}




public function gc($lifetime)
{
$time = new \MongoTimestamp(time() - $lifetime);

$this->getCollection()->remove(array(
$this->options['time_field'] => array('$lt' => $time),
));
}




public function write($sessionId, $data)
{
$data = array(
$this->options['id_field'] => $sessionId,
$this->options['data_field'] => new \MongoBinData($data),
$this->options['time_field'] => new \MongoTimestamp()
);

$this->getCollection()->update(
array($this->options['id_field'] => $sessionId),
array('$set' => $data),
array('upsert' => true)
);

return true;
}




public function read($sessionId)
{
$dbData = $this->getCollection()->findOne(array(
$this->options['id_field'] => $sessionId,
));

return null === $dbData ? '' : $dbData[$this->options['data_field']]->bin;
}






private function getCollection()
{
if (null === $this->collection) {
$this->collection = $this->mongo->selectDB($this->options['database'])->selectCollection($this->options['collection']);
}

return $this->collection;
}
}