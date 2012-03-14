<?php










namespace Symfony\Component\DomCrawler\Field;











class InputFormField extends FormField
{
    




    protected function initialize()
    {
        if ('input' != $this->node->nodeName) {
            throw new \LogicException(sprintf('An InputFormField can only be created from an input tag (%s given).', $this->node->nodeName));
        }

        if ('checkbox' == $this->node->getAttribute('type')) {
            throw new \LogicException('Checkboxes should be instances of ChoiceFormField.');
        }

        if ('file' == $this->node->getAttribute('type')) {
            throw new \LogicException('File inputs should be instances of FileFormField.');
        }

        $this->value = $this->node->getAttribute('value');
    }
}
