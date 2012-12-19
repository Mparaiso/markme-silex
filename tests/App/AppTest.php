<?php
namespace App\AppTest;

use Silex\WebTestCase;

class AppTest extends WebTestCase{
    function createApplication(){
        putenv("MARKME_DB_DRIVER=pdo_sqlite");
        $schema = file_get_contents(__DIR__.'/../../Database/schema.sqlite.sql');
        $app = require __DIR__.'/../../App/application.php';
        $app["debug"] = true;
        $app["exception_handler"]->disable();
        $app["session.test"] = true;
        $statement = $app["db"]->prepare($schema);
        $statement->execute();
        return $app;
    }

    function testIndex(){
        $client = $this->createClient();
        $crawler = $client->request("GET","/");

        $this->assertTrue($client->getResponse()->isOk());
    }

    function testTest(){
        $this->assertEquals(true,true);
    }
}

