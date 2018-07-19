-- phpMyAdmin SQL Dump
-- version 4.7.4
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 19-07-2018 a las 03:50:24
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
-- Estructura de tabla para la tabla `afiliado_moneda`
--

CREATE TABLE `afiliado_moneda` (
  `id_comercio_afiliado` bigint(11) UNSIGNED NOT NULL,
  `codigo` int(11) UNSIGNED NOT NULL,
  `monto` float UNSIGNED NOT NULL,
  `fecha` int(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `comercio_afiliado`
--

CREATE TABLE `comercio_afiliado` (
  `id_comercio_afiliado` bigint(11) UNSIGNED NOT NULL,
  `nombre` varchar(45) NOT NULL,
  `direccion` varchar(100) NOT NULL,
  `sucursal` varchar(45) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

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
  `codigo_qr` varchar(90) NOT NULL,
  `qr_alfanumerico` varchar(30) NOT NULL,
  `id_origen` int(11) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `moneda`
--

INSERT INTO `moneda` (`codigo`, `fecha_elaboracion`, `diametro`, `espesor`, `composicion`, `peso`, `codigo_qr`, `qr_alfanumerico`, `id_origen`) VALUES
(1, 1531877197, 12, 44, 'oro', 12, 'http://localhost/franklinsgold/framework//views/img/codigos/monedas/1.png', 'VEN 012 044 ORO 012 17072018', 1),
(2, 1531956451, 3, 4, 'oro', 2, 'http://localhost/franklinsgold/framework//views/img/codigos/monedas/2.png', 'VEN 003 004 ORO 002 18072018', 1),
(5, 1531963125, 3, 4, 'plata', 5, 'http://localhost/franklinsgold/framework//views/img/codigos/monedas/5.png', 'VEN 003 004 PLA 005 18072018', 1);

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
(1, 'Venezuela', 'VEN');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sucursal`
--

CREATE TABLE `sucursal` (
  `id_sucursal` int(11) UNSIGNED NOT NULL,
  `nombre` varchar(45) NOT NULL,
  `direccion` varchar(45) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `sucursal`
--

INSERT INTO `sucursal` (`id_sucursal`, `nombre`, `direccion`) VALUES
(1, 'Delos', 'LA LUCHA');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `telefono`
--

CREATE TABLE `telefono` (
  `id_comercio_afiliado` bigint(11) UNSIGNED NOT NULL,
  `telefono` int(11) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `transaccion`
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
  `id_sucursal` int(11) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `transaccion`
--

INSERT INTO `transaccion` (`id_transaccion`, `fecha`, `tipo`, `codigo_moneda`, `codigo_moneda2`, `id_usuario`, `id_usuario2`, `precio_moneda1`, `precio_moneda2`, `id_sucursal`) VALUES
(9, 1531956555, 1, 1, NULL, 16, NULL, 525, NULL, 1),
(10, 1531956565, 1, 2, NULL, 18, NULL, 87, NULL, 1),
(11, 1531960865, 3, 2, 1, 18, 16, 87, 525, NULL),
(14, 1531963656, 1, 5, NULL, 9, NULL, 2, NULL, 1),
(15, 1531963684, 2, 5, NULL, 9, NULL, 2, NULL, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `transaccion_en_espera`
--

CREATE TABLE `transaccion_en_espera` (
  `id` int(11) UNSIGNED NOT NULL,
  `id_usuario` int(11) UNSIGNED NOT NULL,
  `codigo_qr_moneda` varchar(30) NOT NULL
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
  `tipo` tinyint(2) UNSIGNED NOT NULL COMMENT '0: admin, 1: normal',
  `usuario` varchar(45) NOT NULL,
  `pass` varchar(120) NOT NULL,
  `tmp_pass` varchar(90) NOT NULL,
  `token` varchar(90) NOT NULL,
  `sexo` varchar(45) NOT NULL,
  `telefono` int(11) NOT NULL,
  `email` varchar(70) NOT NULL,
  `codigo_qr` varchar(90) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `users`
--

INSERT INTO `users` (`id_user`, `primer_nombre`, `segundo_nombre`, `primer_apellido`, `segundo_apellido`, `tipo`, `usuario`, `pass`, `tmp_pass`, `token`, `sexo`, `telefono`, `email`, `codigo_qr`) VALUES
(7, 'Fraklin', '', 'Gold', '', 1, 'franklin', '$2a$10$fed9dff63481ec29c83eeOcNOtlM0shSOLnCVQArUj3X08syd0hNO', '', '', 'm', 414123123, 'franklingold@gmail.com', '../views/img/codigos/usuarios/usuarios7.png'),
(9, 'Ellen', '', 'Ripley', '', 2, 'ellenripley', '$2a$10$f5e74d32850c3961548e0eroa65KJyfBRHxq/wQzA87fuLguvYFbS', '$2a$10$7209e9319a149aa22a3e3umw83P4YSNP1wPUaLbKo4lKJmYlqtY6S', 'da08994d625050f8d492a72b10339633', 'f', 1234567, 'deadgreen_spk@hotmail.com', '../views/img/codigos/usuarios/usuarios9.png'),
(14, 'User', '', 'De Api', '', 2, 'userdeapi', '$2a$10$67374e33e71c07fe1c8abeJ1wI4AufhNboMbsCjYJNY5UawnnvV/e', '', '', 'm', 123456, 'user@api.com', '../views/img/codigos/usuarios/usuarios14.png'),
(15, 'admin', '', 'admin', '', 0, 'admin', '$2a$10$f5e7c087a530bbf4e118duBLka3xc715bOuv0WMOawlo5vAaN0TIm', '', '', 'm', 123, 'admin@hotmail.com', '../views/img/codigos/usuarios/usuarios15.png'),
(16, 'PruebaPrimerNombre', 'PruebaSegundoNombre', 'PruebaPrimerApellido', 'PruebaSegundoApellido', 2, 'PruebaApp', '$2a$10$d06bc1b79887aa681d4b8un9iQcHeOfSio/2SEFxZPC48LVHCRhx6', '', '', 'm', 212, 'pruebaap@pruebaapp.com', '../views/img/codigos/usuarios/usuarios16.png'),
(18, 'aja', 'aja', 'aja', 'aja', 2, 'aja', '$2a$10$8ea8a1b0aa1dcd64bbaa4uvvfa57EvO2w6oWy.sPc514oXNalLQga', '$2a$10$828ae6034e6f74353c390uCwB82JvUvZ94OupmgbYHrONbtKhJeBO', '28c3ef6619586745c2dddd63386f4772', 'm', 123456789, 'g.a95@hotmail.com', '../views/img/codigos/usuarios/usuarios18.png');

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
(11, 18, 1),
(12, 16, 2);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `afiliado_moneda`
--
ALTER TABLE `afiliado_moneda`
  ADD KEY `id_comercio_afiliado` (`id_comercio_afiliado`),
  ADD KEY `codigo` (`codigo`);

--
-- Indices de la tabla `comercio_afiliado`
--
ALTER TABLE `comercio_afiliado`
  ADD PRIMARY KEY (`id_comercio_afiliado`);

--
-- Indices de la tabla `moneda`
--
ALTER TABLE `moneda`
  ADD PRIMARY KEY (`codigo`),
  ADD KEY `id_origen` (`id_origen`),
  ADD KEY `qr_alfanumerico` (`qr_alfanumerico`);

--
-- Indices de la tabla `origen`
--
ALTER TABLE `origen`
  ADD PRIMARY KEY (`id_origen`);

--
-- Indices de la tabla `sucursal`
--
ALTER TABLE `sucursal`
  ADD PRIMARY KEY (`id_sucursal`);

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
  ADD KEY `id_usuario2` (`id_usuario2`),
  ADD KEY `id_sucursal` (`id_sucursal`);

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
  ADD PRIMARY KEY (`id_user`);

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
  MODIFY `id_comercio_afiliado` bigint(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `moneda`
--
ALTER TABLE `moneda`
  MODIFY `codigo` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `origen`
--
ALTER TABLE `origen`
  MODIFY `id_origen` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `sucursal`
--
ALTER TABLE `sucursal`
  MODIFY `id_sucursal` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `transaccion`
--
ALTER TABLE `transaccion`
  MODIFY `id_transaccion` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT de la tabla `transaccion_en_espera`
--
ALTER TABLE `transaccion_en_espera`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `users`
--
ALTER TABLE `users`
  MODIFY `id_user` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT de la tabla `user_moneda`
--
ALTER TABLE `user_moneda`
  MODIFY `id_usuario_moneda` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `afiliado_moneda`
--
ALTER TABLE `afiliado_moneda`
  ADD CONSTRAINT `afiliado_moneda_ibfk_2` FOREIGN KEY (`codigo`) REFERENCES `moneda` (`codigo`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `afiliado_moneda_ibfk_3` FOREIGN KEY (`id_comercio_afiliado`) REFERENCES `comercio_afiliado` (`id_comercio_afiliado`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `moneda`
--
ALTER TABLE `moneda`
  ADD CONSTRAINT `moneda_ibfk_1` FOREIGN KEY (`id_origen`) REFERENCES `origen` (`id_origen`) ON DELETE SET NULL;

--
-- Filtros para la tabla `telefono`
--
ALTER TABLE `telefono`
  ADD CONSTRAINT `telefono_ibfk_1` FOREIGN KEY (`id_comercio_afiliado`) REFERENCES `comercio_afiliado` (`id_comercio_afiliado`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `transaccion`
--
ALTER TABLE `transaccion`
  ADD CONSTRAINT `transaccion_ibfk_1` FOREIGN KEY (`codigo_moneda`) REFERENCES `moneda` (`codigo`) ON DELETE CASCADE,
  ADD CONSTRAINT `transaccion_ibfk_2` FOREIGN KEY (`codigo_moneda2`) REFERENCES `moneda` (`codigo`) ON DELETE CASCADE,
  ADD CONSTRAINT `transaccion_ibfk_3` FOREIGN KEY (`id_usuario`) REFERENCES `users` (`id_user`) ON DELETE CASCADE,
  ADD CONSTRAINT `transaccion_ibfk_4` FOREIGN KEY (`id_usuario2`) REFERENCES `users` (`id_user`) ON DELETE CASCADE,
  ADD CONSTRAINT `transaccion_ibfk_5` FOREIGN KEY (`id_sucursal`) REFERENCES `sucursal` (`id_sucursal`) ON DELETE SET NULL;

--
-- Filtros para la tabla `transaccion_en_espera`
--
ALTER TABLE `transaccion_en_espera`
  ADD CONSTRAINT `transaccion_en_espera_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `users` (`id_user`) ON DELETE CASCADE,
  ADD CONSTRAINT `transaccion_en_espera_ibfk_2` FOREIGN KEY (`codigo_qr_moneda`) REFERENCES `moneda` (`qr_alfanumerico`) ON DELETE CASCADE;

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
