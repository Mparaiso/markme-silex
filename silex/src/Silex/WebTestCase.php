<?php










namespace Silex;

use Symfony\Component\HttpKernel\Client;
use Symfony\Component\HttpKernel\Test\WebTestCase as BaseWebTestCase;






abstract class WebTestCase extends \PHPUnit_Framework_TestCase
{
protected $app;







public function setUp()
{
$this->app = $this->createApplication();
}






abstract public function createApplication();









public function createClient(array $options = array(), array $server = array())
{
return new Client($this->app);
}
}
