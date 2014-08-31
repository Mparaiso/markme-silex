<?php


use MarkMe\App;

date_default_timezone_set('Europe/Paris') ;

$autoload = require_once __DIR__.'/../vendor/autoload.php';
$autoload->add('MarkMe',__DIR__."/../");

# router for php builtin server router

$filename = __DIR__ . preg_replace('#(\?.*)$#', '', $_SERVER['REQUEST_URI']);
if (php_sapi_name() === 'cli-server' && is_file($filename)) {
    return false;
}

$debug = getenv('PHP_ENV') == "production" ? FALSE : TRUE;
$app = new App(array('debug' => $debug));
$app->run();