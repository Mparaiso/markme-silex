<?php










namespace Symfony\Component\HttpFoundation\File\MimeType;
















class ExtensionGuesser implements ExtensionGuesserInterface
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
$this->register(new MimeTypeExtensionGuesser());
}








public function register(ExtensionGuesserInterface $guesser)
{
array_unshift($this->guessers, $guesser);
}












public function guess($mimeType)
{
foreach ($this->guessers as $guesser) {
$extension = $guesser->guess($mimeType);

if (null !== $extension) {
break;
}
}

return $extension;
}
}
