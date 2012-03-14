<?php










namespace Symfony\Component\HttpKernel\DataCollector;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Debug\TraceableEventDispatcherInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;






class EventDataCollector extends DataCollector
{
    private $dispatcher;

    public function setEventDispatcher(EventDispatcherInterface $dispatcher)
    {
        if ($dispatcher instanceof TraceableEventDispatcherInterface) {
            $this->dispatcher = $dispatcher;
        }
    }

    


    public function collect(Request $request, Response $response, \Exception $exception = null)
    {
        $this->data = array(
            'called_listeners'     => null !== $this->dispatcher ? $this->dispatcher->getCalledListeners() : array(),
            'not_called_listeners' => null !== $this->dispatcher ? $this->dispatcher->getNotCalledListeners() : array(),
        );
    }

    






    public function getCalledListeners()
    {
        return $this->data['called_listeners'];
    }

    






    public function getNotCalledListeners()
    {
        return $this->data['not_called_listeners'];
    }

    


    public function getName()
    {
        return 'events';
    }
}
