<?php










namespace Symfony\Component\HttpFoundation\Session\Storage\Handler;







if (version_compare(phpversion(), '5.4.0', '>=')) {
class NativeSessionHandler extends \SessionHandler {}
} else {
class NativeSessionHandler {}
}
