<?php
/**
 * @copyrights 2014 mparaiso <mparaiso@online.fr>
 * @All rights reserved
 */
namespace MarkMe\Controller;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;

class Tag {

    /**
     * get all tags for a user
     * @param \Silex\Application $app
     * @param \Symfony\Component\HttpFoundation\Request $req
     * @return string
     */
    function index(Application $app, Request $req) {
        $user = $app->security->getToken()->getUser();
        $tags = $app->bookmarks->getAllTags($user);
        return $app->serializer->serialize(array("status" => "ok", "tags" => $tags), 'json');
    }

    /**
     * search for tags
     * @param \Silex\Application $app
     * @param \Symfony\Component\HttpFoundation\Request $req
     * @return string
     */
    function search(Application $app, Request $req) {
        $user = $app->security->getToken()->getUser();
        $q = $req->query->get("q");
        $limit = $req->query->get("limit", 10);
        $tags = $app->bookmarks->searchTags($q, $user, $limit);
        return $app->serializer->serialize(array("status" => "ok", "tags" => $tags), 'json');
    }

}
