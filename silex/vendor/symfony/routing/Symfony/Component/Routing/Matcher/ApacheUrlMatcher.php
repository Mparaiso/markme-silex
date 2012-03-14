<?php










namespace Symfony\Component\Routing\Matcher;

use Symfony\Component\Routing\Exception\MethodNotAllowedException;






class ApacheUrlMatcher extends UrlMatcher
{
    










    public function match($pathinfo)
    {
        $parameters = array();
        $allow = array();
        $match = false;

        foreach ($_SERVER as $key => $value) {
            $name = $key;

            if (0 === strpos($name, 'REDIRECT_')) {
                $name = substr($name, 9);
            }

            if (0 === strpos($name, '_ROUTING_')) {
                $name = substr($name, 9);
            } else {
                continue;
            }

            if ('_route' == $name) {
                $match = true;
                $parameters[$name] = $value;
            } elseif (0 === strpos($name, '_allow_')) {
                $allow[] = substr($name, 7);
            } else {
                $parameters[$name] = $value;
            }

            unset($_SERVER[$key]);
        }

        if ($match) {
            return $parameters;
        } elseif (0 < count($allow)) {
            throw new MethodNotAllowedException($allow);
        } else {
            return parent::match($pathinfo);
        }
    }
}
