<?php










namespace Symfony\Component\CssSelector\Node;

use Symfony\Component\CssSelector\XPathExpr;
use Symfony\Component\CssSelector\Exception\ParseException;









class AttribNode implements NodeInterface
{
protected $selector;
protected $namespace;
protected $attrib;
protected $operator;
protected $value;










public function __construct($selector, $namespace, $attrib, $operator, $value)
{
$this->selector = $selector;
$this->namespace = $namespace;
$this->attrib = $attrib;
$this->operator = $operator;
$this->value = $value;
}




public function __toString()
{
if ($this->operator == 'exists') {
return sprintf('%s[%s[%s]]', __CLASS__, $this->selector, $this->formatAttrib());
}

return sprintf('%s[%s[%s %s %s]]', __CLASS__, $this->selector, $this->formatAttrib(), $this->operator, $this->value);
}




public function toXpath()
{
$path = $this->selector->toXpath();
$attrib = $this->xpathAttrib();
$value = $this->value;
if ($this->operator == 'exists') {
$path->addCondition($attrib);
} elseif ($this->operator == '=') {
$path->addCondition(sprintf('%s = %s', $attrib, XPathExpr::xpathLiteral($value)));
} elseif ($this->operator == '!=') {

 if ($value) {
$path->addCondition(sprintf('not(%s) or %s != %s', $attrib, $attrib, XPathExpr::xpathLiteral($value)));
} else {
$path->addCondition(sprintf('%s != %s', $attrib, XPathExpr::xpathLiteral($value)));
}

 } elseif ($this->operator == '~=') {
$path->addCondition(sprintf("contains(concat(' ', normalize-space(%s), ' '), %s)", $attrib, XPathExpr::xpathLiteral(' '.$value.' ')));
} elseif ($this->operator == '|=') {

 $path->addCondition(sprintf('%s = %s or starts-with(%s, %s)', $attrib, XPathExpr::xpathLiteral($value), $attrib, XPathExpr::xpathLiteral($value.'-')));
} elseif ($this->operator == '^=') {
$path->addCondition(sprintf('starts-with(%s, %s)', $attrib, XPathExpr::xpathLiteral($value)));
} elseif ($this->operator == '$=') {

 $path->addCondition(sprintf('substring(%s, string-length(%s)-%s) = %s', $attrib, $attrib, strlen($value) - 1, XPathExpr::xpathLiteral($value)));
} elseif ($this->operator == '*=') {

 $path->addCondition(sprintf('contains(%s, %s)', $attrib, XPathExpr::xpathLiteral($value)));
} else {
throw new ParseException(sprintf('Unknown operator: %s', $this->operator));
}

return $path;
}






protected function xpathAttrib()
{

 if ($this->namespace == '*') {
return '@'.$this->attrib;
}

return sprintf('@%s:%s', $this->namespace, $this->attrib);
}






protected function formatAttrib()
{
if ($this->namespace == '*') {
return $this->attrib;
}

return sprintf('%s|%s', $this->namespace, $this->attrib);
}
}
