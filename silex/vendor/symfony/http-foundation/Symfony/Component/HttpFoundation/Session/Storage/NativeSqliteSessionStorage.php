<?php










namespace Symfony\Component\HttpFoundation\Session\Storage;








class NativeSqliteSessionStorage extends AbstractSessionStorage
{
    


    private $dbPath;

    







    public function __construct($dbPath, array $options = array())
    {
        if (!extension_loaded('sqlite')) {
            throw new \RuntimeException('PHP does not have "sqlite" session module registered');
        }

        $this->dbPath = $dbPath;
        parent::__construct($options);
    }

    


    protected function registerSaveHandlers()
    {
        ini_set('session.save_handler', 'sqlite');
        ini_set('session.save_path', $this->dbPath);
    }
}
