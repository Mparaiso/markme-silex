<?php

/**
 * @copyrights 2014 mparaiso <mparaiso@online.fr>
 * @All rights reserved
 */

namespace MarkMe\Controller;

use MarkMe\Form\Login;
use Silex\Application;
use Doctrine\DBAL\DBALException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * FR : gère les utilisateurs de l'application
 *
 * @author M.Paraiso
 */
class User {

    /**
     * FR : connecte un utilisateur
     * @param \Silex\Application $app
     */
    function login(Request $req, Application $app) {


        /** @var \MarkMe\App $app */
        $form = $app->formFactory->create(new Login(), array(
            'username' => $app->session->get('_security.last_username')
        ));

        return $app->twig->render('login.twig', array(
                    'error' => $app['security.last_error']($req),
                    'form' => $form->createView()
        ));
    }

    function getCurrent(Application $app) {
        /** @var \MarkMe\App $app */
        $user = $app->security->getToken()->getUser();
        if (NULL == $user) {
            $app->session->invalidate();
            return new Response($app->serializer->serialize(array('status' => 404, 'message' => 'user not found'), 'json'), 404);
        }
        return $app->serializer->serialize(array('status' => 200, 'user' => $user), 'json');
    }

}
