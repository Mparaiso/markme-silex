<?php










namespace Symfony\Component\HttpKernel\DataCollector;

use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\HttpKernel\DataCollector\DataCollector;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;






class ConfigDataCollector extends DataCollector
{
    private $kernel;

    




    public function __construct(KernelInterface $kernel)
    {
        $this->kernel = $kernel;
    }

    


    public function collect(Request $request, Response $response, \Exception $exception = null)
    {
        $this->data = array(
            'token'           => $response->headers->get('X-Debug-Token'),
            'symfony_version' => Kernel::VERSION,
            'name'            => $this->kernel->getName(),
            'env'             => $this->kernel->getEnvironment(),
            'debug'           => $this->kernel->isDebug(),
            'php_version'     => PHP_VERSION,
            'xdebug_enabled'  => extension_loaded('xdebug'),
            'eaccel_enabled'  => extension_loaded('eaccelerator') && ini_get('eaccelerator.enable'),
            'apc_enabled'     => extension_loaded('apc') && ini_get('apc.enabled'),
            'xcache_enabled'  => extension_loaded('xcache') && ini_get('xcache.cacher'),
            'bundles'         => array(),
        );

        foreach ($this->kernel->getBundles() as $name => $bundle) {
            $this->data['bundles'][$name] = $bundle->getPath();
        }
    }

    




    public function getToken()
    {
        return $this->data['token'];
    }

    




    public function getSymfonyVersion()
    {
        return $this->data['symfony_version'];
    }

    




    public function getPhpVersion()
    {
        return $this->data['php_version'];
    }

    




    public function getAppName()
    {
        return $this->data['name'];
    }

    




    public function getEnv()
    {
        return $this->data['env'];
    }

    




    public function isDebug()
    {
        return $this->data['debug'];
    }

    




    public function hasXDebug()
    {
        return $this->data['xdebug_enabled'];
    }

    




    public function hasEAccelerator()
    {
        return $this->data['eaccel_enabled'];
    }

    




    public function hasApc()
    {
        return $this->data['apc_enabled'];
    }

    




    public function hasXCache()
    {
        return $this->data['xcache_enabled'];
    }

    




    public function hasAccelerator()
    {
        return $this->hasApc() || $this->hasEAccelerator() || $this->hasXCache();
    }

    public function getBundles()
    {
        return $this->data['bundles'];
    }

    


    public function getName()
    {
        return 'config';
    }
}
