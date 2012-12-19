-- schema for sqlite
CREATE TABLE `users` (
  `id` int(11) NOT NULL ,
  `username` varchar(255) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `last_login` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
);

CREATE TABLE `bookmarks` (
  `id` int(11) NOT NULL ,
  `user_id` int(11) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `url` text,
  `private` tinyint(4) DEFAULT '0',
  `created_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
);

CREATE TABLE `tags` (
  `bookmark_id` int(11) DEFAULT NULL,
  `tag` varchar(255) DEFAULT NULL
);