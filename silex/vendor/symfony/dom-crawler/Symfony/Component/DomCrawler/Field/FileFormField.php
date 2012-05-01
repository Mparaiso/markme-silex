<?php










namespace Symfony\Component\DomCrawler\Field;








class FileFormField extends FormField
{







public function setErrorCode($error)
{
$codes = array(UPLOAD_ERR_INI_SIZE, UPLOAD_ERR_FORM_SIZE, UPLOAD_ERR_PARTIAL, UPLOAD_ERR_NO_FILE, UPLOAD_ERR_NO_TMP_DIR, UPLOAD_ERR_CANT_WRITE, UPLOAD_ERR_EXTENSION);
if (!in_array($error, $codes)) {
throw new \InvalidArgumentException(sprintf('The error code %s is not valid.', $error));
}

$this->value = array('name' => '', 'type' => '', 'tmp_name' => '', 'error' => $error, 'size' => 0);
}








public function upload($value)
{
$this->setValue($value);
}






public function setValue($value)
{
if (null !== $value && is_readable($value)) {
$error = UPLOAD_ERR_OK;
$size = filesize($value);
$name = basename($value);


 $tmp = tempnam(sys_get_temp_dir(), 'upload');
unlink($tmp);
copy($value, $tmp);
$value = $tmp;
} else {
$error = UPLOAD_ERR_NO_FILE;
$size = 0;
$name = '';
$value = '';
}

$this->value = array('name' => $name, 'type' => '', 'tmp_name' => $value, 'error' => $error, 'size' => $size);
}






protected function initialize()
{
if ('input' != $this->node->nodeName) {
throw new \LogicException(sprintf('A FileFormField can only be created from an input tag (%s given).', $this->node->nodeName));
}

if ('file' != $this->node->getAttribute('type')) {
throw new \LogicException(sprintf('A FileFormField can only be created from an input tag with a type of file (given type is %s).', $this->node->getAttribute('type')));
}

$this->setValue(null);
}
}
