<?php
/**
 *
 *  View documentation at http://silex.sensiolabs.org/documentation
 *
 */
use Silex\Application;
use Silex\Provider\MonologServiceProvider;
use App\Controller\IndexController;

$loader = require_once(dirname(__DIR__).'/vendor/autoload.php');

# Create new app
$app = new Silex\Application();
$app['config.mongodb_server']=getenv("MONGODB_SERVER")?getenv("MONGODB_SERVER"):"localhost";
$app['config.mongodb_database']=getenv("MONGODB_DATABASE")?getenv("MONGODB_DATABASE"):'log';

$app['autoloader'] = $app->share(function(Application $app)use($loader){
	return $loader;
});
$app['autoloader']->add("App",dirname(__DIR__));
$app['mongo'] = $app->share(
	function(Application $app){
		return new Mongo($app['config.mongodb_server']);
	}
);
### SERVICES 

$app->register(new MonologServiceProvider(),
	array("monolog.logfile"=>dirname(__DIR__)."/log/application.log")
);
// $app['monolog.handler'] = $app->share(
// 	function(Application $app){
// 		return new Monolog\Handler\MongoDBHandler(
// 			new $app['mongo'],
// 			$app['config.mongodb_database'],
// 			"log");
// 	}
// );
$app->mount('/',new IndexController());
# Enable debugging
$app['debug'] = true;

# info
if($app['debug']===true):
	$app->get('/info',function(Application $app){
		return phpinfo();
	});
endif;

#$app['monolog']->addInfo("Application configured.");
$collection = $app['mongo']->selectDB($app['config.mongodb_database'])->selectCollection('log');
$collection->insert(array("message"=>"test"));

# Run the app
$app->run();

return $app;
