<?php










namespace Symfony\Component\HttpFoundation\Session\Storage\Handler;







class PdoSessionHandler implements \SessionHandlerInterface
{





private $pdo;







private $dbOptions;










public function __construct(\PDO $pdo, array $dbOptions = array(), array $options = array())
{
if (!array_key_exists('db_table', $dbOptions)) {
throw new \InvalidArgumentException('You must provide the "db_table" option for a PdoSessionStorage.');
}

$this->pdo = $pdo;
$this->dbOptions = array_merge(array(
'db_id_col' => 'sess_id',
'db_data_col' => 'sess_data',
'db_time_col' => 'sess_time',
), $dbOptions);
}




public function open($path, $name)
{
return true;
}




public function close()
{
return true;
}




public function destroy($id)
{

 $dbTable = $this->dbOptions['db_table'];
$dbIdCol = $this->dbOptions['db_id_col'];


 $sql = "DELETE FROM $dbTable WHERE $dbIdCol = :id";

try {
$stmt = $this->pdo->prepare($sql);
$stmt->bindParam(':id', $id, \PDO::PARAM_STR);
$stmt->execute();
} catch (\PDOException $e) {
throw new \RuntimeException(sprintf('PDOException was thrown when trying to manipulate session data: %s', $e->getMessage()), 0, $e);
}

return true;
}




public function gc($lifetime)
{

 $dbTable = $this->dbOptions['db_table'];
$dbTimeCol = $this->dbOptions['db_time_col'];


 $sql = "DELETE FROM $dbTable WHERE $dbTimeCol < (:time - $lifetime)";

try {
$stmt = $this->pdo->prepare($sql);
$stmt->bindValue(':time', time(), \PDO::PARAM_INT);
$stmt->execute();
} catch (\PDOException $e) {
throw new \RuntimeException(sprintf('PDOException was thrown when trying to manipulate session data: %s', $e->getMessage()), 0, $e);
}

return true;
}




public function read($id)
{

 $dbTable = $this->dbOptions['db_table'];
$dbDataCol = $this->dbOptions['db_data_col'];
$dbIdCol = $this->dbOptions['db_id_col'];

try {
$sql = "SELECT $dbDataCol FROM $dbTable WHERE $dbIdCol = :id";

$stmt = $this->pdo->prepare($sql);
$stmt->bindParam(':id', $id, \PDO::PARAM_STR);

$stmt->execute();

 
 $sessionRows = $stmt->fetchAll(\PDO::FETCH_NUM);

if (count($sessionRows) == 1) {
return base64_decode($sessionRows[0][0]);
}


 $this->createNewSession($id);

return '';
} catch (\PDOException $e) {
throw new \RuntimeException(sprintf('PDOException was thrown when trying to read the session data: %s', $e->getMessage()), 0, $e);
}
}




public function write($id, $data)
{

 $dbTable = $this->dbOptions['db_table'];
$dbDataCol = $this->dbOptions['db_data_col'];
$dbIdCol = $this->dbOptions['db_id_col'];
$dbTimeCol = $this->dbOptions['db_time_col'];

$sql = ('mysql' === $this->pdo->getAttribute(\PDO::ATTR_DRIVER_NAME))
? "INSERT INTO $dbTable ($dbIdCol, $dbDataCol, $dbTimeCol) VALUES (:id, :data, :time) "
."ON DUPLICATE KEY UPDATE $dbDataCol = VALUES($dbDataCol), $dbTimeCol = CASE WHEN $dbTimeCol = :time THEN (VALUES($dbTimeCol) + 1) ELSE VALUES($dbTimeCol) END"
: "UPDATE $dbTable SET $dbDataCol = :data, $dbTimeCol = :time WHERE $dbIdCol = :id";

try {

 $encoded = base64_encode($data);
$stmt = $this->pdo->prepare($sql);
$stmt->bindParam(':id', $id, \PDO::PARAM_STR);
$stmt->bindParam(':data', $encoded, \PDO::PARAM_STR);
$stmt->bindValue(':time', time(), \PDO::PARAM_INT);
$stmt->execute();

if (!$stmt->rowCount()) {

 
 $this->createNewSession($id, $data);
}
} catch (\PDOException $e) {
throw new \RuntimeException(sprintf('PDOException was thrown when trying to write the session data: %s', $e->getMessage()), 0, $e);
}

return true;
}









private function createNewSession($id, $data = '')
{

 $dbTable = $this->dbOptions['db_table'];
$dbDataCol = $this->dbOptions['db_data_col'];
$dbIdCol = $this->dbOptions['db_id_col'];
$dbTimeCol = $this->dbOptions['db_time_col'];

$sql = "INSERT INTO $dbTable ($dbIdCol, $dbDataCol, $dbTimeCol) VALUES (:id, :data, :time)";


 $encoded = base64_encode($data);
$stmt = $this->pdo->prepare($sql);
$stmt->bindParam(':id', $id, \PDO::PARAM_STR);
$stmt->bindParam(':data', $encoded, \PDO::PARAM_STR);
$stmt->bindValue(':time', time(), \PDO::PARAM_INT);
$stmt->execute();

return true;
}
}
