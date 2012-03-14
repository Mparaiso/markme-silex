<?php










namespace Symfony\Component\HttpKernel\DataCollector;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;






class MemoryDataCollector extends DataCollector
{
    


    public function collect(Request $request, Response $response, \Exception $exception = null)
    {
        $this->data = array(
            'memory' => memory_get_peak_usage(true),
        );
    }

    




    public function getMemory()
    {
        return $this->data['memory'];
    }

    


    public function getName()
    {
        return 'memory';
    }
}
