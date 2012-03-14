<?php










namespace Symfony\Component\DomCrawler;

use Symfony\Component\DomCrawler\Field\FormField;








class Form extends Link implements \ArrayAccess
{
    private $button;
    private $fields;

    










    public function __construct(\DOMNode $node, $currentUri, $method = null)
    {
        parent::__construct($node, $currentUri, $method);

        $this->initialize();
    }

    




    public function getFormNode()
    {
        return $this->node;
    }

    






    public function setValues(array $values)
    {
        foreach ($values as $name => $value) {
            $this->fields->set($name, $value);
        }

        return $this;
    }

    








    public function getValues()
    {
        $values = array();
        foreach ($this->fields->all() as $name => $field) {
            if ($field->isDisabled()) {
                continue;
            }

            if (!$field instanceof Field\FileFormField && $field->hasValue()) {
                $values[$name] = $field->getValue();
            }
        }

        return $values;
    }

    






    public function getFiles()
    {
        if (!in_array($this->getMethod(), array('POST', 'PUT', 'DELETE', 'PATCH'))) {
            return array();
        }

        $files = array();

        foreach ($this->fields->all() as $name => $field) {
            if ($field->isDisabled()) {
                continue;
            }

            if ($field instanceof Field\FileFormField) {
                $files[$name] = $field->getValue();
            }
        }

        return $files;
    }

    









    public function getPhpValues()
    {
        $qs = http_build_query($this->getValues());
        parse_str($qs, $values);

        return $values;
    }

    









    public function getPhpFiles()
    {
        $qs = http_build_query($this->getFiles());
        parse_str($qs, $values);

        return $values;
    }

    










    public function getUri()
    {
        $uri = parent::getUri();

        if (!in_array($this->getMethod(), array('POST', 'PUT', 'DELETE', 'PATCH')) && $queryString = http_build_query($this->getValues(), null, '&')) {
            $sep = false === strpos($uri, '?') ? '?' : '&';
            $uri .= $sep.$queryString;
        }

        return $uri;
    }

    protected function getRawUri()
    {
        return $this->node->getAttribute('action');
    }

    








    public function getMethod()
    {
        if (null !== $this->method) {
            return $this->method;
        }

        return $this->node->getAttribute('method') ? strtoupper($this->node->getAttribute('method')) : 'GET';
    }

    








    public function has($name)
    {
        return $this->fields->has($name);
    }

    








    public function remove($name)
    {
        $this->fields->remove($name);
    }

    










    public function get($name)
    {
        return $this->fields->get($name);
    }

    








    public function set(FormField $field)
    {
        $this->fields->add($field);
    }

    






    public function all()
    {
        return $this->fields->all();
    }

    






    public function offsetExists($name)
    {
        return $this->has($name);
    }

    








    public function offsetGet($name)
    {
        return $this->fields->get($name);
    }

    







    public function offsetSet($name, $value)
    {
        $this->fields->set($name, $value);
    }

    




    public function offsetUnset($name)
    {
        $this->fields->remove($name);
    }

    protected function setNode(\DOMNode $node)
    {
        $this->button = $node;
        if ('button' == $node->nodeName || ('input' == $node->nodeName && in_array($node->getAttribute('type'), array('submit', 'button', 'image')))) {
            do {
                
                if (null === $node = $node->parentNode) {
                    throw new \LogicException('The selected node does not have a form ancestor.');
                }
            } while ('form' != $node->nodeName);
        } elseif('form' != $node->nodeName) {
            throw new \LogicException(sprintf('Unable to submit on a "%s" tag.', $node->nodeName));
        }

        $this->node = $node;
    }

    private function initialize()
    {
        $this->fields = new FormFieldRegistry();

        $document = new \DOMDocument('1.0', 'UTF-8');
        $node = $document->importNode($this->node, true);
        $button = $document->importNode($this->button, true);
        $root = $document->appendChild($document->createElement('_root'));
        $root->appendChild($node);
        $root->appendChild($button);
        $xpath = new \DOMXPath($document);

        foreach ($xpath->query('descendant::input | descendant::textarea | descendant::select', $root) as $node) {
            if (!$node->hasAttribute('name')) {
                continue;
            }

            $nodeName = $node->nodeName;

            if ($node === $button) {
                $this->set(new Field\InputFormField($node));
            } elseif ('select' == $nodeName || 'input' == $nodeName && 'checkbox' == $node->getAttribute('type')) {
                $this->set(new Field\ChoiceFormField($node));
            } elseif ('input' == $nodeName && 'radio' == $node->getAttribute('type')) {
                if ($this->has($node->getAttribute('name'))) {
                    $this->get($node->getAttribute('name'))->addChoice($node);
                } else {
                    $this->set(new Field\ChoiceFormField($node));
                }
            } elseif ('input' == $nodeName && 'file' == $node->getAttribute('type')) {
                $this->set(new Field\FileFormField($node));
            } elseif ('input' == $nodeName && !in_array($node->getAttribute('type'), array('submit', 'button', 'image'))) {
                $this->set(new Field\InputFormField($node));
            } elseif ('textarea' == $nodeName) {
                $this->set(new Field\TextareaFormField($node));
            }
        }
    }
}

class FormFieldRegistry
{
    private $fields = array();

    private $base;

    






    public function add(FormField $field)
    {
        $segments = $this->getSegments($field->getName());

        $target =& $this->fields;
        while ($segments) {
            if (!is_array($target)) {
                $target = array();
            }
            $path = array_shift($segments);
            if ('' === $path) {
                $target =& $target[];
            } else {
                $target =& $target[$path];
            }
        }
        $target = $field;
    }

    






    public function remove($name)
    {
        $segments = $this->getSegments($name);
        $target =& $this->fields;
        while (count($segments) > 1) {
            $path = array_shift($segments);
            if (!array_key_exists($path, $target)) {
                return;
            }
            $target =& $target[$path];
        }
        unset($target[array_shift($segments)]);
    }

    









    public function &get($name)
    {
        $segments = $this->getSegments($name);
        $target =& $this->fields;
        while ($segments) {
            $path = array_shift($segments);
            if (!array_key_exists($path, $target)) {
                throw new \InvalidArgumentException(sprintf('Unreachable field "%s"', $path));
            }
            $target =& $target[$path];
        }

        return $target;
    }

    






    public function has($name)
    {
        try {
            $this->get($name);

            return true;
        } catch (\InvalidArgumentException $e) {
            return false;
        }
    }

    








    public function set($name, $value)
    {
        $target =& $this->get($name);
        if (is_array($value)) {
            $fields = self::create($name, $value);
            foreach ($fields->all() as $k => $v) {
                $this->set($k, $v);
            }
        } else {
            $target->setValue($value);
        }
    }

    




    public function all()
    {
        return $this->walk($this->fields, $this->base);
    }

    










    static private function create($base, array $values)
    {
        $registry = new static();
        $registry->base = $base;
        $registry->fields = $values;

        return $registry;
    }

    








    private function walk(array $array, $base = '', array &$output = array())
    {
        foreach ($array as $k => $v) {
            $path = empty($base) ? $k : sprintf("%s[%s]", $base, $k);
            if (is_array($v)) {
                $this->walk($v, $path, $output);
            } else {
                $output[$path] = $v;
            }
        }

        return $output;
    }

    












    private function getSegments($name)
    {
        if (preg_match('/^(?P<base>[^[]+)(?P<extra>(\[.*)|$)/', $name, $m)) {
            $segments = array($m['base']);
            while (preg_match('/^\[(?P<segment>.*?)\](?P<extra>.*)$/', $m['extra'], $m)) {
                $segments[] = $m['segment'];
            }

            return $segments;
        }

        throw new \InvalidArgumentException(sprintf('Malformed field path "%s"', $name));
    }
}
