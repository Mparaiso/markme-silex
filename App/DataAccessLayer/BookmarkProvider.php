<?php

namespace App\DataAccessLayer {

    use Doctrine\DBAL\Connection;
use App\DataTransferObjects\Bookmark;

    class BookmarkProvider implements IBookmarkProvider {

        /**
         * FR : Connexion à la base de donnée
         * @var Doctrine\DBAL\Connection $connection
         */
        protected $connection;

        /**
         * FR : nom de la table;
         * @var string $tableName 
         */
        protected $tableName;

        function __construct(Connection $connection, $tableName = "bookmarks") {
            $this->connection = $connection;
            $this->tableName = $tableName;
        }

        /**
         * obtient les bookmarks selon l'id utilisateur
         */
        function getAll($offset, $limit, $user_id) {
            $offset = intval($offset);
            $limit = intval($limit);
            $user_id = intval($user_id);
            $bookmarks = $this->connection->fetchAll("SELECT " .
                    "id,url,title,description," .
                    " created_at ," .
                    "GROUP_CONCAT(tag)" .
                    "AS tags FROM bookmarks LEFT OUTER JOIN tags ON " .
                    "bookmarks.id = tags.bookmark_id WHERE " .
                    " user_id = :user_id GROUP BY id ORDER BY created_at DESC " .
                    " LIMIT $offset , $limit ", array("user_id" => $user_id));
            return $this->recordArrayToBookmarkArray($bookmarks);
        }

        function getByTag($tagName, $user_id) {
            $data['user_id'] = $user_id;
            $data['tag'] = $tagName;
            $query = "SELECT id,title,description,url," .
                    " created_at," .
                    "GROUP_CONCAT(tag) AS tags FROM bookmarks" .
                    " LEFT OUTER JOIN tags " .
                    "ON bookmarks.id = tags.bookmark_id WHERE " .
                    " user_id = :user_id " .
                    " AND id IN (SELECT bookmark_id from " .
                    " tags WHERE tag = :tag ) GROUP BY id " .
                    "ORDER BY created_at DESC ";
            $bookmarks = $this->connection->fetchAll($query, $data);
            return $this->recordArrayToBookmarkArray($bookmarks);
        }

        /**
         * FR : convertit une liste d'enregistrements en liste de Bookmarks
         * @param type $array
         * @return array
         */
        protected function recordArrayToBookmarkArray($array) {
            return @array_map(array($this, "recordToBookmark"), $array);
        }

        /**
         * FR : convertit un enregistrement en bookmark
         * @param type $record
         * @return \App\DataTransferObjects\Bookmark
         */
        protected function recordToBookmark($record) {
            $bookmark = new Bookmark();
            $bookmark->id = $record["id"];
            $bookmark->url = $record["url"];
            $bookmark->title = $record["title"];
            $bookmark->description = $record['description'];
            $bookmark->tags = $record["tags"];
            $bookmark->created_at = $record["created_at"];
            $bookmark->user_id = $record["user_id"];
            return $bookmark;
        }

        /**
         * FR : cherche les bookmarks selon une expression
         * @param string $query
         * @param type $user_id
         * @return array un tableau de Bookmarks
         */
        public function search($query, $user_id) {
            $data['user_id'] = $user_id;
            $data["query"] = "%" . $query . "%";
            $query = "SELECT id,url,title, description," .
                    " created_at, " .
                    "GROUP_CONCAT(DISTINCT tag) as tags " .
                    "FROM bookmarks LEFT OUTER JOIN tags ON "
                    . " bookmarks.id = tags.bookmark_id WHERE user_id = :user_id "
                    . " AND (( title LIKE :query OR description LIKE :query " .
                    "OR url LIKE :query ) OR id IN ( SELECT bookmark_id " .
                    "FROM tags WHERE tag like :query )) GROUP BY id ORDER BY " .
                    "created_at DESC ";
            $bookmarks = $this->connection->fetchAll($query, $data);
            return $this->recordArrayToBookmarkArray($bookmarks);
        }

        /**
         * efface un bookmark
         * @param type $id
         * @param type $user_id
         * @return integer le nombre de lignes affectée
         */
        public function delete($id, $user_id) {
            $data["user_id"] = $user_id;
            $data["id"] = $id;
            return $this->connection->delete($this->tableName, $data);
        }

        /**
         * 
         * @param \App\DataTransferObjects\Bookmark $bookmark
         * @param integer $user_id
         * @return \App\DataTransferObjects\Bookmark
         */
        public function create(Bookmark $bookmark, $user_id) {
            $data = array();
            $data["title"] = $bookmark->title;
            $data["description"] = $bookmark->description;
            $data["url"] = $bookmark->url;
            $data["created_at"] = $bookmark->created_at;
            $data["user_id"] = $user_id;
            $tags = $bookmark->tags;
            $this->connection->insert($this->tableName, $data);
            $lastInsertedId = intval($this->connection->lastInsertId());
            $this->connection->close();
            foreach ($tags as $tag):
                $this->connection->insert("tags", array("bookmark_id" => $lastInsertedId, "tag" => $tag));
                $this->connection->close();
            endforeach;
            $bookmark->id = $lastInsertedId;
            $bookmark->user_id = $user_id;
            return $bookmark;
        }

        /**
         * 
         * @param int $id
         * @return Bookmark
         */
        public function getById($id) {
            $id = intval($id);
            $bookmark = $this->connection->fetchAssoc("SELECT id,url," .
                    " description,title,created_at, " .
                    " GROUP_CONCAT(tag) as tags FROM $this->tableName " .
                    "JOIN tags ON tags.bookmark_id = bookmark.id WHERE " .
                    "id = :id ", array("id" => $id));
            return $this->recordToBookmark($bookmark);
        }

        /**
         * @param \App\DataTransferObjects\Bookmark $bookmark
         * @return integer
         */
        public function update(Bookmark $bookmark) {
            $data = array();
            $data["title"] = $bookmark->title;
            $data["description"] = $bookmark->description;
            $data["url"] = $bookmark->url;
            $tags = $bookmark->tags;
            $result = $this->connection->update(
                    $this->tableName, $data, array(
                "user_id" => $bookmark->user_id, "id" => $bookmark->id
                    )
            );
            $this->connection->delete("tags", array("bookmark_id" => $bookmark->id));
            $connection = $this->connection;
            array_walk($tags, function($el)use($connection,$bookmark) {
                        $connection->insert("tags", array("bookmark_id" => $bookmark->id, "tag" => $el));
                    }
            );
            return $result;
        }

    }

}
