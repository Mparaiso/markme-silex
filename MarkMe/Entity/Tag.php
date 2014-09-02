<?php 
//namespace MarkMe\Entity {
//
//
//    /**
//     * a tag
//     * @Entity(
//     * repositoryClass="\MarkMe\Service\Tag"
//     * )
//     * @Table(name="tags")
//     *
//     */
//    class Tag
//    {
//        /**
//         * @Id
//         * @Column(name="id",type="integer")
//         * @GeneratedValue(strategy="AUTO")
//         * @var integer
//         */
//        private $id;
//        /**
//         * @Column(name="name",type="string")
//         * @var string
//         */
//        private $name;
//        /**
//         * @ManyToMany(targetEntity="\MarkMe\Entity\Bookmark",inversedBy="tags")
//         * @var \MarkMe\Entity\Bookmark[];
//         */
//        private $bookmarks;
//
//        function __construct()
//        {
//            $this->bookmarks = array();
//        }
//
//        /**
//         * @param \MarkMe\Entity\Bookmark[] $bookmarks
//         */
//        public function setBookmarks($bookmarks)
//        {
//            $this->bookmarks = $bookmarks;
//        }
//
//        /**
//         * @return \MarkMe\Entity\Bookmark[]
//         */
//        public function getBookmarks()
//        {
//            return $this->bookmarks;
//        }
//
//
//        /**
//         * @param int $id
//         */
//        public function setId($id)
//        {
//            $this->id = $id;
//        }
//
//        /**
//         * @return int
//         */
//        public function getId()
//        {
//            return $this->id;
//        }
//
//        /**
//         * @param string $name
//         */
//        public function setName($name)
//        {
//            $this->name = $name;
//        }
//
//        /**
//         * @return string
//         */
//        public function getName()
//        {
//            return $this->name;
//        }
//
//        public function addBookmark(Bookmark $bookmark)
//        {
//            if (!in_array($bookmark, $this->bookmarks)) {
//                $this->bookmarks[] = $bookmark;
//            }
//        }
//
//    }
//
//}
