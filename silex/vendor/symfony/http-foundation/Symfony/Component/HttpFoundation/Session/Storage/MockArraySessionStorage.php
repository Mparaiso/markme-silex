<?php










namespace Symfony\Component\HttpFoundation\Session\Storage;













class MockArraySessionStorage extends AbstractSessionStorage
{
    


    protected $sessionId;

    


    protected $sessionData = array();

    public function setSessionData(array $array)
    {
        $this->sessionData = $array;
    }

    


    public function start()
    {
        if ($this->started && !$this->closed) {
            return true;
        }

        $this->started = true;
        $this->loadSession($this->sessionData);

        $this->sessionId = $this->generateSessionId();
        session_id($this->sessionId);

        return true;
    }


    


    public function regenerate($destroy = false)
    {
        if ($this->options['auto_start'] && !$this->started) {
            $this->start();
        }

        $this->sessionId = $this->generateSessionId();
        session_id($this->sessionId);

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
        
        $this->closed = false;
    }

    


    public function clear()
    {
        
        foreach ($this->bags as $bag) {
            $bag->clear();
        }

        
        $this->sessionData = array();

        
        $this->loadSession($this->sessionData);
    }

    




    protected function generateSessionId()
    {
        return sha1(uniqid(mt_rand(), true));
    }
}
