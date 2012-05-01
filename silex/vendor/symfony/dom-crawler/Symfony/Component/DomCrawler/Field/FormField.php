<?php










namespace Symfony\Component\DomCrawler\Field;






abstract class FormField
{



protected $node;



protected $name;



protected $value;



protected $document;



protected $xpath;



protected $disabled;






public function __construct(\DOMNode $node)
{
$this->node = $node;
$this->name = $node->getAttribute('name');

$this->document = new \DOMDocument('1.0', 'UTF-8');
$this->node = $this->document->importNode($this->node, true);

$root = $this->document->appendChild($this->document->createElement('_root'));
$root->appendChild($this->node);
$this->xpath = new \DOMXPath($this->document);

$this->initialize();
}






public function getName()
{
return $this->name;
}






public function getValue()
{
return $this->value;
}








public function setValue($value)
{
$this->value = (string) $value;
}






public function hasValue()
{
return true;
}






public function isDisabled()
{
return $this->node->hasAttribute('disabled');
}




abstract protected function initialize();
}
