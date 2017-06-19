-- Adminer 4.2.2 MySQL dump

SET NAMES utf8;
SET time_zone = '+00:00';

DROP DATABASE IF EXISTS `foto`;
CREATE DATABASE `foto` /*!40100 DEFAULT CHARACTER SET utf8 */;
USE `foto`;

DROP TABLE IF EXISTS `images`;
CREATE TABLE `images` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `op` int(255) NOT NULL,
  `file_name` varchar(255) NOT NULL,
  `filepath` varchar(255) NOT NULL,
  `timestamp` datetime NOT NULL,
  `type` enum('zamereni','montaz','servis','expedice','brilix','vyroba','reklamace','stavebni_prace','eshop') DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- 2017-05-12 08:45:43
