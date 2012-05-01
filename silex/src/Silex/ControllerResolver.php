<?php










namespace Silex;

use Symfony\Component\HttpKernel\Controller\ControllerResolver as BaseControllerResolver;
use Symfony\Component\HttpKernel\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;






class ControllerResolver extends BaseControllerResolver
{
protected $app;







public function __construct(Application $app, LoggerInterface $logger = null)
{
$this->app = $app;

parent::__construct($logger);
}

protected function doGetArguments(Request $request, $controller, array $parameters)
{
foreach ($parameters as $param) {
if ($param->getClass() && $param->getClass()->isInstance($this->app)) {
$request->attributes->set($param->getName(), $this->app);

break;
}
}

return parent::doGetArguments($request, $controller, $parameters);
}
}
