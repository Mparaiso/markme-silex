<?php
namespace App\Services\WebScreenGrabber{

    interface IScreenGrabber{
        /**
         * @return File retourne un fichier d'image
         * @param string $url
         */
        function request($url);
    }
}

