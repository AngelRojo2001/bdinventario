-- phpMyAdmin SQL Dump
-- version 4.0.4
-- http://www.phpmyadmin.net
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 01-08-2013 a las 22:04:37
-- Versión del servidor: 5.5.32
-- Versión de PHP: 5.4.16

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Base de datos: `bdinventario`
--
CREATE DATABASE IF NOT EXISTS `bdinventario` DEFAULT CHARACTER SET utf8 COLLATE utf8_spanish_ci;
USE `bdinventario`;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `editorial`
--

CREATE TABLE IF NOT EXISTS `editorial` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(200) COLLATE utf8_spanish_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci AUTO_INCREMENT=5 ;

--
-- Volcado de datos para la tabla `editorial`
--

INSERT INTO `editorial` (`id`, `nombre`) VALUES
(1, 'Limusa'),
(2, 'Amigos del libro'),
(3, 'La Paz'),
(4, 'Kantuta');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `libro`
--

CREATE TABLE IF NOT EXISTS `libro` (
  `nro` int(11) NOT NULL,
  `fecha` date NOT NULL,
  `autor` varchar(200) COLLATE utf8_spanish_ci NOT NULL,
  `titulo` text COLLATE utf8_spanish_ci NOT NULL,
  `id_lug` int(11) NOT NULL,
  `id_edi` int(11) NOT NULL,
  `anio` year(4) NOT NULL,
  `edicion` varchar(10) COLLATE utf8_spanish_ci NOT NULL,
  `volumen` varchar(5) COLLATE utf8_spanish_ci NOT NULL,
  `paginas` int(11) NOT NULL,
  `compra` tinyint(1) DEFAULT NULL,
  `precio` int(11) DEFAULT NULL,
  `donac` tinyint(1) DEFAULT NULL,
  `dl` tinyint(1) DEFAULT NULL,
  `id_pro` int(11) NOT NULL,
  `observacion` text COLLATE utf8_spanish_ci,
  PRIMARY KEY (`nro`),
  KEY `id_lug` (`id_lug`,`id_edi`,`id_pro`),
  KEY `id_lug_2` (`id_lug`),
  KEY `id_edi` (`id_edi`),
  KEY `id_pro` (`id_pro`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `libro`
--

INSERT INTO `libro` (`nro`, `fecha`, `autor`, `titulo`, `id_lug`, `id_edi`, `anio`, `edicion`, `volumen`, `paginas`, `compra`, `precio`, `donac`, `dl`, `id_pro`, `observacion`) VALUES
(0, '2013-07-30', 'Danny', 'Quien es mas y por que?', 5, 2, 2012, '1', '3', 145, 1, 45, 0, 0, 1, ''),
(11111, '2013-08-01', 'Juan Carlos Ortiz Flores', 'Independencia de Bolivia', 1, 3, 2013, '1', '5', 6, 1, 12, 0, 1, 4, ''),
(25412, '0000-00-00', 'asas', 'sasas', 5, 2, 0000, '', '', 0, 0, 0, 0, 0, 1, ''),
(54545, '2013-08-01', 'Raul', 'MariÃ±o', 5, 2, 2013, '', '', 0, 1, 0, 1, 1, 1, '');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `login`
--

CREATE TABLE IF NOT EXISTS `login` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `usuario` varchar(100) COLLATE utf8_spanish_ci NOT NULL,
  `password` varchar(100) COLLATE utf8_spanish_ci NOT NULL,
  `categoria` enum('Admin','Private') COLLATE utf8_spanish_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci AUTO_INCREMENT=3 ;

--
-- Volcado de datos para la tabla `login`
--

INSERT INTO `login` (`id`, `usuario`, `password`, `categoria`) VALUES
(1, 'Danny', 'almanza', 'Admin'),
(2, 'Sandro', 'Sandro', 'Private');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `lugar`
--

CREATE TABLE IF NOT EXISTS `lugar` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(200) COLLATE utf8_spanish_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci AUTO_INCREMENT=6 ;

--
-- Volcado de datos para la tabla `lugar`
--

INSERT INTO `lugar` (`id`, `nombre`) VALUES
(1, 'Buenos Aires'),
(2, 'Cochabamba'),
(3, 'Bolivia'),
(4, 'Chuquisaca'),
(5, 'Beni');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `procedencia`
--

CREATE TABLE IF NOT EXISTS `procedencia` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(200) COLLATE utf8_spanish_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci AUTO_INCREMENT=5 ;

--
-- Volcado de datos para la tabla `procedencia`
--

INSERT INTO `procedencia` (`id`, `nombre`) VALUES
(1, 'BaÃºl del Libro'),
(2, 'LibrerÃ­a Yachaywasi'),
(3, 'Chuquisaca'),
(4, 'El Alto');

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `libro`
--
ALTER TABLE `libro`
  ADD CONSTRAINT `libro_ibfk_1` FOREIGN KEY (`id_lug`) REFERENCES `lugar` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `libro_ibfk_2` FOREIGN KEY (`id_edi`) REFERENCES `editorial` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `libro_ibfk_3` FOREIGN KEY (`id_pro`) REFERENCES `procedencia` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
