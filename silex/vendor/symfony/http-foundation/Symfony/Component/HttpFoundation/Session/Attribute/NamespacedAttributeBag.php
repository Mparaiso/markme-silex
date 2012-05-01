<?php










namespace Symfony\Component\HttpFoundation\Session\Attribute;







class NamespacedAttributeBag extends AttributeBag
{





private $namespaceCharacter;







public function __construct($storageKey = '_sf2_attributes', $namespaceCharacter = '/')
{
$this->namespaceCharacter = $namespaceCharacter;
parent::__construct($storageKey);
}




public function has($name)
{
$attributes = $this->resolveAttributePath($name);
$name = $this->resolveKey($name);

return array_key_exists($name, $attributes);
}




public function get($name, $default = null)
{
$attributes = $this->resolveAttributePath($name);
$name = $this->resolveKey($name);

return array_key_exists($name, $attributes) ? $attributes[$name] : $default;
}




public function set($name, $value)
{
$attributes = & $this->resolveAttributePath($name, true);
$name = $this->resolveKey($name);
$attributes[$name] = $value;
}




public function remove($name)
{
$retval = null;
$attributes = & $this->resolveAttributePath($name);
$name = $this->resolveKey($name);
if (array_key_exists($name, $attributes)) {
$retval = $attributes[$name];
unset($attributes[$name]);
}

return $retval;
}











protected function &resolveAttributePath($name, $writeContext = false)
{
$array = & $this->attributes;
$name = (strpos($name, $this->namespaceCharacter) === 0) ? substr($name, 1) : $name;


 if (!$name) {
return $array;
}

$parts = explode($this->namespaceCharacter, $name);
if (count($parts) < 2) {
if (!$writeContext) {
return $array;
}

$array[$parts[0]] = array();

return $array;
}

unset($parts[count($parts)-1]);

foreach ($parts as $part) {
if (!array_key_exists($part, $array)) {
if (!$writeContext) {
return $array;
}

$array[$part] = array();
}

$array = & $array[$part];
}

return $array;
}










protected function resolveKey($name)
{
if (strpos($name, $this->namespaceCharacter) !== false) {
$name = substr($name, strrpos($name, $this->namespaceCharacter)+1, strlen($name));
}

return $name;
}
}
