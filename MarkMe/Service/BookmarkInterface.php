<?php

namespace MarkMe\Service {

    use MarkMe\Entity\Bookmark as BookmarkEntity;
    use MarkMe\Entity\User as UserEntity;

    interface BookmarkInterface
    {

        public function count(UserEntity $user);

        public function create(BookmarkEntity $bookmark);

        public function update(BookmarkEntity $bookmark);

        public function delete(BookmarkEntity $bookmark);

        public function search($query, UserEntity $user);

        public function getByTag($tagName, UserEntity $user);

        function getAll($offset, $limit, UserEntity $user);
    }

}