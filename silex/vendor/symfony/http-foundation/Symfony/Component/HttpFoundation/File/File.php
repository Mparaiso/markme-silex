<?php










namespace Symfony\Component\HttpFoundation\File;

use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\Exception\FileNotFoundException;
use Symfony\Component\HttpFoundation\File\MimeType\MimeTypeGuesser;
use Symfony\Component\HttpFoundation\File\MimeType\ExtensionGuesser;








class File extends \SplFileInfo
{










public function __construct($path, $checkPath = true)
{
if ($checkPath && !is_file($path)) {
throw new FileNotFoundException($path);
}

parent::__construct($path);
}










public function guessExtension()
{
$type = $this->getMimeType();
$guesser = ExtensionGuesser::getInstance();

return $guesser->guess($type);
}












public function getMimeType()
{
$guesser = MimeTypeGuesser::getInstance();

return $guesser->guess($this->getPathname());
}










public function getExtension()
{
return pathinfo($this->getBasename(), PATHINFO_EXTENSION);
}













public function move($directory, $name = null)
{
if (!is_dir($directory)) {
if (false === @mkdir($directory, 0777, true)) {
throw new FileException(sprintf('Unable to create the "%s" directory', $directory));
}
} elseif (!is_writable($directory)) {
throw new FileException(sprintf('Unable to write in the "%s" directory', $directory));
}

$target = $directory.DIRECTORY_SEPARATOR.(null === $name ? $this->getBasename() : basename($name));

if (!@rename($this->getPathname(), $target)) {
$error = error_get_last();
throw new FileException(sprintf('Could not move the file "%s" to "%s" (%s)', $this->getPathname(), $target, strip_tags($error['message'])));
}

chmod($target, 0666 & ~umask());

return new File($target);
}
}
