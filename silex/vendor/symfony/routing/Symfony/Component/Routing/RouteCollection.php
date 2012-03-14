<?php










namespace Symfony\Component\Routing;

use Symfony\Component\Config\Resource\ResourceInterface;











class RouteCollection implements \IteratorAggregate
{
    private $routes;
    private $resources;
    private $prefix;
    private $parent;

    




    public function __construct()
    {
        $this->routes = array();
        $this->resources = array();
        $this->prefix = '';
    }

    public function __clone()
    {
        foreach ($this->routes as $name => $route) {
            $this->routes[$name] = clone $route;
            if ($route instanceof RouteCollection) {
                $this->routes[$name]->setParent($this);
            }
        }
    }

    




    public function getParent()
    {
        return $this->parent;
    }

    




    public function setParent(RouteCollection $parent)
    {
        $this->parent = $parent;
    }

    




    public function getIterator()
    {
        return new \ArrayIterator($this->routes);
    }

    









    public function add($name, Route $route)
    {
        if (!preg_match('/^[a-z0-9A-Z_.]+$/', $name)) {
            throw new \InvalidArgumentException(sprintf('Name "%s" contains non valid characters for a route name.', $name));
        }

        $parent = $this;
        while ($parent->getParent()) {
            $parent = $parent->getParent();
        }

        if ($parent) {
            $parent->remove($name);
        }

        $this->routes[$name] = $route;
    }

    




    public function all()
    {
        $routes = array();
        foreach ($this->routes as $name => $route) {
            if ($route instanceof RouteCollection) {
                $routes = array_merge($routes, $route->all());
            } else {
                $routes[$name] = $route;
            }
        }

        return $routes;
    }

    






    public function get($name)
    {
        
        foreach (array_reverse($this->routes) as $routes) {
            if (!$routes instanceof RouteCollection) {
                continue;
            }

            if (null !== $route = $routes->get($name)) {
                return $route;
            }
        }

        if (isset($this->routes[$name])) {
            return $this->routes[$name];
        }
    }

    




    public function remove($name)
    {
        if (isset($this->routes[$name])) {
            unset($this->routes[$name]);
        }

        foreach ($this->routes as $routes) {
            if ($routes instanceof RouteCollection) {
                $routes->remove($name);
            }
        }
    }

    









    public function addCollection(RouteCollection $collection, $prefix = '', $defaults = array(), $requirements = array())
    {
        $collection->setParent($this);
        $collection->addPrefix($prefix, $defaults, $requirements);

        
        foreach (array_keys($collection->all()) as $name) {
            $this->remove($name);
        }

        $this->routes[] = $collection;
    }

    








    public function addPrefix($prefix, $defaults = array(), $requirements = array())
    {
        
        $prefix = rtrim($prefix, '/');

        
        if ($prefix && '/' !== $prefix[0]) {
            $prefix = '/'.$prefix;
        }

        $this->prefix = $prefix.$this->prefix;

        foreach ($this->routes as $name => $route) {
            if ($route instanceof RouteCollection) {
                $route->addPrefix($prefix, $defaults, $requirements);
            } else {
                $route->setPattern($prefix.$route->getPattern());
                $route->addDefaults($defaults);
                $route->addRequirements($requirements);
            }
        }
    }

    public function getPrefix()
    {
        return $this->prefix;
    }

    




    public function getResources()
    {
        $resources = $this->resources;
        foreach ($this as $routes) {
            if ($routes instanceof RouteCollection) {
                $resources = array_merge($resources, $routes->getResources());
            }
        }

        return array_unique($resources);
    }

    




    public function addResource(ResourceInterface $resource)
    {
        $this->resources[] = $resource;
    }
}
