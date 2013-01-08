<?php

namespace App\BusinessLogicLayer{

    use App\DataAccessLayer\IUserProvider;
    use App\DataTransferObjec\User;
    
    class UserManager{

        /**
         * @var App\DataAccessLayer\IUserProvider $userProvider
         */
        protected $userProvider;

        protected $_salt;

        function __construct(IUserProvider $userProvider,$salt){
            $this->userProvider = $userProvider;
            $this->setSalt($salt);
        }

        function setSalt($salt){
            $this->_salt = $salt;
        }  

        protected function _encryptPassword($username,$password){
            if($this->_salt==null)throw new Exception("Salt cannot be null", 1);
            return md5($username.$password.$this->_salt);
        }
    }
}