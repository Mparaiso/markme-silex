<?php










namespace Symfony\Component\CssSelector\Node;

use Symfony\Component\CssSelector\XPathExprOr;









class OrNode implements NodeInterface
{
    protected $items;

    




    public function __construct($items)
    {
        $this->items = $items;
    }

    


    public function __toString()
    {
        return sprintf('%s(%s)', __CLASS__, $this->items);
    }

    


    public function toXpath()
    {
        $paths = array();
        foreach ($this->items as $item) {
            $paths[] = $item->toXpath();
        }

        return new XPathExprOr($paths);
    }
}
