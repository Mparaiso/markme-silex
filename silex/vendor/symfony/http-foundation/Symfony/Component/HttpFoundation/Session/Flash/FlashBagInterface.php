<?php










namespace Symfony\Component\HttpFoundation\Session\Flash;

use Symfony\Component\HttpFoundation\Session\SessionBagInterface;






interface FlashBagInterface extends SessionBagInterface
{






function add($type, $message);







function set($type, $message);









function peek($type, array $default = array());






function peekAll();









function get($type, array $default = array());






function all();




function setAll(array $messages);








function has($type);






function keys();
}
