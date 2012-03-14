<?php










namespace Symfony\Component\HttpKernel\DataCollector;

use Symfony\Component\HttpKernel\DataCollector\DataCollector;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;






class TimeDataCollector extends DataCollector
{
    protected $kernel;

    public function __construct(KernelInterface $kernel = null)
    {
        $this->kernel = $kernel;
    }

    


    public function collect(Request $request, Response $response, \Exception $exception = null)
    {
        $this->data = array(
            'start_time' => (null !== $this->kernel ? $this->kernel->getStartTime() : $_SERVER['REQUEST_TIME']) * 1000,
            'events'     => array(),
        );
    }

    




    public function setEvents(array $events)
    {
        foreach ($events as $event) {
            $event->ensureStopped();
        }

        $this->data['events'] = $events;
    }

    




    public function getEvents()
    {
        return $this->data['events'];
    }

    




    public function getTotalTime()
    {
        $lastEvent = $this->data['events']['__section__'];

        return $lastEvent->getOrigin() + $lastEvent->getTotalTime() - $this->data['start_time'];
    }

    






    public function getInitTime()
    {
        return $this->data['events']['__section__']->getOrigin() - $this->getStartTime();
    }

    




    public function getStartTime()
    {
        return $this->data['start_time'];
    }

    


    public function getName()
    {
        return 'time';
    }
}
