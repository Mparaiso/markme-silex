<?php










namespace Symfony\Component\Routing;






class CompiledRoute
{
    private $route;
    private $variables;
    private $tokens;
    private $staticPrefix;
    private $regex;

    








    public function __construct(Route $route, $staticPrefix, $regex, array $tokens, array $variables)
    {
        $this->route = $route;
        $this->staticPrefix = $staticPrefix;
        $this->regex = $regex;
        $this->tokens = $tokens;
        $this->variables = $variables;
    }

    




    public function getRoute()
    {
        return $this->route;
    }

    




    public function getStaticPrefix()
    {
        return $this->staticPrefix;
    }

    




    public function getRegex()
    {
        return $this->regex;
    }

    




    public function getTokens()
    {
        return $this->tokens;
    }

    




    public function getVariables()
    {
        return $this->variables;
    }

    




    public function getPattern()
    {
        return $this->route->getPattern();
    }

    




    public function getOptions()
    {
        return $this->route->getOptions();
    }

    




    public function getDefaults()
    {
        return $this->route->getDefaults();
    }

    




    public function getRequirements()
    {
        return $this->route->getRequirements();
    }
}
