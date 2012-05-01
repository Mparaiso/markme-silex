<?php










namespace Silex;

use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\Route;
use Silex\Controller;











class ControllerCollection
{
protected $controllers = array();











public function match($pattern, $to)
{
$route = new Route($pattern, array('_controller' => $to));
$controller = new Controller($route);
$this->add($controller);

return $controller;
}









public function get($pattern, $to)
{
return $this->match($pattern, $to)->method('GET');
}









public function post($pattern, $to)
{
return $this->match($pattern, $to)->method('POST');
}









public function put($pattern, $to)
{
return $this->match($pattern, $to)->method('PUT');
}









public function delete($pattern, $to)
{
return $this->match($pattern, $to)->method('DELETE');
}






public function add(Controller $controller)
{
$this->controllers[] = $controller;
}






public function flush($prefix = '')
{
$routes = new RouteCollection();

foreach ($this->controllers as $controller) {
if (!$controller->getRouteName()) {
$controller->bindDefaultRouteName($prefix);
}
$routes->add($controller->getRouteName(), $controller->getRoute());
$controller->freeze();
}

$this->controllers = array();

return $routes;
}
}
