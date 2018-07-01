-- phpMyAdmin SQL Dump
-- version 4.7.7
-- https://www.phpmyadmin.net/
--
-- Servidor: localhost:3306
-- Tiempo de generación: 01-07-2018 a las 14:54:43
-- Versión del servidor: 10.1.34-MariaDB
-- Versión de PHP: 5.6.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `corpora1_franklinsgold`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `moneda`
--

CREATE TABLE `moneda` (
  `codigo` int(11) NOT NULL,
  `fecha_elaboracion` varchar(15) NOT NULL,
  `diametro` float NOT NULL,
  `espesor` float NOT NULL,
  `composicion` varchar(45) NOT NULL,
  `peso` int(11) NOT NULL,
  `origen_idorigen` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `moneda`
--

INSERT INTO `moneda` (`codigo`, `fecha_elaboracion`, `diametro`, `espesor`, `composicion`, `peso`, `origen_idorigen`) VALUES
(1, '3/10/2018', 62.29, 19.12, 'Crossfire', 72, 1),
(2, '7/4/2017', 76.92, 50.09, 'Explorer', 76, 2),
(3, '8/9/2017', 57.64, 30.22, '600', 34, 3),
(4, '6/28/2018', 74.08, 81.89, 'Tahoe', 94, 4),
(5, '4/7/2018', 45.46, 79.59, 'Lancer', 99, 5),
(6, '5/8/2018', 74.96, 50.24, 'Flex', 41, 6),
(7, '9/28/2017', 31.39, 30.33, 'Maxima', 62, 7),
(8, '6/1/2018', 55.24, 7.79, 'XG300', 44, 8),
(9, '8/5/2017', 66.06, 99.68, 'Regal', 16, 9),
(10, '4/12/2018', 22.51, 92.07, 'Sportvan G10', 45, 10);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `origen`
--

CREATE TABLE `origen` (
  `idorigen` int(11) NOT NULL,
  `nombre` varchar(45) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `origen`
--

INSERT INTO `origen` (`idorigen`, `nombre`) VALUES
(1, 'Morelos'),
(2, 'Tubli'),
(3, 'Mvomero'),
(4, 'Sumberwaru'),
(5, 'Athabasca'),
(6, 'Huangdao'),
(7, 'Kamárai'),
(8, 'Depapre'),
(9, 'Skutskär'),
(10, 'Jiangmen');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sucursal`
--

CREATE TABLE `sucursal` (
  `idsucursal` int(11) NOT NULL,
  `nombre` varchar(45) NOT NULL,
  `direccion` varchar(45) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `sucursal`
--

INSERT INTO `sucursal` (`idsucursal`, `nombre`, `direccion`) VALUES
(1, 'Jedlnia-Letnisko', 'Poland'),
(2, 'San Juan de los Morros', 'Venezuela'),
(3, 'Kodyma', 'Ukraine'),
(4, 'Pamedaran', 'Indonesia'),
(5, 'Aral', 'Kazakhstan'),
(6, 'A’ershan', 'China'),
(7, 'San Jerónimo', 'Peru'),
(8, 'Charleston', 'United States'),
(9, 'Sulahan', 'Indonesia'),
(10, 'Lubao', 'China');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `transaccion`
--

CREATE TABLE `transaccion` (
  `usuario_idusuario` int(11) NOT NULL,
  `moneda_codigo` int(11) NOT NULL,
  `fecha` date NOT NULL,
  `sucursal_idsucursal` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `transaccion`
--

INSERT INTO `transaccion` (`usuario_idusuario`, `moneda_codigo`, `fecha`, `sucursal_idsucursal`) VALUES
(1, 7, '0000-00-00', 2),
(2, 6, '0000-00-00', 5),
(5, 2, '0000-00-00', 3),
(7, 5, '0000-00-00', 4),
(7, 10, '0000-00-00', 5),
(8, 1, '0000-00-00', 7),
(8, 3, '0000-00-00', 9),
(8, 7, '0000-00-00', 8),
(10, 2, '0000-00-00', 5),
(10, 9, '0000-00-00', 9);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuario`
--

CREATE TABLE `usuario` (
  `idusuario` int(11) NOT NULL,
  `primer_nombre` varchar(45) NOT NULL,
  `segundo_nombre` varchar(45) NOT NULL,
  `primer_apellido` varchar(45) NOT NULL,
  `segundo_apellido` varchar(45) NOT NULL,
  `usuario` varchar(45) NOT NULL,
  `contraseña` varchar(45) NOT NULL,
  `sexo` varchar(45) NOT NULL,
  `telefono` int(11) NOT NULL,
  `correo` varchar(45) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `usuario`
--

INSERT INTO `usuario` (`idusuario`, `primer_nombre`, `segundo_nombre`, `primer_apellido`, `segundo_apellido`, `usuario`, `contraseña`, `sexo`, `telefono`, `correo`) VALUES
(1, 'Adrian', 'De witt', 'Silbersak', 'Ridett', 'dridett0', 'IVkgWfqCma7', 'Male', 239, 'dridett0@indiatimes.com'),
(2, 'Truda', 'Margy', 'Filtness', 'Towndrow', 'mtowndrow1', 'd9DfYh7jbPT', 'Female', 201, 'mtowndrow1@addthis.com'),
(3, 'Hinda', 'Caryn', 'Kleinplac', 'Faulks', 'cfaulks2', 'vh3btkAaTtj', 'Female', 634, 'cfaulks2@hp.com'),
(4, 'Levi', 'Moishe', 'Giggs', 'Maty', 'mmaty3', 'O8hAUSfd7CB', 'Male', 441, 'mmaty3@seattletimes.com'),
(5, 'Franklyn', 'Thornton', 'Dyzart', 'Proudlock', 'tproudlock4', 'VFHqYkw3n4a', 'Male', 583, 'tproudlock4@chron.com'),
(6, 'Jethro', 'Dmitri', 'Rapper', 'Eggerton', 'deggerton5', 'zGrk5SJn7', 'Male', 818, 'deggerton5@oracle.com'),
(7, 'Philippe', 'Christie', 'Yitzhakof', 'Coatman', 'ccoatman6', 'uzGNwVuUV3T', 'Female', 827, 'ccoatman6@amazon.com'),
(8, 'Batsheva', 'Debor', 'Bordessa', 'Poschel', 'dposchel7', 'jtDi4BhtVN1E', 'Female', 542, 'dposchel7@house.gov'),
(9, 'Adoree', 'Lanita', 'Constantine', 'Twitching', 'ltwitching8', 'AnbeNi8A2q1q', 'Female', 868, 'ltwitching8@unesco.org'),
(10, 'Stanley', 'Schuyler', 'Dmych', 'Hodcroft', 'shodcroft9', '5FLmHPSA', 'Male', 593, 'shodcroft9@archive.org');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `moneda`
--
ALTER TABLE `moneda`
  ADD PRIMARY KEY (`codigo`,`origen_idorigen`),
  ADD KEY `fk_moneda_origen1_idx` (`origen_idorigen`);

--
-- Indices de la tabla `origen`
--
ALTER TABLE `origen`
  ADD PRIMARY KEY (`idorigen`);

--
-- Indices de la tabla `sucursal`
--
ALTER TABLE `sucursal`
  ADD PRIMARY KEY (`idsucursal`);

--
-- Indices de la tabla `transaccion`
--
ALTER TABLE `transaccion`
  ADD PRIMARY KEY (`usuario_idusuario`,`moneda_codigo`,`fecha`,`sucursal_idsucursal`),
  ADD KEY `fk_usuario_has_moneda_moneda1_idx` (`moneda_codigo`),
  ADD KEY `fk_usuario_has_moneda_usuario1_idx` (`usuario_idusuario`),
  ADD KEY `fk_transaccion_sucursal1_idx` (`sucursal_idsucursal`);

--
-- Indices de la tabla `usuario`
--
ALTER TABLE `usuario`
  ADD PRIMARY KEY (`idusuario`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `moneda`
--
ALTER TABLE `moneda`
  MODIFY `codigo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de la tabla `origen`
--
ALTER TABLE `origen`
  MODIFY `idorigen` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de la tabla `sucursal`
--
ALTER TABLE `sucursal`
  MODIFY `idsucursal` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de la tabla `usuario`
--
ALTER TABLE `usuario`
  MODIFY `idusuario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `moneda`
--
ALTER TABLE `moneda`
  ADD CONSTRAINT `fk_moneda_origen1` FOREIGN KEY (`origen_idorigen`) REFERENCES `origen` (`idorigen`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Filtros para la tabla `transaccion`
--
ALTER TABLE `transaccion`
  ADD CONSTRAINT `fk_transaccion_sucursal1` FOREIGN KEY (`sucursal_idsucursal`) REFERENCES `sucursal` (`idsucursal`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_usuario_has_moneda_moneda1` FOREIGN KEY (`moneda_codigo`) REFERENCES `moneda` (`codigo`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_usuario_has_moneda_usuario1` FOREIGN KEY (`usuario_idusuario`) REFERENCES `usuario` (`idusuario`) ON DELETE NO ACTION ON UPDATE NO ACTION;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
