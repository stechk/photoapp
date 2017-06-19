-- Adminer 4.2.5 MySQL dump

SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

CREATE DATABASE `auth_test` /*!40100 DEFAULT CHARACTER SET latin1 */;
USE `auth_test`;

DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `users_id` int(11) NOT NULL AUTO_INCREMENT,
  `users_roles_id` int(11) DEFAULT NULL,
  `name` varchar(100) NOT NULL,
  `lastname` varchar(100) NOT NULL,
  `password` varchar(45) NOT NULL,
  `email` varchar(100) NOT NULL,
  `last_logged` datetime DEFAULT NULL,
  `status` tinyint(1) DEFAULT '1',
  `archived` tinyint(1) DEFAULT '0',
  `created` datetime DEFAULT NULL,
  `note` text,
  `only_login` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`users_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- 2017-05-23 17:19:11
