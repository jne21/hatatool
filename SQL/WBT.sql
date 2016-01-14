-- phpMyAdmin SQL Dump
-- version 4.5.2
-- http://www.phpmyadmin.net
--
-- Хост: localhost
-- Время создания: Янв 14 2016 г., 02:28
-- Версия сервера: 10.0.21-MariaDB
-- Версия PHP: 5.6.15

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

CREATE TABLE `admin` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `login` varchar(25) NOT NULL,
  `password` varchar(40) NOT NULL,
  `state` tinyint(3) UNSIGNED NOT NULL DEFAULT '0',
  `description` text,
  `rights` tinyint(3) UNSIGNED NOT NULL DEFAULT '0',
  `date_login` datetime DEFAULT NULL,
  `date_create` datetime NOT NULL,
  `locale` char(2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `admin`
--

INSERT INTO `admin` (`id`, `name`, `email`, `login`, `password`, `state`, `description`, `rights`, `date_login`, `date_create`, `locale`) VALUES
(1, 'Евгений Кутузов', 'wbt@jne21.com', 'jne', '6cb8cc8a9f63d1414411302c7fbd9c844d10e5df', 1, 'Executive developer', 0, NULL, '2016-01-03 00:00:00', 'uk');

-- --------------------------------------------------------

--
-- Структура таблицы `attachment`
--

CREATE TABLE `attachment` (
  `id` int(10) UNSIGNED NOT NULL,
  `parent_table` varchar(50) NOT NULL,
  `parent_id` int(10) UNSIGNED NOT NULL,
  `filename` varchar(255) NOT NULL,
  `description` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `capability`
--

CREATE TABLE `capability` (
  `name` varchar(50) NOT NULL,
  `object` varchar(50) NOT NULL,
  `object_id` int(10) UNSIGNED NOT NULL,
  `value` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `course`
--

CREATE TABLE `course` (
  `id` int(10) UNSIGNED NOT NULL,
  `owner_id` int(10) UNSIGNED NOT NULL,
  `date_create` date NOT NULL,
  `date_update` date DEFAULT NULL,
  `state` tinyint(3) UNSIGNED NOT NULL,
  `order` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `course_l10n`
--

CREATE TABLE `course_l10n` (
  `locale_id` char(2) NOT NULL,
  `parent_id` int(10) UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` mediumtext,
  `state` int(11) NOT NULL,
  `brief` text,
  `title` varchar(255) NOT NULL,
  `meta` mediumtext NOT NULL,
  `url` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `exercise`
--

CREATE TABLE `exercise` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text,
  `script` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `l10n`
--

CREATE TABLE `l10n` (
  `object` varchar(100) NOT NULL,
  `locale_id` char(2) NOT NULL,
  `parent_id` int(10) UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL,
  `value` mediumtext
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `lesson`
--

CREATE TABLE `lesson` (
  `id` int(10) UNSIGNED NOT NULL,
  `course_id` int(10) UNSIGNED NOT NULL,
  `name` varchar(200) NOT NULL,
  `order` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `lesson_l10n`
--

CREATE TABLE `lesson_l10n` (
  `locale_id` char(2) NOT NULL,
  `parent_id` int(10) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `brief` text NOT NULL,
  `description` mediumtext NOT NULL,
  `title` varchar(255) NOT NULL,
  `meta` text NOT NULL,
  `url` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf32;

-- --------------------------------------------------------

--
-- Структура таблицы `login_error`
--

CREATE TABLE `login_error` (
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `ip` int(4) UNSIGNED NOT NULL,
  `login` varchar(100) NOT NULL,
  `password` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `module`
--

CREATE TABLE `module` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `url` varchar(255) NOT NULL,
  `path` varchar(255) NOT NULL,
  `order` int(11) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `redirect`
--

CREATE TABLE `redirect` (
  `id` int(10) UNSIGNED NOT NULL,
  `source` varchar(255) NOT NULL,
  `destination` varchar(255) NOT NULL,
  `order` int(10) UNSIGNED NOT NULL,
  `active` tinyint(1) NOT NULL,
  `status` int(11) NOT NULL,
  `date_request` datetime DEFAULT NULL,
  `date_create` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `redirect_query`
--

CREATE TABLE `redirect_query` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `redirect_id` int(10) UNSIGNED NOT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `HTTP_REFERER` varchar(255) DEFAULT NULL,
  `REMOTE_ADDR` varchar(15) DEFAULT NULL,
  `HTTP_USER_AGENT` text,
  `REDIRECT_URL` varchar(255) DEFAULT NULL,
  `REDIRECT_QUERY_STRING` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `setup`
--

CREATE TABLE `setup` (
  `name` varchar(30) NOT NULL,
  `value` longtext NOT NULL,
  `description` text NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `setup`
--

INSERT INTO `setup` (`name`, `value`, `description`) VALUES
('cms_locale', 'ru', 'Локализация административного интерфейса');

-- --------------------------------------------------------

--
-- Структура таблицы `stage`
--

CREATE TABLE `stage` (
  `id` int(10) UNSIGNED NOT NULL,
  `lesson_id` int(10) UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL,
  `exercise_id` int(10) UNSIGNED NOT NULL,
  `order` int(10) UNSIGNED NOT NULL,
  `settings` mediumtext NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `stage_l10n`
--

CREATE TABLE `stage_l10n` (
  `locale_id` char(2) NOT NULL,
  `parent_id` int(10) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `brief` text NOT NULL,
  `description` mediumtext NOT NULL,
  `meta` text NOT NULL,
  `title` varchar(100) NOT NULL,
  `url` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `login` (`login`,`password`),
  ADD KEY `name` (`name`),
  ADD KEY `state` (`state`);

--
-- Индексы таблицы `attachment`
--
ALTER TABLE `attachment`
  ADD PRIMARY KEY (`id`),
  ADD KEY `parent_table` (`parent_table`,`parent_id`);

--
-- Индексы таблицы `capability`
--
ALTER TABLE `capability`
  ADD UNIQUE KEY `name` (`name`,`object`,`object_id`);

--
-- Индексы таблицы `course`
--
ALTER TABLE `course`
  ADD PRIMARY KEY (`id`),
  ADD KEY `owner_id` (`owner_id`),
  ADD KEY `state` (`state`),
  ADD KEY `date_create` (`date_create`),
  ADD KEY `date_update` (`date_update`),
  ADD KEY `order` (`order`);

--
-- Индексы таблицы `course_l10n`
--
ALTER TABLE `course_l10n`
  ADD PRIMARY KEY (`locale_id`,`parent_id`),
  ADD UNIQUE KEY `url` (`url`) USING BTREE,
  ADD KEY `name` (`name`),
  ADD KEY `state` (`state`),
  ADD KEY `parent_id` (`parent_id`);

--
-- Индексы таблицы `exercise`
--
ALTER TABLE `exercise`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Индексы таблицы `l10n`
--
ALTER TABLE `l10n`
  ADD PRIMARY KEY (`object`,`locale_id`,`parent_id`,`name`),
  ADD KEY `object` (`object`,`locale_id`,`name`),
  ADD KEY `parent_id` (`parent_id`);
ALTER TABLE `l10n` ADD FULLTEXT KEY `value` (`value`);

--
-- Индексы таблицы `lesson`
--
ALTER TABLE `lesson`
  ADD PRIMARY KEY (`id`),
  ADD KEY `course_id` (`course_id`),
  ADD KEY `order` (`order`);

--
-- Индексы таблицы `lesson_l10n`
--
ALTER TABLE `lesson_l10n`
  ADD PRIMARY KEY (`locale_id`,`parent_id`) USING BTREE,
  ADD KEY `parent_id` (`parent_id`),
  ADD KEY `url` (`url`(191));

--
-- Индексы таблицы `module`
--
ALTER TABLE `module`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `url` (`url`),
  ADD KEY `order` (`order`),
  ADD KEY `name` (`name`);

--
-- Индексы таблицы `redirect`
--
ALTER TABLE `redirect`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `source` (`source`),
  ADD KEY `num` (`order`),
  ADD KEY `active` (`active`);

--
-- Индексы таблицы `redirect_query`
--
ALTER TABLE `redirect_query`
  ADD PRIMARY KEY (`id`),
  ADD KEY `redirect_id` (`redirect_id`),
  ADD KEY `dt` (`date`);

--
-- Индексы таблицы `stage`
--
ALTER TABLE `stage`
  ADD PRIMARY KEY (`id`),
  ADD KEY `lesson_id` (`lesson_id`),
  ADD KEY `order` (`order`),
  ADD KEY `exercise_id` (`exercise_id`);

--
-- Индексы таблицы `stage_l10n`
--
ALTER TABLE `stage_l10n`
  ADD UNIQUE KEY `locale_id` (`locale_id`,`parent_id`),
  ADD KEY `parent_id` (`parent_id`),
  ADD KEY `url` (`url`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `admin`
--
ALTER TABLE `admin`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=44;
--
-- AUTO_INCREMENT для таблицы `attachment`
--
ALTER TABLE `attachment`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT для таблицы `course`
--
ALTER TABLE `course`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;
--
-- AUTO_INCREMENT для таблицы `exercise`
--
ALTER TABLE `exercise`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;
--
-- AUTO_INCREMENT для таблицы `lesson`
--
ALTER TABLE `lesson`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;
--
-- AUTO_INCREMENT для таблицы `module`
--
ALTER TABLE `module`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;
--
-- AUTO_INCREMENT для таблицы `redirect`
--
ALTER TABLE `redirect`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT для таблицы `redirect_query`
--
ALTER TABLE `redirect_query`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT для таблицы `stage`
--
ALTER TABLE `stage`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;
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
  ADD CONSTRAINT `course_l10n_ibfk_1` FOREIGN KEY (`parent_id`) REFERENCES `course` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ограничения внешнего ключа таблицы `lesson`
--
ALTER TABLE `lesson`
  ADD CONSTRAINT `lesson_ibfk_1` FOREIGN KEY (`course_id`) REFERENCES `course` (`id`);

--
-- Ограничения внешнего ключа таблицы `lesson_l10n`
--
ALTER TABLE `lesson_l10n`
  ADD CONSTRAINT `lesson_l10n_ibfk_1` FOREIGN KEY (`parent_id`) REFERENCES `lesson` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ограничения внешнего ключа таблицы `redirect_query`
--
ALTER TABLE `redirect_query`
  ADD CONSTRAINT `redirect_query_ibfk_1` FOREIGN KEY (`redirect_id`) REFERENCES `redirect` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

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
  ADD CONSTRAINT `stage_l10n_ibfk_1` FOREIGN KEY (`parent_id`) REFERENCES `stage` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
