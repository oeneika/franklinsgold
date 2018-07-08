-- phpMyAdmin SQL Dump
-- version 4.7.4
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 08, 2018 at 04:22 PM
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
  `codigo` int(11) NOT NULL,
  `fecha_elaboracion` varchar(15) NOT NULL,
  `diametro` float NOT NULL,
  `espesor` float NOT NULL,
  `composicion` varchar(45) NOT NULL,
  `peso` int(11) NOT NULL,
  `id_origen` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `moneda`
--

INSERT INTO `moneda` (`codigo`, `fecha_elaboracion`, `diametro`, `espesor`, `composicion`, `peso`, `id_origen`) VALUES
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
-- Table structure for table `origen`
--

CREATE TABLE `origen` (
  `id_origen` int(11) NOT NULL,
  `nombre` varchar(45) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `origen`
--

INSERT INTO `origen` (`id_origen`, `nombre`) VALUES
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
-- Table structure for table `sucursal`
--

CREATE TABLE `sucursal` (
  `id_sucursal` int(11) NOT NULL,
  `nombre` varchar(45) NOT NULL,
  `direccion` varchar(45) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `sucursal`
--

INSERT INTO `sucursal` (`id_sucursal`, `nombre`, `direccion`) VALUES
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
-- Table structure for table `transaccion`
--

CREATE TABLE `transaccion` (
  `id_usuario` int(11) NOT NULL,
  `codigo` int(11) NOT NULL,
  `fecha` date NOT NULL,
  `id_sucursal` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `transaccion`
--

INSERT INTO `transaccion` (`id_usuario`, `codigo`, `fecha`, `id_sucursal`) VALUES
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
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id_user` int(11) NOT NULL,
  `primer_nombre` varchar(45) NOT NULL,
  `segundo_nombre` varchar(45) NOT NULL,
  `primer_apellido` varchar(45) NOT NULL,
  `segundo_apellido` varchar(45) NOT NULL,
  `tipo` tinyint(2) UNSIGNED NOT NULL COMMENT '0: admin, 1: normal',
  `usuario` varchar(45) NOT NULL,
  `pass` varchar(120) NOT NULL,
  `tmp_pass` varchar(90) NOT NULL,
  `sexo` varchar(45) NOT NULL,
  `telefono` int(11) NOT NULL,
  `email` varchar(70) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id_user`, `primer_nombre`, `segundo_nombre`, `primer_apellido`, `segundo_apellido`, `tipo`, `usuario`, `pass`, `tmp_pass`, `sexo`, `telefono`, `email`) VALUES
(1, 'Adrian', 'De witt', 'Silbersak', 'Ridett', 0, 'dridett0', 'IVkgWfqCma7', '', 'Male', 239, 'dridett0@indiatimes.com'),
(2, 'Truda', 'Margy', 'Filtness', 'Towndrow', 0, 'mtowndrow1', 'd9DfYh7jbPT', '', 'Female', 201, 'mtowndrow1@addthis.com'),
(3, 'Hinda', 'Caryn', 'Kleinplac', 'Faulks', 0, 'cfaulks2', 'vh3btkAaTtj', '', 'Female', 634, 'cfaulks2@hp.com'),
(4, 'Levi', 'Moishe', 'Giggs', 'Maty', 0, 'mmaty3', 'O8hAUSfd7CB', '', 'Male', 441, 'mmaty3@seattletimes.com'),
(5, 'Franklyn', 'Thornton', 'Dyzart', 'Proudlock', 0, 'tproudlock4', 'VFHqYkw3n4a', '', 'Male', 583, 'tproudlock4@chron.com'),
(6, 'Jethro', 'Dmitri', 'Rapper', 'Eggerton', 0, 'deggerton5', 'zGrk5SJn7', '', 'Male', 818, 'deggerton5@oracle.com'),
(7, 'Philippe', 'Christie', 'Yitzhakof', 'Coatman', 0, 'ccoatman6', 'uzGNwVuUV3T', '', 'Female', 827, 'ccoatman6@amazon.com'),
(8, 'Batsheva', 'Debor', 'Bordessa', 'Poschel', 0, 'dposchel7', 'jtDi4BhtVN1E', '', 'Female', 542, 'dposchel7@house.gov'),
(9, 'Adoree', 'Lanita', 'Constantine', 'Twitching', 0, 'ltwitching8', 'AnbeNi8A2q1q', '', 'Female', 868, 'ltwitching8@unesco.org'),
(10, 'Stanley', 'Schuyler', 'Dmych', 'Hodcroft', 0, 'shodcroft9', '5FLmHPSA', '', 'Male', 593, 'shodcroft9@archive.org');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `moneda`
--
ALTER TABLE `moneda`
  ADD PRIMARY KEY (`codigo`,`id_origen`),
  ADD KEY `fk_moneda_origen1_idx` (`id_origen`);

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
  ADD PRIMARY KEY (`id_usuario`,`codigo`,`fecha`,`id_sucursal`),
  ADD KEY `fk_usuario_has_moneda_moneda1_idx` (`codigo`),
  ADD KEY `fk_usuario_has_moneda_usuario1_idx` (`id_usuario`),
  ADD KEY `fk_transaccion_sucursal1_idx` (`id_sucursal`);

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
  MODIFY `codigo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `origen`
--
ALTER TABLE `origen`
  MODIFY `id_origen` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `sucursal`
--
ALTER TABLE `sucursal`
  MODIFY `id_sucursal` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id_user` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `moneda`
--
ALTER TABLE `moneda`
  ADD CONSTRAINT `fk_moneda_origen1` FOREIGN KEY (`id_origen`) REFERENCES `origen` (`id_origen`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `transaccion`
--
ALTER TABLE `transaccion`
  ADD CONSTRAINT `fk_transaccion_sucursal1` FOREIGN KEY (`id_sucursal`) REFERENCES `sucursal` (`id_sucursal`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_usuario_has_moneda_moneda1` FOREIGN KEY (`codigo`) REFERENCES `moneda` (`codigo`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_usuario_has_moneda_usuario1` FOREIGN KEY (`id_usuario`) REFERENCES `users` (`id_user`) ON DELETE NO ACTION ON UPDATE NO ACTION;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
