<?php

namespace App\BusinessLogicLayer {

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
         * @dataProvider htmlProvider
         * @covers \App\BusinessLogicLayer\BookmarkManager::import
         */
        
        function testImport($html,$bookmarkCount){
            // le client importe un fichier html 
            $user_id = 2;
            $bookmarkManager = $this->app["bookmark_manager"];
            /* @var $bookmarkManager \App\BusinessLogicLayer\BookmarkManager  */
            $bookmarks = $bookmarkManager->import($html,$user_id);
            $this->assertCount($bookmarkCount, $bookmarks);
            $_bookmarks = $bookmarkManager->getAll(0, 10000, $user_id);
            $this->assertCount($bookmarkCount,$_bookmarks);
        }

        /**
         * @dataProvider provider
         */
        function testToHtml($bookmark) {
            // test si le bookmark converti en fragment html est valide
            // @var \App\BusinessLogicLayer\BookmarkManager $bookmarkManager 
            $bookmarkManager = $this->app["bookmark_manager"];
            $html = $bookmarkManager->toHtml(array($bookmark));
            $expected = "/<DT><A HREF=\"http:\/\/bookmark.com\" ".
                    "ADD_DATE=\"\d+\" LAST_VISIT=\"\d+\" ".
                    "LAST_MODIFIED=\"\d+\">bookmark title<\/A>/i";
            $this->assertRegExp($expected, $html);
        }

        /**
         * @dataProvider htmlProvider
         */
        function testSplitLinks($html,$length){
            $this->assertNotNull($html);
            /* @var \App\BusinessLogicLayer\BookmarkManager $bookmarkManager */
            $bookmarkManager=$this->app["bookmark_manager"];
            $arrayResult = $bookmarkManager->splitLinks($html);
            $this->assertCount($length,$arrayResult);
            //return $arrayResult;
        }

        /**
         * @dataProvider linksProvider
         */
        function testBookmarkFromLink($htmlLink,$title,$url){
            $bookmarkManager = $this->app["bookmark_manager"];
            $bookmark = $bookmarkManager->bookmarkFromLink($htmlLink);
            $this->assertEquals($bookmark->title,$title);
            $this->assertEquals($bookmark->url,$url);
            $this->assertEquals($bookmark->description,$title);
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

        function linksProvider(){
            return array(
                array("<a href=\"http://yahoo.com\">yahoo.com</a>",
                    "yahoo.com",
                    "http://yahoo.com"),
                array("<a href=\"http://google.com\">google.com</a>",
                    "google.com",
                    "http://google.com",
                    ),
                array("<a href=\"http://msn.com\">msn.com</a>",
                    "msn.com",
                    "http://msn.com",
                    ),
                );
        }
        

        function htmlProvider(){
            $html = <<<EOF
            <!DOCTYPE HTML>
            <html lang="en-US">
            <head>
                <meta charset="UTF-8">
                <title></title>
            </head>
            <body>
                <a href="http://yahoo.com">yahoo.com</a>
                <a href="http://google.com">google.com</a>
                <a href="http://msn.com">msn.com</a>
            </body>
            </html>
EOF;
            // un fichier de bookmarks réel , exporté de firefox
            $html2 = file_get_contents(__DIR__."/files/bookmarks.html");
            return array(
                array($html,3),
                array($html2,25)
            );
        }

    }

}
