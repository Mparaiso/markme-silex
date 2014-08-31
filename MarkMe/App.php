<?php
/**
 * @copyrights 2014 mparaiso <mparaiso@online.fr>
 * @license GPL
 */
namespace MarkMe;

use Doctrine\ORM\EntityManager;
use Psr\Log\LoggerInterface;
use Silex\Application;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Generator\UrlGenerator;
use Symfony\Component\Security\Core\Authentication\AuthenticationProviderManager;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\Serializer\Serializer;

/**
 * Class App
 * @package MarkMe
 * @property \MarkMe\Service\User users
 * @property \MarkMe\Service\Bookmark bookmarks
 * @property \MarkMe\Service\Tag tags
 * @property \Twig_Environment twig
 * @property LoggerInterface logger
 * @property SecurityContext security
 * @property Session session
 * @property Request request
 * @property Serializer serializer
 * @property UrlGenerator url_generator
 * @property FormFactory formFactory
 * @property EntityManager entityManager
 */
class App extends Application implements AppInterface
{
    function __construct(array $params = array())
    {
        parent::__construct($params);
        $this->register(new Config());
    }

    /**
     * @param string $property
     * @return Void|mixed
     */
    function __get($property)
    {
        if($property=="entityManager"){
            return $this->offsetGet('orm.em');
        }
        if($property=="formFactory"){
            return $this->offsetGet('form.factory');
        }
        if ($this->offsetExists($property)) {
            return $this->offsetGet($property);
        }
    }
    
    function foo(){}

}