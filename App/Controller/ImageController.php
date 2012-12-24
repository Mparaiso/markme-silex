<?php

namespace App\Controller{

    use Silex\Application;
use App\Services\WebScreenGrabber\ThumbalizrScreenGrabber;
use Symfony\Component\HttpFoundation\Response;

    class ImageController{

        /**
         * si l'image existe , retourne l'image , sinon, 
         * crée l'image et retourne l'image
         * @param \Silex\Application $app
         * @param string $imageName
         */
        function get(Application $app, $imageName){
            return;
        }

        /**
         * crée une image via une url et retourne l'image
         * parcoure une liste de services , pour chaque service , tente de récupere une image
         * @param \Silex\Application $app
         * @return type
         */
        function getByUrl(Application $app){
            $url = $app["request"]->get("url");
            $services = array(                
                "\App\Services\WebScreenGrabber\WimgCaScreenGrabber",
                "App\Services\WebScreenGrabber\ThumbalizrScreenGrabber",
                "\App\Services\WebScreenGrabber\RobothumbScreenGraber",
                );
            $i = 0;
            $validResponse = false;
            while ($validResponse === false && $i < count($services)){
                $service = $services[$i];
                $serviceInstance = new $service();
                $response = $serviceInstance->request($url);
                if ($response->status < 400){
                    $validResponse = true;
                    $app["logger"]->info("service $service is UP for image delivery for url $url");
                } else{
                    $app["logger"]->err("service $service is DOWN for image delivery for url $url");
                }
                $i+=1;
            }
            $contentType = $response->headers["Content-Type"];
            if ($response->content){
                return new Response($response->content, 200, array("Content-Type"=>$contentType));
            } else{
                return $app->abord(404, "Image not found");
            }
        }

    }

}