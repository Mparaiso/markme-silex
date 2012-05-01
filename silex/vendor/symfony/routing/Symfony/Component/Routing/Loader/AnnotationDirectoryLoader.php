<?php










namespace Symfony\Component\Routing\Loader;

use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Config\Resource\DirectoryResource;







class AnnotationDirectoryLoader extends AnnotationFileLoader
{










public function load($path, $type = null)
{
$dir = $this->locator->locate($path);

$collection = new RouteCollection();
$collection->addResource(new DirectoryResource($dir, '/\.php$/'));
$files = iterator_to_array(new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($dir), \RecursiveIteratorIterator::LEAVES_ONLY));
usort($files, function (\SplFileInfo $a, \SplFileInfo $b) {
return (string) $a > (string) $b ? 1 : -1;
});

foreach ($files as $file) {
if (!$file->isFile() || '.php' !== substr($file->getFilename(), -4)) {
continue;
}

if ($class = $this->findClass($file)) {
$refl = new \ReflectionClass($class);
if ($refl->isAbstract()) {
continue;
}

$collection->addCollection($this->loader->load($class, $type));
}
}

return $collection;
}









public function supports($resource, $type = null)
{
try {
$path = $this->locator->locate($resource);
} catch (\Exception $e) {
return false;
}

return is_string($resource) && is_dir($path) && (!$type || 'annotation' === $type);
}
}
