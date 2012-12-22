<?php
namespace App\Services\WebScreenGrabber{

    interface IWebScreenGrabber{
        /**
         * @return File retourne un fichier d'image
         * @param string $url
         */
        function request($url);
    }
}

