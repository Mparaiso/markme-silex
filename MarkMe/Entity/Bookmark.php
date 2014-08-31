<?php

namespace MarkMe\Entity {

    use \Symfony\Component\Serializer\Normalizer\NormalizableInterface;
    use \Symfony\Component\Serializer\Normalizer\NormalizerInterface;

    /**
     * a bookmark
     * @Entity(repositoryClass="\MarkMe\Service\Bookmark")
     * @Table(name="bookmarks")
     * @HasLifecycleCallbacks
     */
    class Bookmark implements NormalizableInterface {

        /**
         * @Column(name="id",type="integer",length=11,unique=true)
         * @Id
         * @GeneratedValue(strategy="AUTO")
         * @var integer
         */
        private $id;

        /**
         * @ManyToOne(targetEntity="\MarkMe\Entity\User",inversedBy="bookmarks")
         * @var \MarkMe\Entity\User
         */
        private $user;

        /**
         * @Column(name="description",type="string",length=255)
         * @var string
         */
        private $description;

        /**
         * @Column(name="url",type="string")
         * @var string
         */
        private $url;

        /**
         * @Column(name="title",type="string",length=255)
         * @var string
         */
        private $title;

        /**
         * @Column(name="created_at",type="datetime")
         * @var \DateTime
         */
        private $createdAt;

        /**
         * @Column(name="updated_at",type="datetime")
         * @var \DateTime
         */
        private $updatedAt;

        /**
         * @Column(name="tags",type="simple_array")
         * @var array
         */
        private $tags;

        /**
         * @Column(name="private",type="boolean")
         * @var boolean
         */
        private $private;

        public function __construc() {
            $this->tags = array();
        }

        public function addTag($tag) {
            $this->tags[] = $tag;
        }

        public function removeTag($tag) {
            $index = array_search($tag, $this->tags);
            if ($index != false) {
                unset($this->tags[$index]);
            }
        }

        /**
         * @param \DateTime $createdAt
         */
        public function setCreatedAt($createdAt) {
            $this->createdAt = $createdAt;
        }

        /**
         * @return \DateTime
         */
        public function getCreatedAt() {
            return $this->createdAt;
        }

        /**
         * @param string $description
         */
        public function setDescription($description) {
            $this->description = $description;
        }

        /**
         * @return string
         */
        public function getDescription() {
            return $this->description;
        }

        /**
         * @param int $id
         */
        public function setId($id) {
            $this->id = $id;
        }

        /**
         * @return int
         */
        public function getId() {
            return $this->id;
        }

        /**
         * @param boolean $private
         */
        public function setPrivate($private) {
            $this->private = $private;
        }

        /**
         * @return boolean
         */
        public function getPrivate() {
            return $this->private;
        }

        /**
         * @param \MarkMe\Entity\Tag[] $tags
         */
        public function setTags($tags) {
            $this->tags = $tags;
        }

        /**
         * @return \MarkMe\Entity\Tag[]
         */
        public function getTags() {
            return $this->tags;
        }

        /**
         * @param string $title
         */
        public function setTitle($title) {
            $this->title = $title;
        }

        /**
         * @return string
         */
        public function getTitle() {
            return $this->title;
        }

        /**
         * @param \DateTime $updatedAt
         */
        public function setUpdatedAt($updatedAt) {
            $this->updatedAt = $updatedAt;
        }

        /**
         * @return \DateTime
         */
        public function getUpdatedAt() {
            return $this->updatedAt;
        }

        /**
         * @param string $url
         */
        public function setUrl($url) {
            $this->url = $url;
        }

        /**
         * @return string
         */
        public function getUrl() {
            return $this->url;
        }

        /**
         * @param \MarkMe\Entity\User $user
         */
        public function setUser($user) {
            $this->user = $user;
        }

        /**
         * @return \MarkMe\Entity\User
         */
        public function getUser() {
            return $this->user;
        }

        /**
         * @PrePersist
         */
        protected function prePersist() {
            $this->setUpdatedAt(new \DateTime());
            if ($this->getCreatedAt() == NULL) {
                $this->setCreatedAt(new \DateTime());
            }
        }

        public function normalize(NormalizerInterface $normalizer, $format = null, array $context = array()) {
            return array(
                'createdAt' => $this->createdAt,
                'description' => $this->description,
                'id' => $this->id,
                'private' => $this->private,
                'tags' => $this->tags,
                'title' => $this->title,
                'updatedAt' => $this->updatedAt,
                'url' => $this->url,
                'user' => $this->user
            );
        }

    }

}
