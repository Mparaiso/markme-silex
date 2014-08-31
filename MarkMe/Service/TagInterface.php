<?php

namespace MarkMe\Service {
    use MarkMe\Entity\Tag as TagEntity;


    interface TagInterface
    {
        function findWhereUserHasBookmark(\MarkMe\Entity\User $user,$tagName);

        function create(TagEntity $tag,$flush);

    }

}
