<?php

namespace App\Services\WebScreenGrabber{
    /**
     * @author M.Paraiso
     * permet d'interroger le service Thumbalizr
     * pour obtenir la miniature d'une capture d'écran
     * d'un site internet grace à l'url de ce site.
     */
    class ThumbalizrScreenGrabber extends ScreenGrabber {

        const URL = "http://api.thumbalizr.com/";

        function __construct($options = null){
            $this->width = $options["width"];
        }

        /**
         * @return File retourne un fichier d'image
         * @param string $url
         */
        function request($url){
            $queryString = $this->getQueryString($url);
            $_url = self::URL."?".$queryString;
            return $this->doRequest($_url);
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

