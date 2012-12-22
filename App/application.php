<?php
/**
 * FR : MarkMe est une application écrite en PHP avec le framework silex avec un front-end utilisant le framework AngularJS
 * Avec MarkMe , les internautes peuvent marquer n'importe quelle page internet , indépendament du navigateur utilisé , et retrouver
 * les marques pages sur n'importe quel os , ordinateur , et navigateur , à tout moment.
 * 
 * @author M.Paraiso
 * 
 * API : 
 * PUT /json/user/ Update a user's profile
 * GET /json/tag/ Retrieve a user's tags
 * GET /json/autocomplete/ Autocomplete for tagging, returns tags matching input
 * PUT /json/bookmark/:id update a bookmark
 * DELETE /json/bookmark/:id Delete a bookmark
 * POST /json/import Import bookmarks from an HTML file
 */

use \Silex\Provider\DoctrineServiceProvider;
use \Symfony\Component\HttpFoundation\Request;
use \Silex\Provider\SessionServiceProvider;

if(! defined("ROOT")):
    define('ROOT',dirname(__DIR__));
endif;
/**
 *  @var Composer\Autoload\ClassLoader
 */
$loader = require(ROOT.'/vendor/autoload.php');
$app = new Silex\Application();

/**
 * 
 * CONFIGURATION
 * 
 */

$loader->add("App",ROOT);
$app["debug"]=true;
//used for session and password hashes
$app['salt']="yMeb2v7+hnJxEWpG/SgytDv57qKEg5Uw1t2I9dNmd/o=";
// enregistrement de DoctrineServiceProvider
$app->register(new DoctrineServiceProvider(),array("db.options"=>array(
    "driver"=>getenv("MARKME_DB_DRIVER"),
    "dbname"=>"markme",
    "host"=>"localhost",
    "user"=>getenv("MARKME_DB_USERNAME"),
    "password"=>getenv("MARKME_DB_PASSWORD"),
    "memory"=>true,
)));
// enregistrement de Twig
$app->register(new Silex\Provider\TwigServiceProvider(),array(
    "twig.path"=>array(ROOT."/App/Views/",ROOT."/public/"),"twig.options"=>array(
        "cache"=>ROOT."/cache/",
    ),
));

// enregistrement de monolog pour log des infos
$app->register(new \Silex\Provider\MonologServiceProvider(),array(
   "monolog.logfile"=>ROOT."/log/access.log",
   "monolog.name"=>"markme",
));
// FR : enregistrement de SessionServiceProvider
$app->register(new SessionServiceProvider(),array(
    "session.storage.options"=>array(
        "httponly"=>true,
        "domain"=>"markme.app"
    ),
));
/**
 * 
 * MIDDLEWARE
 * 
 */
// transforme le corps d'une requete json en données de formulaire classique
$app->before(function(Request $req){
    if(0===strpos($req->headers->get('Content-Type'),'application/json')):
        $data= json_decode($req->getContent(),true);
        $req->request->replace(is_array($data)?$data:array());
        return $req;
    endif;
});
/** vérifie si un utilisateur est loggé **/
$mustBeLoggedIn = function()use($app){
    if(!($app[session]->get("user_id") && $app[session]->get("user"))):
        $app["session"]->invalidate();
        return $app->abort("401",'Unauthorized user');
    endif;
};
$mustBeAnonymous =function()use($app){
    if($app["session"]->get("user_id")):
        return $app->abort("404","User already logged in");
    endif;
};
// la requète post doit être un json
$mustBeValidJSON = function(Request $request)use($app){
    $data= json_decode($request->getContent(),true);
    if(!isset($data)):
        return $app->abort("403");
    endif;
};
/**
 * 
 * ROUTES
 * 
 */


// FR : enregistre un nouvel utilisateur
$app->post("/json/register",
        "App\Controller\UserController::register")->before($mustBeValidJSON)
        ->before($mustBeAnonymous);

$app->post("/json/login",
        "App\Controller\UserController::login")->before($mustBeValidJSON);

// images
$app->get("/image",
    "App\Controller\ImageController::getByUrl");

// root route
$app->match("/{name}","App\Controller\IndexController::index")
        ->value("name","Silex");

// FR : routes protégée
$protectedRoutes = $app["controllers_factory"];
$protectedRoutes->before($mustBeLoggedIn);
$protectedRoutes->post("/json/logout",
        "App\Controller\UserController::logout");
$protectedRoutes->get("/json/user",
        "App\Controller\UserController::getCurrent");
$protectedRoutes->put("/json/user",
        "App\Controller\UserController::updateUser")->before($mustBeValidJSON);
// bookmarks
$protectedRoutes->post("/json/bookmark",
        "App\Controller\BookmarkController::create")->before($mustBeValidJSON);
$protectedRoutes->delete("/json/bookmark/{id}",
        "App\Controller\BookmarkController::delete");
$protectedRoutes->get("/json/bookmark",
        "App\Controller\BookmarkController::getAll");
$protectedRoutes->get("/json/bookmark/tag",
        "App\Controller\BookmarkController::getByTag")->before($mustBeValidJSON);
$protectedRoutes->get("/json/bookmark/search",
    "App\Controller\BookmarkController::search")->before($mustBeValidJSON);
// tags
$protectedRoutes->get("/json/tag",
    "App\Controller\TagController::get");
$protectedRoutes->get("/json/tag/{tag}",
    "App\Controller\TagController::autocomplete");
// images
$protectedRoutes->get("/image/{imageName}",
    "App\Controller\ImageController::get");
// installer les routes protégées
$app->mount("/",$protectedRoutes);

// export la variable app du module application
return $app;