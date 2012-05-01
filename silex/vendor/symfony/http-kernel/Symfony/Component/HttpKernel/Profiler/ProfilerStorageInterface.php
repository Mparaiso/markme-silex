<?php










namespace Symfony\Component\HttpKernel\Profiler;






interface ProfilerStorageInterface
{










function find($ip, $url, $limit, $method);










function read($token);








function write(Profile $profile);




function purge();
}
