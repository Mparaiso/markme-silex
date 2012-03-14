<?php










namespace Silex;

use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\Matcher\UrlMatcherInterface;






class LazyUrlMatcher implements UrlMatcherInterface
{
    private $factory;
    private $urlMatcher;

    public function __construct(\Closure $factory)
    {
        $this->factory = $factory;
    }

    




    public function getUrlMatcher()
    {
        $urlMatcher = call_user_func($this->factory);
        if (!$urlMatcher instanceof UrlMatcherInterface) {
            throw new \LogicException("Factory supplied to LazyUrlMatcher must return implementation of UrlMatcherInterface.");
        }
        return $urlMatcher;
    }

    


    public function match($pathinfo)
    {
        return $this->getUrlMatcher()->match($pathinfo);
    }

    


    public function setContext(RequestContext $context)
    {
        $this->getUrlMatcher()->setContext($context);
    }

    


    public function getContext()
    {
        return $this->getUrlMatcher()->getContext();
    }
}
