<?php

namespace App\Controller {

    use Silex\Application;
    use Doctrine\DBAL\DBALException;

    class TagController {

        /**
         * obtient la liste des $tags d'un utilisateur
         * */
        function get(Application $app) {
            /**
             * @var  \Doctrine\DBAL\Connection connexion à la base de données
             */
            $db = $app["db"];
            $user_id = $app["session"]->get("id");

            try {
                $tags = $db->fetchAll("SELECT $tag, COUNT(*) AS `count` FROM $tags " .
                    "INNER JOIN bookmarks ON bookmarks.$id = $tags.bookmark_" .
                    "id WHERE $user_id = :$id GROUP BY $tag " .
                    "ORDER BY COUNT(*) DESC", array("id" => $user_id));
                return $app->json(array_merge(array("status"=>"ok"),$tags),200);
                
            }catch (DBALException $e) {
                $app["monolog"]->addError($e->getMessage());
            }
            return $app->json(array("status"=>"error","message"=>"cant get $tags"));

        }

        /**
         * Autocomplete for tagging, returns $tags matching input
         */
        function autocomplete(Application $app,$tag){
            $user_id = $app["session"]->get("user_id");
            try {
                $tags = $app["db"]->fetchAll("SELECT DISTINCT $tag FROM $tags INNER JOIN bookmarks ON bookmarks.$id = $tags.bookmark_id WHERE $user_id = :$id AND $tag LIKE :$tag ",array("id"=>$user_id,"tag"=>$tag));
                return $app->json(array_merge(array("status"=>"ok"),$tags),200);
                
            } catch (DBALException $e) {
                $app["monolog"]->addError($e->getMessage());
            }
            return $app->json(array("status"=>"error","message"=>"Cant get $tags"),200);

        }

    }

}