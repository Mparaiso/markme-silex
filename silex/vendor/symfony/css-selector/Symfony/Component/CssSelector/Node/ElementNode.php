<?php










namespace Symfony\Component\CssSelector\Node;

use Symfony\Component\CssSelector\XPathExpr;









class ElementNode implements NodeInterface
{
protected $namespace;
protected $element;







public function __construct($namespace, $element)
{
$this->namespace = $namespace;
$this->element = $element;
}




public function __toString()
{
return sprintf('%s[%s]', __CLASS__, $this->formatElement());
}






public function formatElement()
{
if ($this->namespace == '*') {
return $this->element;
}

return sprintf('%s|%s', $this->namespace, $this->element);
}




public function toXpath()
{
if ($this->namespace == '*') {
$el = strtolower($this->element);
} else {

 $el = sprintf('%s:%s', $this->namespace, $this->element);
}

return new XPathExpr(null, null, $el);
}
}
