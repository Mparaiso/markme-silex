<?php










namespace Symfony\Component\HttpFoundation\File;

use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\Exception\FileNotFoundException;










class UploadedFile extends File
{







private $test = false;






private $originalName;






private $mimeType;






private $size;






private $error;



























public function __construct($path, $originalName, $mimeType = null, $size = null, $error = null, $test = false)
{
if (!ini_get('file_uploads')) {
throw new FileException(sprintf('Unable to create UploadedFile because "file_uploads" is disabled in your php.ini file (%s)', get_cfg_var('cfg_file_path')));
}

$this->originalName = basename($originalName);
$this->mimeType = $mimeType ?: 'application/octet-stream';
$this->size = $size;
$this->error = $error ?: UPLOAD_ERR_OK;
$this->test = (Boolean) $test;

parent::__construct($path, UPLOAD_ERR_OK === $this->error);
}











public function getClientOriginalName()
{
return $this->originalName;
}











public function getClientMimeType()
{
return $this->mimeType;
}











public function getClientSize()
{
return $this->size;
}











public function getError()
{
return $this->error;
}








public function isValid()
{
return $this->error === UPLOAD_ERR_OK;
}













public function move($directory, $name = null)
{
if ($this->isValid() && ($this->test || is_uploaded_file($this->getPathname()))) {
return parent::move($directory, $name);
}

throw new FileException(sprintf('The file "%s" has not been uploaded via Http', $this->getPathname()));
}






static public function getMaxFilesize()
{
$max = trim(ini_get('upload_max_filesize'));

if ('' === $max) {
return PHP_INT_MAX;
}

switch (strtolower(substr($max, -1))) {
case 'g':
$max *= 1024;
case 'm':
$max *= 1024;
case 'k':
$max *= 1024;
}

return (integer) $max;
}
}
