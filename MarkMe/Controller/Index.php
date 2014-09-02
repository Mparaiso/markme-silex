<?php
/**
 * @copyrights 2014 mparaiso <mparaiso@online.fr>
 * @All rights reserved
 */

namespace MarkMe\Controller {

    use MarkMe\App as Application;
    use MarkMe\Form\Register;
    use Symfony\Component\HttpFoundation\Response;
    use  MarkMe\Entity\User as UserEntity;
    use Symfony\Component\HttpFoundation\Request;
    use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
    use Symfony\Component\Security\Core\Authorization\Voter\AuthenticatedVoter;

    class Index
    {

        /**
         * homepage
         * @param \Silex\Application $app
         * @return Response
         */
        function index(Request $req, Application $app)
        {
            /** @var \MarkMe\App $app */
            if ($app->security->isGranted(AuthenticatedVoter::IS_AUTHENTICATED_FULLY)) {
                return $app->redirect($app->url_generator->generate('application'));
            }
            $user = new UserEntity;
            $form = $app->formFactory->create(new Register(), $user);
            if ("POST" == $req->getMethod() && $form->submit($req) && $form->isValid()) {
                $app->users->register($user);
                $app->security->setToken(new UsernamePasswordToken($user, null, 'secured', $user->getRoles()));
                $app->session->set('_security_main', serialize($user));
                return $app->redirect($app->url_generator->generate('application'));
            }
            return $app->twig->render("index.twig", array(
                'form' => $form->createView()
            ));
        }

        /**
         * application
         * @param \Silex\Application $app
         */
        function application(Application $app)
        {
            return $app->twig->render("application.twig");
        }

    }

}
