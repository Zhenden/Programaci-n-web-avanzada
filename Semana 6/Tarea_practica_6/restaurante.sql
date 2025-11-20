-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 20-11-2025 a las 00:59:37
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
-- Base de datos: `restaurante`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `comments`
--
CREATE DATABASE IF NOT EXISTS restaurante 
CHARACTER SET utf8mb4 
COLLATE utf8mb4_unicode_ci;

-- Usar la base de datos
USE restaurante;

CREATE TABLE `comments` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `dish_id` int(11) NOT NULL,
  `comment` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `comments`
--

INSERT INTO `comments` (`id`, `user_id`, `dish_id`, `comment`, `created_at`) VALUES
(1, 5, 1, 'es una basura que asco', '2025-11-19 17:59:46'),
(2, 5, 2, 'esta muy cara', '2025-11-19 21:13:32'),
(3, 5, 1, 'a', '2025-11-19 21:16:51'),
(6, 5, 2, 'poronga de perrro', '2025-11-19 22:10:52'),
(7, 8, 1, 'sigma boy', '2025-11-19 23:07:30'),
(8, 7, 2, 'COMO QUE CUESTAA 100 DOLARES', '2025-11-19 23:25:37'),
(9, 5, 4, 'QUE TIENE BLUETOOTH EL PULPO O QUE', '2025-11-19 23:50:03');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `dishes`
--

CREATE TABLE `dishes` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `dishes`
--

INSERT INTO `dishes` (`id`, `name`, `description`, `price`, `created_at`) VALUES
(1, 'Pasta Alfredo', 'Pasta con salsa cremosa y parmesano', 8.50, '2025-11-19 16:40:11'),
(2, 'Ensalada César', 'Lechuga, pollo, aderezo César', 100.00, '2025-11-19 16:40:11'),
(4, 'Pulpo a la cocacola', 'EL MEJOR PULPO QUE VAS A PROBAR', 10000.00, '2025-11-19 23:49:24');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `dish_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `status` enum('Pending','Preparing','Ready to serve','Served') NOT NULL DEFAULT 'Pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `dish_id`, `quantity`, `status`, `created_at`) VALUES
(1, 5, 2, 5, 'Ready to serve', '2025-11-19 20:57:45'),
(2, 5, 2, 4, 'Preparing', '2025-11-19 22:10:39'),
(3, 5, 4, 7, 'Pending', '2025-11-19 23:50:17'),
(4, 5, 1, 1, 'Served', '2025-11-19 23:50:30');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `roles`
--

CREATE TABLE `roles` (
  `id` int(11) NOT NULL,
  `name` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `roles`
--

INSERT INTO `roles` (`id`, `name`) VALUES
(1, 'Administrator'),
(2, 'Chef'),
(3, 'Waiter'),
(4, 'Client');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `role_id`, `created_at`) VALUES
(5, 'admin', 'admin@gmail.com', '$2y$10$F4Z2mu3i1L.rOrhQbj5KQ.1hW2LN5V082GSw9EkNR75tB9d3piGBq', 1, '2025-11-19 17:16:09'),
(7, 'chef', 'chef@gmail.com', '$2y$10$ynNBobwvZugcDlBhWGOPeuNRFjk5Ig9uPnirsM5hdUfotVRTLvbr2', 2, '2025-11-19 20:36:24'),
(8, 'usuario', 'usuario@gmail.com', '$2y$10$kc0QDTvZv5hm60rZK6bPt.AfTf1okY1/jtqYP5BH47/4IE24wVPdy', 4, '2025-11-19 23:06:47'),
(9, 'waiter', 'waiter@gmail.com', '$2y$10$LmDoyfgxduFbS6SmXIFcyeZk9Hix4tBX5zreDLGsnG54HgLVUjP1C', 3, '2025-11-19 23:26:49');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `comments`
--
ALTER TABLE `comments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `dish_id` (`dish_id`);

--
-- Indices de la tabla `dishes`
--
ALTER TABLE `dishes`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `dish_id` (`dish_id`);

--
-- Indices de la tabla `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `role_id` (`role_id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `comments`
--
ALTER TABLE `comments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de la tabla `dishes`
--
ALTER TABLE `dishes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `comments`
--
ALTER TABLE `comments`
  ADD CONSTRAINT `comments_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `comments_ibfk_2` FOREIGN KEY (`dish_id`) REFERENCES `dishes` (`id`);

--
-- Filtros para la tabla `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `orders_ibfk_2` FOREIGN KEY (`dish_id`) REFERENCES `dishes` (`id`);

--
-- Filtros para la tabla `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
