<?php










namespace Symfony\Component\CssSelector\Node;

use Symfony\Component\CssSelector\Exception\ParseException;









class PseudoNode implements NodeInterface
{
static protected $unsupported = array(
'indeterminate', 'first-line', 'first-letter',
'selection', 'before', 'after', 'link', 'visited',
'active', 'focus', 'hover',
);

protected $element;
protected $type;
protected $ident;










public function __construct($element, $type, $ident)
{
$this->element = $element;

if (!in_array($type, array(':', '::'))) {
throw new ParseException(sprintf('The PseudoNode type can only be : or :: (%s given).', $type));
}

$this->type = $type;
$this->ident = $ident;
}




public function __toString()
{
return sprintf('%s[%s%s%s]', __CLASS__, $this->element, $this->type, $this->ident);
}





public function toXpath()
{
$elXpath = $this->element->toXpath();

if (in_array($this->ident, self::$unsupported)) {
throw new ParseException(sprintf('The pseudo-class %s is unsupported', $this->ident));
}
$method = 'xpath_'.str_replace('-', '_', $this->ident);
if (!method_exists($this, $method)) {
throw new ParseException(sprintf('The pseudo-class %s is unknown', $this->ident));
}

return $this->$method($elXpath);
}






protected function xpath_checked($xpath)
{

 $xpath->addCondition("(@selected or @checked) and (name(.) = 'input' or name(.) = 'option')");

return $xpath;
}








protected function xpath_root($xpath)
{

 throw new ParseException();
}








protected function xpath_first_child($xpath)
{
$xpath->addStarPrefix();
$xpath->addNameTest();
$xpath->addCondition('position() = 1');

return $xpath;
}








protected function xpath_last_child($xpath)
{
$xpath->addStarPrefix();
$xpath->addNameTest();
$xpath->addCondition('position() = last()');

return $xpath;
}








protected function xpath_first_of_type($xpath)
{
if ($xpath->getElement() == '*') {
throw new ParseException('*:first-of-type is not implemented');
}
$xpath->addStarPrefix();
$xpath->addCondition('position() = 1');

return $xpath;
}










protected function xpath_last_of_type($xpath)
{
if ($xpath->getElement() == '*') {
throw new ParseException('*:last-of-type is not implemented');
}
$xpath->addStarPrefix();
$xpath->addCondition('position() = last()');

return $xpath;
}








protected function xpath_only_child($xpath)
{
$xpath->addNameTest();
$xpath->addStarPrefix();
$xpath->addCondition('last() = 1');

return $xpath;
}










protected function xpath_only_of_type($xpath)
{
if ($xpath->getElement() == '*') {
throw new ParseException('*:only-of-type is not implemented');
}
$xpath->addCondition('last() = 1');

return $xpath;
}








protected function xpath_empty($xpath)
{
$xpath->addCondition('not(*) and not(normalize-space())');

return $xpath;
}
}
