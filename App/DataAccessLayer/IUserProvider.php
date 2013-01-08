<?php

namespace App\DataAccessLayer {

    use \App\DataTransferObjects\User;

    interface IUserProvider{

        /**
         * @return \App\DataTransferObjects\User
         */
        function create(User $user);

        /**
         * @return \App\DataTransferObjects\User
         */
        function getByUserNameAndPassword($username,$password);

        function updateLastLoginDate($user_id);

        function update(User $user,$user_id);

        /**
         *  @return \App\DataTransferObjects\User
         */
        function getByUsername($username);

        /**
         *  @return \App\DataTransferObjects\User
         */
        function getByEmail($email);
        
        /**
         *  @return \App\DataTransferObjects\User
         */
        function getById($user_id);




    }
}