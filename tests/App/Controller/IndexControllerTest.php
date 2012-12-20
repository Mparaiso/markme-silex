<?php

/**
 * Description of IndexControllerTest
 *
 * @author mark prades
 */
use Silex\WebTestCase;

namespace App\Controller {

    class IndexControllerTest extends \Silex\WebTestCase {

        public function createApplication() {
            putenv("MARKME_DB_DRIVER=pdo_sqlite");
            $schema = file_get_contents(
                    ROOT . '/Database/schema.sqlite.sql');
            $app = require ROOT . '/App/application.php';
            $app["debug"] = true;
            $app["exception_handler"]->disable();
            $app["session.test"] = true;
            $statement = $app["db"]->prepare($schema);
            $statement->execute();
            return $app;
        }

        function provider() {
            return array(
                array(
                    array(
                        "json" => json_encode(
                                array("username" => "superman",
                                    "email" => "superman@free.fr",
                                    "password" => "password")
                                ),
                        "validJsonResponse" => json_encode(
                                array("id" => 1,
                                    "username" => "superman",
                                    "email" => "superman@free.fr",
                                    "status" => "ok")),
                        "userResponse" => array("username" => "superman",
                            "email" => "superman@free.fr", "id" => 1),
                        "requestFailedResponse" => json_encode(
                                array("status" => "error",
                                    "message" => "There is already an account with that e-mail or username")),
                        "requestMissingArgumentResponse" => json_encode(
                                array("status" => "error",
                                    "message" =>
                                    "request error")),
                    ),
                ),
            );
        }

        /**
         * @dataProvider provider
         */
        function testRegister($data) {
            $client = $this->createClient();
            $client->request("POST", "/json/register", array(), array(), array("HTTP_Content-Type" => "application/json"), $data['json']);
            $response = $client->getResponse();
            // FR : la réponse est valide
            $this->assertEquals($data['validJsonResponse'], $response->getContent());
            $this->assertEquals($this->app["session"]->get("user"), $data["userResponse"]);
            $this->assertEquals($this->app["session"]->get("user_id"), 1);
            // FR : la requète échoue parce que l'utilisateur or l'email existe déja
            $client2 = $this->createClient();
            $client2->request("POST", "/json/register", array(), array(), array("HTTP_Content-Type" => "application/json"), $data['json']);
            $response2 = $client2->getResponse();
            $this->assertEquals($data["requestFailedResponse"], $response2->getContent());
            // FR : la requète ne contient pas les paramètres attendus
            $client3 = $this->createClient();
            $client3->request("POST", "/json/register", array(), array(), array("HTTP_Content-Type" => "application/json"), "{}");
            $response3 = $client3->getResponse();
            $this->assertEquals($data["requestMissingArgumentResponse"], $response3->getContent());
        }

        /**
         * @depends testRegister
         */
        function testLogin() {
            /**$this->createClient()->request(
                    "POST",
                    "/json/register",
                    array(),
                    array(),
                    array(
                        "HTTP_Content-Type"=>"application/json"
                    ),
                    json_encode(
                            array(
                                "username"=>"superman",
                                "password"=>"password",
                                "email"=>"superman@free.fr",
                            )
                    )
            );**/
            $client = $this->createClient();
            $client->request("POST","/json/login",
                    array(),
                    array(),
                    array(
                        "HTTP_Content-Type"=>"application/json"
                    ),
                    json_encode(
                            array("username"=>"camus",
                                "password"=>"password"
                            )
                    )
            );
            $response = $client->getResponse();
            print($response->getContent());
            $this->assertEquals($response->getStatusCode(),
                    200);
            $this->assertEquals($this->app["session"]->get("user_id"),1);
            $this->assertEquals(
                    $this->app["session"]->get("user"),
                    array(
                        "username"=>"superman",
                        "email"=>"superman@free.fr",
                        "id"=>1
                    )
             );
            
        }

    }

}
?>
