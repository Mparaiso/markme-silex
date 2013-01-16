<?php

/**
 * @author M.Paraiso
 */

namespace App\Controller {

    use Symfony\Component\HttpFoundation\Response;
use Silex\Application;
use Doctrine\DBAL\DBALException;
use App\DataTransferObjects\Bookmark;

    class BookmarkController extends BaseController {

        protected $_table = "bookmarks";

        const DEL_ERR = "Cant delete Bookmark";

        function count(Application $app) {
            $user_id = $app["session"]->get("user_id");
            $bookmarkManager = $app["bookmark_manager"];
            /* @var  $bookmarkManager \App\BusinessLogicLayer\BookmarkManager */
            try {
                $count = $bookmarkManager->count($user_id);
                return $app->json(array("status" => "ok", "count" => $count));
            } catch (DBALException $exc) {
                $app["logger"]->error($exc->getMessage());
                return $app->json($this->err(self::DB_ERR));
            }
            return $app->json($this->err(self::REQ_ERR));
        }

        /**
         * FR : retrouver tout les bookmarks , par limite de 50 ( par défaut );
         * @param \Silex\Application $app
         */
        function getAll(Application $app) {
            $user_id = $app["session"]->get("user_id");
            $limit = intval($app["request"]->query->get("limit", 50));
            $offset = intval($app['request']->query->get("offset", 0)) * $limit;
            try {
                $bookmarks = $app["bookmark_manager"]->getAll($offset, $limit, $user_id);
                $count = $app["bookmark_manager"]->count($user_id);
                return $app->json(array("status" => "ok", "bookmarks" => $bookmarks, "count" => intval($count)));
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
                $count = count($bookmarks);
                return $app->json(array("status" => "ok", "bookmarks" => $bookmarks, "count" => $count));
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
                $count = count($bookmarks);
                return $app->json(array("status" => "ok", "bookmarks" => $bookmarks, "count" => $count));
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
            try {
                $result = $app["bookmark_manager"]->update($bookmark);
                return $app->json(array("status" => "ok", "bookmark" => $bookmark));
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

        /**
         * FR : exporte les bookmarks vers un fichier html
         */
        function export(Application $app) {
            $user_id = $app["session"]->get("user_id");
            $html = $app["bookmark_manager"]->export($user_id);
            $response = new Response($html, 200, array("content-disposition" => "attachment; filename=bookmarks.html"));
            return $response;
        }

        /**
         * FR : import les bookmarks à partir d'un fichier HTML
         * @param \Silex\Application $app
         */
        function import(Application $app) {
            $user_id = $app["session"]->get("user_id");
            $file = $app["request"]->files->get("imported_file");
            // try to get file content
            if ($file) {
                try {
                    $app["logger"]->info("updloaded file info" . print_r($file, true));
                    $filename = md5(time());
                    $oldFileName = $file->getBasename();
                    $newFile = $file->move($app["upload_dir"], $filename);
                    $html = file_get_contents($app["upload_dir"] . "/" . $filename);
                    unlink($newFile->getRealPath());
                } catch (Exception $e) {
                    $app["logger"]->err($e->getMessage());
                    $app["session"]->getFlashBag()
                            ->add("error", "Error uploading file $oldFileName , no bookmark imported");
                }
                // try to import bookmarks from html content
                try {
                    $bookmarks = $app["bookmark_manager"]->import($html, $user_id);
                    $app["session"]->getFlashBag()
                            ->add("notice", count($bookmarks) . " imported successfully");
                } catch (DBALException $e) {
                    $app["logger"]->err($e->getMessage());
                    $app["session"]->getFlashBag()
                            ->add("error", "Error importing bookmarks , no bookmark imported");
                }
            } else {
                $app["session"]->getFlashBag()
                        ->add("error", "Error importing bookmarks , no bookmark imported");
            }
            return $app->redirect(
                            $app["url_generator"]->generate("application"), 302
            );
        }

    }

}