<?php










namespace Symfony\Component\Routing\Loader;

use Symfony\Component\Config\Resource\FileResource;
use Symfony\Component\Config\Loader\FileLoader;










class PhpFileLoader extends FileLoader
{








public function load($file, $type = null)
{

 $loader = $this;

$path = $this->locator->locate($file);
$this->setCurrentDir(dirname($path));

$collection = include $path;
$collection->addResource(new FileResource($path));

return $collection;
}











public function supports($resource, $type = null)
{
return is_string($resource) && 'php' === pathinfo($resource, PATHINFO_EXTENSION) && (!$type || 'php' === $type);
}
}
