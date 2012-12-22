<?php

namespace App\Services\WebScreenGrabber{

    /**
     * @author M.Paraiso
     * permet d'interroger le service wimg.ca
     * et d'obtenir la miniature d'une capture d'écran
     * d'un site internet grace à l'url de ce site.
     */
    class WimgCaScreenGrabber extends ScreenGrabber{

        const URL = "http://wimg.ca/";

        /**
         * @return File retourne un fichier d'image
         * @param string $url
         */
        function request($url){
            $_url = self::URL.$url;
            $context=array(
                "http"=>array(
                    "header"=>"User-Agent: Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.17 (KHTML, like Gecko) Chrome/24.0.1312.45 Safari/537.17",
                )
            );
            return $this->doRequest($_url,$context);
        }


    }

}

