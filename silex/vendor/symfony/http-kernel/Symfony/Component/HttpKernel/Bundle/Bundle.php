<?php










namespace Symfony\Component\HttpKernel\Bundle;

use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\Console\Application;
use Symfony\Component\Finder\Finder;









abstract class Bundle extends ContainerAware implements BundleInterface
{
protected $name;
protected $reflected;
protected $extension;




public function boot()
{
}




public function shutdown()
{
}











public function build(ContainerBuilder $container)
{
}








public function getContainerExtension()
{
if (null === $this->extension) {
$basename = preg_replace('/Bundle$/', '', $this->getName());

$class = $this->getNamespace().'\\DependencyInjection\\'.$basename.'Extension';
if (class_exists($class)) {
$extension = new $class();


 $expectedAlias = Container::underscore($basename);
if ($expectedAlias != $extension->getAlias()) {
throw new \LogicException(sprintf(
'The extension alias for the default extension of a '.
'bundle must be the underscored version of the '.
'bundle name ("%s" instead of "%s")',
$expectedAlias, $extension->getAlias()
));
}

$this->extension = $extension;
} else {
$this->extension = false;
}
}

if ($this->extension) {
return $this->extension;
}
}








public function getNamespace()
{
if (null === $this->reflected) {
$this->reflected = new \ReflectionObject($this);
}

return $this->reflected->getNamespaceName();
}








public function getPath()
{
if (null === $this->reflected) {
$this->reflected = new \ReflectionObject($this);
}

return dirname($this->reflected->getFileName());
}








public function getParent()
{
return null;
}








final public function getName()
{
if (null !== $this->name) {
return $this->name;
}

$name = get_class($this);
$pos = strrpos($name, '\\');

return $this->name = false === $pos ? $name : substr($name, $pos + 1);
}











public function registerCommands(Application $application)
{
if (!$dir = realpath($this->getPath().'/Command')) {
return;
}

$finder = new Finder();
$finder->files()->name('*Command.php')->in($dir);

$prefix = $this->getNamespace().'\\Command';
foreach ($finder as $file) {
$ns = $prefix;
if ($relativePath = $file->getRelativePath()) {
$ns .= '\\'.strtr($relativePath, '/', '\\');
}
$r = new \ReflectionClass($ns.'\\'.$file->getBasename('.php'));
if ($r->isSubclassOf('Symfony\\Component\\Console\\Command\\Command') && !$r->isAbstract()) {
$application->add($r->newInstance());
}
}
}
}
