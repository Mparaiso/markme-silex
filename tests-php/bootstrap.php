<?php

/** charge l'autoloader pour les tests **/

$autoload = require(__DIR__ . "/../vendor/autoload.php");
$autoload->add("", __DIR__);
$autoload->add("MarkMe", __DIR__ . '/../');

class Bootstrap
{

    /**
     * crée une application et configure celle-ci.
     * @return Silex\Application
     */
    static function createApplication()
    {
        putenv("MARKME_DB_DRIVER=pdo_sqlite");
        $app = new \MarkMe\App(array('debug' => true));
        $app["debug"] = true;
        $app["exception_handler"]->disable();
        $app["session.test"] = true;
        $app->boot();
        return $app;
    }

}