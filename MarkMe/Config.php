<?php

/**
 * @copyrights 2014 mparaiso <mparaiso@online.fr>
 * @license GPL
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
use Silex\Controller;
use Silex\ControllerCollection;
use Silex\Provider\DoctrineServiceProvider;
use Silex\Provider\FormServiceProvider;
use Silex\Provider\MonologServiceProvider;
use Silex\Provider\SecurityServiceProvider;
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

class Config implements \Silex\ServiceProviderInterface {

    /**
     * @inheritdoc
     */
    public function register(Application $app) {
        /* @var App $app */

        $app->register(new DoctrineServiceProvider(), array("db.options" => array(
                "driver" => getenv("MARKME_DB_DRIVER"),
                "dbname" => getenv("MARKME_DB_DATABASE_NAME"),
                "host" => getenv("MARKME_DB_HOST"),
                "user" => getenv("MARKME_DB_USERNAME"),
                "password" => getenv("MARKME_DB_PASSWORD"),
                "memory" => true,
        )));

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

        #twig templates
        $app->register(new TwigServiceProvider(), array(
            "twig.path" => array(__DIR__ . "/Views/"), "twig.options" => array(
                "cache" => __DIR__ . "/../temp/twig",
                "debug" => $app['debug']
            ),
        ));

        # logging service
        $app->register(new MonologServiceProvider(), array(
            "monolog.logfile" => __DIR__ . "/../temp/access.log",
            "monolog.name" => "markme",
        ));

        # session management
        $app->register(new SessionServiceProvider(), array(
            "session.storage.options" => array(
                "httponly" => true,
                "domain" => "markme.app"
            ),
        ));

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
                Role::ROLE_USER => array()
            )
                )
        );
        $app->register(new SerializerServiceProvider());
        $app->register(new ServiceControllerServiceProvider());

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
            return $app->entityManager->getRepository('\MarkMe\Entity\Bookmark');
        });

        $app['tags'] = $app->share(function (App $app) {
            return $app->entityManager->getRepository('\MarkMe\Entity\Tag');
        });

        $app['users'] = $app->share(function (App $app) {
            $users = $app->entityManager->getRepository('\MarkMe\Entity\User');
            /* @var \MarkMe\Service\User $users */
            $users->setEncoderFactory($app['security.encoder_factory']);
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

        # middlewares

        $app['middleware.jsonToFormData'] = $app->protect(function (Request $req) use ($app) {
            if ($req->getMethod() == "POST") {
                if (0 === strpos($req->headers->get('Content-Type'), 'application/json')) {
                    $data = json_decode($req->getContent(), true);
                    $req->request->replace(is_array($data) ? $data : array());
                }
            }
        });

        $app['middleware.mustBeValidJSON'] = $app->protect(function (Request $request) use ($app) {
            if ("POST" == $request->getMethod()) {
                $data = json_decode($request->getContent(), true);
                if (!isset($data)) {
                    $app->abort("403", "must be valid json : " . $request->getContent());
                }
            }
        });
    }

    /**
     * @inheritdoc
     */
    public function boot(Application $app) {
        # routing 
        # transform the body of a JSON request to a form encoded request
        $app->match('/', 'controller.index:index')->method('GET|POST')->bind('index');
        $app->get('/application', 'controller.index:application')->bind('application');
        $app->post('/login-check', $app['noop'])->bind('login-check');
        $app->get('/login', 'controller.user:login')->bind('login');

        # ajax apis
        /* @var ControllerCollection|Controller $json */
        $json = $app['controllers_factory'];
        $json->before($app['middleware.mustBeValidJSON']);
        $json->before($app['middleware.jsonToFormData']);

        # user 
        $json->get('/user', 'controller.user:getCurrent')->bind('user_get_current');
        $json->put('/user', 'controller.user:updateUser')->bind('user_update_current');

        #bookmarks
        $json->get('/bookmark', 'controller.bookmark:findByUser')->bind('bookmark_find_by_current_user');
        $json->post('/bookmark', 'controller.bookmark:create')->bind('create_bookmark');
        $json->get('/bookmark/search', 'controller.bookmark:search')->bind('bookmark.search');
        $json->post('/bookmark/count', 'controller.bookmark:count')->bind('bookmark.count');
        $json->post('/bookmark/export', 'controller.bookmark:export')->bind('bookmark.export');
        $json->post('/bookmark/import', 'controller.bookmark:import')->bind('bookmark.import');
        $json->put('/bookmark/{id}', 'controller.bookmark:update')->bind('update_bookmark');
        $json->delete('/bookmark/{id}', 'controller.bookmark:delete')->bind('delete_bookmark');
        $json->get('/bookmark/{id}', 'controller.bookmark:read')->bind('read_bookmark');
        $json->get('/bookmark/tag/{tagName}', 'controller.bookmark:getByTag');

        # tags 
        $json->get('autocomplete', 'controller.tag:autocomplete')->bind('search_tag');
        $json->get('tag', 'controller.tag:get')->bind('get_tags');

        $app->mount('/json', $json);
    }

}
