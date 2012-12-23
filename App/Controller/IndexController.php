<?php

/**
 * @author M.paraiso
 */

namespace App\Controller{

    use Silex\Application;

    class IndexController{

        /**
         * homepage
         * @param \Silex\Application $app
         * @return Response
         */
        function index(Application $app){
                return $app["twig"]->render("index.twig");
        }
        
        /**
         * application
         * @param \Silex\Application $app
         */
        function application(Application $app){
            return $app["twig"]->render("application.twig");
        }

    }

}
