<?php










namespace Symfony\Component\DomCrawler;








class Link
{
    protected $node;
    protected $method;
    protected $currentUri;

    










    public function __construct(\DOMNode $node, $currentUri, $method = 'GET')
    {
        if (!in_array(substr($currentUri, 0, 4), array('http', 'file'))) {
            throw new \InvalidArgumentException(sprintf('Current URI must be an absolute URL ("%s").', $currentUri));
        }

        $this->setNode($node);
        $this->method = $method ? strtoupper($method) : null;
        $this->currentUri = $currentUri;
    }

    




    public function getNode()
    {
        return $this->node;
    }

    






    public function getMethod()
    {
        return $this->method;
    }

    






    public function getUri()
    {
        $uri = trim($this->getRawUri());

        
        if (0 === strpos($uri, 'http')) {
            return $uri;
        }

        
        if (!$uri) {
            return $this->currentUri;
        }

        
        if ('#' ===  $uri[0]) {
            $baseUri = $this->currentUri;
            if (false !== $pos = strpos($baseUri, '#')) {
                $baseUri = substr($baseUri, 0, $pos);
            }

            return $baseUri.$uri;
        }

        
        if ('?' === $uri[0]) {
            $baseUri = $this->currentUri;

            
            if (false !== $pos = strpos($baseUri, '?')) {
                $baseUri = substr($baseUri, 0, $pos);
            }

            return $baseUri.$uri;
        }

        
        if ('/' === $uri[0]) {
            return preg_replace('#^(.*?//[^/]+)(?:\/.*)?$#', '$1', $this->currentUri).$uri;
        }

        
        return substr($this->currentUri, 0, strrpos($this->currentUri, '/') + 1).$uri;
    }

    protected function getRawUri()
    {
        return $this->node->getAttribute('href');
    }

    protected function setNode(\DOMNode $node)
    {
        if ('a' != $node->nodeName) {
            throw new \LogicException(sprintf('Unable to click on a "%s" tag.', $node->nodeName));
        }

        $this->node = $node;
    }
}
