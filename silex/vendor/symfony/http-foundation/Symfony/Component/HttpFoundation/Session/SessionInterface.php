<?php










namespace Symfony\Component\HttpFoundation\Session;






interface SessionInterface
{









function start();








function getId();








function setId($id);








function getName();








function setName($name);
















function invalidate($lifetime = null);















function migrate($destroy = false, $lifetime = null);








function save();










function has($name);











function get($name, $default = null);









function set($name, $value);








function all();






function replace(array $attributes);










function remove($name);






function clear();






function registerBag(SessionBagInterface $bag);








function getBag($name);






function getMetadataBag();
}
