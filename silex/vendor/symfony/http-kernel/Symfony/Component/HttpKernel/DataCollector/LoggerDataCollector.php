<?php










namespace Symfony\Component\HttpKernel\DataCollector;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Log\DebugLoggerInterface;






class LoggerDataCollector extends DataCollector
{
    private $logger;

    public function __construct($logger = null)
    {
        if (null !== $logger && $logger instanceof DebugLoggerInterface) {
            $this->logger = $logger;
        }
    }

    


    public function collect(Request $request, Response $response, \Exception $exception = null)
    {
        if (null !== $this->logger) {
            $this->data = array(
                'error_count' => $this->logger->countErrors(),
                'logs'        => $this->sanitizeLogs($this->logger->getLogs()),
            );
        }
    }

    






    public function countErrors()
    {
        return isset($this->data['error_count']) ? $this->data['error_count'] : 0;
    }

    




    public function getLogs()
    {
        return isset($this->data['logs']) ? $this->data['logs'] : array();
    }

    


    public function getName()
    {
        return 'logger';
    }

    private function sanitizeLogs($logs)
    {
        foreach ($logs as $i => $log) {
            $logs[$i]['context'] = $this->sanitizeContext($log['context']);
        }

        return $logs;
    }

    private function sanitizeContext($context)
    {
        if (is_array($context)) {
            foreach ($context as $key => $value) {
                $context[$key] = $this->sanitizeContext($value);
            }

            return $context;
        }

        if (is_resource($context)) {
            return sprintf('Resource(%s)', get_resource_type($context));
        }

        if (is_object($context)) {
            return sprintf('Object(%s)', get_class($context));
        }

        return $context;
    }
}
