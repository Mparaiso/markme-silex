<?php










namespace Symfony\Component\HttpFoundation\Session\Storage;

use Symfony\Component\HttpFoundation\Session\SessionBagInterface;
use Symfony\Component\HttpFoundation\Session\Storage\MetadataBag;









interface SessionStorageInterface
{









function start();








function getId();








function setId($id);








function getName();








function setName($name);
























function regenerate($destroy = false, $lifetime = null);









function save();




function clear();










function getBag($name);






function registerBag(SessionBagInterface $bag);




function getMetadataBag();
}
