<?php

namespace App\BusinessLogicLayer{
    
    use App\DataAccessLayer\IBookmarkProvider;
    use App\DataTransferObjects\Bookmark;
    
    class BookmarkManager{
        
        protected $bookmarkProvider;

        function __construct(IBookmarkProvider $bookmarkProvider){
            $this->bookmarkProvider = $bookmarkProvider;
        }
        
        function getAll($offset,$limit,$user_id){
            return $this->bookmarkProvider->getAll($offset, $limit, $user_id);
        }
        
        function getByTag($tagName,$user_id){
            return $this->bookmarkProvider->getByTag($tagName,$user_id);
        }
        
        function search($query,$user_id){
            return $this->bookmarkProvider->search($query,$user_id);
        }
        
        function delete($id,$user_id){
            return $this->bookmarkProvider->delete($id,$user_id);
        }
        
        function create(Bookmark $bookmark,$user_id){
            return $this->bookmarkProvider->create($bookmark,$user_id);
        }
        
        function update(Bookmark $bookmark) {
            return $this->bookmarkProvider->update($bookmark);
        }
    }
}