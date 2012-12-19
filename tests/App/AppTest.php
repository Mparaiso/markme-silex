<?php
namespace App\AppTest;

use Silex\WebTestCase;

class AppTest extends WebTestCase{
    function createApplication(){
        return require __DIR__.'/../../App/application.php';
    }

    function testTest(){
        $this->assertEquals(true,true);
    }
}

