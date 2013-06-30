<?php

namespace App\DataAccessLayer {

    use App\DataAccessLayer\UserProvider;
    use App\DataTransferObjects\User;

    /**
     * @author M.Paraiso
     */
    class UserProviderTest extends \PHPUnit_Framework_TestCase {

        /**
         * @var \Silex\Application $app
         */
        protected $app;

        /**
         * @var \App\DataAccessLayer\UserProvider $userProvider
         */
        protected $userProvider;

        function setUp() {
            parent::setUp();
            $this->app = createApplication();
            $this->userProvider =  new UserProvider($this->app["db"]);
        }

        /**
         * @dataProvider provider
         */
        function testGetByEmail(User $user){
            // le client cherche un email absent de la base de données
            $result = $this->userProvider->getByEmail($user->email);
            $this->assertNull($result);
            // le client crée un utilisateur puis cherche l'utilisateur par email
            $newUser = $this->userProvider->create($user);
            $fetchedUser = $this->userProvider->getByEmail($newUser->email);
            $this->assertNotNull($newUser);
            $this->assertEquals($user->email,$fetchedUser->email);
        }

        /**
         * @dataProvider provider
         */
        function testGetById(User $user){
            // le client cherche un utilisateur absent de la BDD
            $result = $this->userProvider->getById(2);
            $this->assertNull($result);
            // le client crée un utilisateur puis cherche l'utilisateur par id
            $newUser = $this->userProvider->create($user);
            $fetchedUser = $this->userProvider->getById(1);
            $this->assertEquals(1,$fetchedUser->id);
            $this->assertEquals("superman",$fetchedUser->username);
        }

        /**
         * @dataProvider provider
         */
        function testCreate($user) {
            # un utilisateur est crée
            $user_id = 1;
            $userProvider = new UserProvider($this->app["db"]);
            $newUser = $userProvider->create($user);
            $this->assertEquals(1, $newUser->id);
        }

        /**
         * @dataProvider provider
         */
        function testUpdate(User $user) {
            // l'utilisateur crée un nouvel utilisateur puis change ses paramètres
            $newUser = $this->userProvider->create($user);
            $newUser->email = "superman@crimesyndicate.com";
            $affectedRows = $this->userProvider->update($newUser,$newUser->id);
            $this->assertEquals(1,$affectedRows);
            $updatedUser = $this->userProvider->getById($newUser->id);
            $this->assertEquals("superman@crimesyndicate.com",$updatedUser->email);
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
