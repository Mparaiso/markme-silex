<?php

namespace App\Services\WebScreenGrabber{

    class ThumbalizrScreenGrabber extends ScreenGrabber implements IScreenGrabber{

        const URL = "http://api.thumbalizr.com/";

        function __construct($options = null){
            $this->width = $options["width"];
        }

        /**
         * @return File retourne un fichier d'image
         * @param string $url
         */
        function request($url){
            $context = stream_context_create(array(
                "http"=>array(
                    "method"=>"GET"
                )
            ));
            $queryString = $this->getQueryString($url);
            $image = file_get_contents(URL."?".$queryString,false,$context);
            $responseHeaders = $this->splitHeaders($http_reponse_header);
            return array(
                "content"=>$image,
                "headers"=>$responseHeaders
            );
        }
        
        /**
         * construit le query string
         * @param string $url
         * @return string
         */
        public function getQueryString($url){
            return http_build_query(array(
                    "url"=>$url,
                    "width"=>$this->width
                ));
        }

    }

}

