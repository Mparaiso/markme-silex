<?php

namespace App\Controller;

use Silex\Application;
use Doctrine\DBAL\DBALException;

/**
 * FR : gère les utilisateurs de l'application
 *
 * @author M.Paraiso
 */
class UserController {

    /**
     * 
     * @param \Silex\Application $app
     * @return Symfony\Component\HttpFoundation\JsonResponse
     */
    function register(Application $app) {
        $jsonContentType = array("Content-Type" => "application/javascript");
        $username = $app['request']->get("username");
        $password = md5($app['request']->get("password") + $username + $app['salt']);
        $email = $app['request']->get("email");
        if ($username AND $password AND $email):
            $time = time();
            try {
                $affetchedRows = $app["db"]->insert('users', array('username' => $username,
                    'email' => $email, 'password' => $password, 'created_at' => $time,
                    'last_login' => $time));
                $LastInsertedId = intval($app["db"]->lastInsertId());
                $user = array("id" => $LastInsertedId, "username" => $username, "email" => $email);
                $this->_setLoggedUserSession($app, $user);
                $response = $app->json(array_merge($user, array("status" => "ok")), 200, $jsonContentType);
            } catch (DBALException $exc) {
                $app['monolog']->addError("error : {$exc->getMessage()}");
                $response = $app->json(array("status" => "error", "message" => "There is already an account with that e-mail or username"), 200, $jsonContentType);
            }
        else:
            $response = $app->json(array("status" => "error", "message" => "request error"), 200, $jsonContentType);
        endif;
        return $response;
    }

    /**
     * FR : connecte un utilisateur
     * @param \Silex\Application $app
     */
    function login(Application $app) {
        $username = $app['request']->get("username");
        $password = $app["request"]->get("password") ? md5($username + $app['request']->get("password") + $app["salt"]) : null;
        if ($username AND $password):
            try {
                $user = $app["db"]->fetchAssoc("SELECT id, username, email FROM users WHERE username = :username AND password = :password", array(
                    "username" => $username,
                    "password" => $password,
                        ));
                if ($user):
                    $this->_setLoggedUserSession($app, $user);
                    $app["db"]->udpate(array("last_login" => time()), array("id" => $user["id"]));
                    return $app->json(array_merge($user, array("status" => "ok")), 200, array("HTTP_Cache-Control" => 'max-age=0, must-revalidate, no-cache, no-store', "HTTP_Content-type" => 'application/javascript'));
                else:
                    return $app->json(array("status" => "error", "message" => "User not found"), 200);
                endif;
            } catch (DBALException $exc) {
                $app['monolog']->addError($exc->getTraceAsString());
                $app["session"]->invalidate();
                return $app->json(array("status" => "error", "message" => "Database Error"), 200);
            }
        else:
            return $app->json(array("status" => "error", "message" => "Invalid parameters"), 200);
        endif;
    }

    function logout(Application $app) {
        if ($app["session"]->get("user") && $app["session"]->get("user_id")):
            $response = $app->json(array("status" => "ok", "message" => "user logged out"), 200);
        else:
            $response = $app->json(array("status" => "error", "message" => "no user found"), 200);
        endif;
        $app["session"]->invalidate();
        return $response;
    }

    function getCurrent(Application $app) {
        $user = $app["session"]->get("user");
        if (isset($user)):
            return $app->json(array_merge($user, array("status" => "ok")), 200);
        else:
            $app["session"]->invalidate();
            return $app->json(array("status" => "error", "message" => "User not found"), 200);
        endif;
    }

    function updateUser(Application $app) {

        $username = $app["request"]->get("username");
        $email = $app["request"]->get("email");
        $password = $app["request"]->get("password") ? md5($username + $app["request"]->get("password") + $app["salt"]) : null;
        if (isset($username) && isset($email)):
            try {
                $user_id = $app["session"]->get("user_id");
                $affectedRows = $app["db"]->update("users", array("username" => $username, "password" => $password, "email" => $email), array("id" => $user_id));
                if ($affectedRows > 0) {
                    $user = $app["db"]->fetchAssoc("SELECT id,username,email from users WHERE id = ?", array($user_id));
                    if ($user):
                        $this->_setLoggedUserSession($app, $user);
                        return $app->json(array("username" => $user["username"], "email" => $user["email"], "status" => "ok"), 200);
                    endif;
                }
            } catch (DBALException $exc) {
                $app["logger"]->addError($exc->getTraceAsString());
            }
        else:
            return $app->json(array("status" => "error", "message" => "Unable to update user"), 200);
        endif;
    }

    /**
     * si un utilisateur valide existe , parametrer la session
     * @param type $app
     * @param type $user
     */
    protected function _setLoggedUserSession($app, $user) {
        $app['session']->set("user_id", $user["id"]);
        $app["session"]->set("user", array("id" => $user["id"], "username" => $user["username"], "email" => $user["email"]));
    }

}

?>
