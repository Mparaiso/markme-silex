<?php
/**
 * @author M.paraiso
 */
namespace App\Controller{

    use Silex\Application;
    
    class IndexController{
        
        function index(Application $app,$name){
            return $app["twig"]->render("index.twig",array("name"=>$name));
        }
    }
        
}
