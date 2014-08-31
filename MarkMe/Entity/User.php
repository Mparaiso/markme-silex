<?php

namespace MarkMe\Entity {

    #use  MarkMe\Entity\Role as RoleEntity;
    use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
    #use Symfony\Component\Security\Core\Role\Role;
    use Symfony\Component\Security\Core\User\UserInterface;
    use Symfony\Component\Validator\Constraints\Email;
    use Symfony\Component\Validator\Constraints\Length;
    use Symfony\Component\Validator\Mapping\ClassMetadata;

    /**
     * Class User
     * @package MarkMe\Entity
     * @Entity(repositoryClass="\MarkMe\Service\User")
     * @Table(name="users")
     * @HasLifecycleCallbacks
     */
    class User implements UserInterface, \JsonSerializable
    {
        /**
         * @Column(name="id",type="integer",length=11,unique=true)
         * @Id
         * @GeneratedValue(strategy="AUTO")
         * @var integer
         */
        private $id;
        /**
         * @Column(name="username",type="string",length=100,unique=true)
         * @Index
         * @var string
         */
        private $username;
        /**
         * @Column(name="email",type="string",length=100,unique=true)
         * @Index
         * @var string
         */
        private $email;
        /**
         * @Column(name="password",type="string")
         * @var string
         */
        private $password;
        /**
         * @Column(name="created_at",type="datetime")
         * @var \DateTime
         */
        private $created_at;
        /**
         * @Column(name="updated_at",type="datetime")
         * @var \DateTime
         */
        private $updated_at;
        /**
         * @Column(name="salt",type="string",length=200)
         * @var string
         */
        private $salt;
        /**
         * @Column(name="last_login",type="datetime")
         * @var \DateTime
         */
        private $last_login;

        /**
         * @OneToMany(targetEntity="\MarkMe\Entity\Bookmark",mappedBy="user")
         * @var Bookmark[]
         */
        private $bookmarks;

        function __construct()
        {
            $this->bookmarks = array();
        }

        /**
         * Returns the roles granted to the user.
         *
         * <code>
         * public function getRoles()
         * {
         *     return array('ROLE_USER');
         * }
         * </code>
         *
         * Alternatively, the roles might be stored on a ``roles`` property,
         * and populated in any number of different ways when the user object
         * is created.
         *
         * @return Role[] The user roles
         */
        public function getRoles()
        {
            return array('ROLE_USER');
        }

        /**
         * Returns the password used to authenticate the user.
         *
         * This should be the encoded password. On authentication, a plain-text
         * password will be salted, encoded, and then compared to this value.
         *
         * @return string The password
         */
        public function getPassword()
        {
            return $this->password;
        }

        /**
         * Returns the salt that was originally used to encode the password.
         *
         * This can return null if the password was not encoded using a salt.
         *
         * @return string|null The salt
         */
        public function getSalt()
        {
            return $this->salt;
        }

        /**
         * Returns the username used to authenticate the user.
         *
         * @return string The username
         */
        public function getUsername()
        {
            return $this->username;
        }

        /**
         * Removes sensitive data from the user.
         *
         * This is important if, at any given point, sensitive information like
         * the plain-text password is stored on this object.
         */
        public function eraseCredentials()
        {
            $this->password = null;
        }

        /**
         * @param \DateTime $created_at
         */
        public function setCreatedAt($created_at)
        {
            $this->created_at = $created_at;
        }

        /**
         * @return \DateTime
         */
        public function getCreatedAt()
        {
            return $this->created_at;
        }

        /**
         * @param string $email
         */
        public function setEmail($email)
        {
            $this->email = $email;
        }

        /**
         * @return string
         */
        public function getEmail()
        {
            return $this->email;
        }

        /**
         * @param int $id
         */
        public function setId($id)
        {
            $this->id = $id;
        }

        /**
         * @return int
         */
        public function getId()
        {
            return $this->id;
        }

        /**
         * @param \DateTime $last_login
         */
        public function setLastLogin($last_login)
        {
            $this->last_login = $last_login;
        }

        /**
         * @return \DateTime
         */
        public function getLastLogin()
        {
            return $this->last_login;
        }

        /**
         * @param \DateTime $updated_at
         */
        public function setUpdatedAt($updated_at)
        {
            $this->updated_at = $updated_at;
        }

        /**
         * @return \DateTime
         */
        public function getUpdatedAt()
        {
            return $this->updated_at;
        }

        /**
         * @param string $password
         */
        public function setPassword($password)
        {
            $this->password = $password;
        }

        /**
         * @param string $salt
         */
        public function setSalt($salt)
        {
            $this->salt = $salt;
        }

        /**
         * @param string $username
         */
        public function setUsername($username)
        {
            $this->username = $username;
        }

        /**
         * @param \MarkMe\Entity\Bookmark[] $bookmarks
         */
        public function setBookmarks($bookmarks)
        {
            $this->bookmarks = $bookmarks;
        }

        /**
         * @return \MarkMe\Entity\Bookmark[]
         */
        public function getBookmarks()
        {
            return $this->bookmarks;
        }


        /**
         * @PrePersist
         */
        function prePersist()
        {
            $this->setUpdatedAt(new \DateTime());
            $this->setLastLogin(new \DateTime());
            if ($this->getCreatedAt() == null) {
                $this->setCreatedAt(new \DateTime());
            }
        }

        function __toString()
        {
            return $this->getUsername();
        }


        /**
         * (PHP 5 &gt;= 5.4.0)<br/>
         * Specify data which should be serialized to JSON
         * @link http://php.net/manual/en/jsonserializable.jsonserialize.php
         * @return mixed data which can be serialized by <b>json_encode</b>,
         * which is a value of any type other than a resource.
         */
        public function jsonSerialize()
        {
            return get_object_vars($this);
        }

        public static function loadValidatorMetadata(ClassMetadata $metadata)
        {
            $metadata->addConstraint(new UniqueEntity(array('fields' => 'email')));
            $metadata->addConstraint(new UniqueEntity(array('fields' => 'username')));
            $metadata->addPropertyConstraint('username', new Length(array('min' => 5, 'max' => 100)));
            $metadata->addPropertyConstraint('email', new Length(array('min' => 8, 'max' => 100)));
            $metadata->addPropertyConstraint('email', new Email());
        }
    }
}