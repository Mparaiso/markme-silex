<?php

/**
 * @author M.Paraiso
 */

namespace App\Controller {


    class UserControllerTest extends \Silex\WebTestCase {

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
                                array("status" => "ok",
                                    "user"=>array("id" => 1,
                                    "username" => "superman",
                                    "email" => "superman@free.fr"),
                                    )
                                ),
                        "userResponse" => array("username" => "superman",
                            "email" => "superman@free.fr", "id" => 1),
                        "requestFailedResponse" => json_encode(
                                array("status" => "error",
                                    "message" => "Email already used")),
                        "requestMissingArgumentResponse" => json_encode(
                                array("status" => "error",
                                    "message" =>
                                    "Request error")),
                    ),
                ),
            );
        }

        /**
         * FR : teste l'enregistrement d'un utilisateur
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
            $this->app["session"]->invalidate();
            $client->restart();
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
         * FR : teste le login utilisateur
         * @depends testRegister
         */
        function testLogin() {
            // l'utilisateur se connecte
           $client = $this->createClient();
           $client->request(
                    "POST", "/json/register", array(), array(), array(
                "HTTP_Content-Type" => "application/json"
                    ), json_encode(
                            array(
                                "username" => "superman",
                                "password" => "password",
                                "email" => "superman@free.fr",
                            )
                    )
            );
            $this->app["session"]->invalidate;
            $client->restart();
            $client->request("POST", "/json/login", array(), array(), array(
                "HTTP_Content-Type" => "application/json"
                    ), json_encode(
                            array("username" => "superman",
                                "password" => "password"
                            )
                    )
            );
            $response = $client->getResponse();
            $this->assertTrue($response->getStatusCode()<400);
            $this->assertEquals(1,$this->app["session"]->get("user_id"));
            $this->assertEquals(
                    $this->app["session"]->get("user"), array(
                "username" => "superman",
                "email" => "superman@free.fr",
                "id" => 1
                    )
            );
        }

        /**
         * FR : test la déconnexion d'un utilisateur
         * @depends testLogin
         */
        public function testLogout() {
            // l'utilisateur était connecté et est bien déconnecté
            $client = $this->createClient();
            $client->request(
                    "POST", "/json/register", array(), array(), array("HTTP_Content-Type" => "application/json"), json_encode(
                            array(
                                "username" => "superman",
                                "email" => "superman@free.fr",
                                "password" => "password"
                            )
                    )
            );
            $client->request("POST", "/json/logout");
            $response = $client->getResponse();
            $this->assertEquals($response->getContent(), json_encode(
                            array("status" => "ok", "message" => "user logged out")));
            // l'utilisateur n'était pas connecté et ne peut donc pas se déconnecté , l'application lève une exception
            $client2 = $this->createClient();
            $this->setExpectedException("Symfony\Component\HttpKernel\Exception\HttpException", "Unauthorized user");
            $client2->request("POST", "/json/logout");
        }

        /**
         * FR : teste l'obtention de l'utilisateur courant
         * @covers App\Controller\UserController::getCurrent
         * @dataProvider provider
         */
        public function testGetCurrent($data) {
            // l'utilisateur est enregistré
            $client = $this->createClient();
            $client->request("POST", "/json/register", array(), array(), array(
                "HTTP_Content-Type" => "application/json"
                    ), $data["json"]
            );
            $client->request("GET", "/json/user");
            $response = $client->getResponse();
            $this->assertEquals($response->getContent(), $data['validJsonResponse']);
            $client->request("POST", "/json/logout");
            // l'utilisateur n'est pas connecté
            $this->setExpectedException("Symfony\Component\HttpKernel\Exception\HttpException", "Unauthorized user");
            $client->request("GET", "/json/user");
        }

        /**
         * @dataProvider provider
         * @param array $data
         */
        public function testUpdateUser($data) {
            // un utilisateur connecté met à jour ses informations
            $newDatas = json_encode(array("username" => "superboy", "email" => "superboy@free.fr", "password" => "password2"));
            $responseData = json_encode(array("username" => "superboy", "email" => "superboy@free.fr", "status" => "ok"));
            $client = $this->createClient();
            $client->request("POST", "/json/register", array(), array(), array("HTTP_Content-Type" => "application/json"), $data["json"]);
            $client->request("PUT", "/json/user", array(), array(), array("HTTP_Content-Type" => "application/json"), $newDatas);
            $response = $client->getResponse();
            $this->assertEquals($responseData, $response->getContent());
            $client->request("POST", "/json/logout");
            $client->restart();
            // un utilisateur non connecté tente de mettre à jour ses informations
            $this->setExpectedException("Symfony\Component\HttpKernel\Exception\HttpException", "Unauthorized user");
            $client->request("PUT", "/json/user", array(), array(), array("HTTP_Content-Type" => "application/json"), $newDatas);
        }

        public function createApplication() {
            return createApplication();
        }

    }

}
?>
