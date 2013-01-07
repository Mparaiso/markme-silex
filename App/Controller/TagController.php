<?php

namespace App\Controller{

    use Silex\Application;
use Doctrine\DBAL\DBALException;

    class TagController extends BaseController{

        /**
         * obtient la liste des $tags d'un utilisateur
         */
        function get(Application $app){
            $user_id = $app["session"]->get("user_id");
            try{
                $tags = $app["tag_manager"]->get($user_id);
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
            try{
                $tags = $app["tag_manager"]->search($tag,$user_id);
                return $app->json(array("status"=>"ok", "tags"=>$tags), 200);
            } catch (DBALException $e){
                $app["logger"]->err($e->getMessage());
                return $app->json($this->err(self::DB_ERR));
            }
            return $app->json($this->err(self::REQ_ERR));
        }

    }

}