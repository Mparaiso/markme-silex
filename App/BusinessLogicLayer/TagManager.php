<?php

namespace App\BusinessLogicLayer{

    use App\DataAccessLayer\ITagProvider;
    use App\DataTransfertObject\Tag;

    class TagManager{
        /**
         * @var ITagProvider $tagProvider
         */
        protected $tagProvider;

        function __construct(ITagProvider $tagProvider){
            $this->tagProvider = $tagProvider;
        }
        /**
         * obtient la liste des $tags d'un utilisateur
         * @return array
         */
        function get($user_id){
            return $this->tagProvider->get($user_id);
        }

        /**
         * retourne une liste de tags suivant leurs nom
         * @return array
         */
        function search($expression,$user_id){
            return $this->tagProvider->search($expression,$user_id);
        }

    }

}
