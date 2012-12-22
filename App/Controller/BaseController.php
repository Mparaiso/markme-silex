<?php

/*
 * @author M.Paraiso
 */

namespace App\Controller{


    class BaseController{

        const DB_ERR = "Database error";
        const REQ_ERR = "Request error";

        /**
         * retourne un tableau contenant l'erreur
         * @param type $errorMessage
         * @return array
         */
        function err($errorMessage){
            return array("status"=>"error", "message"=>$errorMessage);
        }

        function ok(){
            return array("status"=>"ok");
        }

    }

}
?>
