<?php










namespace Symfony\Component\HttpFoundation\Session\Attribute;

use Symfony\Component\HttpFoundation\Session\SessionBagInterface;






interface AttributeBagInterface extends SessionBagInterface
{







function has($name);









function get($name, $default = null);







function set($name, $value);






function all();






function replace(array $attributes);








function remove($name);
}
