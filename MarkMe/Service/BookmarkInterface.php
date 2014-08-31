<?php

namespace MarkMe\Service {

    use MarkMe\Entity\Bookmark as BookmarkEntity;
    use MarkMe\Entity\User as UserEntity;

    interface BookmarkInterface {

        public function count(UserEntity $user);

        public function create(BookmarkEntity $bookmark);

        public function update(BookmarkEntity $bookmark);

        public function delete(BookmarkEntity $bookmark);

        public function search($query, UserEntity $user, $limit, $offset);

        public function findByTag($tagName, UserEntity $user, $limit, $offset);

        function getAll( UserEntity $user,$limit,$offset);

        function searchTags($tags, UserEntity $user, $limit = 10);

        function getAllTags(UserEntity $user, $limit = 30);
    }

}