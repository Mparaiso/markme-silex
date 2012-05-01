<?php










namespace Symfony\Component\HttpKernel\DataCollector;

use Symfony\Component\HttpKernel\Profiler\Profiler;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;








interface DataCollectorInterface
{









function collect(Request $request, Response $response, \Exception $exception = null);








function getName();
}
