-- phpMyAdmin SQL Dump
-- version 4.7.4
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 30-08-2018 a las 03:04:52
-- Versión del servidor: 10.1.26-MariaDB
-- Versión de PHP: 7.1.9

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
-- Estructura de tabla para la tabla `comercio_afiliado`
--

CREATE TABLE `comercio_afiliado` (
  `id_comercio_afiliado` int(11) UNSIGNED NOT NULL,
  `nombre` varchar(45) NOT NULL,
  `direccion` varchar(100) NOT NULL,
  `sucursal` varchar(45) NOT NULL,
  `id_user` int(11) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `comercio_afiliado`
--

INSERT INTO `comercio_afiliado` (`id_comercio_afiliado`, `nombre`, `direccion`, `sucursal`, `id_user`) VALUES
(5, 'GammaExpres4', 'petare dos pisos ta ta', 'petareee', 65),
(6, 'GammaExpres4', '1600 chelsea drive', 'petaree', 67),
(9, 'Daka', 'lasd lasd', 'LasAdjuntas', 75);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `divisa`
--

CREATE TABLE `divisa` (
  `id_divisa` int(11) NOT NULL,
  `nombre_divisa` varchar(32) NOT NULL,
  `precio_dolares` float NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `divisa`
--

INSERT INTO `divisa` (`id_divisa`, `nombre_divisa`, `precio_dolares`) VALUES
(2, 'EURO', 1.145),
(3, 'Bolívar Soberano', 2),
(4, 'Oro Franklin', 45),
(5, 'Plata Franklin', 10);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `documentos`
--

CREATE TABLE `documentos` (
  `id_documento` int(11) NOT NULL,
  `documento_identidad` varchar(200) NOT NULL,
  `pasaporte` varchar(200) DEFAULT NULL,
  `rif` varchar(200) DEFAULT NULL,
  `referencia_bancaria_1` varchar(200) DEFAULT NULL,
  `referencia_bancaria_2` varchar(200) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `documentos`
--

INSERT INTO `documentos` (`id_documento`, `documento_identidad`, `pasaporte`, `rif`, `referencia_bancaria_1`, `referencia_bancaria_2`) VALUES
(4, 'http://localhost/franklinsgold/framework/views/img/documentos/usuarios/documentoidentidad59.png', 'http://localhost/franklinsgold/framework/views/img/documentos/usuarios/pasaporte59.png', 'http://localhost/franklinsgold/framework/views/img/documentos/usuarios/rif59.png', 'http://localhost/franklinsgold/framework/views/img/documentos/usuarios/primerareferenciabancaria59.png', 'http://localhost/franklinsgold/framework/views/img/documentos/usuarios/segundareferenciabancaria59.png');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `moneda`
--

CREATE TABLE `moneda` (
  `codigo` int(11) UNSIGNED NOT NULL,
  `fecha_elaboracion` int(20) UNSIGNED NOT NULL,
  `diametro` float UNSIGNED NOT NULL,
  `espesor` float UNSIGNED NOT NULL,
  `composicion` varchar(32) DEFAULT NULL,
  `peso` float UNSIGNED NOT NULL,
  `codigo_qr` varchar(200) NOT NULL,
  `qr_alfanumerico` varchar(30) NOT NULL,
  `id_origen` int(11) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `moneda`
--

INSERT INTO `moneda` (`codigo`, `fecha_elaboracion`, `diametro`, `espesor`, `composicion`, `peso`, `codigo_qr`, `qr_alfanumerico`, `id_origen`) VALUES
(12, 1532404026, 4, 4, 'plata', 5, 'http://localhost/franklinsgold/framework//views/img/codigos/monedas/12.png', 'VEN 004 004 PLA 005 23072018', 1),
(13, 1532404776, 1, 1, 'oro', 1, 'http://localhost/franklinsgold/framework//views/img/codigos/monedas/13.png', 'VEN 001 001 ORO 001 23072018', 1),
(14, 1534295995, 0.1, 0.1, 'oro', 0.1, 'http://localhost/franklinsgold/framework//views/img/codigos/monedas/14.png', 'VEN 000014 0.1 0.1 ORO 0.1 140', 1),
(15, 1535558679, 1, 2, 'plata', 2, 'http://localhost/franklinsgold/framework/views/img/codigos/monedas/15.png', 'VEN 000015 001 002 PLA 002 290', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `orden`
--

CREATE TABLE `orden` (
  `id_orden` int(11) UNSIGNED NOT NULL,
  `id_usuario` int(11) UNSIGNED NOT NULL,
  `id_sucursal` int(11) UNSIGNED DEFAULT NULL,
  `tipo_gramo` varchar(5) NOT NULL,
  `cantidad` float UNSIGNED NOT NULL,
  `precio` float NOT NULL,
  `tipo_orden` int(11) UNSIGNED NOT NULL,
  `estado` int(11) UNSIGNED NOT NULL,
  `fecha` int(11) UNSIGNED NOT NULL,
  `codigo_moneda` int(11) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `orden`
--

INSERT INTO `orden` (`id_orden`, `id_usuario`, `id_sucursal`, `tipo_gramo`, `cantidad`, `precio`, `tipo_orden`, `estado`, `fecha`, `codigo_moneda`) VALUES
(3, 50, NULL, 'oro', 3, 1207.08, 1, 3, 1535330586, NULL),
(4, 50, NULL, 'oro', 4, 1207.08, 1, 4, 1535330608, NULL),
(5, 59, NULL, 'oro', 666, 1207.36, 1, 4, 1535417022, NULL),
(6, 59, NULL, 'oro', 1, 1207.36, 2, 4, 1535417094, NULL),
(7, 59, NULL, 'oro', 1, 1207.36, 1, 4, 1535426762, NULL),
(8, 59, NULL, 'oro', 1, 1207.36, 1, 1, 1535427028, NULL),
(9, 59, NULL, 'oro', 1, 1207.36, 1, 1, 1535427823, NULL),
(10, 72, NULL, 'oro', 1, 1210.87, 1, 4, 1535515256, NULL),
(11, 72, NULL, 'oro', 1, 1210.87, 1, 4, 1535515441, NULL),
(12, 76, NULL, 'oro', 2, 1210.87, 1, 4, 1535517128, NULL),
(13, 15, NULL, 'oro', 25199.6, 1205.5, 1, 4, 1535584019, NULL),
(14, 15, NULL, 'plata', 5.67, 14.87, 1, 1, 1535584924, NULL),
(15, 15, NULL, 'oro', 25199.6, 1205.5, 2, 1, 1535585396, NULL),
(16, 15, NULL, 'plata', 5.67, 14.87, 2, 1, 1535585415, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `orden_en_espera`
--

CREATE TABLE `orden_en_espera` (
  `id` int(11) UNSIGNED NOT NULL,
  `id_usuario_cliente` int(11) UNSIGNED DEFAULT NULL,
  `id_usuario_vendedor` int(11) UNSIGNED DEFAULT NULL,
  `tipo_gramo` varchar(5) NOT NULL,
  `cantidad` int(11) UNSIGNED NOT NULL,
  `codigo_confirmacion` varchar(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `orden_en_espera`
--

INSERT INTO `orden_en_espera` (`id`, `id_usuario_cliente`, `id_usuario_vendedor`, `tipo_gramo`, `cantidad`, `codigo_confirmacion`) VALUES
(1, 59, 72, 'oro', 1, 'h5b86c3e');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `origen`
--

CREATE TABLE `origen` (
  `id_origen` int(11) UNSIGNED NOT NULL,
  `nombre` varchar(32) NOT NULL,
  `abreviatura` varchar(5) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `origen`
--

INSERT INTO `origen` (`id_origen`, `nombre`, `abreviatura`) VALUES
(1, 'Venezuela', 'VEN'),
(2, 'dsad', 'asd'),
(3, 'dsds', 'sss'),
(4, 'aa', 'ppp');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `rango`
--

CREATE TABLE `rango` (
  `id_rango` int(11) NOT NULL,
  `nombre_rango` varchar(32) NOT NULL,
  `monto_diario` float UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `rango`
--

INSERT INTO `rango` (`id_rango`, `nombre_rango`, `monto_diario`) VALUES
(3, 'Simple', 1234.34),
(4, 'Medio', 10),
(5, 'Premiun', 100000000000);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sucursal`
--

CREATE TABLE `sucursal` (
  `id_sucursal` int(11) UNSIGNED NOT NULL,
  `nombre` varchar(45) NOT NULL,
  `telefono` varchar(11) NOT NULL,
  `direccion` varchar(45) NOT NULL,
  `id_user` int(11) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `sucursal`
--

INSERT INTO `sucursal` (`id_sucursal`, `nombre`, `telefono`, `direccion`, `id_user`) VALUES
(12, 'Laesquina', '04168352573', '1600 chelsea drive', 73);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `telefono`
--

CREATE TABLE `telefono` (
  `id_comercio_afiliado` int(11) UNSIGNED NOT NULL,
  `telefono` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `telefono`
--

INSERT INTO `telefono` (`id_comercio_afiliado`, `telefono`) VALUES
(5, '04168352577'),
(5, '02122380193'),
(6, '04124211142'),
(9, '04163525453');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `transaccion`
--

CREATE TABLE `transaccion` (
  `id_transaccion` int(11) UNSIGNED NOT NULL,
  `fecha` int(100) UNSIGNED NOT NULL,
  `tipo` tinyint(2) UNSIGNED NOT NULL,
  `codigo_moneda` int(11) UNSIGNED NOT NULL,
  `codigo_moneda2` int(11) UNSIGNED DEFAULT NULL,
  `precio_moneda` int(11) UNSIGNED NOT NULL,
  `precio_moneda2` int(11) UNSIGNED DEFAULT NULL,
  `id_usuario` int(11) UNSIGNED NOT NULL,
  `id_usuario2` int(11) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `transaccion_en_espera`
--

CREATE TABLE `transaccion_en_espera` (
  `id` int(11) UNSIGNED NOT NULL,
  `id_usuario` int(11) UNSIGNED NOT NULL,
  `codigo_qr_moneda` varchar(30) NOT NULL,
  `token_confirmacion` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `users`
--

CREATE TABLE `users` (
  `id_user` int(11) UNSIGNED NOT NULL,
  `primer_nombre` varchar(45) NOT NULL,
  `segundo_nombre` varchar(45) NOT NULL,
  `primer_apellido` varchar(45) NOT NULL,
  `segundo_apellido` varchar(45) NOT NULL,
  `tipo` tinyint(2) UNSIGNED NOT NULL COMMENT '0: admin, 1: vendedor, 2: cliente,3:supervisor',
  `es_sucursal` int(11) DEFAULT NULL COMMENT '1:si',
  `es_comercio_afiliado` int(11) DEFAULT NULL,
  `id_sucursal` int(11) UNSIGNED DEFAULT NULL,
  `id_comercio_afiliado` int(11) UNSIGNED DEFAULT NULL,
  `tipo_cliente` varchar(32) DEFAULT NULL COMMENT 'Simple, Medio, Premiun',
  `usuario` varchar(45) NOT NULL,
  `pass` varchar(120) NOT NULL,
  `tmp_pass` varchar(90) NOT NULL,
  `token` varchar(90) NOT NULL,
  `sexo` varchar(45) NOT NULL,
  `telefono` varchar(20) NOT NULL,
  `email` varchar(70) NOT NULL,
  `numero_cuenta` varchar(20) DEFAULT NULL,
  `id_documentos` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `users`
--

INSERT INTO `users` (`id_user`, `primer_nombre`, `segundo_nombre`, `primer_apellido`, `segundo_apellido`, `tipo`, `es_sucursal`, `es_comercio_afiliado`, `id_sucursal`, `id_comercio_afiliado`, `tipo_cliente`, `usuario`, `pass`, `tmp_pass`, `token`, `sexo`, `telefono`, `email`, `numero_cuenta`, `id_documentos`) VALUES
(15, 'admin', '', 'admin', '', 0, NULL, NULL, NULL, NULL, 'Premiun', 'admin', '$2a$10$f5e7c087a530bbf4e118duBLka3xc715bOuv0WMOawlo5vAaN0TIm', '', '', 'm', '123', 'admin@hotmail.com', '18446744073709551615', NULL),
(50, 'cliente', '', 'uno', '', 2, NULL, NULL, NULL, NULL, 'Premiun', 'clienteuno', '$2a$10$7b19c6d9730c02986b60eeldH3XQRsA3VHqPewqEttL/0iYOpnaSC', '', '', 'm', '04168352573', 'clienteuno@hotmail.com', '12345698746325148000', NULL),
(59, 'foto', '', 'foto', '', 2, NULL, NULL, NULL, NULL, 'Medio', 'foto', '$2a$10$2eccc213d972a89f9541duNWlBiyJfYyFsfM1Gb43VUOaN7wWaNsu', '', '', 'm', '04168352573', 'gomzjale@gmail.com', '64643124579864310000', 4),
(62, 'Prueba', '', 'Prueba', '', 1, NULL, 1, NULL, NULL, NULL, 'Prueba', '$2a$10$044a0ebd710d9f1835b56OCX2cK1OGye8St5QD07bdIpylEJWglNO', '', '', 'm', '02122380193', 'Prueba@franklingolds.com', NULL, NULL),
(65, 'GammaExpres4', '', 'petareee', '', 1, NULL, 1, NULL, NULL, NULL, 'petareee', '$2a$10$ca60243157de84ac94197uQSku1My2vmR5FHTc0Hk.ias3G8isiEe', '', '', 'm', '04168352577', 'petareee@franklingolds.com', NULL, NULL),
(67, 'GammaExpres4', '', 'petaree', '', 1, NULL, 1, NULL, NULL, NULL, 'petaree', '$2a$10$26dd3165ae6c4b2a820a0OA/WSLH3ZlkAZr4JnFLlQDaR3CbV9.gm', '', '', 'm', '04124211142', 'petaree@franklingolds.com', NULL, NULL),
(71, 'vendedor', '', 'comercioafiliado', '', 1, NULL, NULL, NULL, 5, NULL, 'vendcom', '$2a$10$8fcc0da99373e46ad9b17uRPKaHQ2FH38a2zpUyjPhQZ8uAyqzN26', '', '', 'f', '04168352573', 'vendcom@hotmail.com', '12345678912345678911', NULL),
(72, 'supervisor', '', 'comercioafiliado', '', 3, NULL, NULL, NULL, 5, NULL, 'supercom', '$2a$10$346ec62c9e1f80646edc9uAHiXkKqSbQRx2P1AikCoGAD7mHiTuQG', '', '', 'f', '04168352573', 'supercom@hotmail.com', '96385274112345678912', NULL),
(73, 'Laesquina', '', 'Laesquina', '', 1, 1, NULL, NULL, NULL, NULL, 'Laesquina', '$2a$10$e00b5099fc8f4f98a101auElUSP1aPcL1xRPtHuNMs.e6KqWBtDqi', '', '', 'm', '04168352573', 'Laesquina@franklingolds.com', NULL, NULL),
(74, 'vendedor', '', 'sucursal', '', 1, NULL, NULL, 12, NULL, NULL, 'vendsucu', '$2a$10$dcbf4b4ff4905c9c4290bOPG3ZyHm44Btq92a9SB2aeodi.XR04wG', '', '', 'f', '04168352573', 'g.a95@hotmail.comvendsucu', '64646754918724356497', NULL),
(75, 'Daka', '', 'LasAdjuntas', '', 1, NULL, 1, NULL, NULL, NULL, 'LasAdjuntas', '$2a$10$8c212e0d718b5d6e05724eVfET2zqtIZkZ/fRZHLih4LxC/rcZ17K', '', '', 'm', '04163525453', 'LasAdjuntas@franklingolds.com', NULL, NULL),
(76, 'vendedor', '', 'daka', '', 1, NULL, NULL, NULL, 9, NULL, 'vendedaka', '$2a$10$bf511d3d22116dabd9f21OTJouq/gTttXuI8Uc8TmozYTgEEjI.BO', '', '', 'f', '04168352573', 'vendedaka@hotmail.com', '36985214774125896315', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `user_gramo`
--

CREATE TABLE `user_gramo` (
  `id_usuario_gramo` int(11) UNSIGNED NOT NULL,
  `id_usuario` int(11) UNSIGNED NOT NULL,
  `tipo_gramo` varchar(5) NOT NULL,
  `cantidad` int(11) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `user_gramo`
--

INSERT INTO `user_gramo` (`id_usuario_gramo`, `id_usuario`, `tipo_gramo`, `cantidad`) VALUES
(1, 15, 'plata', 106),
(2, 15, 'oro', 25703),
(3, 50, 'oro', 4),
(4, 59, 'oro', 661);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `user_moneda`
--

CREATE TABLE `user_moneda` (
  `id_usuario_moneda` int(11) UNSIGNED NOT NULL,
  `id_usuario` int(11) UNSIGNED NOT NULL,
  `codigo_moneda` int(11) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `user_moneda`
--

INSERT INTO `user_moneda` (`id_usuario_moneda`, `id_usuario`, `codigo_moneda`) VALUES
(2, 50, 12),
(3, 73, 15);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `comercio_afiliado`
--
ALTER TABLE `comercio_afiliado`
  ADD PRIMARY KEY (`id_comercio_afiliado`),
  ADD KEY `id_user` (`id_user`);

--
-- Indices de la tabla `divisa`
--
ALTER TABLE `divisa`
  ADD PRIMARY KEY (`id_divisa`);

--
-- Indices de la tabla `documentos`
--
ALTER TABLE `documentos`
  ADD PRIMARY KEY (`id_documento`);

--
-- Indices de la tabla `moneda`
--
ALTER TABLE `moneda`
  ADD PRIMARY KEY (`codigo`),
  ADD KEY `id_origen` (`id_origen`),
  ADD KEY `qr_alfanumerico` (`qr_alfanumerico`);

--
-- Indices de la tabla `orden`
--
ALTER TABLE `orden`
  ADD PRIMARY KEY (`id_orden`),
  ADD KEY `id_usuario` (`id_usuario`),
  ADD KEY `id_sucursal` (`id_sucursal`),
  ADD KEY `codigo_moneda` (`codigo_moneda`);

--
-- Indices de la tabla `orden_en_espera`
--
ALTER TABLE `orden_en_espera`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_usuario_cliente` (`id_usuario_cliente`),
  ADD KEY `id_usuario_vendedor` (`id_usuario_vendedor`);

--
-- Indices de la tabla `origen`
--
ALTER TABLE `origen`
  ADD PRIMARY KEY (`id_origen`);

--
-- Indices de la tabla `rango`
--
ALTER TABLE `rango`
  ADD PRIMARY KEY (`id_rango`);

--
-- Indices de la tabla `sucursal`
--
ALTER TABLE `sucursal`
  ADD PRIMARY KEY (`id_sucursal`),
  ADD KEY `id_user` (`id_user`);

--
-- Indices de la tabla `telefono`
--
ALTER TABLE `telefono`
  ADD KEY `id_comercio_afiliado` (`id_comercio_afiliado`);

--
-- Indices de la tabla `transaccion`
--
ALTER TABLE `transaccion`
  ADD PRIMARY KEY (`id_transaccion`),
  ADD KEY `codigo_moneda` (`codigo_moneda`),
  ADD KEY `codigo_moneda2` (`codigo_moneda2`),
  ADD KEY `id_usuario` (`id_usuario`),
  ADD KEY `id_usuario2` (`id_usuario2`);

--
-- Indices de la tabla `transaccion_en_espera`
--
ALTER TABLE `transaccion_en_espera`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_usuario` (`id_usuario`),
  ADD KEY `codigo_qr_moneda` (`codigo_qr_moneda`);

--
-- Indices de la tabla `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id_user`),
  ADD KEY `id_sucursal` (`id_sucursal`),
  ADD KEY `id_comercio_afiliado` (`id_comercio_afiliado`),
  ADD KEY `id_documentos` (`id_documentos`);

--
-- Indices de la tabla `user_gramo`
--
ALTER TABLE `user_gramo`
  ADD PRIMARY KEY (`id_usuario_gramo`),
  ADD KEY `id_usuario` (`id_usuario`);

--
-- Indices de la tabla `user_moneda`
--
ALTER TABLE `user_moneda`
  ADD PRIMARY KEY (`id_usuario_moneda`),
  ADD KEY `id_usuario` (`id_usuario`),
  ADD KEY `codigo_moneda` (`codigo_moneda`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `comercio_afiliado`
--
ALTER TABLE `comercio_afiliado`
  MODIFY `id_comercio_afiliado` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de la tabla `divisa`
--
ALTER TABLE `divisa`
  MODIFY `id_divisa` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `documentos`
--
ALTER TABLE `documentos`
  MODIFY `id_documento` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `moneda`
--
ALTER TABLE `moneda`
  MODIFY `codigo` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT de la tabla `orden`
--
ALTER TABLE `orden`
  MODIFY `id_orden` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT de la tabla `orden_en_espera`
--
ALTER TABLE `orden_en_espera`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `origen`
--
ALTER TABLE `origen`
  MODIFY `id_origen` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `rango`
--
ALTER TABLE `rango`
  MODIFY `id_rango` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `sucursal`
--
ALTER TABLE `sucursal`
  MODIFY `id_sucursal` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT de la tabla `transaccion`
--
ALTER TABLE `transaccion`
  MODIFY `id_transaccion` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `transaccion_en_espera`
--
ALTER TABLE `transaccion_en_espera`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `users`
--
ALTER TABLE `users`
  MODIFY `id_user` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=77;

--
-- AUTO_INCREMENT de la tabla `user_gramo`
--
ALTER TABLE `user_gramo`
  MODIFY `id_usuario_gramo` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `user_moneda`
--
ALTER TABLE `user_moneda`
  MODIFY `id_usuario_moneda` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `comercio_afiliado`
--
ALTER TABLE `comercio_afiliado`
  ADD CONSTRAINT `comercio_afiliado_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `users` (`id_user`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Filtros para la tabla `moneda`
--
ALTER TABLE `moneda`
  ADD CONSTRAINT `moneda_ibfk_1` FOREIGN KEY (`id_origen`) REFERENCES `origen` (`id_origen`) ON DELETE SET NULL;

--
-- Filtros para la tabla `orden`
--
ALTER TABLE `orden`
  ADD CONSTRAINT `orden_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `users` (`id_user`) ON DELETE CASCADE,
  ADD CONSTRAINT `orden_ibfk_2` FOREIGN KEY (`id_sucursal`) REFERENCES `sucursal` (`id_sucursal`) ON DELETE CASCADE,
  ADD CONSTRAINT `orden_ibfk_3` FOREIGN KEY (`codigo_moneda`) REFERENCES `moneda` (`codigo`) ON DELETE CASCADE;

--
-- Filtros para la tabla `orden_en_espera`
--
ALTER TABLE `orden_en_espera`
  ADD CONSTRAINT `orden_en_espera_ibfk_1` FOREIGN KEY (`id_usuario_cliente`) REFERENCES `users` (`id_user`) ON DELETE SET NULL,
  ADD CONSTRAINT `orden_en_espera_ibfk_2` FOREIGN KEY (`id_usuario_vendedor`) REFERENCES `users` (`id_user`) ON DELETE SET NULL;

--
-- Filtros para la tabla `sucursal`
--
ALTER TABLE `sucursal`
  ADD CONSTRAINT `sucursal_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `users` (`id_user`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Filtros para la tabla `telefono`
--
ALTER TABLE `telefono`
  ADD CONSTRAINT `telefono_ibfk_1` FOREIGN KEY (`id_comercio_afiliado`) REFERENCES `comercio_afiliado` (`id_comercio_afiliado`);

--
-- Filtros para la tabla `transaccion`
--
ALTER TABLE `transaccion`
  ADD CONSTRAINT `transaccion_ibfk_1` FOREIGN KEY (`codigo_moneda`) REFERENCES `moneda` (`codigo`) ON DELETE CASCADE,
  ADD CONSTRAINT `transaccion_ibfk_2` FOREIGN KEY (`codigo_moneda2`) REFERENCES `moneda` (`codigo`) ON DELETE CASCADE,
  ADD CONSTRAINT `transaccion_ibfk_3` FOREIGN KEY (`id_usuario`) REFERENCES `users` (`id_user`) ON DELETE CASCADE,
  ADD CONSTRAINT `transaccion_ibfk_4` FOREIGN KEY (`id_usuario2`) REFERENCES `users` (`id_user`) ON DELETE CASCADE;

--
-- Filtros para la tabla `transaccion_en_espera`
--
ALTER TABLE `transaccion_en_espera`
  ADD CONSTRAINT `transaccion_en_espera_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `users` (`id_user`) ON DELETE CASCADE,
  ADD CONSTRAINT `transaccion_en_espera_ibfk_2` FOREIGN KEY (`codigo_qr_moneda`) REFERENCES `moneda` (`qr_alfanumerico`) ON DELETE CASCADE;

--
-- Filtros para la tabla `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_ibfk_1` FOREIGN KEY (`id_sucursal`) REFERENCES `sucursal` (`id_sucursal`) ON DELETE SET NULL,
  ADD CONSTRAINT `users_ibfk_2` FOREIGN KEY (`id_comercio_afiliado`) REFERENCES `comercio_afiliado` (`id_comercio_afiliado`) ON DELETE SET NULL,
  ADD CONSTRAINT `users_ibfk_3` FOREIGN KEY (`id_documentos`) REFERENCES `documentos` (`id_documento`) ON DELETE SET NULL;

--
-- Filtros para la tabla `user_gramo`
--
ALTER TABLE `user_gramo`
  ADD CONSTRAINT `user_gramo_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `users` (`id_user`) ON DELETE CASCADE;

--
-- Filtros para la tabla `user_moneda`
--
ALTER TABLE `user_moneda`
  ADD CONSTRAINT `user_moneda_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `users` (`id_user`) ON DELETE CASCADE,
  ADD CONSTRAINT `user_moneda_ibfk_2` FOREIGN KEY (`codigo_moneda`) REFERENCES `moneda` (`codigo`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
