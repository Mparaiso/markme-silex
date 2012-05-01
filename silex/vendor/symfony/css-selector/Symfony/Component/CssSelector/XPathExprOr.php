<?php










namespace Symfony\Component\CssSelector;











class XPathExprOr extends XPathExpr
{






public function __construct($items, $prefix = null)
{
$this->items = $items;
$this->prefix = $prefix;
}






public function __toString()
{
$prefix = $this->getPrefix();

$tmp = array();
foreach ($this->items as $i) {
$tmp[] = sprintf('%s%s', $prefix, $i);
}

return implode($tmp, ' | ');
}
}
