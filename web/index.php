<?php

use MarkMe\App;
use Symfony\Component\Debug\ErrorHandler;
use Symfony\Component\Debug\ExceptionHandler;
use Symfony\Component\HttpFoundation\Request;
date_default_timezone_set('Europe/Paris');

$autoload = require_once __DIR__ . '/../vendor/autoload.php';
$autoload->add('MarkMe', __DIR__ . "/../");

# router for php builtin server router

$filename = __DIR__ . preg_replace('#(\?.*)$#', '', $_SERVER['REQUEST_URI']);
if (php_sapi_name() === 'cli-server' && is_file($filename)) {
    return false;
}

$debug = getenv('MARKME_ENVIRONMENT') == "production" ? FALSE : TRUE;

ErrorHandler::register();
ExceptionHandler::register($debug);

$app = new App(array('debug' => $debug));
$app['http_cache']->run();
