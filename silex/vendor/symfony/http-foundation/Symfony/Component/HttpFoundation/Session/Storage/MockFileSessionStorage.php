<?php










namespace Symfony\Component\HttpFoundation\Session\Storage;










class MockFileSessionStorage extends MockArraySessionStorage
{
    


    private $savePath;

    







    public function __construct($savePath = null, array $options = array())
    {
        if (null === $savePath) {
            $savePath = sys_get_temp_dir();
        }

        if (!is_dir($savePath)) {
            mkdir($savePath, 0777, true);
        }

        $this->savePath = $savePath;

        parent::__construct($options);
    }

    


    public function start()
    {
        if ($this->started) {
            return true;
        }

        if (!session_id()) {
            session_id($this->generateSessionId());
        }

        $this->sessionId = session_id();

        $this->read();

        $this->started = true;

        return true;
    }

    


    public function regenerate($destroy = false)
    {
        if ($destroy) {
            $this->destroy();
        }

        session_id($this->generateSessionId());
        $this->sessionId = session_id();

        $this->save();

        return true;
    }

    


    public function getId()
    {
        if (!$this->started) {
            return '';
        }

        return $this->sessionId;
    }

    


    public function save()
    {
        file_put_contents($this->getFilePath(), serialize($this->sessionData));
    }

    private function destroy()
    {
        if (is_file($this->getFilePath())) {
            unlink($this->getFilePath());
        }
    }

    




    public function getFilePath()
    {
        return $this->savePath.'/'.$this->sessionId.'.sess';
    }

    private function read()
    {
        $filePath = $this->getFilePath();
        $this->sessionData = is_readable($filePath) && is_file($filePath) ? unserialize(file_get_contents($filePath)) : array();

        $this->loadSession($this->sessionData);
    }
}
