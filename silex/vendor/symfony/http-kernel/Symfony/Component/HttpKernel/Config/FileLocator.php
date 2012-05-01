<?php










namespace Symfony\Component\HttpKernel\Config;

use Symfony\Component\Config\FileLocator as BaseFileLocator;
use Symfony\Component\HttpKernel\KernelInterface;






class FileLocator extends BaseFileLocator
{
private $kernel;
private $path;








public function __construct(KernelInterface $kernel, $path = null, array $paths = array())
{
$this->kernel = $kernel;
$this->path = $path;
$paths[] = $path;

parent::__construct($paths);
}




public function locate($file, $currentPath = null, $first = true)
{
if ('@' === $file[0]) {
return $this->kernel->locateResource($file, $this->path, $first);
}

return parent::locate($file, $currentPath, $first);
}
}
