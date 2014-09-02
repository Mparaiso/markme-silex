<?php

namespace MarkMe\Entity;

class BookmarkImport {

    private $title;
    private $url;

    public function getTitle() {
        return $this->title;
    }

    public function setTitle($title) {
        $this->title = $title;
    }

    public function getUrl() {
        return $this->url;
    }

    public function setUrl($url) {
        $this->url = $url;
    }

}
