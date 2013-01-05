<?php

namespace App\DataAccessLayer {
    
    use App\DataTransferObjects\Bookmark;
    
    interface IBookmarkProvider {

        public function create(Bookmark $bookmark, $user_id);
       
        public function update(Bookmark $bookmark);

        public function delete($id, $user_id);

        public function search($query, $user_id);

        public function getByTag($tagName, $user_id);

        function getAll($offset, $limit, $user_id);
    }

}