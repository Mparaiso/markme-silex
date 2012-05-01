<?php










namespace Symfony\Component\Routing\Generator\Dumper;

use Symfony\Component\Routing\Route;









class PhpGeneratorDumper extends GeneratorDumper
{














public function dump(array $options = array())
{
$options = array_merge(array(
'class' => 'ProjectUrlGenerator',
'base_class' => 'Symfony\\Component\\Routing\\Generator\\UrlGenerator',
), $options);

return <<<EOF
<?php

use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\Exception\RouteNotFoundException;

/**
 * {$options['class']}
 *
 * This class has been auto-generated
 * by the Symfony Routing Component.
 */
class {$options['class']} extends {$options['base_class']}
{
    static private \$declaredRoutes = {$this->generateDeclaredRoutes()};

    /**
     * Constructor.
     */
    public function __construct(RequestContext \$context)
    {
        \$this->context = \$context;
    }

{$this->generateGenerateMethod()}
}

EOF;
}







private function generateDeclaredRoutes()
{
$routes = "array(\n";
foreach ($this->getRoutes()->all() as $name => $route) {
$compiledRoute = $route->compile();

$properties = array();
$properties[] = $compiledRoute->getVariables();
$properties[] = $compiledRoute->getDefaults();
$properties[] = $compiledRoute->getRequirements();
$properties[] = $compiledRoute->getTokens();

$routes .= sprintf("        '%s' => %s,\n", $name, str_replace("\n", '', var_export($properties, true)));
}
$routes .= '    )';

return $routes;
}






private function generateGenerateMethod()
{
return <<<EOF
    public function generate(\$name, \$parameters = array(), \$absolute = false)
    {
        if (!isset(self::\$declaredRoutes[\$name])) {
            throw new RouteNotFoundException(sprintf('Route "%s" does not exist.', \$name));
        }

        list(\$variables, \$defaults, \$requirements, \$tokens) = self::\$declaredRoutes[\$name];

        return \$this->doGenerate(\$variables, \$defaults, \$requirements, \$tokens, \$parameters, \$name, \$absolute);
    }
EOF;
}
}
