<?php










namespace Symfony\Component\CssSelector;









class XPathExpr
{
private $prefix;
private $path;
private $element;
private $condition;
private $starPrefix;










public function __construct($prefix = null, $path = null, $element = '*', $condition = null, $starPrefix = false)
{
$this->prefix = $prefix;
$this->path = $path;
$this->element = $element;
$this->condition = $condition;
$this->starPrefix = $starPrefix;
}






public function getPrefix()
{
return $this->prefix;
}






public function getPath()
{
return $this->path;
}






public function hasStarPrefix()
{
return $this->starPrefix;
}






public function getElement()
{
return $this->element;
}






public function getCondition()
{
return $this->condition;
}






public function __toString()
{
$path = '';
if (null !== $this->prefix) {
$path .= $this->prefix;
}

if (null !== $this->path) {
$path .= $this->path;
}

$path .= $this->element;

if ($this->condition) {
$path .= sprintf('[%s]', $this->condition);
}

return $path;
}







public function addCondition($condition)
{
if ($this->condition) {
$this->condition = sprintf('%s and (%s)', $this->condition, $condition);
} else {
$this->condition = $condition;
}
}







public function addPrefix($prefix)
{
if ($this->prefix) {
$this->prefix = $prefix.$this->prefix;
} else {
$this->prefix = $prefix;
}
}






public function addNameTest()
{
if ($this->element == '*') {

 return;
}

$this->addCondition(sprintf('name() = %s', XPathExpr::xpathLiteral($this->element)));
$this->element = '*';
}






public function addStarPrefix()
{




if ($this->path) {
$this->path .= '*/';
} else {
$this->path = '*/';
}

$this->starPrefix = true;
}









public function join($combiner, $other)
{
$prefix = (string) $this;

$prefix .= $combiner;
$path = $other->getPrefix().$other->getPath();



if ($other->hasStarPrefix() && '*/' == $path) {
$path = '';
}
$this->prefix = $prefix;
$this->path = $path;
$this->element = $other->getElement();
$this->condition = $other->GetCondition();
}








static public function xpathLiteral($s)
{
if ($s instanceof Node\ElementNode) {

 $s = $s->formatElement();
} else {
$s = (string) $s;
}

if (false === strpos($s, "'")) {
return sprintf("'%s'", $s);
}

if (false === strpos($s, '"')) {
return sprintf('"%s"', $s);
}

$string = $s;
$parts = array();
while (true) {
if (false !== $pos = strpos($string, "'")) {
$parts[] = sprintf("'%s'", substr($string, 0, $pos));
$parts[] = "\"'\"";
$string = substr($string, $pos + 1);
} else {
$parts[] = "'$string'";
break;
}
}

return sprintf('concat(%s)', implode($parts, ', '));
}
}
