<?php










namespace Symfony\Component\Routing\Matcher\Dumper;






interface MatcherDumperInterface
{
    











    function dump(array $options = array());

    




    function getRoutes();
}
