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
$app['config.mongodb_server']=getenv("MONGODB_SERVER");
$app['config.mongodb_database']=getenv("MONGODB_DATABASE");

$app['autoloader'] = $app->share(function(Application $app)use($loader){
	return $loader;
});
$app['autoloader']->add("App",dirname(__DIR__));

### SERVICES 

$app->register(new MonologServiceProvider(),
	array("monolog.logfile"=>dirname(__DIR__)."/log/application.log")
);
/*$app['monolog.handler'] = $app->share(function(Application $app){
		return new Monolog\Handler\MongoDBHandler(
			new Mongo($app['config.mongodb_server']),
			$app['config.mongodb_database'],
			"log"
			);
	}
);*/
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

# Run the app
$app->run();
?>
