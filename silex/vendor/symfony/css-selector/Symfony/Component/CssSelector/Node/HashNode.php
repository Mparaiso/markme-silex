<?php










namespace Symfony\Component\CssSelector\Node;

use Symfony\Component\CssSelector\XPathExpr;









class HashNode implements NodeInterface
{
protected $selector;
protected $id;







public function __construct($selector, $id)
{
$this->selector = $selector;
$this->id = $id;
}




public function __toString()
{
return sprintf('%s[%s#%s]', __CLASS__, $this->selector, $this->id);
}




public function toXpath()
{
$path = $this->selector->toXpath();
$path->addCondition(sprintf('@id = %s', XPathExpr::xpathLiteral($this->id)));

return $path;
}
}
