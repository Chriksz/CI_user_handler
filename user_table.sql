CREATE TABLE `user` (
  `user_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `password` char(40) NOT NULL,
  `email` char(100) DEFAULT NULL,
  `nickname` char(40) NOT NULL,
  `birth` date DEFAULT NULL,
  `reg_date` date DEFAULT NULL,
  `pwres` char(40) DEFAULT NULL,
  `salt` char(10) DEFAULT NULL,
  `valid` enum('Y','N') DEFAULT 'N',
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `nickname` (`nickname`)
)
