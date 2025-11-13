-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 13-11-2025 a las 23:10:43
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `biblioteca_db`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `roles`
--

CREATE TABLE `roles` (
  `id` int(11) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `descripcion` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `roles`
--

INSERT INTO `roles` (`id`, `nombre`, `descripcion`) VALUES
(1, 'Administrador', 'Acceso completo: gestionar usuarios, ver transacciones'),
(2, 'Bibliotecario', 'Gestionar catálogo y préstamos'),
(3, 'Lector', 'Explorar catálogo y solicitar préstamos');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `password` varchar(255) NOT NULL,
  `rol_id` int(11) NOT NULL,
  `creado_en` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `nombre`, `email`, `password`, `rol_id`, `creado_en`) VALUES
(1, 'Administrador Demo', 'admin@biblioteca.com', '$2y$10$7WbgfN7qYB1g7M3M3I7Feu6W8nWj7eEoY.QZtjsyMYwUwqUTtfaaK', 1, '2025-11-13 20:43:32'),
(2, 'Bibliotecario Demo', 'biblio@biblioteca.com', '$2y$10$4idwh0iRsszGjsDbYz4NceQmEDvP3mEP3lFSpCbiwZK35W84KDxY6', 2, '2025-11-13 20:43:32'),
(3, 'Lector Demo', 'lector@biblioteca.com', '$2y$10$zOwDWpvIRy1SmjK/Bsoj7OkOoy8PEZMFcHESQ6LhQv2cf8G2bRkqW', 3, '2025-11-13 20:43:32'),
(4, 'Admin', 'admin@biblioteca.local', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1, '2025-11-13 20:43:32'),
(5, 'Zhen', 'ax078546@gmail.com', '$2y$10$ul9aU4nO4qVCBlgUWPWECOACKCSjWp/v0Iux.kG1vCF1DCP/Sby7.', 1, '2025-11-13 21:21:36'),
(6, 'Juan Martínez Díaz', 'bro_sigma@gmail.com', '$2y$10$X4Y7Bj/ZxVUQ64csvULze.vr8E8isqdeTusw2P2pC0THAH/a5wYdS', 3, '2025-11-13 22:06:31'),
(7, 'bibliotecario', 'bibl@biblioteca.local', '$2y$10$Ic.iNFhm15fgXS9z5zStU.AAafktwAo7WPzAB5gNtMDf1sFVugmum', 2, '2025-11-13 22:08:28');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nombre` (`nombre`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `rol_id` (`rol_id`),
  ADD KEY `idx_usuarios_email` (`email`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD CONSTRAINT `usuarios_ibfk_1` FOREIGN KEY (`rol_id`) REFERENCES `roles` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
