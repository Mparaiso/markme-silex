<?php










namespace Symfony\Component\Routing\Generator\Dumper;

use Symfony\Component\Routing\RouteCollection;








interface GeneratorDumperInterface
{












function dump(array $options = array());






function getRoutes();
}
