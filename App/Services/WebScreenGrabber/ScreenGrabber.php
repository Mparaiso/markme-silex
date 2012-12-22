<?php

/**
 * @author M.Paraiso
 */

namespace App\Services\WebScreenGrabber{

    class ScreenGrabber implements IScreenGrabber{

        /**
         * sépate les headers dans un tableau associatif
         * @param array $headers
         * @return array
         */
        function splitHeaders(array $headers){
            /* @var array */
            $result = array();
            foreach ($headers as $value){
                $split = preg_split("/:/", $value, 2);
                $result[$split[0]] = $split[1];
            }
            return $result;
        }

    }

}
