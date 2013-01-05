<?php

namespace App\DataTransferObjects{
    /**
     * FR : représente un utilisateur
     */
    class User {
       public $id;
       public $username;
       public $password;
       public $email;
       public $created_at;
       public $last_login;
    }

}