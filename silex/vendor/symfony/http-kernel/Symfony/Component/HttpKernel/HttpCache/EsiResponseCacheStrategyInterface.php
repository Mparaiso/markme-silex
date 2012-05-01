<?php














namespace Symfony\Component\HttpKernel\HttpCache;

use Symfony\Component\HttpFoundation\Response;







interface EsiResponseCacheStrategyInterface
{





function add(Response $response);






function update(Response $response);
}
