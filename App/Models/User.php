<?php

namespace App\Models {

    use \Exception;
use Doctrine\DBAL\DBALException;

    class User extends Base {

        protected $_id;
        protected $username;
        protected $email;
        protected $password;
        protected $created_at;
        protected $last_login;
        protected $_table = "users";

        function __construct($username, $email, $password, $created_at, $last_login) {
            $this->username = $username;
            $this->email = $email;
            $this->password = $password;
            $this->created_at = $created_at;
            $this->last_login = $last_login;
        }

        function insert() {
            $db = $this->getDb();
            $this->_id = $db->insert($this->_table, array('username' => $this->username,
                'email' => $this->email, 'password' => $this->password, 'created_at' => $this->time,
                'last_login' => $this->time));
            return $this->_id;
        }

        function update() {
           
        }

    }

}