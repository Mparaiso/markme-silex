<?php










namespace Symfony\Component\CssSelector\Node;

use Symfony\Component\CssSelector\Exception\ParseException;









class CombinedSelectorNode implements NodeInterface
{
    static protected $methodMapping = array(
        ' ' => 'descendant',
        '>' => 'child',
        '+' => 'direct_adjacent',
        '~' => 'indirect_adjacent',
    );

    protected $selector;
    protected $combinator;
    protected $subselector;

    






    public function __construct($selector, $combinator, $subselector)
    {
        $this->selector = $selector;
        $this->combinator = $combinator;
        $this->subselector = $subselector;
    }

    


    public function __toString()
    {
        $comb = $this->combinator == ' ' ? '<followed>' : $this->combinator;

        return sprintf('%s[%s %s %s]', __CLASS__, $this->selector, $comb, $this->subselector);
    }

    



    public function toXpath()
    {
        if (!isset(self::$methodMapping[$this->combinator])) {
            throw new ParseException(sprintf('Unknown combinator: %s', $this->combinator));
        }

        $method = '_xpath_'.self::$methodMapping[$this->combinator];
        $path = $this->selector->toXpath();

        return $this->$method($path, $this->subselector);
    }

    





    protected function _xpath_descendant($xpath, $sub)
    {
        
        $xpath->join('/descendant::', $sub->toXpath());

        return $xpath;
    }

    





    protected function _xpath_child($xpath, $sub)
    {
        
        $xpath->join('/', $sub->toXpath());

        return $xpath;
    }

    





    protected function _xpath_direct_adjacent($xpath, $sub)
    {
        
        $xpath->join('/following-sibling::', $sub->toXpath());
        $xpath->addNameTest();
        $xpath->addCondition('position() = 1');

        return $xpath;
    }

    





    protected function _xpath_indirect_adjacent($xpath, $sub)
    {
        
        $xpath->join('/following-sibling::', $sub->toXpath());

        return $xpath;
    }
}
