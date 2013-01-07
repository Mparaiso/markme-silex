<?php

namespace App\BusinessLogicLayer{

    use App\DataAccessLayer\IUserProvider;
    use App\DataTransferObjec\User;
    
    class UseManager{

        /**
         * @var App\DataAccessLayer\IUserProvider $userProvider
         */
        protected $userProvider;

        function __construct(IUserProvider $userProvider){
            $this->userProvider = $userProvider;
        }
    }
}