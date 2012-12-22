<?php

namespace App\Controller{

    use Silex\Application;
use Doctrine\DBAL\DBALException;

    class TagController extends BaseController{

        /**
         * obtient la liste des $tags d'un utilisateur
         */
        function get(Application $app){
            /**
             * @var  \Doctrine\DBAL\Connection connexion à la base de données
             */
            $db = $app["db"];
            $user_id = $app["session"]->get("user_id");
            try{
                $tags = $db->fetchAll("SELECT tag, COUNT(*) AS count FROM tags ".
                    "INNER JOIN bookmarks ON bookmarks.id = tags.bookmark_id ".
                    " WHERE user_id = :id GROUP BY tag ".
                    " ORDER BY COUNT(*) DESC", array("id"=>$user_id));
                $app["logger"]->info("tags = ".json_encode($tags));
                return $app->json(array("status"=>"ok", "tags"=>$tags), 200);
            } catch (DBALException $e){
                $app["logger"]->err($e->getMessage());
                return $app->json($this->err(self::DB_ERR));
            }
            return $app->json($this->err(self::REQ_ERR));
        }

        /**
         * retourne une liste de tags suivant leurs nom
         */
        function autocomplete(Application $app, $tag){
            $user_id = $app["session"]->get("user_id");
            $tag = "%".$tag."%";
            try{
                $tags = $app["db"]->fetchAll("SELECT DISTINCT tag FROM ".
                    "tags INNER JOIN bookmarks ".
                    "ON bookmarks.id = tags.bookmark_id WHERE ".
                    " user_id = :id AND tag LIKE :tag ", 
                    array("id"=>$user_id, "tag"=>$tag));
                return $app->json(array("status"=>"ok", "tags"=>$tags), 200);
            } catch (DBALException $e){
                $app["logger"]->err($e->getMessage());
                return $app->json($this->err(self::DB_ERR));
            }
            return $app->json($this->err(self::REQ_ERR));
        }

    }

}