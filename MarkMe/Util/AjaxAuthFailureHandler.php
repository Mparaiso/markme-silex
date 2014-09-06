<?php

namespace MarkMe\Util;

use \Symfony\Component\Security\Http\Authentication\AuthenticationFailureHandlerInterface;
use \Symfony\Component\HttpFoundation\Request;
use \Symfony\Component\Security\Core\Exception\AuthenticationException;
use \Symfony\Component\Security\Http\Authentication\DefaultAuthenticationFailureHandler;
use \Symfony\Component\HttpFoundation\Response;

class AjaxAuthFailureHandler extends DefaultAuthenticationFailureHandler {

    /**
     * 
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param \Symfony\Component\Security\Core\Exception\AuthenticationException $exception
     */
    public function onAuthenticationFailure(Request $request, AuthenticationException $exception) {
                file_put_contents('php://stdout','/////////////////ajaxfailure'.$request->getContent());

        $response = parent::onAuthenticationFailure($request, $exception);
        file_put_contents('php://stdout','ajaxfailure'.$request->getContent());
        if (in_array($request->getRequestFormat(), array('json', 'xml'))) {
            return new Response($response->getContent(), 403);
        }
        return $response;
    }

//put your code here
}
