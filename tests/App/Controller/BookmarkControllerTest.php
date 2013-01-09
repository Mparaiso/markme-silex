<?php

namespace App\Controller;

use Silex\WebTestCase;
use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * @author M.PAraiso
 */
class BookmarkControllerTest extends WebTestCase {

    function setUp() {
        parent::setUp();
        $client = $this->createClient();
        $client->request("POST", "/json/register", array(), array(), array("HTTP_Content-Type" => "application/json"), json_encode(
                        array("username" => "super.guy", "email" => "super.guy@yahoo.fr", "password" => "password")
                )
        );
    }

    function tearDown() {
        parent::tearDown();
        $app = $this->createApplication();
        $app["db"]->executeUpdate("DELETE FROM users");
    }

    function provider() {
        return array(
            array(
                array(
                    array("title" => "yahoo.com", "description" =>
                        "yahoo's website", "url" => "http://yahoo.fr", "private" => 1,
                        "tags" => array("yahoo", "advertising", "life style")),
                    array("title" => "google.com", "description" =>
                        "google search engine", "url" => "http://google.com", "private" => 0,
                        "tags" => array("google", "search", "popular", "advertising")),
                ),
                array("HTTP_Content-Type" => "application/json"),
            ),
        );
    }

    /**
     * @covers App\Controller\BookmarkController::create
     * @dataProvider provider
     */
    public function testCreate($bookmarks, $headers) {
        # l'utilisateur crée 2 bookmarks
        $client = $this->createClient();
        $client->request("POST", "/json/bookmark", array(), array(), $headers, json_encode($bookmarks[0]));
        $client->request("POST", "/json/bookmark", array(), array(), $headers, json_encode($bookmarks[1]));
        $response = $client->getResponse();
        $json = json_decode($response->getContent(), true);
        $this->assertEquals($json['status'], "ok");
        $this->assertEquals($json['bookmark']["tags"], $bookmarks[1]['tags']);
        $this->assertEquals($json["bookmark"]["user_id"], 1);
        # @note @doctrine retrouver une valeur unique d'une colonne avec count par exemple 
        $length = $this->app["db"]->fetchColumn("SELECT COUNT(*) from bookmarks");
        $this->assertEquals($length, 2);
        $googleBookMark = $this->app["db"]->fetchAssoc("SELECT * FROM " .
                "bookmarks where title= :title", array("title" => $bookmarks[1]["title"]));
        $tags = $this->app["db"]->fetchAll(" SELECT * FROM tags " .
                "where bookmark_id= :bookmark_id", array("bookmark_id" => $googleBookMark["id"]));
        $this->assertEquals(count($bookmarks[1][tags]), count($tags));
        // un utilisateur non connecté tente de crée des bookmarks
        $this->app["session"]->invalidate();
        $client->restart();
        $this->setExpectedException("Symfony\Component\HttpKernel\Exception\HttpException");
        $client->request("POST", "/json/bookmark", array(), array(), $headers, json_encode($bookmarks[0]));
    }

    /**
     * @covers App\Controller\BookmarkController::delete
     * @dataProvider provider
     */
    public function testDelete($bookmarks, $headers) {
        # l'utilisateur crée 2 bookmarks puis en efface 1
        $client = $this->createClient();
        $client->request("POST", "/json/bookmark", array(), array(), $headers, json_encode($bookmarks[0]));
        $client->request("POST", "/json/bookmark", array(), array(), $headers, json_encode($bookmarks[1]));
        $bookmarkId = json_decode($client->getResponse()->getContent())->bookmark->user_id;
        $client->request("DELETE", "/json/bookmark/$bookmarkId", array(), array(), $headers);
        $response = $client->getResponse()->getContent();
        $this->assertEquals($response, json_encode(array("status" => "ok", "rows" => 1)));
        $rows = $this->app["db"]->fetchColumn("SELECT COUNT(*) FROM bookmarks");
        $this->assertEquals(1, $rows);
    }

    /**
     * Test l'obtention des bookmarks
     * @dataProvider provider
     * @param type $bookmarks
     * @param type $user
     * @param type $headers
     */
    function testGetAll($bookmarks, $headers) {
        // un utilisateur obtient des bookmarks crées
        $client = $this->createClient();
        $client->request("POST", "/json/bookmark", array(), array(), $headers, json_encode($bookmarks[0]));
        $client->request("POST", "/json/bookmark", array(), array(), $headers, json_encode($bookmarks[1]));
        $client->request("GET", "/json/bookmark", array(), array(), $headers);
        $response = $client->getResponse();
        $_bookmarks = json_decode($response->getContent())->bookmarks;
        $this->assertNotNull($_bookmarks);
        $this->assertEquals(2, count($_bookmarks));
        //print_r($_bookmarks);
        $this->assertEquals("yahoo.com", $_bookmarks[0]->title);
        // un utilisateur sans bookmarks n'obtient aucun bookmark
    }

    /**
     * @dataProvider provider
     * teste une recherche de bookmarks
     * @param type $bookmarks
     * @param type $user
     * @param type $headers
     */
    function testSearch($bookmarks, $headers) {
        $client = $this->createClient();
        $client->request("POST", "/json/bookmark", array(), array(), $headers, json_encode($bookmarks[0]));
        $client->request("POST", "/json/bookmark", array(), array(), $headers, json_encode($bookmarks[1]));
        $client->request("GET", "/json/bookmark/search", array(), array(), $headers, json_encode(array("query" => "engine")));
        $response = $client->getResponse();
        $json = json_decode($response->getContent());
        //print_r($json);
        $this->assertEquals(1, count($json->bookmarks));
        $this->assertTrue(preg_match("/engine/", $json->bookmarks[0]->description) >= 1);
    }

    /**
     * test l'obtention de bookmarks par tag
     * @dataProvider provider
     * @param type $bookmarks
     * @param type $user
     * @param type $headers
     */
    function testGetByTag($bookmarks, $headers) {
        $client = $this->createClient();
        $client->request("POST", "/json/bookmark", array(), array(), $headers, json_encode($bookmarks[0]));
        $client->request("POST", "/json/bookmark", array(), array(), $headers, json_encode($bookmarks[1]));
        $client->request("GET", "/json/bookmark/tag/advertising", array(), array());
        $response = $client->getResponse();
        $content = json_decode($response->getContent(), true);
        $this->assertEquals(2, count($content['bookmarks']));
        $this->assertEquals($bookmarks[0]["title"], $content["bookmarks"][0]["title"]);
        print_r($content->bookmarks);
    }

    public function createApplication() {
        return createApplication();
    }

    /**
     * @covers App\Controller\BookmarkController::export
     * @dataProvider provider
     */
    public function testExport($bookmarks, $headers) {
        # l'utilisateur crée 2 bookmarks
        $client = $this->createClient();
        $client->request("POST", "/json/bookmark", array(), array(), $headers, json_encode($bookmarks[0]));
        $client->request("POST", "/json/bookmark", array(), array(), $headers, json_encode($bookmarks[1]));
        $crawler = $client->request("POST", "/json/bookmark/export");
        // @var Symfony\Component\HttpFoundation\Response $response 
        $response = $client->getResponse();
        $status = $response->getStatusCode();
        $this->assertEquals(200, $status);
        $this->assertTrue($response->isOk());
        $this->assertCount(2, $crawler->filter("a"));
    }

    /**
     * FR : teste la méthode count
     * @dataProvider provider
     * @covers App\Controller\BookmarkController::count
     * @param array $bookmarks
     * @param array $headers
     */
    public function testCount($bookmarks, $headers) {
        // le client crée 2 bookmarks , requiert le nombre de bookmark pour l'utilisateur courant
        $client = $this->createClient();
        $client->request("POST", "/json/bookmark", array(), array(), $headers, json_encode($bookmarks[0]));
        $client->request("POST", "/json/bookmark", array(), array(), $headers, json_encode($bookmarks[1]));
        $client->request("POST", "/json/bookmark/count");
        $response = $client->getResponse();
        $this->assertEquals(200, $response->getStatusCode());
        $json = json_decode($response->getContent());
        $this->assertEquals(count($bookmarks), $json->count);
    }

}

?>
