<?php

/**
 * @author M.Paraiso
 */

namespace App\Controller{

    use Silex\Application;
use Doctrine\DBAL\DBALException;

    class BookmarkController extends BaseController{

        protected $_table = "bookmarks";

        /**
         * @todo regler le problème de limit
         * retrouver tout les bookmarks , par offset de 50;
         * @param \Silex\Application $app
         */
        function getAll(Application $app){
            $data = array();
            $data['user_id'] = $app["session"]->get("user_id");
            $offset =intval($app['request']->get("offset", 0));
            $limit = 50;
            try{
                $bookmarks = $app["db"]->fetchAll("SELECT ".
                    "id,url,title,description,".
                    " created_at ,".
                    "GROUP_CONCAT(tag,',')".
                    "AS tags FROM bookmarks LEFT OUTER JOIN tags ON ".
                    "bookmarks.id = tags.bookmark_id WHERE ".
                    " user_id = :user_id GROUP BY id ORDER BY created_at DESC ".
                    " LIMIT $offset , $limit ", $data);
                return $app->json(array("status"=>"ok", "bookmarks"=>$bookmarks));
            } catch (DBALException $exc){
                $app["logger"]->err($exc->getMessage());
                return $app->json($this->err(self::DB_ERR));
            }

            return $app->json($this->err(self::REQU_ERR));
        }

        /**
         * trouver par tag
         * @param \App\Controller\Application $app
         */
        function getByTag(Application $app,$tagName){
            $data = array();
            $data['user_id'] = $app["session"]->get("user_id");
            $data['tag'] = $tagName;
            if ($data["tag"]):
                try{
                    $query = "SELECT id,title,description,url,".
                        // "date(created_at,'unixepoch') as timestamp,". doesnt work with mysql
                        " created_at,".
                        "GROUP_CONCAT(tag) AS tags FROM bookmarks".
                        " LEFT OUTER JOIN tags ".
                        "ON bookmarks.id = tags.bookmark_id WHERE ".
                        " user_id = :user_id ".
                        " AND id IN (SELECT bookmark_id from ".
                        " tags WHERE tag = :tag ) GROUP BY id ".
                        "ORDER BY created_at DESC ";
                    $bookmarks = $app["db"]->fetchAll($query, $data);
                    return $app->json(array("status"=>"ok", "bookmarks"=>$bookmarks));
                } catch (DBALException $e){
                    $app["logger"]->err($e->getMessage());
                    return $app->json($this->err(self::DB_ERR));
                }
            else:
                return $app->json($this->err(self::REQ_ERR));
            endif;
        }

        /**
         * trouver par tag,description ou titre
         * @param \Silex\Application $app
         */
        function search(Application $app){
            $data = array();
            $data['user_id'] = $app["session"]->get("user_id");
            $data["query"] = "%".$app["request"]->get("query")."%";
            if ($data["query"] && $data['user_id']):
                try{
                    $query = "SELECT id,url,title, description,".
                        // " date(created_at,'unixepoch') as timestamp, ". doesnt work with mysql
                        " created_at, ".
                        "GROUP_CONCAT(DISTINCT tag) as tags ".
                        "FROM bookmarks LEFT OUTER JOIN tags ON "
                        ." bookmarks.id = tags.bookmark_id WHERE user_id = :user_id "
                        ." AND (( title LIKE :query OR description LIKE :query ".
                        "OR url LIKE :query ) OR id IN ( SELECT bookmark_id ".
                        "FROM tags WHERE tag like :query )) GROUP BY id ORDER BY ".
                        "created_at DESC ";
                    $bookmarks = $app["db"]->fetchAll($query, $data);
                    return $app->json(array("status"=>"ok", "bookmarks"=>$bookmarks));
                } catch (DBALException $exc){
                    $app["logger"]->err($exc->getMessage());
                    return $app->json($this->err(self::DB_ERR));
                }
            else:
                return $app->json($this->err(self::REQ_ERR));
            endif;
        }

        /**
         * Create a new bookmark
         * @TODO corriger le problème du temps
         */
        function create(Application $app){
            $data = array();
            $data['user_id'] = $app["session"]->get("user_id");
            $data['url'] = $app["request"]->get("url");
            $data['title'] = $app["request"]->get("title");
            $data['description'] = $app["request"]->get("description");
            $tags = $app['request']->get("tags");
            $data["created_at"] = $app["current_time"];
            $data["private"] = $app["request"]->get("private");
            try{
                $app["db"]->insert($this->_table, $data);
                $lastInsertedId = intval($app["db"]->lastInsertId());
                foreach ($tags as $tag):
                    $app['db']->insert("tags", array("bookmark_id"=>$lastInsertedId, "tag"=>$tag));
                endforeach;
                return $app->json(array("status"=>"ok", "bookmark"=>array_merge(array("tags"=>$tags), $data)), 200);
            } catch (DBALException $e){
                $app['logger']->err($e->getMessage());
            }
            return $app->json(array("status"=>"error", "message"=>"Cant create bookmark"));
        }

        /**
         * met à jour un bookmark
         * @param \Silex\Application $app
         * @param type $id
         * @return type
         */
        function update(Application $app, $id){
            $bookmark = array();
            $reqData = $app["request"]->get("bookmark");
            $user_id = $app["session"]->get("user_id");
            $id = $reqData["id"];
            $bookmark["url"] = $reqData["url"];
            $bookmark["title"] = $reqData["title"];
            $bookmark["description"] = $reqData["desrcription"];
            $bookmark['private'] = $reqData['private'];
            $tags = array_map("trim", explode(",", $reqData["tags"]));
            if (isset($bookmark)):
                try{
                    $app["db"]->update($this->_table, $bookmark, array("user_id"=>$user_id, "id"=>$id));
                    $app["db"]->delete("tags", array("bookmark_id"=>$id));
                    array_walk($tags, function($el){
                            $app["db"]->insert("tags", array("bookmark_id"=>$id, "tag"=>$el));
                        });
                    return $app->json(array("status"=>"ok"));
                } catch (DBALException $exc){
                    $app["logger"]->err($exc->getMessage());
                    return $app->json($this->err(self::DB_ERR));
                }
            endif;
            return $app->json($this->err(self::REQ_ERR));
        }

        /**
         * efface un bookmark
         * @param \Silex\Application $app
         * @param type $id
         * @return type
         */
        function delete(Application $app, $id){
            $data["user_id"] = $app["session"]->get("user_id");
            $data["id"] = $id;
            try{
                $app["db"]->executeQuery('PRAGMA foreign_keys = true;');
                $rows = $app["db"]->delete($this->_table, $data);
                return $app->json(array("status"=>"ok"));
            } catch (DBALException $e){
                $app["logger"]->err($e->getMessage());
            }
            return $app->json(array("status"=>"error", "message"=>"Cant delete Bookmark"));
        }

    }

}