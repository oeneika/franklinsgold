-- phpMyAdmin SQL Dump
-- version 4.7.4
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 10, 2018 at 03:12 PM
-- Server version: 10.1.26-MariaDB
-- PHP Version: 7.1.9

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
  `id_origen` int(11) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `moneda`
--

INSERT INTO `moneda` (`codigo`, `fecha_elaboracion`, `diametro`, `espesor`, `composicion`, `peso`, `codigo_qr`, `id_origen`) VALUES
(47, 1531178448, 1, 2, 'plata', 123, '../views/img/codigos/monedas/monedas47.png', 2),
(48, 1531178473, 7, 7, 'oro', 7, '../views/img/codigos/monedas/monedas48.png', 2),
(49, 1531178485, 6, 6, 'oro', 6, '../views/img/codigos/monedas/monedas49.png', 2),
(50, 1531189990, 1, 2, 'plata', 3, '../views/img/codigos/monedas/monedas50.png', 2),
(51, 1531191360, 1, 1, 'plata', 222, '../views/img/codigos/monedas/monedas51.png', 2);

-- --------------------------------------------------------

--
-- Table structure for table `origen`
--

CREATE TABLE `origen` (
  `id_origen` int(11) UNSIGNED NOT NULL,
  `nombre` varchar(32) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `origen`
--

INSERT INTO `origen` (`id_origen`, `nombre`) VALUES
(2, 'pakistan');

-- --------------------------------------------------------

--
-- Table structure for table `sucursal`
--

CREATE TABLE `sucursal` (
  `id_sucursal` int(11) UNSIGNED NOT NULL,
  `nombre` varchar(45) NOT NULL,
  `direccion` varchar(45) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `sucursal`
--

INSERT INTO `sucursal` (`id_sucursal`, `nombre`, `direccion`) VALUES
(2, 'San Juan de los Morros', 'Venezuela'),
(4, 'Pamedaran', 'Indonesia'),
(5, 'Aral', 'Kazakhstan'),
(6, 'A’ershan', 'China'),
(7, 'San Jerónimo', 'Peru'),
(8, 'Charleston', 'United States'),
(9, 'Sulahan', 'Indonesia'),
(10, 'Lubao', 'China'),
(11, 'Nueva sucursal', '123123');

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
  `id_sucursal` int(11) UNSIGNED DEFAULT NULL
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
-- Dumping data for table `users`
--

INSERT INTO `users` (`id_user`, `primer_nombre`, `segundo_nombre`, `primer_apellido`, `segundo_apellido`, `tipo`, `usuario`, `pass`, `tmp_pass`, `token`, `sexo`, `telefono`, `email`, `codigo_qr`) VALUES
(1, 'Sergio', '', 'Garcia', '', 0, 'Sergio', '$2a$10$07a36d71872cf809963e2OMQFms6SShU4zC/uFlHQUVvuX5ZLh3pe', '', '', 'm', 12345, 'sergioagarciag.301@gmail.com', '../views/img/codigos/usuarios/usuarios1.png');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `moneda`
--
ALTER TABLE `moneda`
  ADD PRIMARY KEY (`codigo`),
  ADD KEY `id_origen` (`id_origen`);

--
-- Indexes for table `origen`
--
ALTER TABLE `origen`
  ADD PRIMARY KEY (`id_origen`);

--
-- Indexes for table `sucursal`
--
ALTER TABLE `sucursal`
  ADD PRIMARY KEY (`id_sucursal`);

--
-- Indexes for table `transaccion`
--
ALTER TABLE `transaccion`
  ADD PRIMARY KEY (`id_transaccion`),
  ADD KEY `codigo_moneda` (`codigo_moneda`),
  ADD KEY `codigo_moneda2` (`codigo_moneda2`),
  ADD KEY `id_usuario` (`id_usuario`),
  ADD KEY `id_usuario2` (`id_usuario2`),
  ADD KEY `id_sucursal` (`id_sucursal`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id_user`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `moneda`
--
ALTER TABLE `moneda`
  MODIFY `codigo` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=52;

--
-- AUTO_INCREMENT for table `origen`
--
ALTER TABLE `origen`
  MODIFY `id_origen` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `sucursal`
--
ALTER TABLE `sucursal`
  MODIFY `id_sucursal` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `transaccion`
--
ALTER TABLE `transaccion`
  MODIFY `id_transaccion` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id_user` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `moneda`
--
ALTER TABLE `moneda`
  ADD CONSTRAINT `moneda_ibfk_1` FOREIGN KEY (`id_origen`) REFERENCES `origen` (`id_origen`) ON DELETE SET NULL;

--
-- Constraints for table `transaccion`
--
ALTER TABLE `transaccion`
  ADD CONSTRAINT `transaccion_ibfk_1` FOREIGN KEY (`codigo_moneda`) REFERENCES `moneda` (`codigo`) ON DELETE CASCADE,
  ADD CONSTRAINT `transaccion_ibfk_2` FOREIGN KEY (`codigo_moneda2`) REFERENCES `moneda` (`codigo`) ON DELETE CASCADE,
  ADD CONSTRAINT `transaccion_ibfk_3` FOREIGN KEY (`id_usuario`) REFERENCES `users` (`id_user`) ON DELETE CASCADE,
  ADD CONSTRAINT `transaccion_ibfk_4` FOREIGN KEY (`id_usuario2`) REFERENCES `users` (`id_user`) ON DELETE CASCADE,
  ADD CONSTRAINT `transaccion_ibfk_5` FOREIGN KEY (`id_sucursal`) REFERENCES `sucursal` (`id_sucursal`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
