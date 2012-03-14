<?php










namespace Symfony\Component\HttpKernel\Debug;






class StopwatchEvent
{
    private $periods;
    private $origin;
    private $category;
    private $started;

    







    public function __construct($origin, $category = null)
    {
        $this->origin = $this->formatTime($origin);
        $this->category = is_string($category) ? $category : 'default';
        $this->started = array();
        $this->periods = array();
    }

    




    public function getCategory()
    {
        return $this->category;
    }

    




    public function getOrigin()
    {
        return $this->origin;
    }

    




    public function start()
    {
        $this->started[] = $this->getNow();

        return $this;
    }

    




    public function stop()
    {
        if (!count($this->started)) {
            throw new \LogicException('stop() called but start() has not been called before.');
        }

        $this->periods[] = array(array_pop($this->started), $this->getNow());

        return $this;
    }

    




    public function lap()
    {
        return $this->stop()->start();
    }

    


    public function ensureStopped()
    {
        while (count($this->started)) {
            $this->stop();
        }
    }

    




    public function getPeriods()
    {
        return $this->periods;
    }

    




    public function getStartTime()
    {
        return isset($this->periods[0]) ? $this->periods[0][0] : 0;
    }

    




    public function getEndTime()
    {
        return ($count = count($this->periods)) ? $this->periods[$count - 1][1] : 0;
    }

    




    public function getTotalTime()
    {
        $total = 0;
        foreach ($this->periods as $period) {
            $total += $period[1] - $period[0];
        }

        return $this->formatTime($total);
    }

    




    protected function getNow()
    {
        return $this->formatTime(microtime(true) * 1000 - $this->origin);
    }

    








    private function formatTime($time)
    {
        if (!is_numeric($time)) {
            throw new \InvalidArgumentException('The time must be a numerical value');
        }

        return round($time, 1);
    }
}
