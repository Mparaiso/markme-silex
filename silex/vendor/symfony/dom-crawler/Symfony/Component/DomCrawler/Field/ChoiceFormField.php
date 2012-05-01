<?php










namespace Symfony\Component\DomCrawler\Field;










class ChoiceFormField extends FormField
{



private $type;



private $multiple;



private $options;






public function hasValue()
{

 if (in_array($this->type, array('checkbox', 'radio')) && null === $this->value) {
return false;
}

return true;
}






public function isDisabled()
{
foreach ($this->options as $option) {
if ($option['value'] == $this->value && $option['disabled']) {
return true;
}
}

return false;
}








public function select($value)
{
$this->setValue($value);
}








public function tick()
{
if ('checkbox' !== $this->type) {
throw new \LogicException(sprintf('You cannot tick "%s" as it is not a checkbox (%s).', $this->name, $this->type));
}

$this->setValue(true);
}








public function untick()
{
if ('checkbox' !== $this->type) {
throw new \LogicException(sprintf('You cannot tick "%s" as it is not a checkbox (%s).', $this->name, $this->type));
}

$this->setValue(false);
}








public function setValue($value)
{
if ('checkbox' == $this->type && false === $value) {

 $this->value = null;
} elseif ('checkbox' == $this->type && true === $value) {

 $this->value = $this->options[0]['value'];
} else {
if (is_array($value)) {
if (!$this->multiple) {
throw new \InvalidArgumentException(sprintf('The value for "%s" cannot be an array.', $this->name));
}

foreach ($value as $v) {
if (!$this->containsOption($v, $this->options)) {
throw new \InvalidArgumentException(sprintf('Input "%s" cannot take "%s" as a value (possible values: %s).', $this->name, $v, implode(', ', $this->availableOptionValues())));
}
}
} elseif (!$this->containsOption($value, $this->options)) {
throw new \InvalidArgumentException(sprintf('Input "%s" cannot take "%s" as a value (possible values: %s).', $this->name, $value, implode(', ', $this->availableOptionValues())));
}

if ($this->multiple) {
$value = (array) $value;
}

if (is_array($value)) {
$this->value = $value;
} else {
parent::setValue($value);
}
}
}










public function addChoice(\DOMNode $node)
{
if (!$this->multiple && 'radio' != $this->type) {
throw new \LogicException(sprintf('Unable to add a choice for "%s" as it is not multiple or is not a radio button.', $this->name));
}

$option = $this->buildOptionValue($node);
$this->options[] = $option;

if ($node->getAttribute('checked')) {
$this->value = $option['value'];
}
}






public function getType()
{
return $this->type;
}






public function isMultiple()
{
return $this->multiple;
}






protected function initialize()
{
if ('input' != $this->node->nodeName && 'select' != $this->node->nodeName) {
throw new \LogicException(sprintf('A ChoiceFormField can only be created from an input or select tag (%s given).', $this->node->nodeName));
}

if ('input' == $this->node->nodeName && 'checkbox' != $this->node->getAttribute('type') && 'radio' != $this->node->getAttribute('type')) {
throw new \LogicException(sprintf('A ChoiceFormField can only be created from an input tag with a type of checkbox or radio (given type is %s).', $this->node->getAttribute('type')));
}

$this->value = null;
$this->options = array();
$this->multiple = false;

if ('input' == $this->node->nodeName) {
$this->type = $this->node->getAttribute('type');
$optionValue = $this->buildOptionValue($this->node);
$this->options[] = $optionValue;

if ($this->node->getAttribute('checked')) {
$this->value = $optionValue['value'];
}
} else {
$this->type = 'select';
if ($this->node->hasAttribute('multiple')) {
$this->multiple = true;
$this->value = array();
$this->name = str_replace('[]', '', $this->name);
}

$found = false;
foreach ($this->xpath->query('descendant::option', $this->node) as $option) {
$this->options[] = $this->buildOptionValue($option);

if ($option->getAttribute('selected')) {
$found = true;
if ($this->multiple) {
$this->value[] = $option->getAttribute('value');
} else {
$this->value = $option->getAttribute('value');
}
}
}


 $option = $this->xpath->query('descendant::option', $this->node)->item(0);
if (!$found && !$this->multiple && $option instanceof \DOMElement) {
$this->value = $option->getAttribute('value');
}
}
}








private function buildOptionValue($node)
{
$option = array();

$defaultValue = (isset($node->nodeValue) && !empty($node->nodeValue)) ? $node->nodeValue : '1';
$option['value'] = $node->hasAttribute('value') ? $node->getAttribute('value') : $defaultValue;
$option['disabled'] = ($node->hasAttribute('disabled') && $node->getAttribute('disabled') == 'disabled');

return $option;
}









public function containsOption($optionValue, $options)
{
foreach ($options as $option) {
if ($option['value'] == $optionValue) {
return true;
}
}

return false;
}






public function availableOptionValues()
{
$values = array();

foreach ($this->options as $option) {
$values[] = $option['value'];
}

return $values;
}
}
