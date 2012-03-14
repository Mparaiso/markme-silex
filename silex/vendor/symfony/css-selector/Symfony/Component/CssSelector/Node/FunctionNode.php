<?php










namespace Symfony\Component\CssSelector\Node;

use Symfony\Component\CssSelector\Exception\ParseException;
use Symfony\Component\CssSelector\XPathExpr;









class FunctionNode implements NodeInterface
{
    static protected $unsupported = array('target', 'lang', 'enabled', 'disabled');

    protected $selector;
    protected $type;
    protected $name;
    protected $expr;

    







    public function __construct($selector, $type, $name, $expr)
    {
        $this->selector = $selector;
        $this->type = $type;
        $this->name = $name;
        $this->expr = $expr;
    }

    


    public function __toString()
    {
        return sprintf('%s[%s%s%s(%s)]', __CLASS__, $this->selector, $this->type, $this->name, $this->expr);
    }

    



    public function toXpath()
    {
        $selPath = $this->selector->toXpath();
        if (in_array($this->name, self::$unsupported)) {
            throw new ParseException(sprintf('The pseudo-class %s is not supported', $this->name));
        }
        $method = '_xpath_'.str_replace('-', '_', $this->name);
        if (!method_exists($this, $method)) {
            throw new ParseException(sprintf('The pseudo-class %s is unknown', $this->name));
        }

        return $this->$method($selPath, $this->expr);
    }

    









    protected function _xpath_nth_child($xpath, $expr, $last = false, $addNameTest = true)
    {
        list($a, $b) = $this->parseSeries($expr);
        if (!$a && !$b && !$last) {
            
            $xpath->addCondition('false() and position() = 0');

            return $xpath;
        }

        if ($addNameTest) {
            $xpath->addNameTest();
        }

        $xpath->addStarPrefix();
        if ($a == 0) {
            if ($last) {
                $b = sprintf('last() - %s', $b);
            }
            $xpath->addCondition(sprintf('position() = %s', $b));

            return $xpath;
        }

        if ($last) {
            
            $a = -$a;
            $b = -$b;
        }

        if ($b > 0) {
            $bNeg = -$b;
        } else {
            $bNeg = sprintf('+%s', -$b);
        }

        if ($a != 1) {
            $expr = array(sprintf('(position() %s) mod %s = 0', $bNeg, $a));
        } else {
            $expr = array();
        }

        if ($b >= 0) {
            $expr[] = sprintf('position() >= %s', $b);
        } elseif ($b < 0 && $last) {
            $expr[] = sprintf('position() < (last() %s)', $b);
        }
        $expr = implode($expr, ' and ');

        if ($expr) {
            $xpath->addCondition($expr);
        }

        return $xpath;
        






    }

    







    protected function _xpath_nth_last_child($xpath, $expr)
    {
        return $this->_xpath_nth_child($xpath, $expr, true);
    }

    







    protected function _xpath_nth_of_type($xpath, $expr)
    {
        if ($xpath->getElement() == '*') {
            throw new ParseException('*:nth-of-type() is not implemented');
        }

        return $this->_xpath_nth_child($xpath, $expr, false, false);
    }

    







    protected function _xpath_nth_last_of_type($xpath, $expr)
    {
        return $this->_xpath_nth_child($xpath, $expr, true, false);
    }

    







    protected function _xpath_contains($xpath, $expr)
    {
        
        if ($expr instanceof ElementNode) {
            $expr = $expr->formatElement();
        }

        
        
        $xpath->addCondition(sprintf('contains(string(.), %s)', XPathExpr::xpathLiteral($expr)));

        

        return $xpath;
    }

    







    protected function _xpath_not($xpath, $expr)
    {
        
        $expr = $expr->toXpath();
        $cond = $expr->getCondition();
        
        $xpath->addCondition(sprintf('not(%s)', $cond));

        return $xpath;
    }

    






    protected function parseSeries($s)
    {
        if ($s instanceof ElementNode) {
            $s = $s->formatElement();
        }

        if (!$s || '*' == $s) {
            
            return array(0, 0);
        }

        if (is_string($s)) {
            
            return array(0, $s);
        }

        if ('odd' == $s) {
            return array(2, 1);
        }

        if ('even' == $s) {
            return array(2, 0);
        }

        if ('n' == $s) {
            return array(1, 0);
        }

        if (false === strpos($s, 'n')) {
            

            return array(0, intval((string) $s));
        }

        list($a, $b) = explode('n', $s);
        if (!$a) {
            $a = 1;
        } elseif ('-' == $a || '+' == $a) {
            $a = intval($a.'1');
        } else {
            $a = intval($a);
        }

        if (!$b) {
            $b = 0;
        } elseif ('-' == $b || '+' == $b) {
            $b = intval($b.'1');
        } else {
            $b = intval($b);
        }

        return array($a, $b);
    }
}
