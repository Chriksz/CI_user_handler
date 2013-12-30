CREATE TABLE `user` (
  `u_user_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `u_password` char(40) NOT NULL,
  `u_email` char(100) unique DEFAULT NULL,
  `u_nickname` char(40) NOT NULL,
  `u_birth` date DEFAULT NULL,
  `u_reg_date` date DEFAULT NULL,
  `u_pwres` char(40) DEFAULT NULL,
  `u_salt` char(10) DEFAULT NULL,
  `u_valid` enum('Y','N') DEFAULT 'N',
  PRIMARY KEY (`u_user_id`),
  UNIQUE KEY `nickname` (`u_nickname`)
)
