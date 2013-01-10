<?php

namespace App\DataTransferObjects {

    /**
     * FR : représente un marque page
     */
    class Bookmark {

        public $id;
        public $user_id;
        public $description;
        public $url;
        public $title;
        public $created_at;
        public $tags;
        /**
         * @var array $tags
         */
        //protected $tags;
        
        public $private;
        
        /**
        public function __set($attr, $val) {
            $method = "set" . ucfirst($attr);
            if (method_exists($this, $method)) {
                return $this->$method($val);
            } else {
                throw new \Exception("Method $method doesnt exist !");
            }
        }

        public function __get($attr) {
            $method = "set" . ucfirst($attr);
            if (method_exists($this, $method)) {
                return $this->$method();
            } else {
                throw new \Exception("Method $method doesnt exist !");
            }
        }

        public function setTags(array $tags=null) {
            $this->tags = $tags;
        }

        public function getTags() {
            return $this->tags;
        }
        **/
        
    }

}
