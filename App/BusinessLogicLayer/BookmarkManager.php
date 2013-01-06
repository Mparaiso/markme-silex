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

        function toHTML(array $bookmarks){
            $time = time();
            $result = array_map(function($bookmark)use($time){
                /*return "<DT><A href='$bookmark->url' ".
                "title='$bookmark->title' alt='$bookmark->description'>".
                "$bookmark->title</A><DD>$bookmark->description";*/
                return <<<EOF

            <DT><A HREF="$bookmark->url" ADD_DATE="$time" LAST_VISIT="$time" LAST_MODIFIED="$time">$bookmark->title</A>

EOF;
            },$bookmarks);
            return implode(" ", $result);
        }

        function fromHTML($html){
            $regexp = "/<a([^>]*)>([^<]*)<\/a>/gmi";
            $list = array();
        }

        function toValidHtml(array $bookmarks){
            $time= time();
            $top = <<<EOF
<!DOCTYPE NETSCAPE-Bookmark-file-1>
    <!--This is an automatically generated file.
    It will be read and overwritten.
    Do Not Edit! -->
    <META HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=UTF-8">
    <TITLE>Bookmarks</TITLE>
    <H1>Bookmarks Menu</H1>
    
    <DL>
    <DT><H3 FOLDED ADD_DATE="$time">Mark.me</H3>
    <DL><p>
EOF;
        $bottom="</DL><p></DL>";
            return $top.$this->toHTML($bookmarks).$bottom;
        }
    }
}