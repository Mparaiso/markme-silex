<?php










namespace Symfony\Component\HttpKernel\Log;








interface LoggerInterface
{



function emerg($message, array $context = array());




function alert($message, array $context = array());




function crit($message, array $context = array());




function err($message, array $context = array());




function warn($message, array $context = array());




function notice($message, array $context = array());




function info($message, array $context = array());




function debug($message, array $context = array());
}
