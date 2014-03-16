-- phpMyAdmin SQL Dump
-- version 3.5.1
-- http://www.phpmyadmin.net
--
-- Хост: 127.0.0.1
-- Время создания: Мар 16 2014 г., 13:32
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
-- Структура таблицы `cities`
--

CREATE TABLE IF NOT EXISTS `cities` (
  `id_city` int(11) NOT NULL AUTO_INCREMENT,
  `id_country` int(11) NOT NULL,
  `title` varchar(20) NOT NULL,
  PRIMARY KEY (`id_city`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;

--
-- Дамп данных таблицы `cities`
--

INSERT INTO `cities` (`id_city`, `id_country`, `title`) VALUES
(1, 1, 'Санкт-Петербург'),
(2, 1, 'Москва'),
(3, 2, 'Париж'),
(4, 2, 'Сен-Тропе');

-- --------------------------------------------------------

--
-- Структура таблицы `countries`
--

CREATE TABLE IF NOT EXISTS `countries` (
  `id_country` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(20) NOT NULL,
  PRIMARY KEY (`id_country`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

--
-- Дамп данных таблицы `countries`
--

INSERT INTO `countries` (`id_country`, `title`) VALUES
(1, 'Россия'),
(2, 'Франция');

-- --------------------------------------------------------

--
-- Структура таблицы `hotels`
--

CREATE TABLE IF NOT EXISTS `hotels` (
  `id_hotel` int(11) NOT NULL AUTO_INCREMENT,
  `id_city` int(11) NOT NULL,
  `title` varchar(30) NOT NULL,
  PRIMARY KEY (`id_hotel`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=9 ;

--
-- Дамп данных таблицы `hotels`
--

INSERT INTO `hotels` (`id_hotel`, `id_city`, `title`) VALUES
(1, 1, 'Астория'),
(2, 1, 'Спутник'),
(3, 2, 'Ленинград'),
(4, 2, 'Союз'),
(5, 3, 'Paris Tour Eiffel'),
(6, 3, 'Paris La Villette'),
(7, 4, 'Sezz Saint-Tropez'),
(8, 4, 'Pan Dei Palais ');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
