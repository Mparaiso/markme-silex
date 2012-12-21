<?php

namespace App\Controller;

use Silex\WebTestCase;

/**
 * @author M.PAraiso
 */
class BookmarkControllerTest extends WebTestCase {

    function setUp() {
        parent::setUp();
        $client = $this->createClient();
        $client->request("POST", "/json/register", array(), array(), array("HTTP_Content-Type" => "application/json"), json_encode(
                        array("username" => "superman", "email" => "superman@yahoo.fr", "password" => "password")
                )
        );
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
                array(
                    "username" => "superman", "email" => "superman@yahoo.fr", "password" => "password"
                ),
                array("HTTP_Content-Type" => "application/json"),
            ),
        );
    }

    /**
     * @covers App\Controller\BookmarkController::create
     * @dataProvider provider
     */
    public function testCreate($bookmarks, $user, $headers) {
        // Remove the following lines when you implement this test.
        $client = $this->createClient();

        $client->request("POST", "/json/bookmark", array(), array(), $headers, json_encode($bookmarks[0]));
        $client->request("POST", "/json/bookmark", array(), array(), $headers, json_encode($bookmarks[1]));
        $response = $client->getResponse();
        $json = json_decode($response->getContent(), true);
        $this->assertEquals($json['status'], "ok");
        $this->assertEquals($json['bookmark']["tags"], $bookmarks[1]['tags']);
        $this->assertEquals($json["bookmark"]["user_id"],1);
        /** @note @doctrine retrouver une valeur unique d'une colonne avec count par exemple * */
        $length = $this->app["db"]->fetchColumn("SELECT COUNT(*) from bookmarks");
        $this->assertEquals($length, 2);
    }

    /**
     * @covers App\Controller\BookmarkController::delete
     * @dataProvider provider
     */
    public function testDelete($bookmarks,$user,$headers) {
        $client = $this->createClient();
        $client->request("POST", "/json/bookmark", array(), array(), $headers, json_encode($bookmarks[0]));
        $client->request("POST", "/json/bookmark", array(), array(), $headers, json_encode($bookmarks[1]));
        $bookmarkId = json_decode($client->getResponse()->getContent())->bookmark->user_id;
        $client->request("DELETE","/json/bookmark/$bookmarkId",array(),array(),$headers);
        $response = $client->getResponse()->getContent();
        $this->assertEquals($response,  json_encode(array("status" => "ok")));
        $rows = $this->app["db"]->fetchColumn("SELECT COUNT(*) FROM bookmarks");
        $this->assertEquals(1,$rows);
    }

    public function createApplication() {
        return createApplication();
    }

}

?>
