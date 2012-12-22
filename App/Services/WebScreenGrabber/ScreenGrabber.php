<?php

/**
 * @author M.Paraiso
 */

namespace App\Services\WebScreenGrabber{

    abstract class ScreenGrabber implements IScreenGrabber{

        /**
         * sépate les headers dans un tableau associatif
         * @param array $headers
         * @return array
         */
        protected function splitHeaders(array $headers){
            /* @var array */
            $result = array();
            foreach ($headers as $value){
                $split = preg_split("/:/", $value, 2);
                $result[$split[0]] = $split[1];
            }
            return $result;
        }

        /**
         * retourne le nombre du status
         * @param array $headers
         * @return int
         */
        protected function getStatus(array $headers){
            $status = preg_split("/\s/", $headers[0], 3);
            return $status[1];
        }

        /**
         * retourne le message du status
         * @param array $headers
         * @return string
         */
        protected function getStatusMessage(array $headers){
            $status = preg_split("/\s/", $headers[0], 3);
            return $status[2];
        }

        /**
         * effectue la requête vers le webservice
         * @param type $url
         * @param type $context
         * @return \App\Services\WebScreenGrabber\ScreenGrabberResponse
         */
        protected function doRequest($url,array $context = array()){
            if (!isset($context)){
                $streamContent = stream_context_create($context);
            }
            $image = file_get_contents($url, false, $streamContent);
            $responseHeaders = $this->splitHeaders($http_response_header);

            return new ScreenGrabberResponse($image, $responseHeaders,
                    $this->getStatus($http_response_header),
                    $this->getStatusMessage($http_response_header)
            );
        }

        /**
         * retourne le contenu binaire d'une image demandée
         * @param type $url
         * @return binary
         */
        function requestImage($url){
            $response = $this->request($url);
            return $response->content;
        }
        
        abstract function request($url);

    }

}
