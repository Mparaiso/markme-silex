<?php

namespace App\DataTransferObjects{
    /**
     * FR : représente un marque page
     */
    class Bookmark{
        public $id;
        public $user_id;
        public $description;
        public $url;
        public $title;
        public $created_at;
        public $tags;
        public $private;
    }
}
