-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 07-11-2025 a las 02:18:18
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
-- Base de datos: `gestion_tareas`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `categorias_tareas`
--

CREATE TABLE `categorias_tareas` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `color` varchar(7) DEFAULT '#007bff',
  `estado` tinyint(4) DEFAULT 1,
  `usuario_id_creacion` int(11) DEFAULT NULL,
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `categorias_tareas`
--

INSERT INTO `categorias_tareas` (`id`, `nombre`, `descripcion`, `color`, `estado`, `usuario_id_creacion`, `fecha_creacion`) VALUES
(1, 'Trabajo', 'Tareas relacionadas con el trabajo', '#007bff', 1, 1, '2025-11-06 20:36:54'),
(2, 'Personal', 'Tareas personales', '#28a745', 1, 1, '2025-11-06 20:36:54'),
(3, 'Estudio', 'Tareas de estudio y aprendizaje', '#ffc107', 1, 1, '2025-11-06 20:36:54'),
(4, 'Salud', 'Tareas relacionadas con salud', '#dc3545', 1, 1, '2025-11-06 20:36:54'),
(5, 'Hogar', 'Tareas del hogar', '#6f42c1', 1, 1, '2025-11-06 20:36:54');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `comentarios_tareas`
--

CREATE TABLE `comentarios_tareas` (
  `id` int(11) NOT NULL,
  `tarea_id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `comentario` text NOT NULL,
  `adjuntos` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`adjuntos`)),
  `estado` tinyint(4) DEFAULT 1,
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `historial_tareas`
--

CREATE TABLE `historial_tareas` (
  `id` int(11) NOT NULL,
  `tarea_id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `accion` varchar(100) NOT NULL,
  `campo_afectado` varchar(100) DEFAULT NULL,
  `valor_anterior` text DEFAULT NULL,
  `valor_nuevo` text DEFAULT NULL,
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `log_actividades`
--

CREATE TABLE `log_actividades` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) DEFAULT NULL,
  `accion` varchar(100) NOT NULL,
  `modulo` varchar(50) NOT NULL,
  `detalles` text DEFAULT NULL,
  `ip` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `perfiles`
--

CREATE TABLE `perfiles` (
  `id` int(11) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `permisos` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`permisos`)),
  `estado` tinyint(4) DEFAULT 1,
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `perfiles`
--

INSERT INTO `perfiles` (`id`, `nombre`, `descripcion`, `permisos`, `estado`, `fecha_creacion`) VALUES
(1, 'Administrador', 'Acceso completo al sistema', '{\r\n    \"usuarios\": [\"crear\", \"leer\", \"actualizar\", \"eliminar\"],\r\n    \"tareas\": [\"crear\", \"leer\", \"actualizar\", \"eliminar\", \"asignar\", \"completar\"],\r\n    \"categorias\": [\"crear\", \"leer\", \"actualizar\", \"eliminar\"],\r\n    \"reportes\": [\"leer\", \"exportar\"]\r\n}', 1, '2025-11-06 20:36:54'),
(2, 'Supervisor', 'Puede gestionar tareas y usuarios limitados', '{\r\n    \"usuarios\": [\"leer\", \"actualizar\"],\r\n    \"tareas\": [\"crear\", \"leer\", \"actualizar\", \"asignar\", \"completar\"],\r\n    \"categorias\": [\"leer\"],\r\n    \"reportes\": [\"leer\"]\r\n}', 1, '2025-11-06 20:36:54'),
(3, 'Usuario', 'Puede gestionar sus propias tareas', '{\r\n    \"tareas\": [\"crear\", \"leer\", \"actualizar\", \"completar\"],\r\n    \"categorias\": [\"leer\"]\r\n}', 1, '2025-11-06 20:36:54');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tareas`
--

CREATE TABLE `tareas` (
  `id` int(11) NOT NULL,
  `titulo` varchar(255) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `categoria_id` int(11) DEFAULT NULL,
  `usuario_id` int(11) NOT NULL,
  `usuario_creador_id` int(11) NOT NULL,
  `prioridad` enum('baja','media','alta','urgente') DEFAULT 'media',
  `estado` enum('pendiente','en_progreso','completada','cancelada') DEFAULT 'pendiente',
  `fecha_vencimiento` date DEFAULT NULL,
  `fecha_completada` datetime DEFAULT NULL,
  `porcentaje_completado` int(11) DEFAULT 0,
  `es_publica` tinyint(4) DEFAULT 0,
  `etiquetas` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`etiquetas`)),
  `adjuntos` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`adjuntos`)),
  `obs` text DEFAULT NULL,
  `usuario_id_creacion` int(11) DEFAULT NULL,
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp(),
  `hora_creacion` time DEFAULT curtime(),
  `usuario_id_actualizacion` int(11) DEFAULT NULL,
  `fecha_actualizacion` datetime DEFAULT NULL,
  `hora_actualizacion` time DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tareas`
--

INSERT INTO `tareas` (`id`, `titulo`, `descripcion`, `categoria_id`, `usuario_id`, `usuario_creador_id`, `prioridad`, `estado`, `fecha_vencimiento`, `fecha_completada`, `porcentaje_completado`, `es_publica`, `etiquetas`, `adjuntos`, `obs`, `usuario_id_creacion`, `fecha_creacion`, `hora_creacion`, `usuario_id_actualizacion`, `fecha_actualizacion`, `hora_actualizacion`) VALUES
(1, 'Revisar documentación del proyecto', 'Revisar y actualizar la documentación técnica del proyecto X', 1, 1, 1, 'alta', 'completada', '2025-11-13', '2025-11-06 19:50:36', 0, 1, NULL, NULL, NULL, 1, '2025-11-06 20:36:54', '15:36:54', NULL, NULL, NULL),
(2, 'ser negro', 'Realizar compra semanal de víveres para la casa', 2, 1, 1, 'media', 'completada', '2025-11-08', '2025-11-06 19:50:42', 0, 0, NULL, NULL, NULL, 1, '2025-11-06 20:36:54', '15:36:54', NULL, NULL, NULL),
(3, 'Estudiar para examen', 'Preparar material de estudio para el examen de certificación', 3, 1, 1, 'alta', 'completada', '2025-11-20', '2025-11-06 19:50:26', 0, 0, NULL, NULL, NULL, 1, '2025-11-06 20:36:54', '15:36:54', NULL, NULL, NULL),
(17, 'sigma boy', '1', NULL, 1, 1, 'media', 'pendiente', NULL, NULL, 0, 0, NULL, NULL, NULL, NULL, '2025-11-07 00:49:51', '19:49:51', NULL, NULL, NULL),
(18, 'exacto', '', NULL, 1, 1, 'alta', 'pendiente', '2025-11-20', NULL, 0, 0, NULL, NULL, NULL, NULL, '2025-11-07 00:52:24', '19:52:24', NULL, NULL, NULL),
(19, 'jodido', '', NULL, 1, 1, 'media', 'pendiente', NULL, NULL, 0, 0, NULL, NULL, NULL, NULL, '2025-11-07 00:52:38', '19:52:38', NULL, NULL, NULL),
(20, 'aat1', '', NULL, 1, 1, 'media', 'pendiente', NULL, NULL, 0, 0, NULL, NULL, NULL, NULL, '2025-11-07 00:52:48', '19:52:48', NULL, NULL, NULL),
(21, 'tarea de maria', '', NULL, 4, 4, 'media', 'pendiente', '2025-11-06', NULL, 0, 0, NULL, NULL, NULL, NULL, '2025-11-07 01:14:18', '20:14:18', NULL, NULL, NULL),
(22, '123', '', NULL, 4, 4, 'media', 'pendiente', '2025-11-03', NULL, 0, 0, NULL, NULL, NULL, NULL, '2025-11-07 01:14:52', '20:14:52', NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `contrasena` varchar(255) NOT NULL,
  `perfil_id` int(11) NOT NULL,
  `rol` int(11) DEFAULT 3,
  `estado` tinyint(4) DEFAULT 1,
  `intentos_fallidos` int(11) DEFAULT 0,
  `bloqueado_hasta` datetime DEFAULT NULL,
  `ultimo_login` datetime DEFAULT NULL,
  `obs` text DEFAULT NULL,
  `usuario_id_creacion` int(11) DEFAULT NULL,
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp(),
  `hora_creacion` time DEFAULT curtime(),
  `usuario_id_actualizacion` int(11) DEFAULT NULL,
  `fecha_actualizacion` datetime DEFAULT NULL,
  `hora_actualizacion` time DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `nombre`, `email`, `contrasena`, `perfil_id`, `rol`, `estado`, `intentos_fallidos`, `bloqueado_hasta`, `ultimo_login`, `obs`, `usuario_id_creacion`, `fecha_creacion`, `hora_creacion`, `usuario_id_actualizacion`, `fecha_actualizacion`, `hora_actualizacion`) VALUES
(1, 'Administrador Principal', 'admin@sistema.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1, 1, 1, 0, NULL, '2025-11-06 20:17:48', NULL, 1, '2025-11-06 20:36:54', '15:36:54', NULL, NULL, NULL),
(4, 'María García', 'maria@demo.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 3, 3, 1, 0, NULL, '2025-11-06 20:13:42', NULL, 1, '2025-11-07 01:13:03', '20:13:03', NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `vista_estadisticas_tareas`
-- (Véase abajo para la vista actual)
--
CREATE TABLE `vista_estadisticas_tareas` (
`usuario_id` int(11)
,`usuario_nombre` varchar(100)
,`total_tareas` bigint(21)
,`tareas_completadas` bigint(21)
,`tareas_pendientes` bigint(21)
,`tareas_en_progreso` bigint(21)
,`tareas_vencidas` bigint(21)
);

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `vista_tareas_completas`
-- (Véase abajo para la vista actual)
--
CREATE TABLE `vista_tareas_completas` (
`id` int(11)
,`titulo` varchar(255)
,`descripcion` text
,`categoria_id` int(11)
,`usuario_id` int(11)
,`usuario_creador_id` int(11)
,`prioridad` enum('baja','media','alta','urgente')
,`estado` enum('pendiente','en_progreso','completada','cancelada')
,`fecha_vencimiento` date
,`fecha_completada` datetime
,`porcentaje_completado` int(11)
,`es_publica` tinyint(4)
,`etiquetas` longtext
,`adjuntos` longtext
,`obs` text
,`usuario_id_creacion` int(11)
,`fecha_creacion` timestamp
,`hora_creacion` time
,`usuario_id_actualizacion` int(11)
,`fecha_actualizacion` datetime
,`hora_actualizacion` time
,`usuario_nombre` varchar(100)
,`usuario_email` varchar(100)
,`creador_nombre` varchar(100)
,`categoria_nombre` varchar(100)
,`categoria_color` varchar(7)
);

-- --------------------------------------------------------

--
-- Estructura para la vista `vista_estadisticas_tareas`
--
DROP TABLE IF EXISTS `vista_estadisticas_tareas`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `vista_estadisticas_tareas`  AS SELECT `u`.`id` AS `usuario_id`, `u`.`nombre` AS `usuario_nombre`, count(`t`.`id`) AS `total_tareas`, count(case when `t`.`estado` = 'completada' then 1 end) AS `tareas_completadas`, count(case when `t`.`estado` = 'pendiente' then 1 end) AS `tareas_pendientes`, count(case when `t`.`estado` = 'en_progreso' then 1 end) AS `tareas_en_progreso`, count(case when `t`.`fecha_vencimiento` < curdate() and `t`.`estado` <> 'completada' then 1 end) AS `tareas_vencidas` FROM (`usuarios` `u` left join `tareas` `t` on(`u`.`id` = `t`.`usuario_id`)) GROUP BY `u`.`id`, `u`.`nombre` ;

-- --------------------------------------------------------

--
-- Estructura para la vista `vista_tareas_completas`
--
DROP TABLE IF EXISTS `vista_tareas_completas`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `vista_tareas_completas`  AS SELECT `t`.`id` AS `id`, `t`.`titulo` AS `titulo`, `t`.`descripcion` AS `descripcion`, `t`.`categoria_id` AS `categoria_id`, `t`.`usuario_id` AS `usuario_id`, `t`.`usuario_creador_id` AS `usuario_creador_id`, `t`.`prioridad` AS `prioridad`, `t`.`estado` AS `estado`, `t`.`fecha_vencimiento` AS `fecha_vencimiento`, `t`.`fecha_completada` AS `fecha_completada`, `t`.`porcentaje_completado` AS `porcentaje_completado`, `t`.`es_publica` AS `es_publica`, `t`.`etiquetas` AS `etiquetas`, `t`.`adjuntos` AS `adjuntos`, `t`.`obs` AS `obs`, `t`.`usuario_id_creacion` AS `usuario_id_creacion`, `t`.`fecha_creacion` AS `fecha_creacion`, `t`.`hora_creacion` AS `hora_creacion`, `t`.`usuario_id_actualizacion` AS `usuario_id_actualizacion`, `t`.`fecha_actualizacion` AS `fecha_actualizacion`, `t`.`hora_actualizacion` AS `hora_actualizacion`, `u`.`nombre` AS `usuario_nombre`, `u`.`email` AS `usuario_email`, `uc`.`nombre` AS `creador_nombre`, `c`.`nombre` AS `categoria_nombre`, `c`.`color` AS `categoria_color` FROM (((`tareas` `t` left join `usuarios` `u` on(`t`.`usuario_id` = `u`.`id`)) left join `usuarios` `uc` on(`t`.`usuario_creador_id` = `uc`.`id`)) left join `categorias_tareas` `c` on(`t`.`categoria_id` = `c`.`id`)) WHERE `t`.`estado` <> 'cancelada' ;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `categorias_tareas`
--
ALTER TABLE `categorias_tareas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `usuario_id_creacion` (`usuario_id_creacion`);

--
-- Indices de la tabla `comentarios_tareas`
--
ALTER TABLE `comentarios_tareas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tarea_id` (`tarea_id`),
  ADD KEY `usuario_id` (`usuario_id`);

--
-- Indices de la tabla `historial_tareas`
--
ALTER TABLE `historial_tareas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tarea_id` (`tarea_id`),
  ADD KEY `usuario_id` (`usuario_id`);

--
-- Indices de la tabla `log_actividades`
--
ALTER TABLE `log_actividades`
  ADD PRIMARY KEY (`id`),
  ADD KEY `usuario_id` (`usuario_id`);

--
-- Indices de la tabla `perfiles`
--
ALTER TABLE `perfiles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nombre` (`nombre`);

--
-- Indices de la tabla `tareas`
--
ALTER TABLE `tareas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `usuario_creador_id` (`usuario_creador_id`),
  ADD KEY `usuario_id_creacion` (`usuario_id_creacion`),
  ADD KEY `usuario_id_actualizacion` (`usuario_id_actualizacion`),
  ADD KEY `idx_tareas_usuario_id` (`usuario_id`),
  ADD KEY `idx_tareas_estado` (`estado`),
  ADD KEY `idx_tareas_prioridad` (`prioridad`),
  ADD KEY `idx_tareas_fecha_vencimiento` (`fecha_vencimiento`),
  ADD KEY `idx_tareas_categoria_id` (`categoria_id`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `usuario_id_creacion` (`usuario_id_creacion`),
  ADD KEY `usuario_id_actualizacion` (`usuario_id_actualizacion`),
  ADD KEY `idx_usuarios_email` (`email`),
  ADD KEY `idx_usuarios_perfil_id` (`perfil_id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `categorias_tareas`
--
ALTER TABLE `categorias_tareas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `comentarios_tareas`
--
ALTER TABLE `comentarios_tareas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `historial_tareas`
--
ALTER TABLE `historial_tareas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `log_actividades`
--
ALTER TABLE `log_actividades`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `perfiles`
--
ALTER TABLE `perfiles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `tareas`
--
ALTER TABLE `tareas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `categorias_tareas`
--
ALTER TABLE `categorias_tareas`
  ADD CONSTRAINT `categorias_tareas_ibfk_1` FOREIGN KEY (`usuario_id_creacion`) REFERENCES `usuarios` (`id`);

--
-- Filtros para la tabla `comentarios_tareas`
--
ALTER TABLE `comentarios_tareas`
  ADD CONSTRAINT `comentarios_tareas_ibfk_1` FOREIGN KEY (`tarea_id`) REFERENCES `tareas` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `comentarios_tareas_ibfk_2` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`);

--
-- Filtros para la tabla `historial_tareas`
--
ALTER TABLE `historial_tareas`
  ADD CONSTRAINT `historial_tareas_ibfk_1` FOREIGN KEY (`tarea_id`) REFERENCES `tareas` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `historial_tareas_ibfk_2` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`);

--
-- Filtros para la tabla `log_actividades`
--
ALTER TABLE `log_actividades`
  ADD CONSTRAINT `log_actividades_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`);

--
-- Filtros para la tabla `tareas`
--
ALTER TABLE `tareas`
  ADD CONSTRAINT `tareas_ibfk_1` FOREIGN KEY (`categoria_id`) REFERENCES `categorias_tareas` (`id`),
  ADD CONSTRAINT `tareas_ibfk_2` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`),
  ADD CONSTRAINT `tareas_ibfk_3` FOREIGN KEY (`usuario_creador_id`) REFERENCES `usuarios` (`id`),
  ADD CONSTRAINT `tareas_ibfk_4` FOREIGN KEY (`usuario_id_creacion`) REFERENCES `usuarios` (`id`),
  ADD CONSTRAINT `tareas_ibfk_5` FOREIGN KEY (`usuario_id_actualizacion`) REFERENCES `usuarios` (`id`);

--
-- Filtros para la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD CONSTRAINT `usuarios_ibfk_1` FOREIGN KEY (`perfil_id`) REFERENCES `perfiles` (`id`),
  ADD CONSTRAINT `usuarios_ibfk_2` FOREIGN KEY (`usuario_id_creacion`) REFERENCES `usuarios` (`id`),
  ADD CONSTRAINT `usuarios_ibfk_3` FOREIGN KEY (`usuario_id_actualizacion`) REFERENCES `usuarios` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
