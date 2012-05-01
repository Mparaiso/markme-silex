<?php










namespace Silex\Provider;

use Silex\Application;
use Silex\ServiceProviderInterface;
use Silex\HttpCache;
use Symfony\Component\HttpKernel\HttpCache\Esi;
use Symfony\Component\HttpKernel\HttpCache\Store;






class HttpCacheServiceProvider implements ServiceProviderInterface
{
public function register(Application $app)
{
$app['http_cache'] = $app->share(function () use ($app) {
return new HttpCache($app, $app['http_cache.store'], $app['http_cache.esi'], $app['http_cache.options']);
});

$app['http_cache.esi'] = $app->share(function () use ($app) {
return new Esi();
});

$app['http_cache.store'] = $app->share(function () use ($app) {
return new Store($app['http_cache.cache_dir']);
});

if (!isset($app['http_cache.options'])) {
$app['http_cache.options'] = array();
}
}
}
