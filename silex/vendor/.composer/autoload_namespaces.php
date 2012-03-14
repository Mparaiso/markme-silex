<?php



$vendorDir = dirname(__DIR__);

return array(
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
    'Silex' => dirname($vendorDir) . '/src/',
    'SessionHandlerInterface' => $vendorDir . '/symfony/http-foundation/Symfony/Component/HttpFoundation/Resources/stub',
    'Pimple' => $vendorDir . '/pimple/pimple/lib/',
);
