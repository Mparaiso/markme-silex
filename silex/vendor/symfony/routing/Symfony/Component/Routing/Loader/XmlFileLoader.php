<?php










namespace Symfony\Component\Routing\Loader;

use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\Route;
use Symfony\Component\Config\Resource\FileResource;
use Symfony\Component\Config\Loader\FileLoader;








class XmlFileLoader extends FileLoader
{
    











    public function load($file, $type = null)
    {
        $path = $this->locator->locate($file);

        $xml = $this->loadFile($path);

        $collection = new RouteCollection();
        $collection->addResource(new FileResource($path));

        
        foreach ($xml->documentElement->childNodes as $node) {
            if (!$node instanceof \DOMElement) {
                continue;
            }

            $this->parseNode($collection, $node, $path, $file);
        }

        return $collection;
    }

    







    protected function parseNode(RouteCollection $collection, \DOMElement $node, $path, $file)
    {
        switch ($node->tagName) {
            case 'route':
                $this->parseRoute($collection, $node, $path);
                break;
            case 'import':
                $resource = (string) $node->getAttribute('resource');
                $type = (string) $node->getAttribute('type');
                $prefix = (string) $node->getAttribute('prefix');

                $defaults = array();
                $requirements = array();

                foreach ($node->childNodes as $n) {
                    if (!$n instanceof \DOMElement) {
                        continue;
                    }

                    switch ($n->tagName) {
                        case 'default':
                            $defaults[(string) $n->getAttribute('key')] = trim((string) $n->nodeValue);
                            break;
                        case 'requirement':
                            $requirements[(string) $n->getAttribute('key')] = trim((string) $n->nodeValue);
                            break;
                        default:
                            throw new \InvalidArgumentException(sprintf('Unable to parse tag "%s"', $n->tagName));
                    }
                }

                $this->setCurrentDir(dirname($path));
                $collection->addCollection($this->import($resource, ('' !== $type ? $type : null), false, $file), $prefix, $defaults, $requirements);
                break;
            default:
                throw new \InvalidArgumentException(sprintf('Unable to parse tag "%s"', $node->tagName));
        }
    }

    









    public function supports($resource, $type = null)
    {
        return is_string($resource) && 'xml' === pathinfo($resource, PATHINFO_EXTENSION) && (!$type || 'xml' === $type);
    }

    








    protected function parseRoute(RouteCollection $collection, \DOMElement $definition, $file)
    {
        $defaults = array();
        $requirements = array();
        $options = array();

        foreach ($definition->childNodes as $node) {
            if (!$node instanceof \DOMElement) {
                continue;
            }

            switch ($node->tagName) {
                case 'default':
                    $defaults[(string) $node->getAttribute('key')] = trim((string) $node->nodeValue);
                    break;
                case 'option':
                    $options[(string) $node->getAttribute('key')] = trim((string) $node->nodeValue);
                    break;
                case 'requirement':
                    $requirements[(string) $node->getAttribute('key')] = trim((string) $node->nodeValue);
                    break;
                default:
                    throw new \InvalidArgumentException(sprintf('Unable to parse tag "%s"', $node->tagName));
            }
        }

        $route = new Route((string) $definition->getAttribute('pattern'), $defaults, $requirements, $options);

        $collection->add((string) $definition->getAttribute('id'), $route);
    }

    








    protected function loadFile($file)
    {
        $dom = new \DOMDocument();
        libxml_use_internal_errors(true);
        if (!$dom->load($file, defined('LIBXML_COMPACT') ? LIBXML_COMPACT : 0)) {
            throw new \InvalidArgumentException(implode("\n", $this->getXmlErrors()));
        }
        $dom->validateOnParse = true;
        $dom->normalizeDocument();
        libxml_use_internal_errors(false);
        $this->validate($dom);

        return $dom;
    }

    






    protected function validate(\DOMDocument $dom)
    {
        $location = __DIR__.'/schema/routing/routing-1.0.xsd';

        $current = libxml_use_internal_errors(true);
        if (!$dom->schemaValidate($location)) {
            throw new \InvalidArgumentException(implode("\n", $this->getXmlErrors()));
        }
        libxml_use_internal_errors($current);
    }

    




    private function getXmlErrors()
    {
        $errors = array();
        foreach (libxml_get_errors() as $error) {
            $errors[] = sprintf('[%s %s] %s (in %s - line %d, column %d)',
                LIBXML_ERR_WARNING == $error->level ? 'WARNING' : 'ERROR',
                $error->code,
                trim($error->message),
                $error->file ? $error->file : 'n/a',
                $error->line,
                $error->column
            );
        }

        libxml_clear_errors();

        return $errors;
    }
}
