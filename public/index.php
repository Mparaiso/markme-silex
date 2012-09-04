<?php

/**
 *
 *  View documentation at http://silex.sensiolabs.org/documentation
 *
 */

use Silex\Application;

require_once '../vendor/autoload.php';

# Create new app
$app = new Silex\Application();

# Enable debugging
$app['debug'] = true;

# No name specified, so give instructions
$app->get('/', function() {
    return 'Hello! To test this Silex app, put your name at the end of the URL in the address bar above! For example: '.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'].'John';
});

# info
if($app['debug']===true):
	$app->get('/info',function(Application $app){
		return phpinfo();
	});
endif;

# Hello {name} example
$app->get('/{name}', function($name) use($app) {
    return 'Hello, '.$app->escape($name).'!';
});

# Run the app
$app->run();
?>
