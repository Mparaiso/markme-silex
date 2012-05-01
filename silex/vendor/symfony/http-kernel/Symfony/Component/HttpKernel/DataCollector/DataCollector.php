<?php










namespace Symfony\Component\HttpKernel\DataCollector;

use Symfony\Component\HttpKernel\Profiler\Profiler;








abstract class DataCollector implements DataCollectorInterface, \Serializable
{
protected $data;

public function serialize()
{
return serialize($this->data);
}

public function unserialize($data)
{
$this->data = unserialize($data);
}
}
