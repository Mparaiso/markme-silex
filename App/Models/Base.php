<?php

namespace App\Models {

    class Base {

        protected $db;

        protected $table;

        function setDb($db){
            $this->db = $db;
        }
        
        function getDb(){
            return $this->db;
        }

        function getTable(){
            return $this->table;
        }

        function __get($attr) {
            $method = "get" . ucwords($attr);
            if (method_exists($this, $method)) {
                return $this->$method();
            }
            throw new Exception("Error , cant get $attr", 1);
        }

        function __set($attr, $value) {
            $method = "set" . ucwords($attr);
            if (method_exists($this, $method)) {
                return $this->$method($value);
            }
            throw new Exception("Error , cant set $attr", 1);
        }

    }

}