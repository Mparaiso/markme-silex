<?php

namespace App\Controller {

    use Silex\Application;
use Doctrine\DBAL\DBALException;

    class BookmarkController {

        protected $_table = "bookmarks";

        /**
         * Create a new bookmark
         */
        function create(Application $app) {
            $data = array();
            $data['user_id'] = $app["session"]->get("user_id");
            $data['url'] = $app["request"]->get("url");
            $data['title'] = $app["request"]->get("title");
            $data['description'] = $app["request"]->get("description");
            $tags = $app['request']->get("tags");
            $data["created_at"] = time();
            $data["private"] = $app["request"]->get("private");
            try {
                $insertedId = $app["db"]->insert($this->_table, $data);
                foreach ($tags as $tag):
                    $app['db']->insert("tags", array("bookmark_id" => $insertedId, "tag" => $tag));
                endforeach;
                return $app->json(array("status" => "ok", "bookmark" => array_merge(array("tags" => $tags), $data)), 200);
            } catch (DBALException $e) {
                $app['monolog']->addError($e->getMessage());
            }
            return $app->json(array("status" => "error", "message" => "Cant create bookmark"));
        }

        /**
         * Delete a bookmark
         */
        function delete(Application $app, $id) {
            $data["user_id"] = $app["session"]->get("user_id");
            $data["id"] = $id;
            try {
                $rows = $app["db"]->delete($this->_table, $data);
                return $app->json(array("status" => "ok"));
            } catch (DBALException $e) {
                $app["monolog"]->addError($e->getMessage());
            }
            return $app->json(array("status" => "error", "message" => "Cant delete Bookmark"));
        }

    }

}