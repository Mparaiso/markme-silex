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

        function export($user_id){
            $bookmarks = $this->getAll(0,10000,$user_id);
            return $this->toValidHtml($bookmarks);
        }

        function import($html,$user_id){
            $self=$this;
            $bookmarks=$this->fromHTML($html,$user_id);
            return array_map(function($bookmark)use($user_id,$self){
              if($bookmark!=null)return $self->create($bookmark,$user_id);
            },$bookmarks);
        }
        
        
        /**
         * 
         * FR : fonction usuelles
         * 
         * 
         * 
         * 
         */
        
        /**
         * 
         * @param array $bookmarks
         * @return string
         */
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


        /**
         * FR : converti une chaine HTML en une liste de bookmarks
         * @param string $html
         * @param integer $user_id
         * @return array
         */
        function fromHTML($html,$user_id){
            $links = $this->splitLinks($html);
            $self= $this;
            $bookmarks = array();
            foreach ($links as $link) {
              $bookmark = $self->bookmarkFromLink($link);
              if($bookmark!=null)array_push($bookmarks, $bookmark);
            }
            return $bookmarks;
        }

        /**
         * converti un lien html en bookmark
         * @param type $link
         * @return \App\DataTransferObjects\Bookmark
         */
        function bookmarkFromLink($link){
            $titleR = "/<a.*>(.*)<\/a>/i";
            preg_match($titleR, $link,$result);
            $title = $result[1];
            $urlR = "/href=[\"\']{1}(http\S*)[\"\']{1}/i";
            preg_match($urlR, $link,$result);
            $url = $result[1];
            if ($url!=null && $title!=null){
              $bookmark = new Bookmark();
              $bookmark->title = $title;
              $bookmark->url = $url;
              $bookmark->description = $title;
              $bookmark->created_at = date('Y-m-d H:i:s', time());
              $bookmark->tags = array();
              return $bookmark;
            }
        }

        /**
         * extrait les liens d'un fichier html dans un tableau de liens
         */
        function splitLinks($html){
              $regexp = "/<a([^>]*)>([^<]*)<\/a>/i";
            preg_match_all($regexp,$html,$links,PREG_SET_ORDER);
            return array_map(function($item){
                return $item[0];
            },$links);
        }
        
        /**
         * @see http://msdn.microsoft.com/en-us/library/aa753582(v=vs.85).aspx
         */
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