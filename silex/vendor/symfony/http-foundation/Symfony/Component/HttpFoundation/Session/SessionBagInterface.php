<?php










namespace Symfony\Component\HttpFoundation\Session;






interface SessionBagInterface
{





function getName();






function initialize(array &$array);






function getStorageKey();






function clear();
}
