<?php

namespace App\Controller{

    use Silex\WebTestCase;
    use App\DataTransferObjects\Tag;

    class TagControllerTest extends WebTestCase{

        /**
         * enregistre et connection un utilisateur
         */
        function setUp(){
            parent::setUp();
            $client = $this->createClient();
            $client->request("POST", "/json/register", array(), array(), array("HTTP_Content-Type"=>"application/json"), json_encode(array("username"=>"supergirl", "email"=>"supergirl@facebook.com", "password"=>"password")));
        }

        function tearDown(){
            parent::tearDown();
        }

        /**
         * data provider
         * @return array
         */
        function provider(){
            return array(
                array(
                    array(
                        array("title"=>"yahoo.com", "description"=>
                            "yahoo's website", "url"=>"http://yahoo.fr", "private"=>1,
                            "tags"=>array("yahoo", "advertising", "life style")),
                        array("title"=>"google.com", "description"=>
                            "google search engine", "url"=>"http://google.com", "private"=>0,
                            "tags"=>array("google", "search", "popular", "advertising")),
                        ),
                    array("HTTP_Content-Type"=>"application/json"),
                    ),
                );
        }

        /**
         * @covers App\Controller\TagController::get
         * @dataProvider provider
         */
        public function testGet($bookmarks, $headers){
            // un utilisateur récupère ses tags
            $client = $this->createClient();
            $client->request("POST", "/json/bookmark", array(), array(), $headers, json_encode($bookmarks[0]));
            $client->request("POST", "/json/bookmark", array(), array(), $headers, json_encode($bookmarks[1]));
            $client->request("GET", "/json/tag");
            $response = $client->getResponse();
            $json = json_decode($response->getContent());
            // print_r($json);
            $this->assertEquals(6, count($json->tags));
            $ad = new \stdClass();
            $ad->count = 2;
            $ad->tag = "advertising";
            $ad->bookmark_id=null;
            $this->assertTrue(in_array($ad, $json->tags));
            
        }

        /**
         * @covers App\Controller\TagController::autocomplete
         * @dataProvider provider
         */
        public function testAutocomplete($bookmarks, $headers){
            // utilisateur recherche des tags par nom
            $client = $this->createClient();
            $client->request("POST", "/json/bookmark", array(), array(), $headers, json_encode($bookmarks[0]));
            $client->request("POST", "/json/bookmark", array(), array(), $headers, json_encode($bookmarks[1]));
            $client->request("GET", "/json/autocomplete",array("q"=>po));
            $response = $client->getResponse();
            $json = json_decode($response->getContent());
            $this->assertEquals(1, count($json->tags));
            $ad = new \stdClass();
            $ad->tag = "popular";
            $ad->count = null;
            $ad->bookmark_id=null;
            $this->assertTrue(in_array($ad, $json->tags));
            $client->request("GET", "/json/autocomplete",array("q"=>oo));
            $response2 = $client->getResponse();
            $json = json_decode($response2->getContent());
            $this->assertEquals(2, count($json->tags));
            $google = new \stdClass();
            $google->tag = "google";
            $google->count = null;
            $google->bookmark_id=null;
            $this->assertTrue(in_array($google, $json->tags));
            $yahoo = new \stdClass();
            $yahoo->tag = "yahoo";
            $yahoo->bookmark_id=null;
            $yahoo->count=null;
            $this->assertTrue(in_array($yahoo, $json->tags));
        }

        public function createApplication(){
            return createApplication();
        }

    }

}
?>
