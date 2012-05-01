<?php



$vendorDir = __DIR__;
$baseDir = dirname($vendorDir);

return array(
'Twig_' => $vendorDir . '/twig/twig/lib/',
'Symfony\\Component\\Routing' => $vendorDir . '/symfony/routing/',
'Symfony\\Component\\Process' => $vendorDir . '/symfony/process/',
'Symfony\\Component\\HttpKernel' => $vendorDir . '/symfony/http-kernel/',
'Symfony\\Component\\HttpFoundation' => $vendorDir . '/symfony/http-foundation/',
'Symfony\\Component\\Finder' => $vendorDir . '/symfony/finder/',
'Symfony\\Component\\EventDispatcher' => $vendorDir . '/symfony/event-dispatcher/',
'Symfony\\Component\\DomCrawler' => $vendorDir . '/symfony/dom-crawler/',
'Symfony\\Component\\CssSelector' => $vendorDir . '/symfony/css-selector/',
'Symfony\\Component\\ClassLoader' => $vendorDir . '/symfony/class-loader/',
'Symfony\\Component\\BrowserKit' => $vendorDir . '/symfony/browser-kit/',
'Symfony' => $vendorDir . '/symfony/symfony/src/',
'Silex' => $baseDir . '/src/',
'SessionHandlerInterface' => $vendorDir . '/symfony/http-foundation/Symfony/Component/HttpFoundation/Resources/stubs',
'Pimple' => $vendorDir . '/pimple/pimple/lib/',
'Monolog' => $vendorDir . '/monolog/monolog/src/',
'Doctrine\\DBAL' => $vendorDir . '/doctrine/dbal/lib/',
'Doctrine\\Common' => $vendorDir . '/doctrine/common/lib/',
);
