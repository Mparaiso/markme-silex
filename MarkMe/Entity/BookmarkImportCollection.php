<?php

namespace MarkMe\Entity;

class BookmarkImportCollection {

    /**
     *
     * @var BookmarkImport[]
     */
    private $bookmarks;

    /**
     * @return BookmarkImport[]
     */
    public function getBookmarks() {
        return $this->bookmarks;
    }

    /**
     * @param BookmarkImport[] $bookmarks
     */
    public function setBookmarks($bookmarks) {
        $this->bookmarks = $bookmarks;
    }

}
