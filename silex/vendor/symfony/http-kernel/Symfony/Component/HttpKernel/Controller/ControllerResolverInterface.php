<?php










namespace Symfony\Component\HttpKernel\Controller;

use Symfony\Component\HttpFoundation\Request;













interface ControllerResolverInterface
{


















function getController(Request $request);













function getArguments(Request $request, $controller);
}
