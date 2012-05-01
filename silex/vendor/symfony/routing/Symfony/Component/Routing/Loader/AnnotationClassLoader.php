<?php










namespace Symfony\Component\Routing\Loader;

use Doctrine\Common\Annotations\Reader;
use Symfony\Component\Routing\Annotation\Route as RouteAnnotation;
use Symfony\Component\Config\Resource\FileResource;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\Config\Loader\LoaderResolverInterface;





































abstract class AnnotationClassLoader implements LoaderInterface
{
protected $reader;
protected $routeAnnotationClass = 'Symfony\\Component\\Routing\\Annotation\\Route';
protected $defaultRouteIndex;






public function __construct(Reader $reader)
{
$this->reader = $reader;
}






public function setRouteAnnotationClass($class)
{
$this->routeAnnotationClass = $class;
}











public function load($class, $type = null)
{
if (!class_exists($class)) {
throw new \InvalidArgumentException(sprintf('Class "%s" does not exist.', $class));
}

$globals = array(
'pattern' => '',
'requirements' => array(),
'options' => array(),
'defaults' => array(),
);

$class = new \ReflectionClass($class);
if ($class->isAbstract()) {
throw new \InvalidArgumentException(sprintf('Annotations from class "%s" cannot be read as it is abstract.', $class));
}

if ($annot = $this->reader->getClassAnnotation($class, $this->routeAnnotationClass)) {
if (null !== $annot->getPattern()) {
$globals['pattern'] = $annot->getPattern();
}

if (null !== $annot->getRequirements()) {
$globals['requirements'] = $annot->getRequirements();
}

if (null !== $annot->getOptions()) {
$globals['options'] = $annot->getOptions();
}

if (null !== $annot->getDefaults()) {
$globals['defaults'] = $annot->getDefaults();
}
}

$collection = new RouteCollection();
$collection->addResource(new FileResource($class->getFileName()));

foreach ($class->getMethods() as $method) {
$this->defaultRouteIndex = 0;
foreach ($this->reader->getMethodAnnotations($method) as $annot) {
if ($annot instanceof $this->routeAnnotationClass) {
$this->addRoute($collection, $annot, $globals, $class, $method);
}
}
}

return $collection;
}

protected function addRoute(RouteCollection $collection, $annot, $globals, \ReflectionClass $class, \ReflectionMethod $method)
{
$name = $annot->getName();
if (null === $name) {
$name = $this->getDefaultRouteName($class, $method);
}

$defaults = array_merge($globals['defaults'], $annot->getDefaults());
$requirements = array_merge($globals['requirements'], $annot->getRequirements());
$options = array_merge($globals['options'], $annot->getOptions());

$route = new Route($globals['pattern'].$annot->getPattern(), $defaults, $requirements, $options);

$this->configureRoute($route, $class, $method, $annot);

$collection->add($name, $route);
}









public function supports($resource, $type = null)
{
return is_string($resource) && preg_match('/^(?:\\\\?[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*)+$/', $resource) && (!$type || 'annotation' === $type);
}






public function setResolver(LoaderResolverInterface $resolver)
{
}






public function getResolver()
{
}









protected function getDefaultRouteName(\ReflectionClass $class, \ReflectionMethod $method)
{
$name = strtolower(str_replace('\\', '_', $class->getName()).'_'.$method->getName());
if ($this->defaultRouteIndex > 0) {
$name .= '_'.$this->defaultRouteIndex;
}
$this->defaultRouteIndex++;

return $name;
}

abstract protected function configureRoute(Route $route, \ReflectionClass $class, \ReflectionMethod $method, $annot);
}
