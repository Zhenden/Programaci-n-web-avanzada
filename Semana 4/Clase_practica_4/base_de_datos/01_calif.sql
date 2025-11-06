-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 06-11-2025 a las 20:55:15
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
-- Base de datos: `01_calif`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `asignaturas`
--

CREATE TABLE `asignaturas` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) DEFAULT NULL,
  `obs` text DEFAULT NULL,
  `usuario_id_creacion` int(11) DEFAULT NULL,
  `fecha_creacion` timestamp NULL DEFAULT NULL,
  `hora_creacion` time DEFAULT NULL,
  `usuario_id_actualizacion` int(11) DEFAULT NULL,
  `fecha_actualizacion` timestamp NULL DEFAULT NULL,
  `hora_actualizacion` time DEFAULT NULL,
  `usuario_id_eliminacion` int(11) DEFAULT NULL,
  `fecha_eliminacion` timestamp NULL DEFAULT NULL,
  `hora_eliminacion` time DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Volcado de datos para la tabla `asignaturas`
--

INSERT INTO `asignaturas` (`id`, `nombre`, `obs`, `usuario_id_creacion`, `fecha_creacion`, `hora_creacion`, `usuario_id_actualizacion`, `fecha_actualizacion`, `hora_actualizacion`, `usuario_id_eliminacion`, `fecha_eliminacion`, `hora_eliminacion`) VALUES
(1, 'Matemáticas', 'Álgebra, geometría y cálculo básico', 1, '2025-11-06 01:55:48', '20:55:48', NULL, NULL, NULL, NULL, NULL, NULL),
(2, 'Física', 'Mecánica, termodinámica y electricidad', 1, '2025-11-06 01:55:48', '20:55:48', NULL, NULL, NULL, NULL, NULL, NULL),
(3, 'Literatura', 'Análisis literario y redacción', 6, '2025-11-06 01:55:48', '20:55:48', NULL, NULL, NULL, NULL, NULL, NULL),
(4, 'Historia', 'Historia universal y nacional', 6, '2025-11-06 01:55:48', '20:55:48', NULL, NULL, NULL, NULL, NULL, NULL),
(5, 'Programación', 'Fundamentos de programación en PHP', 1, '2025-11-06 01:55:48', '20:55:48', NULL, NULL, NULL, NULL, NULL, NULL),
(6, 'Inglés', 'Inglés básico e intermedio', 6, '2025-11-06 01:55:48', '20:55:48', NULL, NULL, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `asignaturas_estudiante`
--

CREATE TABLE `asignaturas_estudiante` (
  `id` int(11) NOT NULL,
  `lugar_id` int(11) DEFAULT NULL,
  `asignatura_id` int(11) DEFAULT NULL,
  `usuario_id` int(11) DEFAULT NULL COMMENT 'Estudiante',
  `usuario_id_creacion` int(11) DEFAULT NULL,
  `fecha_creacion` timestamp NULL DEFAULT NULL,
  `hora_creacion` time DEFAULT NULL,
  `usuario_id_actualizacion` int(11) DEFAULT NULL,
  `fecha_actualizacion` timestamp NULL DEFAULT NULL,
  `hora_actualizacion` time DEFAULT NULL,
  `usuario_id_eliminacion` int(11) DEFAULT NULL,
  `fecha_eliminacion` timestamp NULL DEFAULT NULL,
  `hora_eliminacion` time DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Volcado de datos para la tabla `asignaturas_estudiante`
--

INSERT INTO `asignaturas_estudiante` (`id`, `lugar_id`, `asignatura_id`, `usuario_id`, `usuario_id_creacion`, `fecha_creacion`, `hora_creacion`, `usuario_id_actualizacion`, `fecha_actualizacion`, `hora_actualizacion`, `usuario_id_eliminacion`, `fecha_eliminacion`, `hora_eliminacion`) VALUES
(1, 1, 1, 2, 1, '2025-11-06 01:55:48', '20:55:48', NULL, NULL, NULL, NULL, NULL, NULL),
(2, 1, 2, 2, 1, '2025-11-06 01:55:48', '20:55:48', NULL, NULL, NULL, NULL, NULL, NULL),
(3, 1, 5, 2, 1, '2025-11-06 01:55:48', '20:55:48', NULL, NULL, NULL, NULL, NULL, NULL),
(4, 1, 1, 3, 1, '2025-11-06 01:55:48', '20:55:48', NULL, NULL, NULL, NULL, NULL, NULL),
(5, 1, 5, 3, 1, '2025-11-06 01:55:48', '20:55:48', NULL, NULL, NULL, NULL, NULL, NULL),
(6, 1, 6, 3, 1, '2025-11-06 01:55:48', '20:55:48', NULL, NULL, NULL, NULL, NULL, NULL),
(7, 2, 3, 4, 6, '2025-11-06 01:55:48', '20:55:48', NULL, NULL, NULL, NULL, NULL, NULL),
(8, 2, 4, 4, 6, '2025-11-06 01:55:48', '20:55:48', NULL, NULL, NULL, NULL, NULL, NULL),
(9, 2, 6, 4, 6, '2025-11-06 01:55:48', '20:55:48', NULL, NULL, NULL, NULL, NULL, NULL),
(10, 1, 1, 5, 1, '2025-11-06 01:55:48', '20:55:48', NULL, NULL, NULL, NULL, NULL, NULL),
(11, 1, 2, 5, 1, '2025-11-06 01:55:48', '20:55:48', NULL, NULL, NULL, NULL, NULL, NULL),
(12, 2, 3, 5, 6, '2025-11-06 01:55:48', '20:55:48', NULL, NULL, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `lugares`
--

CREATE TABLE `lugares` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) DEFAULT NULL,
  `obs` text DEFAULT NULL,
  `usuario_id_creacion` int(11) DEFAULT NULL,
  `fecha_creacion` timestamp NULL DEFAULT NULL,
  `hora_creacion` time DEFAULT NULL,
  `usuario_id_actualizacion` int(11) DEFAULT NULL,
  `fecha_actualizacion` timestamp NULL DEFAULT NULL,
  `hora_actualizacion` time DEFAULT NULL,
  `usuario_id_eliminacion` int(11) DEFAULT NULL,
  `fecha_eliminacion` timestamp NULL DEFAULT NULL,
  `hora_eliminacion` time DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Volcado de datos para la tabla `lugares`
--

INSERT INTO `lugares` (`id`, `nombre`, `obs`, `usuario_id_creacion`, `fecha_creacion`, `hora_creacion`, `usuario_id_actualizacion`, `fecha_actualizacion`, `hora_actualizacion`, `usuario_id_eliminacion`, `fecha_eliminacion`, `hora_eliminacion`) VALUES
(1, 'Aula 101', 'Salón de primer año - Capacidad 30 estudiantes', 1, '2025-11-06 01:55:48', '20:55:48', NULL, NULL, NULL, NULL, NULL, NULL),
(2, 'Aula 102', 'Salón de segundo año - Con proyector', 1, '2025-11-06 01:55:48', '20:55:48', NULL, NULL, NULL, NULL, NULL, NULL),
(3, 'Laboratorio de Ciencias', 'Equipado para prácticas de física y química', 1, '2025-11-06 01:55:48', '20:55:48', NULL, NULL, NULL, NULL, NULL, NULL),
(4, 'Sala de Computación', '20 computadores para prácticas', 1, '2025-11-06 01:55:48', '20:55:48', NULL, NULL, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `notas`
--

CREATE TABLE `notas` (
  `id` int(11) NOT NULL,
  `asignatura_id` int(11) DEFAULT NULL,
  `usuario_id` int(11) DEFAULT NULL COMMENT 'Estudiante',
  `parcial` int(1) DEFAULT NULL COMMENT '1 1er,2 2do ,3 Mejoramiento',
  `teoria` float(6,2) DEFAULT NULL,
  `practica` float(6,2) DEFAULT NULL,
  `obs` text DEFAULT NULL,
  `usuario_id_creacion` int(11) DEFAULT NULL,
  `fecha_creacion` timestamp NULL DEFAULT NULL,
  `hora_creacion` time DEFAULT NULL,
  `usuario_id_actualizacion` int(11) DEFAULT NULL,
  `fecha_actualizacion` timestamp NULL DEFAULT NULL,
  `hora_actualizacion` time DEFAULT NULL,
  `usuario_id_eliminacion` int(11) DEFAULT NULL,
  `fecha_eliminacion` timestamp NULL DEFAULT NULL,
  `hora_eliminacion` time DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Volcado de datos para la tabla `notas`
--

INSERT INTO `notas` (`id`, `asignatura_id`, `usuario_id`, `parcial`, `teoria`, `practica`, `obs`, `usuario_id_creacion`, `fecha_creacion`, `hora_creacion`, `usuario_id_actualizacion`, `fecha_actualizacion`, `hora_actualizacion`, `usuario_id_eliminacion`, `fecha_eliminacion`, `hora_eliminacion`) VALUES
(1, 1, 2, 1, 93.00, 95.00, 'Excelente desempeño en álgebra', 1, '2025-11-06 01:55:48', '20:55:48', 7, '2025-11-06 19:35:25', '14:35:25', NULL, NULL, NULL),
(2, 1, 3, 1, 9.80, 9.60, 'Buen trabajo, necesita practicar más', 1, '2025-11-06 01:55:48', '20:55:48', 7, '2025-11-06 18:49:17', '13:49:17', NULL, NULL, NULL),
(3, 1, 5, 1, 65.00, 70.00, 'Debe mejorar en ejercicios prácticos', 1, '2025-11-06 01:55:48', '20:55:48', NULL, NULL, NULL, NULL, NULL, NULL),
(4, 2, 2, 1, 92.00, 88.00, 'Muy buen entendimiento de conceptos', 1, '2025-11-06 01:55:48', '20:55:48', NULL, NULL, NULL, NULL, NULL, NULL),
(5, 2, 5, 1, 72.00, 68.00, 'Regular, necesita repasar teoría', 1, '2025-11-06 01:55:48', '20:55:48', NULL, NULL, NULL, NULL, NULL, NULL),
(6, 5, 2, 1, 88.00, 95.00, 'Excelente en prácticas de programación', 1, '2025-11-06 01:55:48', '20:55:48', NULL, NULL, NULL, NULL, NULL, NULL),
(7, 5, 3, 1, 82.00, 85.00, 'Buen desarrollo lógico', 1, '2025-11-06 01:55:48', '20:55:48', NULL, NULL, NULL, NULL, NULL, NULL),
(8, 3, 4, 1, 90.00, 87.00, 'Muy buena redacción y análisis', 6, '2025-11-06 01:55:48', '20:55:48', NULL, NULL, NULL, NULL, NULL, NULL),
(9, 3, 5, 1, 75.00, 72.00, 'Debe mejorar en análisis literario', 6, '2025-11-06 01:55:48', '20:55:48', NULL, NULL, NULL, NULL, NULL, NULL),
(10, 1, 2, 2, 88.00, 92.00, 'Mantiene excelente rendimiento', 1, '2025-11-06 01:55:48', '20:55:48', NULL, NULL, NULL, NULL, NULL, NULL),
(11, 1, 3, 2, 80.00, 85.00, 'Mejoró significativamente', 1, '2025-11-06 01:55:48', '20:55:48', NULL, NULL, NULL, NULL, NULL, NULL),
(12, 1, 5, 2, 70.00, 75.00, 'Ligera mejora, sigue necesitando apoyo', 1, '2025-11-06 01:55:48', '20:55:48', NULL, NULL, NULL, NULL, NULL, NULL),
(13, 2, 2, 2, 90.00, 92.00, 'Continúa con excelente desempeño', 1, '2025-11-06 01:55:48', '20:55:48', NULL, NULL, NULL, NULL, NULL, NULL),
(14, 2, 5, 2, 75.00, 72.00, 'Mejoró en teoría pero bajó en práctica', 1, '2025-11-06 01:55:48', '20:55:48', NULL, NULL, NULL, NULL, NULL, NULL),
(15, 1, 5, 3, 80.00, 78.00, 'Mejoró considerablemente en el examen de mejoramiento', 1, '2025-11-06 01:55:48', '20:55:48', NULL, NULL, NULL, NULL, NULL, NULL),
(16, 6, 3, 1, 85.00, 80.00, 'Buen dominio de vocabulario', 6, '2025-11-06 01:55:48', '20:55:48', NULL, NULL, NULL, NULL, NULL, NULL),
(17, 6, 4, 1, 92.00, 88.00, 'Excelente pronunciación y gramática', 6, '2025-11-06 01:55:48', '20:55:48', NULL, NULL, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `rol` int(1) DEFAULT NULL COMMENT '1 Docente, 2 Estudiante',
  `perfil_id` int(11) DEFAULT 3,
  `contrasena` varchar(100) DEFAULT NULL,
  `obs` text DEFAULT NULL,
  `usuario_id_creacion` int(11) DEFAULT NULL,
  `fecha_creacion` timestamp NULL DEFAULT NULL,
  `hora_creacion` time DEFAULT NULL,
  `usuario_id_actualizacion` int(11) DEFAULT NULL,
  `fecha_actualizacion` timestamp NULL DEFAULT NULL,
  `hora_actualizacion` time DEFAULT NULL,
  `usuario_id_eliminacion` int(11) DEFAULT NULL,
  `fecha_eliminacion` timestamp NULL DEFAULT NULL,
  `hora_eliminacion` time DEFAULT NULL,
  `estado` int(1) DEFAULT 1 COMMENT '1 Activo, 0 Inactivo',
  `ultimo_login` timestamp NULL DEFAULT NULL,
  `intentos_fallidos` int(2) DEFAULT 0,
  `bloqueado_hasta` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `nombre`, `email`, `rol`, `perfil_id`, `contrasena`, `obs`, `usuario_id_creacion`, `fecha_creacion`, `hora_creacion`, `usuario_id_actualizacion`, `fecha_actualizacion`, `hora_actualizacion`, `usuario_id_eliminacion`, `fecha_eliminacion`, `hora_eliminacion`, `estado`, `ultimo_login`, `intentos_fallidos`, `bloqueado_hasta`) VALUES
(2, 'Ana García López', 'ana.garcia@escuela.edu', 2, 2, '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Estudiante destacada en ciencias', 1, '2025-11-06 01:55:48', '20:55:48', 7, '2025-11-06 18:48:31', '13:48:31', NULL, NULL, NULL, 1, '2025-11-06 16:24:37', 0, NULL),
(3, 'Luis Rodríguez Pérez', 'luis.rodriguez@escuela.edu', 2, 2, '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Interesado en tecnología', 1, '2025-11-06 01:55:48', '20:55:48', 7, '2025-11-06 18:44:32', '13:44:32', NULL, NULL, NULL, 1, '2025-11-06 16:27:39', 0, NULL),
(4, 'María Torres Sánchez', 'maria.torres@escuela.edu', 2, 3, '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Buena en humanidades', 1, '2025-11-06 01:55:48', '20:55:48', 7, '2025-11-06 18:46:20', '13:46:20', NULL, NULL, NULL, 1, NULL, 0, NULL),
(5, 'Juan Martínez Díaz', 'juan.martinez@escuela.edu', 2, 3, '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Deportista', 1, '2025-11-06 01:55:48', '20:55:48', NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, 0, NULL),
(6, 'Profesora Elena Castro', 'elena.castro@escuela.edu', 1, 2, '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Docente de Literatura e Historia', 1, '2025-11-06 01:55:48', '20:55:48', NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-11-06 16:07:59', 0, NULL),
(7, 'Zhen Axel Hu', 'ax078546@gmail.com', 2, 1, '$2y$10$fSmTpx/DENsp1QHSuxu6NOXptlWM33akvzwiSqKUUCR0mlPBVkIpe', NULL, 1, '2025-11-06 17:23:04', '12:23:04', NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-11-06 19:41:53', 0, NULL),
(20, 'carlos', 'carlos.mendoza@escuela.edu', 2, 1, '$2y$10$aw9nScGmGGHzLgEaU2cPJubh/SGlnOdQQVUko8GabSO6TVsUYJDNK', '', 7, '2025-11-06 19:42:18', '14:42:18', NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-11-06 19:54:22', 0, NULL);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `asignaturas`
--
ALTER TABLE `asignaturas`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `asignaturas_estudiante`
--
ALTER TABLE `asignaturas_estudiante`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `lugares`
--
ALTER TABLE `lugares`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `notas`
--
ALTER TABLE `notas`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `asignaturas`
--
ALTER TABLE `asignaturas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `asignaturas_estudiante`
--
ALTER TABLE `asignaturas_estudiante`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT de la tabla `lugares`
--
ALTER TABLE `lugares`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `notas`
--
ALTER TABLE `notas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
