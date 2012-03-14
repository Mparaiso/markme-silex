<?php










namespace Symfony\Component\DomCrawler\Field;








class TextareaFormField extends FormField
{
    




    protected function initialize()
    {
        if ('textarea' != $this->node->nodeName) {
            throw new \LogicException(sprintf('A TextareaFormField can only be created from a textarea tag (%s given).', $this->node->nodeName));
        }

        $this->value = null;
        foreach ($this->node->childNodes as $node) {
            $this->value .= $this->document->saveXML($node);
        }
    }
}
