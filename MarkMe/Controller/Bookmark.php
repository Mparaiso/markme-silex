<?php

/**
 * @author M.Paraiso
 */

namespace MarkMe\Controller;

use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Silex\Application;
use \Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class Bookmark {

    function countByUser(Application $app) {
        /* @var \MarkMe\App $app */
        return $app->serializer->serialize(array(
                    'count' => $app->bookmarks->count($app->security->getToken())
                        ), 'json');
    }

    function findByUser(Application $app) {
        /* @var $app \MarkMe\App  */
        $user = $app->security->getToken()->getUser();
        $limit = intval($app["request"]->query->get("limit", 50));
        $offset = intval($app['request']->query->get("offset", 0)) * $limit;
        $bookmarks = $app->bookmarks->findBy(array('user' => $user), array('createdAt' => 'DESC'), $limit, $offset);
        return $app->serializer->serialize(array(
                    'status' => 200,
                    'bookmarks' => $bookmarks,
                    'limit' => $limit,
                    'offset' => $offset
                        ), 'json');
    }

    function findByUserAndByTag(Application $app, $tagName) {
        /* @var \MarkMe\App $app */
        $user = $app->security->getToken();
        $bookmarks = $app->tags->findWhereUserHasBookmark($user, $tagName);
        return $app->serializer->serialize(array('status' => 200, 'bookmarks' => $bookmarks), 'json');
    }

    /**
     * trouver par tag,description ou titre
     * @param \Silex\Application $app
     */
    function searchWithinUserBookmarks(Request $request, Application $app) {
        /* @var \MarkMe\App $app */
        $user = $app->security->getToken();
        $bookmarks = $app->bookmarks->search($request->get('query', ""), $user);
        return $app->serializer->serialize(array('status' => 200, 'bookmarks' => $bookmarks), 'json');
    }

    function create(Application $app) {
        /* @var \MarkMe\App $app */
        $bookmark = $app->serializer->deserialize(file_get_contents("php://input"), '\MarkMe\Entity\Bookmark', 'json');
        /* @var \MarkMe\Entity\Bookmark $bookmark */
        $bookmark->setUser($app->security->getToken());
        $bookmark->setPrivate(true);
        $app->bookmarks->create($bookmark);
        return $app->serializer->serialize(array('status' => 200, 'bookmark' => $bookmark), 'json');
    }

    function read(Application $app, $id) {
        $user = $app->security->getToken()->getUser();
        $bookmark = $app->bookmarks->findOneBy(array('id' => $id, 'user' => $user)) OR $app->abort(404, 'bookmark with id $id not found');
        return $app->serializer->serialize(array('status' => 200, 'bookmark' => $bookmark), 'json');
    }

    function update(Application $app) {
        /* @var \MarkMe\App $app */
        $user = $app->security->getToken()->getUser();
        /* @var \MarkMe\Entity\Bookmark $bookmark */
        $bookmark = $app->bookmarks->findOneBy(array('id' => $app->request->get('id'), 'user' => $user));
        if ($bookmark == NULL) {
            return new Response($app->serializer->serialize(array('status' => 404, 'message' => 'not found'), 'json'), 404);
        }
        $bookmark->setTitle($app->request->get('title'));
        $bookmark->setDescription($app->request->get('description'));
        $bookmark->setUrl($app->request->get('url'));
        foreach ($app->request->get('tags') as $tag) {
            $bookmark->addTag($app->tags->fromName($tag, false));
        }
        $app->bookmarks->update($bookmark);
        return $app->serializer->serialize(array('status' => 200, 'bookmark' => $bookmark), 'json');
    }

    function delete(Application $app, $id) {
        /* @var \MarkMe\App $app */
        $user = $app->security->getToken()->getUser();
        $bookmark = $app->bookmarks->findOneBy(array('id' => $id, 'user' => $user));
        if (NULL == $bookmark) {
            return new Response($app->serializer->serialize(array('status' => 404, 'message' => 'bookmark not found'), 'json'), 404);
        }
        $app->bookmarks->delete($bookmark);
        return $app->serializer->serialize(array('status' => 200), 'json');
    }

    function export(Application $app) {
        throw new \Exception("not implemented yet");
//
//            $user_id = $app["session"]->get("user_id");
//            $html = $app["bookmark_manager"]->export($user_id);
//            $response = new Response($html, 200, array("content-disposition" => "attachment; filename=bookmarks.html"));
//            return $response;
    }

    /**
     * FR : import les bookmarks à partir d'un fichier HTML
     * @param \Silex\Application $app
     */
    function import(Application $app) {
        throw new \Exception('not implemented yet');
//            $user_id = $app["session"]->get("user_id");
//            $file = $app["request"]->files->get("imported_file");
//            // try to get file content
//            if ($file) {
//                try {
//                    $app["logger"]->info("updloaded file info" . print_r($file, true));
//                    $filename = md5(time());
//                    $oldFileName = $file->getBasename();
//                    $newFile = $file->move($app["upload_dir"], $filename);
//                    $html = file_get_contents($app["upload_dir"] . "/" . $filename);
//                    unlink($newFile->getRealPath());
//                } catch (Exception $e) {
//                    $app["logger"]->err($e->getMessage());
//                    $app["session"]->getFlashBag()
//                        ->add("error", "Error uploading file $oldFileName , no bookmark imported");
//                }
//                // try to import bookmarks from html content
//                try {
//                    $bookmarks = $app["bookmark_manager"]->import($html, $user_id);
//                    $app["session"]->getFlashBag()
//                        ->add("notice", count($bookmarks) . " imported successfully");
//                } catch (DBALException $e) {
//                    $app["logger"]->err($e->getMessage());
//                    $app["session"]->getFlashBag()
//                        ->add("error", "Error importing bookmarks , no bookmark imported");
//                }
//            } else {
//                $app["session"]->getFlashBag()
//                    ->add("error", "Error importing bookmarks , no bookmark imported");
//            }
//            return $app->redirect(
//                $app["url_generator"]->generate("application"), 302
//            );
    }

}
