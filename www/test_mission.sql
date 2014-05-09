-- phpMyAdmin SQL Dump
-- version 3.5.1
-- http://www.phpmyadmin.net
--
-- Хост: 127.0.0.1
-- Время создания: Май 09 2014 г., 23:46
-- Версия сервера: 5.5.25
-- Версия PHP: 5.3.13

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- База данных: `test_mission`
--

-- --------------------------------------------------------

--
-- Структура таблицы `rooms`
--

DROP TABLE IF EXISTS `rooms`;
CREATE TABLE IF NOT EXISTS `rooms` (
  `id_room` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `weapon` varchar(20) NOT NULL,
  `checked` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `users_knb`
--

DROP TABLE IF EXISTS `users_knb`;
CREATE TABLE IF NOT EXISTS `users_knb` (
  `id_user` int(11) NOT NULL AUTO_INCREMENT,
  `login` varchar(30) NOT NULL,
  `score` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id_user`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=6 ;

--
-- Дамп данных таблицы `users_knb`
--

INSERT INTO `users_knb` (`id_user`, `login`, `score`) VALUES
(1, 'test1', 0),
(2, 'test2', 0),
(3, 'test3', 0),
(4, 'Павел', 0),
(5, 'Яра', 0);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
