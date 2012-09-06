<?php
namespace App\Controller{

	use Silex\ControllerProviderInterface;
	use Silex\Application;
	use Symfony\Component\HttpFoundation\Response;
	use Symfony\Component\HttpFoundation\Request;
	/** 
	* index
	*/
	class IndexController implements ControllerProviderInterface{
		function connect(Application $app){
			$index = $app['controllers_factory'];
			$index->get('/','App\Controller\IndexController::index');
			$index->get('/info','App\Controller\IndexController::info');
			$index->get('/log','App\Controller\IndexController::log')->after(
				function(Request $request,Response $response){
					return $response->headers->set('Content-Type','text/plain');
				}
			);
			$index->get('/{name}','App\Controller\IndexController::helloName');
			return $index;
		}

		function index(Application $app){
				try {
					$collection = $app['mongo']->selectDB($app['config.mongodb_database'])->selectCollection('log');
					$collection->insert(array("message"=>"test".date("r")));
				} catch (Exception $e) {
				    echo 'Exception reçue : ',  $e->getMessage(), "\n";
				}
			    return 'home : .'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
		}

		function helloName(Application $app,$name){
			return "Hello $name !";
		}

		function info(Application $app){
			return phpinfo();
		}

		function log(Application $app){
			$log = file_get_contents(ROOT.'/log/application.log');
			return $log;
		}
	}
}