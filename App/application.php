<?php

/**
 * FR : MarkMe est une application écrite en PHP avec le framework silex avec un front-end utilisant le framework AngularJS
 * Avec MarkMe , les internautes peuvent marquer n'importe quelle page internet , indépendament du navigateur utilisé , et retrouver
 * les marques pages sur n'importe quel os , ordinateur , et navigateur , à tout moment.
 * 
 * @author M.Paraiso
 * 
 */
use \Silex\Provider\DoctrineServiceProvider;
use \Symfony\Component\HttpFoundation\Request;
use \Symfony\Component\HttpFoundation\Response;
use \Silex\Provider\SessionServiceProvider;
use App\DataAccessLayer\BookmarkProvider;
use App\BusinessLogicLayer\BookmarkManager;
use App\DataAccessLayer\TagProvider;
use App\BusinessLogicLayer\TagManager;

if (!defined("ROOT")):
    define('ROOT', dirname(__DIR__));
endif;
/**
 *  @var Composer\Autoload\ClassLoader
 */
$loader = require(ROOT . '/vendor/autoload.php');
$app = new Silex\Application();

/**
 * 
 * CONFIGURATION
 * 
 */
$loader->add("App", ROOT);
$app["debug"] = getenv("ENVIRONMENT") === "Production" ? false : true;
//used for session and password hashes
$app['salt'] = "yMeb2v7+hnJxEWpG/SgytDv57qKEg5Uw1t2I9dNmd/o=";
// enregistrement de DoctrineServiceProvider
$app->register(new DoctrineServiceProvider(), array("db.options" => array(
        "driver" => getenv("MARKME_DB_DRIVER"),
        "dbname" => getenv("MARKME_DB_DATABASE_NAME"),
        "host" => getenv("MARKME_DB_HOST"),
        "user" => getenv("MARKME_DB_USERNAME"),
        "password" => getenv("MARKME_DB_PASSWORD"),
        "memory" => true,
        )));
// enregistrement de Twig
$app->register(new Silex\Provider\TwigServiceProvider(), array(
    "twig.path" => array(ROOT . "/App/Views/"), "twig.options" => array(
        "cache" => ROOT . "/cache/",
    ),
));
// enregistrement de monolog pour log des infos
$app->register(new \Silex\Provider\MonologServiceProvider(), array(
    "monolog.logfile" => ROOT . "/log/access.log",
    "monolog.name" => "markme",
));
// FR : enregistrement de SessionServiceProvider
$app->register(new SessionServiceProvider(), array(
    "session.storage.options" => array(
        "httponly" => true,
        "domain" => "markme.app"
    ),
));
// FR : enregistrement de UrlGeneratorServiceProvider
$app->register(new Silex\Provider\UrlGeneratorServiceProvider());

/**
 * Services personnalisés
 */
$app["upload_dir"] = ROOT."/upload";
$app["max_size_upload"] = ini_get("upload_max_filesize");
# retourne le temps actuel au format DATETIME de  MYSQL
$app["current_time"] = function() {
            return date('Y-m-d H:i:s', time());
        };

$app["bookmark_provider"] = function($app) {
            return new BookmarkProvider($app["db"]);
        };

$app["bookmark_manager"] = function( $app) {
            return new BookmarkManager($app["bookmark_provider"]);
        };

$app["tag_provider"] = function($app) {
            return new TagProvider($app["db"]);
        };

$app["tag_manager"] = function($app) {
            return new TagManager($app["tag_provider"]);
        };

$app["get_thumbnail"] = $app->protect(function($url, $width = 200) {
            return "http://api.thumbalizr.com/?url=" . "$url" . "&width=" . $width;
        });
$app["sql_logger"] = $app->share(function($app) {
            return new App\Services\SQLLogger\MonologSQLLogger($app["monolog"]);
        });
/**
 * 
 * MIDDLEWARE
 * 
 */
# transforme le corps d'une requete json en données de formulaire classique
$app->before(function(Request $req) {
            if (0 === strpos($req->headers->get('Content-Type'), 'application/json')):
                $data = json_decode($req->getContent(), true);
                $req->request->replace(is_array($data) ? $data : array());
                return $req;
            endif;
        });

# vérifie si un utilisateur est loggé 
$mustBeLoggedIn = function()use($app) {
            if (!($app["session"]->get("user_id") && $app["session"]->get("user"))):
                $app["session"]->invalidate();
                return $app->abort("401", 'Unauthorized user');
            endif;
        };

# si utilisateur connecté rediriger vers application
$mustBeAnonymous = function()use($app) {
            if ($app["session"]->get("user_id") && $app["session"]->get("user")):
                return $app->redirect($app["url_generator"]->generate("application"));
            endif;
        };

# la requète post doit être un json
$mustBeValidJSON = function(Request $request)use($app) {
            $data = json_decode($request->getContent(), true);
            if (!isset($data)):
                $app["logger"]->err("must be valid json : " . $request->getContent());
                return $app->abort("403");
            endif;
        };

# redirige vers la page de guarde si l'acces n'est pas autorisé
$unauthorizedAccessHandler = function(\Exception $e, $code)use($app) {
            switch ($code) {
                case 401:
                    # acces non autorisé
                    return $app->redirect($app["url_generator"]->generate("index"));
                    break;
                default :
                # ne rien faire
            }
            return new Response($e->getMessage());
        };
# FR : retrouve l'url de base du site internet
$makeBasePath = function(Request $req)use($app) {
            $app["markme.base_url"] = $req->getUriForPath("/");
        };

/**
 * FR : Gestion des exceptions
 */
if (!$app["debug"]) {
    $app->error($unauthorizedAccessHandler);
} else {
    $app["db"]->getConfiguration()->setSQLLogger($app["sql_logger"]);
}
/**
 * 
 * ROUTES
 * 
 */
$app->before($makeBasePath);
# FR : enregistre un nouvel utilisateur
$app->post("/json/register", "App\Controller\UserController::register")
        ->before($mustBeValidJSON)
        ->before($mustBeAnonymous);

$app->post("/json/login", "App\Controller\UserController::login")
        ->before($mustBeAnonymous)
        ->before($mustBeValidJSON);


// application
$app->get("/application", "App\Controller\IndexController::application")
        ->before($mustBeLoggedIn)
        ->bind("application");


// index
$app->match("/", "App\Controller\IndexController::index")
        ->bind("index")
        ->before($mustBeAnonymous);

// FR : routes protégée
$protectedRoutes = $app["controllers_factory"];
$protectedRoutes->before($mustBeLoggedIn);
$protectedRoutes->post("/json/logout", "App\Controller\UserController::logout");
$protectedRoutes->get("/json/user", "App\Controller\UserController::getCurrent");
$protectedRoutes->put("/json/user", "App\Controller\UserController::updateUser")
        ->before($mustBeValidJSON);

// bookmarks
$protectedRoutes->get("/json/bookmark/search", "App\Controller\BookmarkController::search")
        ->bind("search_bookmarks");
$protectedRoutes->post("/json/bookmark/count","App\Controller\BookmarkController::count")
        ->bind("count_bookmarks");
$protectedRoutes->post("/json/bookmark/export", "App\Controller\BookmarkController::export")
        ->bind("export_bookmarks");
$protectedRoutes->post("/json/bookmark/import", "App\Controller\BookmarkController::import")
        ->bind("import_bookmarks");
$protectedRoutes->post("/json/bookmark", "App\Controller\BookmarkController::create")
        ->before($mustBeValidJSON)
        ->bind("create_bookmark");
$protectedRoutes->put("/json/bookmark", "App\Controller\BookmarkController::update")
        ->before($mustBeValidJSON)
        ->bind("update_bookmark");
$protectedRoutes->delete("/json/bookmark/{id}", "App\Controller\BookmarkController::delete")
        ->bind("delete_bookmark");
$protectedRoutes->get("/json/bookmark", "App\Controller\BookmarkController::getAll")
        ->bind("get_bookmarks");
$protectedRoutes->get("/json/bookmark/tag/{tagName}", "App\Controller\BookmarkController::getByTag");


// tags
$protectedRoutes->get("/json/tag", "App\Controller\TagController::get")
        ->bind("get_tags");
$protectedRoutes->get("/json/tag/{tag}", "App\Controller\TagController::autocomplete")
        ->bind("search_tag");

// installation les routes protégées
$app->mount("/", $protectedRoutes);

// export la variable app du module application
return $app;
