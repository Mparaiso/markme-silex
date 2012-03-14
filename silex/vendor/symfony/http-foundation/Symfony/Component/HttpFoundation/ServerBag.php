<?php










namespace Symfony\Component\HttpFoundation;







class ServerBag extends ParameterBag
{
    




    public function getHeaders()
    {
        $headers = array();
        foreach ($this->parameters as $key => $value) {
            if (0 === strpos($key, 'HTTP_')) {
                $headers[substr($key, 5)] = $value;
            }
            
            elseif (in_array($key, array('CONTENT_LENGTH', 'CONTENT_MD5', 'CONTENT_TYPE'))) {
                $headers[$key] = $this->parameters[$key];
            }
        }

        
        if (isset($this->parameters['PHP_AUTH_USER'])) {
            $pass = isset($this->parameters['PHP_AUTH_PW']) ? $this->parameters['PHP_AUTH_PW'] : '';
            $headers['AUTHORIZATION'] = 'Basic '.base64_encode($this->parameters['PHP_AUTH_USER'].':'.$pass);
        }

        return $headers;
    }
}
