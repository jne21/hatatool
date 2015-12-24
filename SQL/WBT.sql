-- phpMyAdmin SQL Dump
-- version 4.5.2
-- http://www.phpmyadmin.net
--
-- Хост: localhost
-- Время создания: Дек 25 2015 г., 01:44
-- Версия сервера: 10.0.21-MariaDB
-- Версия PHP: 5.6.15

SET FOREIGN_KEY_CHECKS=0;
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `WBT`
--

-- --------------------------------------------------------

--
-- Структура таблицы `admin`
--

DROP TABLE IF EXISTS `admin`;
CREATE TABLE IF NOT EXISTS `admin` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `email` varchar(255) NOT NULL,
  `login` varchar(25) NOT NULL,
  `password` varchar(40) NOT NULL,
  `state` tinyint(3) UNSIGNED NOT NULL,
  `description` text NOT NULL,
  `rights` tinyint(3) UNSIGNED NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `login` (`login`,`password`),
  KEY `name` (`name`),
  KEY `state` (`state`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `attachment`
--

DROP TABLE IF EXISTS `attachment`;
CREATE TABLE IF NOT EXISTS `attachment` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `parent_table` varchar(50) NOT NULL,
  `parent_id` int(10) UNSIGNED NOT NULL,
  `filename` varchar(255) NOT NULL,
  `description` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `parent_table` (`parent_table`,`parent_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `capability`
--

DROP TABLE IF EXISTS `capability`;
CREATE TABLE IF NOT EXISTS `capability` (
  `name` varchar(50) NOT NULL,
  `object` varchar(50) NOT NULL,
  `object_id` int(10) UNSIGNED NOT NULL,
  `value` text,
  UNIQUE KEY `name` (`name`,`object`,`object_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `course`
--

DROP TABLE IF EXISTS `course`;
CREATE TABLE IF NOT EXISTS `course` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `owner_id` int(10) UNSIGNED NOT NULL,
  `date_create` date NOT NULL,
  `date_update` date NOT NULL,
  `state` tinyint(3) UNSIGNED NOT NULL,
  `order` int(10) UNSIGNED NOT NULL,
  PRIMARY KEY (`id`),
  KEY `owner_id` (`owner_id`),
  KEY `state` (`state`),
  KEY `date_create` (`date_create`),
  KEY `date_update` (`date_update`),
  KEY `order` (`order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `course_l10n`
--

DROP TABLE IF EXISTS `course_l10n`;
CREATE TABLE IF NOT EXISTS `course_l10n` (
  `locale_id` char(2) NOT NULL,
  `parent_id` int(10) UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` mediumtext,
  `state` int(11) NOT NULL,
  `brief` text,
  PRIMARY KEY (`locale_id`,`parent_id`),
  KEY `name` (`name`),
  KEY `state` (`state`),
  KEY `parent_id` (`parent_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `exercise`
--

DROP TABLE IF EXISTS `exercise`;
CREATE TABLE IF NOT EXISTS `exercise` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `description` int(11) DEFAULT NULL,
  `script` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `lesson`
--

DROP TABLE IF EXISTS `lesson`;
CREATE TABLE IF NOT EXISTS `lesson` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `course_id` int(10) UNSIGNED NOT NULL,
  `order` int(10) UNSIGNED NOT NULL,
  PRIMARY KEY (`id`),
  KEY `course_id` (`course_id`),
  KEY `order` (`order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `lesson_l10n`
--

DROP TABLE IF EXISTS `lesson_l10n`;
CREATE TABLE IF NOT EXISTS `lesson_l10n` (
  `locale_id` char(2) NOT NULL,
  `parent_id` int(10) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` mediumtext NOT NULL,
  UNIQUE KEY `locale_id` (`locale_id`,`parent_id`),
  KEY `parent_id` (`parent_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf32;

-- --------------------------------------------------------

--
-- Структура таблицы `setup`
--

DROP TABLE IF EXISTS `setup`;
CREATE TABLE IF NOT EXISTS `setup` (
  `name` varchar(30) NOT NULL,
  `value` longtext NOT NULL,
  `desc` text NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `stage`
--

DROP TABLE IF EXISTS `stage`;
CREATE TABLE IF NOT EXISTS `stage` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `lesson_id` int(10) UNSIGNED NOT NULL,
  `exercise_id` int(10) UNSIGNED NOT NULL,
  `order` int(10) UNSIGNED NOT NULL,
  `settings` mediumtext NOT NULL,
  PRIMARY KEY (`id`),
  KEY `lesson_id` (`lesson_id`),
  KEY `order` (`order`),
  KEY `exercise_id` (`exercise_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `stage_l10n`
--

DROP TABLE IF EXISTS `stage_l10n`;
CREATE TABLE IF NOT EXISTS `stage_l10n` (
  `locale_id` char(2) NOT NULL,
  `parent_id` int(10) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` mediumtext NOT NULL,
  UNIQUE KEY `locale_id` (`locale_id`,`parent_id`),
  KEY `parent_id` (`parent_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Ограничения внешнего ключа сохраненных таблиц
--

--
-- Ограничения внешнего ключа таблицы `course`
--
ALTER TABLE `course`
  ADD CONSTRAINT `course_ibfk_1` FOREIGN KEY (`owner_id`) REFERENCES `admin` (`id`) ON UPDATE CASCADE;

--
-- Ограничения внешнего ключа таблицы `course_l10n`
--
ALTER TABLE `course_l10n`
  ADD CONSTRAINT `course_l10n_ibfk_1` FOREIGN KEY (`parent_id`) REFERENCES `course` (`id`);

--
-- Ограничения внешнего ключа таблицы `lesson`
--
ALTER TABLE `lesson`
  ADD CONSTRAINT `lesson_ibfk_1` FOREIGN KEY (`course_id`) REFERENCES `course` (`id`);

--
-- Ограничения внешнего ключа таблицы `lesson_l10n`
--
ALTER TABLE `lesson_l10n`
  ADD CONSTRAINT `lesson_l10n_ibfk_1` FOREIGN KEY (`parent_id`) REFERENCES `lesson` (`id`);

--
-- Ограничения внешнего ключа таблицы `stage`
--
ALTER TABLE `stage`
  ADD CONSTRAINT `stage_ibfk_1` FOREIGN KEY (`lesson_id`) REFERENCES `lesson` (`id`),
  ADD CONSTRAINT `stage_ibfk_2` FOREIGN KEY (`exercise_id`) REFERENCES `exercise` (`id`);

--
-- Ограничения внешнего ключа таблицы `stage_l10n`
--
ALTER TABLE `stage_l10n`
  ADD CONSTRAINT `stage_l10n_ibfk_1` FOREIGN KEY (`parent_id`) REFERENCES `stage` (`id`);
SET FOREIGN_KEY_CHECKS=1;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
