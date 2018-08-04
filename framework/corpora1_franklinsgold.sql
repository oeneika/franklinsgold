-- phpMyAdmin SQL Dump
-- version 4.7.4
-- https://www.phpmyadmin.net/
--
<<<<<<< HEAD
-- Host: 127.0.0.1
-- Generation Time: Jul 20, 2018 at 06:31 PM
-- Server version: 10.1.26-MariaDB
-- PHP Version: 7.1.9
=======
-- Servidor: 127.0.0.1
-- Tiempo de generación: 04-08-2018 a las 19:48:55
-- Versión del servidor: 10.1.26-MariaDB
-- Versión de PHP: 7.1.9
>>>>>>> comprayventa

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `corpora1_franklinsgold`
--

-- --------------------------------------------------------

--
<<<<<<< HEAD
-- Table structure for table `comercio_afiliado`
=======
-- Estructura de tabla para la tabla `comercio_afiliado`
>>>>>>> comprayventa
--

CREATE TABLE `comercio_afiliado` (
  `id_comercio_afiliado` bigint(11) UNSIGNED NOT NULL,
  `nombre` varchar(45) NOT NULL,
  `direccion` varchar(100) NOT NULL,
  `sucursal` varchar(45) NOT NULL,
  `id_user` int(11) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `comercio_afiliado`
--

INSERT INTO `comercio_afiliado` (`id_comercio_afiliado`, `nombre`, `direccion`, `sucursal`, `id_user`) VALUES
(1, 'Nuevo Comercio', 'Florida', 'Miami', 21);

-- --------------------------------------------------------

--
-- Table structure for table `moneda`
--

CREATE TABLE `moneda` (
  `codigo` int(11) UNSIGNED NOT NULL,
  `fecha_elaboracion` int(20) UNSIGNED NOT NULL,
  `diametro` float UNSIGNED NOT NULL,
  `espesor` float UNSIGNED NOT NULL,
  `composicion` varchar(32) DEFAULT NULL,
  `peso` float UNSIGNED NOT NULL,
  `codigo_qr` varchar(90) NOT NULL,
  `qr_alfanumerico` varchar(30) NOT NULL,
  `id_origen` int(11) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `moneda`
--

INSERT INTO `moneda` (`codigo`, `fecha_elaboracion`, `diametro`, `espesor`, `composicion`, `peso`, `codigo_qr`, `qr_alfanumerico`, `id_origen`) VALUES
<<<<<<< HEAD
(2, 1532062893, 12, 12, 'oro', 21, 'http://localhost/franklinsgold/framework//views/img/codigos/monedas/2.png', 'VEN 012 012 ORO 021 20072018', 1);
=======
(12, 1532404026, 4, 4, 'plata', 5, 'http://localhost/franklinsgold/framework//views/img/codigos/monedas/12.png', 'VEN 004 004 PLA 005 23072018', 1),
(13, 1532404776, 1, 1, 'oro', 1, 'http://localhost/franklinsgold/framework//views/img/codigos/monedas/13.png', 'VEN 001 001 ORO 001 23072018', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `orden`
--

CREATE TABLE `orden` (
  `id_orden` int(11) UNSIGNED NOT NULL,
  `id_usuario` int(11) UNSIGNED NOT NULL,
  `id_sucursal` int(11) UNSIGNED NOT NULL,
  `tipo_gramo` varchar(5) NOT NULL,
  `cantidad` int(11) UNSIGNED NOT NULL,
  `tipo_orden` int(11) UNSIGNED NOT NULL,
  `estado` int(11) UNSIGNED NOT NULL,
  `fecha` int(11) UNSIGNED NOT NULL,
  `codigo_moneda` int(11) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `orden`
--

INSERT INTO `orden` (`id_orden`, `id_usuario`, `id_sucursal`, `tipo_gramo`, `cantidad`, `tipo_orden`, `estado`, `fecha`, `codigo_moneda`) VALUES
(4, 15, 3, 'plata', 5, 1, 2, 1533325086, NULL),
(5, 15, 3, 'plata', 5, 3, 2, 1533325331, 12);
>>>>>>> comprayventa

-- --------------------------------------------------------

--
-- Table structure for table `origen`
--

CREATE TABLE `origen` (
  `id_origen` int(11) UNSIGNED NOT NULL,
  `nombre` varchar(32) NOT NULL,
  `abreviatura` varchar(5) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `origen`
--

INSERT INTO `origen` (`id_origen`, `nombre`, `abreviatura`) VALUES
(1, 'Venezuela', 'VEN');

-- --------------------------------------------------------

--
-- Table structure for table `sucursal`
--

CREATE TABLE `sucursal` (
  `id_sucursal` int(11) UNSIGNED NOT NULL,
  `nombre` varchar(45) NOT NULL,
  `direccion` varchar(45) NOT NULL,
  `id_user` int(11) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `sucursal`
--

INSERT INTO `sucursal` (`id_sucursal`, `nombre`, `direccion`, `id_user`) VALUES
<<<<<<< HEAD
(1, 'Nueva Sucursal', 'A la esquina', 22);
=======
(1, 'Nueva Sucursal', 'A la esquina', 22),
(3, 'Mi sucrusal', 'petare', 25),
(4, 'Mi otra sucursal', 'la dolorita', NULL);
>>>>>>> comprayventa

-- --------------------------------------------------------

--
-- Table structure for table `telefono`
--

CREATE TABLE `telefono` (
  `id_comercio_afiliado` bigint(11) UNSIGNED NOT NULL,
  `telefono` int(11) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `telefono`
--

INSERT INTO `telefono` (`id_comercio_afiliado`, `telefono`) VALUES
(1, 4294967295);

-- --------------------------------------------------------

--
-- Table structure for table `transaccion`
--

CREATE TABLE `transaccion` (
  `id_transaccion` int(11) UNSIGNED NOT NULL,
  `fecha` int(20) UNSIGNED NOT NULL,
  `tipo` tinyint(2) UNSIGNED NOT NULL,
  `codigo_moneda` int(11) UNSIGNED NOT NULL,
  `codigo_moneda2` int(11) UNSIGNED DEFAULT NULL,
  `id_usuario` int(11) UNSIGNED NOT NULL,
  `id_usuario2` int(11) UNSIGNED DEFAULT NULL,
  `precio_moneda1` int(11) UNSIGNED NOT NULL,
  `precio_moneda2` int(11) UNSIGNED DEFAULT NULL,
  `id_sucursal` int(11) UNSIGNED DEFAULT NULL,
  `id_comercio_afiliado` bigint(11) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `transaccion_en_espera`
--

<<<<<<< HEAD
=======
INSERT INTO `transaccion` (`id_transaccion`, `fecha`, `tipo`, `codigo_moneda`, `codigo_moneda2`, `id_usuario`, `id_usuario2`, `precio_moneda1`, `precio_moneda2`, `id_sucursal`, `id_comercio_afiliado`) VALUES
(7, 1532404625, 1, 12, NULL, 24, NULL, 2, NULL, NULL, NULL),
(8, 1532405136, 3, 12, 13, 24, 25, 2, 43, NULL, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `transaccion_en_espera`
--

>>>>>>> comprayventa
CREATE TABLE `transaccion_en_espera` (
  `id` int(11) UNSIGNED NOT NULL,
  `id_usuario` int(11) UNSIGNED NOT NULL,
  `codigo_qr_moneda` varchar(30) NOT NULL,
  `token_confirmacion` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id_user` int(11) UNSIGNED NOT NULL,
  `primer_nombre` varchar(45) NOT NULL,
  `segundo_nombre` varchar(45) NOT NULL,
  `primer_apellido` varchar(45) NOT NULL,
  `segundo_apellido` varchar(45) NOT NULL,
  `tipo` tinyint(2) UNSIGNED NOT NULL COMMENT '0: admin, 1: vendedor, 2: cliente',
  `usuario` varchar(45) NOT NULL,
  `pass` varchar(120) NOT NULL,
  `tmp_pass` varchar(90) NOT NULL,
  `token` varchar(90) NOT NULL,
  `sexo` varchar(45) NOT NULL,
  `telefono` bigint(20) UNSIGNED NOT NULL,
  `email` varchar(70) NOT NULL,
  `codigo_qr` varchar(90) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id_user`, `primer_nombre`, `segundo_nombre`, `primer_apellido`, `segundo_apellido`, `tipo`, `usuario`, `pass`, `tmp_pass`, `token`, `sexo`, `telefono`, `email`, `codigo_qr`) VALUES
(15, 'admin', '', 'admin', '', 0, 'admin', '$2a$10$f5e7c087a530bbf4e118duBLka3xc715bOuv0WMOawlo5vAaN0TIm', '', '', 'm', 123, 'admin@hotmail.com', '../views/img/codigos/usuarios/usuarios15.png'),
<<<<<<< HEAD
(21, '1', '1', '1', '1', 1, '1', '$2a$10$3920de3c9b12a18972de0e.17NGiSb3p31iOttNdMPIQGo68hInDq', '', '', 'f', 21474836471, '1@1.com', '../views/img/codigos/usuarios/usuarios21.png'),
(22, '1', '1', '1', '1', 1, '2', '$2a$10$75e11d632f10f82877866ODsOjjLMQGIDcFe6CrLquaW7x2woBPgK', '', '', 'f', 21474836471, '2@1.com', '../views/img/codigos/usuarios/usuarios22.png'),
(23, 'Sergio', '', 'Garcia', '', 2, 'Sergio', '$2a$10$0cad18e0e4e32ce2d19c6e24pdAshVog8V4cgT0Bml68SqmOaavi6', '', '', 'm', 12345678901, 'deadgreen_spk@hotmail.com', '../views/img/codigos/usuarios/usuarios23.png');
=======
(21, 'Nuevo', '1', 'Comercio', '1', 1, '1', '$2a$10$3920de3c9b12a18972de0e.17NGiSb3p31iOttNdMPIQGo68hInDq', '', '', 'f', 21474836471, '1@1.com', '../views/img/codigos/usuarios/usuarios21.png'),
(22, 'Nueva', '1', 'Sucursal', '1', 1, '2', '$2a$10$75e11d632f10f82877866ODsOjjLMQGIDcFe6CrLquaW7x2woBPgK', '', '', 'f', 21474836471, 'gomzjale@gmail.com', '../views/img/codigos/usuarios/usuarios22.png'),
(23, 'Sergio', '', 'Garcia', '', 2, 'Sergio', '$2a$10$0cad18e0e4e32ce2d19c6e24pdAshVog8V4cgT0Bml68SqmOaavi6', '', '', 'm', 12345678901, 'deadgreen_spk@hotmail.com', '../views/img/codigos/usuarios/usuarios23.png'),
(24, 'greg', '', 'gomez', '', 2, 'greg', '$2a$10$76ddff869df01a446f8dcu.eyGND5pka5m4QujesIdF5zczsUXtWq', '', '', 'm', 4168352573, 'g.a95@hotmail.com', '../views/img/codigos/usuarios/usuarios24.png'),
(25, 'Mi', '', 'Sucursal', '', 1, 'misucu', '$2a$10$f735a8d0a3d4e9ce35d28u/uaKD.sc9w0TAJWmhjK.pFOYT7/8.La', '', '', 'm', 2122380193, 'misucu@hotmail.com', ''),
(29, 'amate', '', 'rasu', '', 2, 'amaterasu', '$2a$10$b0f6182c9f5e42a04ba8eObkF5ia/KTlJ6PhM9lLBXYHCFHQC7pgG', '', '', 'm', 12345678910, 'amaterasu@gmail.com', '../views/img/codigos/usuarios/usuarios29.png');

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
(1, 15, 'plata', 0);
>>>>>>> comprayventa

-- --------------------------------------------------------

--
-- Table structure for table `user_moneda`
--

CREATE TABLE `user_moneda` (
  `id_usuario_moneda` int(11) UNSIGNED NOT NULL,
  `id_usuario` int(11) UNSIGNED NOT NULL,
  `codigo_moneda` int(11) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `user_moneda`
--

INSERT INTO `user_moneda` (`id_usuario_moneda`, `id_usuario`, `codigo_moneda`) VALUES
<<<<<<< HEAD
(1, 21, 2);
=======
(20, 24, 13),
(22, 15, 12);
>>>>>>> comprayventa

--
-- Indexes for dumped tables
--

--
<<<<<<< HEAD
-- Indexes for table `comercio_afiliado`
=======
-- Indices de la tabla `comercio_afiliado`
>>>>>>> comprayventa
--
ALTER TABLE `comercio_afiliado`
  ADD PRIMARY KEY (`id_comercio_afiliado`),
  ADD KEY `id_user` (`id_user`);

--
-- Indexes for table `moneda`
--
ALTER TABLE `moneda`
  ADD PRIMARY KEY (`codigo`),
  ADD KEY `id_origen` (`id_origen`),
  ADD KEY `qr_alfanumerico` (`qr_alfanumerico`);
<<<<<<< HEAD
=======

--
-- Indices de la tabla `orden`
--
ALTER TABLE `orden`
  ADD PRIMARY KEY (`id_orden`),
  ADD KEY `id_usuario` (`id_usuario`),
  ADD KEY `id_sucursal` (`id_sucursal`),
  ADD KEY `codigo_moneda` (`codigo_moneda`);
>>>>>>> comprayventa

--
-- Indexes for table `origen`
--
ALTER TABLE `origen`
  ADD PRIMARY KEY (`id_origen`);

--
-- Indexes for table `sucursal`
--
ALTER TABLE `sucursal`
  ADD PRIMARY KEY (`id_sucursal`),
  ADD KEY `id_user` (`id_user`);

--
-- Indexes for table `telefono`
--
ALTER TABLE `telefono`
  ADD KEY `id_comercio_afiliado` (`id_comercio_afiliado`);

--
-- Indexes for table `transaccion`
--
ALTER TABLE `transaccion`
  ADD PRIMARY KEY (`id_transaccion`),
  ADD KEY `codigo_moneda` (`codigo_moneda`),
  ADD KEY `codigo_moneda2` (`codigo_moneda2`),
  ADD KEY `id_usuario` (`id_usuario`),
  ADD KEY `id_usuario2` (`id_usuario2`),
  ADD KEY `id_sucursal` (`id_sucursal`),
  ADD KEY `id_comercio_afiliado` (`id_comercio_afiliado`);
<<<<<<< HEAD
=======

--
-- Indices de la tabla `transaccion_en_espera`
--
ALTER TABLE `transaccion_en_espera`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_usuario` (`id_usuario`),
  ADD KEY `codigo_qr_moneda` (`codigo_qr_moneda`);
>>>>>>> comprayventa

--
-- Indexes for table `transaccion_en_espera`
--
ALTER TABLE `transaccion_en_espera`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_usuario` (`id_usuario`),
  ADD KEY `codigo_qr_moneda` (`codigo_qr_moneda`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id_user`);

--
<<<<<<< HEAD
-- Indexes for table `user_moneda`
=======
-- Indices de la tabla `user_gramo`
--
ALTER TABLE `user_gramo`
  ADD PRIMARY KEY (`id_usuario_gramo`),
  ADD KEY `id_usuario` (`id_usuario`);

--
-- Indices de la tabla `user_moneda`
>>>>>>> comprayventa
--
ALTER TABLE `user_moneda`
  ADD PRIMARY KEY (`id_usuario_moneda`),
  ADD KEY `id_usuario` (`id_usuario`),
  ADD KEY `codigo_moneda` (`codigo_moneda`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `comercio_afiliado`
--
ALTER TABLE `comercio_afiliado`
  MODIFY `id_comercio_afiliado` bigint(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `moneda`
--
ALTER TABLE `moneda`
<<<<<<< HEAD
  MODIFY `codigo` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
=======
  MODIFY `codigo` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT de la tabla `orden`
--
ALTER TABLE `orden`
  MODIFY `id_orden` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
>>>>>>> comprayventa

--
-- AUTO_INCREMENT for table `origen`
--
ALTER TABLE `origen`
  MODIFY `id_origen` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `sucursal`
--
ALTER TABLE `sucursal`
<<<<<<< HEAD
  MODIFY `id_sucursal` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
=======
  MODIFY `id_sucursal` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
>>>>>>> comprayventa

--
-- AUTO_INCREMENT for table `transaccion`
--
ALTER TABLE `transaccion`
<<<<<<< HEAD
  MODIFY `id_transaccion` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;
=======
  MODIFY `id_transaccion` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de la tabla `transaccion_en_espera`
--
ALTER TABLE `transaccion_en_espera`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;
>>>>>>> comprayventa

--
-- AUTO_INCREMENT for table `transaccion_en_espera`
--
ALTER TABLE `transaccion_en_espera`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
<<<<<<< HEAD
  MODIFY `id_user` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;
=======
  MODIFY `id_user` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT de la tabla `user_gramo`
--
ALTER TABLE `user_gramo`
  MODIFY `id_usuario_gramo` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
>>>>>>> comprayventa

--
-- AUTO_INCREMENT for table `user_moneda`
--
ALTER TABLE `user_moneda`
<<<<<<< HEAD
  MODIFY `id_usuario_moneda` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
=======
  MODIFY `id_usuario_moneda` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;
>>>>>>> comprayventa

--
-- Constraints for dumped tables
--

--
<<<<<<< HEAD
-- Constraints for table `comercio_afiliado`
=======
-- Filtros para la tabla `comercio_afiliado`
>>>>>>> comprayventa
--
ALTER TABLE `comercio_afiliado`
  ADD CONSTRAINT `comercio_afiliado_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `users` (`id_user`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `moneda`
--
ALTER TABLE `moneda`
  ADD CONSTRAINT `moneda_ibfk_1` FOREIGN KEY (`id_origen`) REFERENCES `origen` (`id_origen`) ON DELETE SET NULL;

--
<<<<<<< HEAD
-- Constraints for table `sucursal`
=======
-- Filtros para la tabla `orden`
--
ALTER TABLE `orden`
  ADD CONSTRAINT `orden_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `users` (`id_user`) ON DELETE CASCADE,
  ADD CONSTRAINT `orden_ibfk_2` FOREIGN KEY (`id_sucursal`) REFERENCES `sucursal` (`id_sucursal`) ON DELETE CASCADE,
  ADD CONSTRAINT `orden_ibfk_3` FOREIGN KEY (`codigo_moneda`) REFERENCES `moneda` (`codigo`) ON DELETE CASCADE;

--
-- Filtros para la tabla `sucursal`
>>>>>>> comprayventa
--
ALTER TABLE `sucursal`
  ADD CONSTRAINT `sucursal_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `users` (`id_user`) ON DELETE SET NULL ON UPDATE CASCADE;

--
<<<<<<< HEAD
-- Constraints for table `telefono`
=======
-- Filtros para la tabla `telefono`
>>>>>>> comprayventa
--
ALTER TABLE `telefono`
  ADD CONSTRAINT `telefono_ibfk_1` FOREIGN KEY (`id_comercio_afiliado`) REFERENCES `comercio_afiliado` (`id_comercio_afiliado`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `transaccion`
--
ALTER TABLE `transaccion`
  ADD CONSTRAINT `transaccion_ibfk_1` FOREIGN KEY (`codigo_moneda`) REFERENCES `moneda` (`codigo`) ON DELETE CASCADE,
  ADD CONSTRAINT `transaccion_ibfk_2` FOREIGN KEY (`codigo_moneda2`) REFERENCES `moneda` (`codigo`) ON DELETE CASCADE,
  ADD CONSTRAINT `transaccion_ibfk_3` FOREIGN KEY (`id_usuario`) REFERENCES `users` (`id_user`) ON DELETE CASCADE,
  ADD CONSTRAINT `transaccion_ibfk_4` FOREIGN KEY (`id_usuario2`) REFERENCES `users` (`id_user`) ON DELETE CASCADE,
  ADD CONSTRAINT `transaccion_ibfk_5` FOREIGN KEY (`id_sucursal`) REFERENCES `sucursal` (`id_sucursal`) ON DELETE SET NULL,
  ADD CONSTRAINT `transaccion_ibfk_6` FOREIGN KEY (`id_comercio_afiliado`) REFERENCES `comercio_afiliado` (`id_comercio_afiliado`) ON DELETE SET NULL ON UPDATE CASCADE;

--
<<<<<<< HEAD
-- Constraints for table `transaccion_en_espera`
=======
-- Filtros para la tabla `transaccion_en_espera`
>>>>>>> comprayventa
--
ALTER TABLE `transaccion_en_espera`
  ADD CONSTRAINT `transaccion_en_espera_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `users` (`id_user`) ON DELETE CASCADE,
  ADD CONSTRAINT `transaccion_en_espera_ibfk_2` FOREIGN KEY (`codigo_qr_moneda`) REFERENCES `moneda` (`qr_alfanumerico`) ON DELETE CASCADE;
<<<<<<< HEAD
=======

--
-- Filtros para la tabla `user_gramo`
--
ALTER TABLE `user_gramo`
  ADD CONSTRAINT `user_gramo_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `users` (`id_user`) ON DELETE CASCADE;
>>>>>>> comprayventa

--
-- Constraints for table `user_moneda`
--
ALTER TABLE `user_moneda`
  ADD CONSTRAINT `user_moneda_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `users` (`id_user`) ON DELETE CASCADE,
  ADD CONSTRAINT `user_moneda_ibfk_2` FOREIGN KEY (`codigo_moneda`) REFERENCES `moneda` (`codigo`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
