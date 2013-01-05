<?php

/**
 * @author M.Paraiso
 */

namespace App\Controller {

    use Silex\Application;
    use Doctrine\DBAL\DBALException;
    use App\DataTransferObjects\Bookmark;

    class BookmarkController extends BaseController {

        protected $_table = "bookmarks";

        const DEL_ERR = "Cant delete Bookmark";

        /**
         * FR : retrouver tout les bookmarks , par offset de 50;
         * @param \Silex\Application $app
         */
        function getAll(Application $app) {
            $user_id = $app["session"]->get("user_id");
            $offset = intval($app['request']->get("offset", 0));
            $limit = 50;
            try {
                $bookmarks = $app["bookmark_manager"]->getAll($offset, $limit, $user_id);
                return $app->json(array("status" => "ok", "bookmarks" => $bookmarks));
            } catch (DBALException $exc) {
                $app["logger"]->err($exc->getMessage());
                return $app->json($this->err(self::DB_ERR));
            }
            return $app->json($this->err(self::REQU_ERR));
        }

        /**
         * trouver par tag
         * @param \App\Controller\Application $app
         */
        function getByTag(Application $app, $tagName) {
            $user_id = $app["session"]->get("user_id");
            try {
                $bookmarks = $app["bookmark_manager"]->getByTag($tagName, $user_id);
                return $app->json(array("status" => "ok", "bookmarks" => $bookmarks));
            } catch (DBALException $e) {
                $app["logger"]->err($e->getMessage());
                return $app->json($this->err(self::DB_ERR));
            }
            return $app->json($this->err(self::REQ_ERR));
        }

        /**
         * trouver par tag,description ou titre
         * @param \Silex\Application $app
         */
        function search(Application $app) {
            $data = array();
            $user_id = $app["session"]->get("user_id");
            $query = $app["request"]->get("query");
            try {
                $bookmarks = $app["bookmark_manager"]->search($query, $user_id);
                return $app->json(array("status" => "ok", "bookmarks" => $bookmarks));
            } catch (DBALException $exc) {
                $app["logger"]->err($exc->getMessage());
                return $app->json($this->err(self::DB_ERR));
            }
            return $app->json($this->err(self::REQ_ERR));
        }

        /**
         * Create a new bookmark
         * @TODO corriger le problème du temps
         */
        function create(Application $app) {
            $bookmark = new Bookmark();
            $user_id = $app["session"]->get("user_id");
            $bookmark->url = $app["request"]->get("url");
            $bookmark->title = $app["request"]->get("title");
            $bookmark->description = $app["request"]->get("description");
            $bookmark->tags = $app['request']->get("tags");
            $bookmark->created_at = $app["current_time"];
            $bookmark->private = $app["request"]->get("private");
            try {
                $result = $app["bookmark_manager"]->create($bookmark, $user_id);
                return $app->json(array("status" => "ok", "bookmark" => $result), 200);
            } catch (DBALException $e) {
                $app['logger']->err($e->getMessage());
            }
            return $app->json(array("status" => "error", "message" => "Cant create bookmark"));
        }

        /**
         * met à jour un bookmark
         * @param \Silex\Application $app
         * @param type $id
         * @return type
         */
        function update(Application $app) {
            $bookmark = new Bookmark();
            /** @var Symfony\Component\HttpFoundation\Request $request */
            $request = $app["request"];
            $bookmark->user_id = intval($app["session"]->get("user_id"));
            $bookmark->id = intval($request->get("id"));
            $bookmark->url = $request->get("url");
            $bookmark->title = $request->get("title");
            $bookmark->description = $request->get("description");
            $bookmark->tags = $request->get("tags");
//            $app["logger"]->info("bookmark = ".json_encode($bookmark));
            try {
                $result = $app["bookmark_manager"]->update($bookmark);
                $app["logger"]->info("update result = $result");
                return $app->json(array("status" => "ok","bookmark"=>$bookmark));
            } catch (DBALException $exc) {
                $app["logger"]->err($exc->getMessage());
                return $app->json($this->err(self::DB_ERR));
            }
            return $app->json($this->err(self::REQ_ERR));
        }

        /**
         * efface un bookmark
         * @param \Silex\Application $app
         * @param type $id
         * @return Response
         */
        function delete(Application $app, $id) {
            $user_id = $app["session"]->get("user_id");
            try {
                $rows = $app["bookmark_manager"]->delete($id, $user_id);
                return $app->json(array("status" => "ok", "rows" => $rows));
            } catch (DBALException $e) {
                $app["logger"]->err($e->getMessage());
                return $app->json($this->err(self::DB_ERR));
            }
            return $app->json($this->err(self::DEL_ERR));
        }

    }

}