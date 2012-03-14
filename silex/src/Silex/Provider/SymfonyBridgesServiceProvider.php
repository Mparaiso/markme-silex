<?php










namespace Silex\Provider;

use Silex\Application;
use Silex\ServiceProviderInterface;






class SymfonyBridgesServiceProvider implements ServiceProviderInterface
{
    public function register(Application $app)
    {
        $app['symfony_bridges'] = true;

        if (isset($app['symfony_bridges.class_path'])) {
            $app['autoloader']->registerNamespace('Symfony\\Bridge', $app['symfony_bridges.class_path']);
        }
    }
}
