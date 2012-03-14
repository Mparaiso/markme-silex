<?php










namespace Symfony\Component\CssSelector\Node;

use Symfony\Component\CssSelector\XPathExpr;









class ClassNode implements NodeInterface
{
    protected $selector;
    protected $className;

    





    public function __construct($selector, $className)
    {
        $this->selector = $selector;
        $this->className = $className;
    }

    


    public function __toString()
    {
        return sprintf('%s[%s.%s]', __CLASS__, $this->selector, $this->className);
    }

    


    public function toXpath()
    {
        $selXpath = $this->selector->toXpath();
        $selXpath->addCondition(sprintf("contains(concat(' ', normalize-space(@class), ' '), %s)", XPathExpr::xpathLiteral(' '.$this->className.' ')));

        return $selXpath;
    }
}
