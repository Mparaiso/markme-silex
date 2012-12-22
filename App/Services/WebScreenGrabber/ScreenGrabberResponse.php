<?php
/**
 * @author M.Paraiso
 */
namespace App\Services\WebScreenGrabber{

    class ScreenGrabberResponse{

        protected $content;
        protected $status;
        protected $headers;
        protected $statusMessage;

        public function __construct($content, array $headers, $status, $statusMessage){
            $this->content = $content;
            $this->headers = $headers;
            $this->status = $status;
            $this->statusMessage = $statusMessage;
        }

        public function __get($name){
            $method = "get".ucfirst($name);
            if (method_exists($this, $method)){
                return $this->$method();
            } else{
                throw new Exception("Cant get $name , method $method doesnt exists");
            }
        }

        function getContent(){
            return $this->content;
        }

        function getStatus(){
            return $this->status;
        }

        function getHeaders(){
            return $this->headers;
        }

        function getStatusMessage(){
            return $this->statusMessage;
        }

    }

}