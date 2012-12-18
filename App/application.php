<?php
/**
 * FR : MarkMe est une application écrite en PHP avec le framework silex avec un front-end utilisant le framework AngularJS
 * Avec MarkMe , les internautes peuvent marquer n'importe quelle page internet , indépendament du navigateur utilisé , et retrouver
 * les marques pages sur n'importe quel os , ordinateur , et navigateur , à tout moment.
 * 
 * @author M.Paraiso
 * 
 * API : 
 * GET  / display default content
 * POST /json/register/ Register a new user
 * POST /json/login/ Log in an existing user, starting a session
 * POST /json/logout/ Log out the current user
 * PUT /json/user/ Update a user's profile
 * GET /json/tag/ Retrieve a user's tags
 * GET /json/autocomplete/ Autocomplete for tagging, returns tags matching input
 * GET /json/bookmark/ Return a user's bookmarks
 * PUT /json/bookmark/:id update a bookmark
 * POST /json/bookmark/:id? Create a new bookmark
 * DELETE /json/bookmark/:id Delete a bookmark
 * POST /json/import Import bookmarks from an HTML file
 */

define('ROOT',dirname(__DIR__));


use \Silex\Provider\DoctrineServiceProvider;
use \Symfony\Component\HttpFoundation\Request;
use \Symfony\Component\HttpFoundation\Response;

$loader = require(ROOT.'/vendor/autoload.php');
$app = new Silex\Application();

$app["debug"]=true;
//used for session and password hashes
$app['salt']="yMeb2v7+hnJxEWpG/SgytDv57qKEg5Uw1t2I9dNmd/o=";

$app->register(new DoctrineServiceProvider(),array());

$app->match("/{name}",function(Silex\Application $app,$name="Dude"){
    return "Hello ".$app->escape($name)." how are you?";
})->value("name","Marc");

$app->run();


// Fonctions 

// callback par défaut temporaire
function DefaultCallback(Silex\Application $app)
{
    return DefaultCallback;
}
