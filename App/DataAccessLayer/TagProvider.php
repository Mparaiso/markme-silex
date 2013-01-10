<?php

namespace  App\DataAccessLayer {

    use App\DataTransferObjects\Tag;
    use Doctrine\DBAL\Connection;

    class TagProvider implements ITagProvider{

        /**
         * @var Doctrine\DBAL\Connection $connection
         */
        protected $connection;

        function __construct(Connection $connection){
            $this->connection = $connection;
        }

        function get($user_id){
            $tags = $this->connection->fetchAll("SELECT tag, COUNT(*) AS count FROM tags ".
                "INNER JOIN bookmarks ON bookmarks.id = tags.bookmark_id ".
                " WHERE user_id = :id GROUP BY tag ".
                " ORDER BY COUNT(*) DESC", array("id"=>$user_id));
            return $this->recordArrayToTagArray($tags);
        }

        function search($expression,$limit,$user_id){
            $tag = "%".$expression."%";
            $limit = intval($limit);
            $tags = $this->connection->fetchAll("SELECT DISTINCT tag FROM ".
                "tags INNER JOIN bookmarks ".
                "ON bookmarks.id = tags.bookmark_id WHERE ".
                " user_id = :id AND tag LIKE :tag LIMIT $limit ", 
                array("id"=>$user_id, "tag"=>$tag));
            return $this->recordArrayToTagArray($tags);
        }

        protected function recordToTag($record){
            $tag = new Tag();
            $tag->bookmark_id = $record["bookmark_id"];
            $tag->tag = $record["tag"];
            $tag->count = $record["count"];
            return $tag;
        }

        protected function recordArrayToTagArray($records){
            return array_map(array($this,"recordToTag"),$records);
        }
    }
}