<?php










namespace Symfony\Component\Routing;

use Symfony\Component\HttpFoundation\Request;








class RequestContext
{
private $baseUrl;
private $method;
private $host;
private $scheme;
private $httpPort;
private $httpsPort;
private $parameters;













public function __construct($baseUrl = '', $method = 'GET', $host = 'localhost', $scheme = 'http', $httpPort = 80, $httpsPort = 443)
{
$this->baseUrl = $baseUrl;
$this->method = strtoupper($method);
$this->host = $host;
$this->scheme = strtolower($scheme);
$this->httpPort = $httpPort;
$this->httpsPort = $httpsPort;
$this->parameters = array();
}

public function fromRequest(Request $request)
{
$this->setBaseUrl($request->getBaseUrl());
$this->setMethod($request->getMethod());
$this->setHost($request->getHost());
$this->setScheme($request->getScheme());
$this->setHttpPort($request->isSecure() ? $this->httpPort : $request->getPort());
$this->setHttpsPort($request->isSecure() ? $request->getPort() : $this->httpsPort);
}






public function getBaseUrl()
{
return $this->baseUrl;
}








public function setBaseUrl($baseUrl)
{
$this->baseUrl = $baseUrl;
}








public function getMethod()
{
return $this->method;
}








public function setMethod($method)
{
$this->method = strtoupper($method);
}






public function getHost()
{
return $this->host;
}








public function setHost($host)
{
$this->host = $host;
}






public function getScheme()
{
return $this->scheme;
}








public function setScheme($scheme)
{
$this->scheme = strtolower($scheme);
}






public function getHttpPort()
{
return $this->httpPort;
}








public function setHttpPort($httpPort)
{
$this->httpPort = $httpPort;
}






public function getHttpsPort()
{
return $this->httpsPort;
}








public function setHttpsPort($httpsPort)
{
$this->httpsPort = $httpsPort;
}






public function getParameters()
{
return $this->parameters;
}










public function setParameters(array $parameters)
{
$this->parameters = $parameters;

return $this;
}








public function getParameter($name)
{
return isset($this->parameters[$name]) ? $this->parameters[$name] : null;
}








public function hasParameter($name)
{
return array_key_exists($name, $this->parameters);
}









public function setParameter($name, $parameter)
{
$this->parameters[$name] = $parameter;
}
}
