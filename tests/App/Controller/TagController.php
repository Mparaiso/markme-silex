<?php

namespace App\Controller {

    use Silex\Application;
use Doctrine\DBAL\DBALException;

    /**
     * Description of TagController
     *
     * @author M.Paraiso
     */
    class TagController {

        /**
         * Obtenir les tags d'un utilisateur
         */
        function get(Application $app) {
            $user_id = $app["session"]->get("user_id");
            if (isset($user_id)):
                try {
                    $tags = $app["db"]->fetchAll("SELECT tag, COUNT(*) AS `count` FROM" .
                            " tags INNER JOIN bookmarks ON bookmarks.id =" .
                            " tags.bookmark_id WHERE user_id = :id GROUP BY" .
                            " tag ORDER BY COUNT(*) DESC", array("id" => $user_id));
                    if (isset($tags)):
                        return $app->json(array_merge($tags, array("status" => "ok")), 200);
                    endif;
                } catch (DBALException $exc) {
                    $app["logger"]->addInfo($exc->getTraceAsString());
                    return $app->json(array("status" => "error", "message" => "Tags not available"), 200);
                }
            endif;
            return $app->json(array("status" => "error", "message" => "no user"), 200);
        }

    }

}
?>
