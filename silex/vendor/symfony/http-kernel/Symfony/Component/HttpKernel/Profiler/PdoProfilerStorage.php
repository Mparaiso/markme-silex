<?php










namespace Symfony\Component\HttpKernel\Profiler;








abstract class PdoProfilerStorage implements ProfilerStorageInterface
{
    protected $dsn;
    protected $username;
    protected $password;
    protected $lifetime;
    protected $db;

    







    public function __construct($dsn, $username = '', $password = '', $lifetime = 86400)
    {
        $this->dsn = $dsn;
        $this->username = $username;
        $this->password = $password;
        $this->lifetime = (int) $lifetime;
    }

    


    public function find($ip, $url, $limit, $method)
    {
        list($criteria, $args) = $this->buildCriteria($ip, $url, $limit, $method);

        $criteria = $criteria ? 'WHERE '.implode(' AND ', $criteria) : '';

        $db = $this->initDb();
        $tokens = $this->fetch($db, 'SELECT token, ip, method, url, time, parent FROM sf_profiler_data '.$criteria.' ORDER BY time DESC LIMIT '.((integer) $limit), $args);
        $this->close($db);

        return $tokens;
    }

    


    public function read($token)
    {
        $db = $this->initDb();
        $args = array(':token' => $token);
        $data = $this->fetch($db, 'SELECT data, parent, ip, method, url, time FROM sf_profiler_data WHERE token = :token LIMIT 1', $args);
        $this->close($db);
        if (isset($data[0]['data'])) {
            return $this->createProfileFromData($token, $data[0]);
        }

        return null;
    }

    


    public function write(Profile $profile)
    {
        $db = $this->initDb();
        $args = array(
            ':token'      => $profile->getToken(),
            ':parent'     => $profile->getParentToken(),
            ':data'       => base64_encode(serialize($profile->getCollectors())),
            ':ip'         => $profile->getIp(),
            ':method'     => $profile->getMethod(),
            ':url'        => $profile->getUrl(),
            ':time'       => $profile->getTime(),
            ':created_at' => time(),
        );

        try {
            if ($this->read($profile->getToken())) {
                $this->exec($db, 'UPDATE sf_profiler_data SET parent = :parent, data = :data, ip = :ip, method = :method, url = :url, time = :time, created_at = :created_at WHERE token = :token', $args);
            } else {
                $this->exec($db, 'INSERT INTO sf_profiler_data (token, parent, data, ip, method, url, time, created_at) VALUES (:token, :parent, :data, :ip, :method, :url, :time, :created_at)', $args);
            }
            $this->cleanup();
            $status = true;
        } catch (\Exception $e) {
            $status = false;
        }

        $this->close($db);

        return $status;
    }

    


    public function purge()
    {
        $db = $this->initDb();
        $this->exec($db, 'DELETE FROM sf_profiler_data');
        $this->close($db);
    }

    









    abstract protected function buildCriteria($ip, $url, $limit, $method);

    




    abstract protected function initDb();

    protected function cleanup()
    {
        $db = $this->initDb();
        $this->exec($db, 'DELETE FROM sf_profiler_data WHERE created_at < :time', array(':time' => time() - $this->lifetime));
        $this->close($db);
    }

    protected function exec($db, $query, array $args = array())
    {
        $stmt = $this->prepareStatement($db, $query);

        foreach ($args as $arg => $val) {
            $stmt->bindValue($arg, $val, is_int($val) ? \PDO::PARAM_INT : \PDO::PARAM_STR);
        }
        $success = $stmt->execute();
        if (!$success) {
            throw new \RuntimeException(sprintf('Error executing query "%s"', $query));
        }
    }

    protected function prepareStatement($db, $query)
    {
        try {
            $stmt = $db->prepare($query);
        } catch (\Exception $e) {
            $stmt = false;
        }

        if (false === $stmt) {
            throw new \RuntimeException('The database cannot successfully prepare the statement');
        }

        return $stmt;
    }

    protected function fetch($db, $query, array $args = array())
    {
        $stmt = $this->prepareStatement($db, $query);

        foreach ($args as $arg => $val) {
            $stmt->bindValue($arg, $val, is_int($val) ? \PDO::PARAM_INT : \PDO::PARAM_STR);
        }
        $stmt->execute();
        $return = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        return $return;
    }

    protected function close($db)
    {
    }

    protected function createProfileFromData($token, $data, $parent = null)
    {
        $profile = new Profile($token);
        $profile->setIp($data['ip']);
        $profile->setMethod($data['method']);
        $profile->setUrl($data['url']);
        $profile->setTime($data['time']);
        $profile->setCollectors(unserialize(base64_decode($data['data'])));

        if (!$parent && !empty($data['parent'])) {
            $parent = $this->read($data['parent']);
        }

        if ($parent) {
            $profile->setParent($parent);
        }

        $profile->setChildren($this->readChildren($token, $profile));

        return $profile;
    }

    







    protected function readChildren($token, $parent)
    {
        $db = $this->initDb();
        $data = $this->fetch($db, 'SELECT token, data, ip, method, url, time FROM sf_profiler_data WHERE parent = :token', array(':token' => $token));
        $this->close($db);

        if (!$data) {
            return array();
        }

        $profiles = array();
        foreach ($data as $d) {
            $profiles[] = $this->createProfileFromData($d['token'], $d, $parent);
        }

        return $profiles;
    }
}
