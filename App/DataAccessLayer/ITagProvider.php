<?php

namespace App\DataAccessLayer{

    interface ITagProvider{
        function get($user_id);
        function search($expression,$user_id);
    }
    
}