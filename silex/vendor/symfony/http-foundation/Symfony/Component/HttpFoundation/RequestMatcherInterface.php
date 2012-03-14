<?php










namespace Symfony\Component\HttpFoundation;








interface RequestMatcherInterface
{
    








    function matches(Request $request);
}
