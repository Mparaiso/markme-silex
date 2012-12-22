-- schema for sqlite
CREATE TABLE `bookmarks` (
  `id` INTEGER NOT NULL ,
  `user_id` int(11) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `url` text,
  `private` tinyint(4) DEFAULT '0',
  `created_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
);

CREATE TABLE `tags` (
  `bookmark_id` INTEGER DEFAULT NULL,
  `tag` varchar(255) DEFAULT NULL,
  FOREIGN KEY(bookmark_id) REFERENCES bookmarks(id) ON DELETE CASCADE

);

CREATE TABLE `users` (
  `id` INTEGER NOT NULL ,
  `username` varchar(255) DEFAULT NULL UNIQUE,
  `password` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL UNIQUE,
  `created_at` datetime DEFAULT NULL,
  `last_login` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
);