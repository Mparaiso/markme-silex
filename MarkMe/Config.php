<?php

/**
 * @copyrights 2014 mparaiso <mparaiso@online.fr>
 * @All rights reserved
 */

namespace MarkMe;

use MarkMe\Controller\Bookmark;
use MarkMe\Controller\Index;
use MarkMe\Controller\Tag;
use MarkMe\Controller\User;
use MarkMe\Entity\Role;
use MarkMe\Logging\MonologSQLLogger;
use Mparaiso\Provider\ConsoleServiceProvider;
use Mparaiso\Provider\DoctrineORMServiceProvider;
use Silex\Application;
use Silex\Provider\DoctrineServiceProvider;
use Silex\Provider\FormServiceProvider;
use Silex\Provider\MonologServiceProvider;
use Silex\Provider\SecurityServiceProvider;
use Silex\Provider\HttpCacheServiceProvider;
use Silex\Provider\SerializerServiceProvider;
use Silex\Provider\ServiceControllerServiceProvider;
use Silex\Provider\SessionServiceProvider;
use Silex\Provider\TranslationServiceProvider;
use Silex\Provider\TwigServiceProvider;
use Silex\Provider\UrlGeneratorServiceProvider;
use Silex\Provider\ValidatorServiceProvider;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authorization\Voter\AuthenticatedVoter;
use Doctrine\ORM\Configuration;
use Monolog\Handler\StreamHandler;
use Symfony\Bridge\Doctrine\HttpFoundation\DbalSessionHandler;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class Config implements \Silex\ServiceProviderInterface {

    /**
     * @inheritdoc
     */
    public function register(Application $app) {
        /* @var $app App */
        # doctrine dbal
        $app->register(new DoctrineServiceProvider(), array("db.options" => array(
                "driver" => getenv("MARKME_DB_DRIVER"),
                "dbname" => getenv("MARKME_DB_DATABASE_NAME"),
                "host" => getenv("MARKME_DB_HOST"),
                "user" => getenv("MARKME_DB_USERNAME"),
                "password" => getenv("MARKME_DB_PASSWORD"),
                "memory" => true,
        )));


        # doctrine orm
        $app->register(new DoctrineORMServiceProvider(), array(
            "orm.proxy_dir" => __DIR__ . '/Proxy',
            "orm.logger" => $app->share(function ($app) {
                return new MonologSQLLogger($app["monolog"]);
            }),
            "orm.driver.configs" => array(
                "default" => array(
                    "namespace" => 'MarkMe', // rootnamespace of your entities
                    "type" => "annotation", // driver type (yaml,xml,annotation)
                    "paths" => array(__DIR__ . '/../MarkMe') // config file path,
                )
            )
        ));
        #doctrine cache
        $app['doctrine.orm.cache_driver'] = $app->share(function($app) {
            return new \Doctrine\Common\Cache\FilesystemCache(__DIR__ . '/../temp/doctrine');
        });

        $app['bookmarkMetadataCache'] = $app->share(function($app) {
            return $app['doctrine.orm.cache_driver'];
        });

        $app["orm.config"] = $app->share($app->extend('orm.config', function(Configuration $config, $app) {
                    $config->setQueryCacheImpl($app['doctrine.orm.cache_driver']);
                    $config->setResultCacheImpl($app['doctrine.orm.cache_driver']);
                    $config->setMetadataCacheImpl($app['doctrine.orm.cache_driver']);
                    return $config;
                }));

        #twig templates
        $app->register(new TwigServiceProvider(), array(
            "twig.path" => array(__DIR__ . "/Views/"), "twig.options" => array(
                "cache" => __DIR__ . "/../temp/twig",
                "debug" => $app['debug']
            ),
        ));

        # logging service
        $app->register(new MonologServiceProvider(), array(
            "monolog.logfile" => __DIR__ . "/../temp/access-" . \date('Y-m-d') . ".log",
            "monolog.name" => "markme",
        ));
        $app['monolog'] = $app->share($app->extend('monolog', function($monolog, $app) {
                    $logLevel = $app['debug'] == TRUE ? \Monolog\Logger::DEBUG : \Monolog\Logger::WARNING;
                    $monolog->pushHandler(new StreamHandler('php://stdout', $logLevel));
                    return $monolog;
                })
        );
        # session management
        $app->register(new SessionServiceProvider(), array(
            "session.storage.options" => array(
                "cookie_httponly" => true,
                "id" => "markme"
            ),
        ));
        $app['session.storage.handler'] = $app->share(function(App $app) {
            $connection = $app->entityManager->getConnection();
            return new DbalSessionHandler($connection, 'sessions');
        });
        # url generator
        $app->register(new UrlGeneratorServiceProvider());
        $app->register(new ConsoleServiceProvider());
        $app->register(new ValidatorServiceProvider());
        $app->register(new FormServiceProvider());
        $app->register(new TranslationServiceProvider());

        # Security
        $app->register(new SecurityServiceProvider(), array(
            'security.firewalls' => array(
                'secured' => array(
                    'pattern' => '^/',
                    "anonymous" => true,
                    'form' => array(
                        'login_path' => "/login",
                        'check_path' => "/login-check",
                        "default_target_path" => "/application",
                        "always_use_default_target_path" => true,
                        'username_parameter' => 'login[username]',
                        'password_parameter' => 'login[password]',
                        "csrf_parameter" => "login[_token]",
                        "failure_path" => "/login"
                    ),
                    'logout' => array(
                        'logout_path' => "/logout",
                        "target" => '/',
                        "invalidate_session" => true
                    ),
                    'users' => $app->share(function (Application $app) {
                        return $app['users'];
                    })
                )
            ),
            'security.access_rules' => array(
                array('^/application', AuthenticatedVoter::IS_AUTHENTICATED_FULLY),
                array('^/login-check', AuthenticatedVoter::IS_AUTHENTICATED_FULLY),
                array('^/logout', AuthenticatedVoter::IS_AUTHENTICATED_FULLY),
                array('^/login', AuthenticatedVoter::IS_AUTHENTICATED_ANONYMOUSLY),
                array('^/json', AuthenticatedVoter::IS_AUTHENTICATED_FULLY),
                array('/', AuthenticatedVoter::IS_AUTHENTICATED_ANONYMOUSLY),
            ),
            'security.role_hierarchy' => array(
                Role::ROLE_USER => array())
                )
        );




        $app->register(new SerializerServiceProvider());
        $app->register(new ServiceControllerServiceProvider());
        $app->register(new HttpCacheServiceProvider(), array(
            'http_cache.cache_dir' => __DIR__ . '/../temp/http/',
            'http_cache.options' => array('debug' => $app['debug'], 'defaut.ttl' => 5),
            'http_cache.esi' => null,
        ));



        # custom services

        $app["upload_dir"] = __DIR__ . "/../upload";
        $app["max_size_upload"] = ini_get("upload_max_filesize");
        $app["current_date"] = $app->share(function () {
            return new \DateTime();
        });

        # repositories
        $app['noop'] = $app->protect(function () {
            
        });
        $app["bookmarks"] = $app->share(function (App $app) {
            /* @var \MarkMe\App $app */
            $bookmarks = $app->entityManager->getRepository('\MarkMe\Entity\Bookmark');
            $bookmarks->setValidator($app['validator']);
            return $bookmarks;
        });



        $app['users'] = $app->share(function (App $app) {
            $users = $app->entityManager->getRepository('\MarkMe\Entity\User');
            /* @var \MarkMe\Service\User $users */
            $users->setEncoderFactory($app['security.encoder_factory']);
            $users->setValidator($app['validator']);
            return $users;
        });

        $app["get_thumbnail"] = $app->protect(function ($url, $width = 200) {
            return "http://api.thumbalizr.com/?url=" . "$url" . "&width=" . $width;
        });

        $app['controller.index'] = $app->share(function () {
            return new Index();
        });
        $app['controller.user'] = $app->share(function () {
            return new User();
        });

        $app['controller.bookmark'] = $app->share(function () {
            return new Bookmark();
        });

        $app['controller.tag'] = $app->share(function () {
            return new Tag();
        });
        /**
         * EXCEPTION HANDLERS
         */
        $app->error(function(AccessDeniedException $exception, $code)use ($app) {
            /**
             * when request format is JSON OR XML do not redirect on request failure
             * but send a serialized response
             */
            if (in_array($app->request->getRequestFormat(), array('json', 'xml'))) {
                $app->logger->error($exception->getMessage());
                return new Response($exception->getMessage(), 403, array('X-Status-Code' => 403));
            }
        }, 100);
    }

    /**
     * @inheritdoc
     */
    public function boot(Application $app) {
        /* @var $app App */
        # routing 
        # transform the body of a JSON request to a form encoded request
        $app->match('/', 'controller.index:index')->method('GET|POST')->bind('index');
        $app->get('/application', 'controller.index:application')->bind('application');
        $app->post('/login-check', $app['noop'])->bind('login-check');
        $app->get('/login', 'controller.user:login')->bind('login');

        # ajax apis
        /* @var ControllerCollection|Controller $json */
        $json = $app['controllers_factory'];
        # user 
        $json->get('/user.{_format}', 'controller.user:getCurrent')->bind('user_get_current');
        $json->put('/user.{_format}', 'controller.user:updateUser')->bind('user_update_current');
        #bookmarks
        $json->get('/bookmark.{_format}', 'controller.bookmark:index')->bind('bookmark_index');
        $json->post('/bookmark.{_format}', 'controller.bookmark:create')->bind('create_bookmark');
        $json->get('/bookmark/search.{_format}', 'controller.bookmark:search')->bind('bookmark_search');
        $json->get('/bookmark/export.{_format}', 'controller.bookmark:export')->bind('bookmark.export');
        $json->post('/bookmark/import.{_format}', 'controller.bookmark:import')->bind('bookmark.import');
        $json->get('/bookmark/suggest.{_format}', 'controller.bookmark:suggestBookmarkData')->bind('bookmark.suggest');
        $json->put('/bookmark/{id}.{_format}', 'controller.bookmark:update')->bind('update_bookmark');
        $json->delete('/bookmark/{id}.{_format}', 'controller.bookmark:delete')->bind('delete_bookmark');
        $json->get('/bookmark/{id}.{_format}', 'controller.bookmark:read')->bind('bookmark_read');
        $json->get('/tag/{tags}.{_format}', 'controller.bookmark:findByTags')->bind('tag_search');
        # tags 
        $json->get('autocomplete.{_format}', 'controller.tag:search')->bind('tag_autocomplete');
        $json->get('tag.{_format}', 'controller.tag:index')->bind('tag_list');

        $json->value('_format', 'json');
        $json->assert('_format', 'json|xml');



        $app->mount('/json', $json);
        $app->after(function(Request $req, Response $res) {
            if ($req->getMethod() == 'GET') {
                $res->setMaxAge(5);
                $res->setTtl(10);
            }
        });
    }

}
