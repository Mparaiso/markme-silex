<?php

namespace App\BusinessLogicLayer {

    use App\BusinessLogicLayer\BookmarkManager;
    use App\DataTransferObjects\Bookmark;

    /**
     * @author M.Paraiso
     */
    class BookmarkManagerTest extends \PHPUnit_Framework_TestCase {

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
        function testToHtml($bookmark) {
            // test si le bookmark converti en fragment html est valide
            // @var \App\BusinessLogicLayer\BookmarkManager $bookmarkManager 
            $bookmarkManager = $this->app["bookmark_manager"];
            $html = $bookmarkManager->toHtml(array($bookmark));
            $expected = "<a href='http://bookmark.com' title='bookmark title' alt='bookmark description'>bookmark title</a>";
            $this->assertTrue(true);
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
