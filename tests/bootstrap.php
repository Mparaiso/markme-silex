<?php

/** charge l'autoloader pour les tests * */
if(! defined("ROOT")):
    define("ROOT", dirname(__DIR__));
endif;

require(__DIR__ . "/../vendor/autoload.php");

/**
 * crée une application et configure celle-ci.
 * @return Silex\Application
 */
function createApplication() {
    putenv("MARKME_DB_DRIVER=pdo_sqlite");
    $schema = file_get_contents(ROOT . '/Database/schema.sqlite.sql');
//    $schema = getSchema();
    $app = require ROOT . '/App/application.php';
    $app["debug"] = true;
    $app["exception_handler"]->disable();
    $app["session.test"] = true;
    $app["db"]->exec($schema);
    return $app;
}

//function getSchema(){
//    $schema = <<<EOF
//-- schema for sqlite
//CREATE TABLE `bookmarks` (
//  `id` INTEGER NOT NULL ,
//  `user_id` int(11) DEFAULT NULL,
//  `title` varchar(255) DEFAULT NULL,
//  `description` varchar(255) DEFAULT NULL,
//  `url` text,
//  `private` tinyint(4) DEFAULT '0',
//  `created_at` datetime DEFAULT NULL,
//  PRIMARY KEY (`id`)
//);
//
//CREATE TABLE `tags` (
//  `bookmark_id` INTEGER DEFAULT NULL,
//  `tag` varchar(255) DEFAULT NULL
//);
//
//CREATE TABLE `users` (
//  `id` INTEGER NOT NULL ,
//  `username` varchar(255) DEFAULT NULL UNIQUE,
//  `password` varchar(255) DEFAULT NULL,
//  `email` varchar(255) DEFAULT NULL UNIQUE,
//  `created_at` datetime DEFAULT NULL,
//  `last_login` datetime DEFAULT NULL,
//  PRIMARY KEY (`id`)
//);
//EOF;
//    return $schema;
//}