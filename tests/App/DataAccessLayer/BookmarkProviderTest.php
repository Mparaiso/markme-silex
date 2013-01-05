<?php

namespace App\DataAccessLayer {

    use App\DataAccessLayer\BookmarkProvider;
use App\DataTransferObjects\Bookmark;

    /**
     * @author M.Paraiso
     */
    class BookmarkProviderTest extends \PHPUnit_Framework_TestCase {

        /**
         * @var Silex\Application $app
         */
        protected $app;

        function setUp() {
            parent::setUp();
            $this->app = createApplication();
        }

        /**
         * @dataProvider provider
         */
        function testCreate($bookmark) {
            # un bookmark est crée
            $user_id = 1;
            $bookmarkProvider = new BookmarkProvider($this->app["db"]);
            $result = $bookmarkProvider->create($bookmark, $user_id);
            $this->assertEquals(1, $result->id);
            $result = $this->app["db"]->fetchAssoc("select * from bookmarks");
            $this->assertEquals("bookmark title", $result["title"]);
        }

        /**
         * @dataProvider provider
         */
        function testUpdate(Bookmark $bookmark) {
            // un bookmark est crée puis mis à jour
            $bookmarkProvider = new BookmarkProvider($this->app["db"]);
            $user_id = 5;
            $newBookmark = $bookmarkProvider->create($bookmark, $user_id);
            $newBookmark->title = "new bookmark title";
            $result = $bookmarkProvider->update($newBookmark);
            $this->assertEquals(1, $result);
        }

        function provider() {
            $app = createApplication();
            $bookmark = new Bookmark();
            $bookmark->title = "bookmark title";
            $bookmark->description = "bookmark description";
            $bookmark->tags = array("bookmark-tag1", "bookmark-tag2", "bookmark-tag3");
            $bookmark->url = "http://bookmark.com";
            $bookmark->created_at = $app["current_time"];
            return array(
                array($bookmark)
            );
        }

    }

}
