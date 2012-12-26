<?php

namespace App\Services\Managers{
    class BookmarkManager{
        const TABLE = "bookmarks";

        function __construct($connection){
            $this->conn= $connection;
        }

        /** 
         * get all bookmarks from user
         */
        function getAll($offset,$limit,$user_id){
            $offset = intval($offset);
            $limit = intval($limit);
            $user_id = intval($user_id);
            $bookmarks = $this->conn->fetchAll("SELECT ".
                "id,url,title,description,".
                " created_at ,".
                "GROUP_CONCAT(tag)".
                "AS tags FROM bookmarks LEFT OUTER JOIN tags ON ".
                "bookmarks.id = tags.bookmark_id WHERE ".
                " user_id = :user_id GROUP BY id ORDER BY created_at DESC ".
                " LIMIT $offset , $limit ", array("user_id"=>$user_id));
            return $bookmarks;
        }
    }
}