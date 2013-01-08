<?php

namespace App\BusinessLogicLayer {

    use \App\BusinessLogicLayer\UserManager;
    use \App\DataAccessLayer\UserProvider;
    use \App\DataTransferObjects\User;

    /**
     * @author M.Paraiso
     */
    class UserManagerTest extends \PHPUnit_Framework_TestCase {

        /**
         * @var Silex\Application $app
         */
        protected $app;

        function setUp() {
            parent::setUp();
            $this->app = createApplication();
            $this->userProvider = new UserProvider($this->app["db"]);
            $this->userManager = new UserManager($this->userProvider,$app["salt"]);
        }

        function testConstruct(){
            $userProvider = new UserProvider($this->app["db"]);
            $userManager = new UserManager($userProvider,$this->app["salt"]);
            $this->assertNotNull($userManager);
        }

        function provider() {
            $app = createApplication();
            $user = new User();
            $user->username = "superman";
            $user->password = "krypton";
            $user->email = "superman@justiceleague.org";
            $user->created_at = $app["current_time"];
            $user->last_login = $app["current_time"];
            return array(
                array($user)
                );
        }
    }
}