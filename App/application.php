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

use \Silex\Provider\DoctrineServiceProvider;
use \Symfony\Component\HttpFoundation\Request;
use \Symfony\Component\HttpFoundation\Response;
use \Silex\Provider\SessionServiceProvider;

define('ROOT',dirname(__DIR__));

$loader = require(ROOT.'/vendor/autoload.php');
$app = new Silex\Application();

/**
 * 
 * CONFIGURATION
 * 
 */

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
    "twig.path"=>ROOT."/App/Views/","twig.options"=>array(
        "cache"=>ROOT."/cache/",
    ),
));
// enregistrement de monolog pour log des infos
$app->register(new \Silex\Provider\MonologServiceProvider(),array(
   "monolog.logfile"=>ROOT."/log/access.log",
   "monolog.name"=>"markme",
));
// enregistrement de SessionServiceProvider
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
/**
 * 
 * ROUTES
 * 
 */

// root route
$app->get("/{name}",function(Silex\Application $app,$name){
    return $app["twig"]->render("index.twig",array("name"=>$name));
})->value("name","Silex");

// enregistre un nouvel utilisateur
$app->post("/json/register",function(Silex\Application $app){
    $jsonContentType = array("Content-Type"=>"application/javascript");
    $username = $app['request']->get("username");
    $password = md5($app['request']->get("password")+$username+$app['salt']);
    $email = $app['request']->get("email");
    if($username AND $password AND $email):
        $time = time();
        $result = $app["db"]->insert('users', array('username' => $username, 
            'email' => $email ,'password'=>$password, 'created_at'=>$time,
            'last_login'=>$time));
        if($result):
            $user = array("id"=>$result['id'],"username"=>$result['username'],
                "email"=>$result["email"]);
            $app['session']->set("user_id",$user["id"]);
            $app["session"]->set("user",$user);
            $response =  $app->json($user,200,$jsonContentType);
        else:
            $response = $app->json(array("status"=>"error","message"=>"database error"),200,$jsonContentType);
        endif;
    endif;
    $response = $app->json(array("status"=>"error","message"=>"request error"),
        200,$jsonContentType);
    return $response;
});

// export la variable app du module application
return $app;