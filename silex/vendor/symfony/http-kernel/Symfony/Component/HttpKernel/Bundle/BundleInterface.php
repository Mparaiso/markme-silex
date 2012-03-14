<?php










namespace Symfony\Component\HttpKernel\Bundle;

use Symfony\Component\DependencyInjection\ContainerBuilder;








interface BundleInterface
{
    




    function boot();

    




    function shutdown();

    








    function build(ContainerBuilder $container);

    






    function getContainerExtension();

    






    function getParent();

    






    function getName();

    






    function getNamespace();

    








    function getPath();
}
