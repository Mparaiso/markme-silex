<?php










namespace Symfony\Component\HttpKernel;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Symfony\Component\Config\Loader\LoaderInterface;










interface KernelInterface extends HttpKernelInterface, \Serializable
{







function registerBundles();








function registerContainerConfiguration(LoaderInterface $loader);






function boot();








function shutdown();








function getBundles();










function isClassInActiveBundle($class);













function getBundle($name, $first = true);





























function locateResource($name, $dir = null, $first = true);








function getName();








function getEnvironment();








function isDebug();








function getRootDir();








function getContainer();








function getStartTime();








function getCacheDir();








function getLogDir();
}
