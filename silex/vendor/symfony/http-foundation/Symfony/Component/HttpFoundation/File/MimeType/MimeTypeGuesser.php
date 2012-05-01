<?php










namespace Symfony\Component\HttpFoundation\File\MimeType;

use Symfony\Component\HttpFoundation\File\Exception\FileNotFoundException;
use Symfony\Component\HttpFoundation\File\Exception\AccessDeniedException;

















class MimeTypeGuesser implements MimeTypeGuesserInterface
{




static private $instance = null;





protected $guessers = array();






static public function getInstance()
{
if (null === self::$instance) {
self::$instance = new self();
}

return self::$instance;
}




private function __construct()
{
if (FileBinaryMimeTypeGuesser::isSupported()) {
$this->register(new FileBinaryMimeTypeGuesser());
}

if (FileinfoMimeTypeGuesser::isSupported()) {
$this->register(new FileinfoMimeTypeGuesser());
}
}








public function register(MimeTypeGuesserInterface $guesser)
{
array_unshift($this->guessers, $guesser);
}















public function guess($path)
{
if (!is_file($path)) {
throw new FileNotFoundException($path);
}

if (!is_readable($path)) {
throw new AccessDeniedException($path);
}

if (!$this->guessers) {
throw new \LogicException('Unable to guess the mime type as no guessers are available (Did you enable the php_fileinfo extension?)');
}

foreach ($this->guessers as $guesser) {
if (null !== $mimeType = $guesser->guess($path)) {
return $mimeType;
}
}
}
}
