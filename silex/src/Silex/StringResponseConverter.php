<?php










namespace Silex;

use Symfony\Component\HttpFoundation\Response;






class StringResponseConverter
{
    






    public function convert($response)
    {
        if (!$response instanceof Response) {
            return new Response((string) $response);
        }

        return $response;
    }
}
