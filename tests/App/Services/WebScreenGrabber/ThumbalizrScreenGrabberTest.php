<?php

/**
 * @author M.Paraiso
 */

namespace App\Services\WebScreenGrabber{

    use Silex\WebTestCase;
    use App\Services\WebScreenGrabber\ThumbalizrScreenGrabber;

    class ThumbalizrScreenGrabberTest extends \PHPUnit_Framework_TestCase{

        
        function setUp(){
            parent::setUp();
        }
        
        function testRequest(){
            $th = new ThumbalizrScreenGrabber();
            $response = $th->request("http://techcrunch.com");
            #print_r($response["status"]);          
            #print_r($response["statusMessage"]);
            $this->assertTrue($response->status<400);

        }
        
        function tearDown(){
            parent::tearDown();
        }
        

    }

}
