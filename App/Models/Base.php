<?php

namespace App\Models {

    class Base {

        protected $db;

        function setDb($db){
            $this->db = $db;
        }
        
        function getDb(){
            return $this->db;
        }

        function __get($attr) {
            $method = "get" . ucwords($attr);
            if (method_exists($this, $method)) {
                return $this->$method();
            }
            if (property_exists($this, $attr)) {
                return $this->$attr;
            }
        }

        function __set($attr, $value) {
            $method = "set" . ucwords($attr);
            if (method_exists($this, $method)) {
                return $this->$method($value);
            }
            if(property_exists($this,$attr)) {
                $this->$attr = $value;
                return $this->$attr;
            }
        }

    }

}