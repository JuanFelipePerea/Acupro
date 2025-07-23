-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 11-06-2025 a las 00:48:48
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
-- Base de datos: `acupro`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `acudientes`
--

CREATE TABLE `acudientes` (
  `num_acudiente` int(11) NOT NULL,
  `nom_acudiente` varchar(100) NOT NULL,
  `telefono` varchar(20) NOT NULL,
  `relacion` varchar(50) NOT NULL,
  `cod_estudiante` int(8) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `acudientes`
--

INSERT INTO `acudientes` (`num_acudiente`, `nom_acudiente`, `telefono`, `relacion`, `cod_estudiante`) VALUES
(18, 'dasd', 'dsa', 'dsa', NULL),
(19, 'Martha López', '3101234567', 'Madre', 54),
(20, 'Roberto González', '3109876543', 'Padre', 55),
(21, 'Patricia Pérez', '3157894561', 'Madre', 56),
(22, 'Jorge Rodríguez', '3004561237', 'Padre', 57),
(23, 'Carmen Flores', '3167894523', 'Madre', 58),
(24, 'Fernando Díaz', '3187452369', 'Padre', 59),
(25, 'Beatriz Morales', '3112589637', 'Madre', 60),
(26, 'Ricardo Torres', '3144569871', 'Padre', 61),
(27, 'Mónica Ortiz', '3154789632', 'Madre', 62),
(28, 'Alberto Ramos', '3177896541', 'Padre', 63),
(29, 'papa', '3435345', 'pene', 63);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `citas`
--

CREATE TABLE `citas` (
  `num_cita` int(8) NOT NULL,
  `fecha` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `motivo` varchar(100) NOT NULL,
  `acc_docente` varchar(400) NOT NULL,
  `nom_docente` varchar(50) NOT NULL,
  `descripcion` varchar(800) NOT NULL,
  `tipo` varchar(20) NOT NULL DEFAULT 'estudiante',
  `cod_estudiante` int(8) UNSIGNED DEFAULT NULL,
  `num_acudiente` int(11) DEFAULT NULL,
  `hora_inicio` time NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `citas`
--

INSERT INTO `citas` (`num_cita`, `fecha`, `motivo`, `acc_docente`, `nom_docente`, `descripcion`, `tipo`, `cod_estudiante`, `num_acudiente`, `hora_inicio`) VALUES
(12417, '2025-06-12 13:30:00', 'Bajo rendimiento académico', '12345678', 'Prof. García', 'El estudiante presenta dificultades en matemáticas y requiere apoyo adicional. Se recomienda refuerzo en operaciones básicas.', 'estudiante', 54, NULL, '08:30:00'),
(12418, '2025-06-12 14:15:00', 'Comportamiento disruptivo', '87654321', 'Prof. López', 'Episodios de indisciplina en clase. Conversación sobre normas de convivencia y establecimiento de compromisos.', 'estudiante', 55, NULL, '09:15:00'),
(12419, '2025-06-13 15:00:00', 'Seguimiento académico', '11223344', 'Prof. Martínez', 'Revisión de avances en lectoescritura. El estudiante muestra mejoras significativas en comprensión lectora.', 'estudiante', 56, NULL, '10:00:00'),
(12420, '2025-06-13 19:30:00', 'Orientación vocacional', '55667788', 'Prof. Rodríguez', 'Sesión de orientación para elección de énfasis académico. Discusión sobre intereses y aptitudes del estudiante.', 'estudiante', 57, NULL, '14:30:00'),
(12421, '2025-06-14 12:45:00', 'Dificultades de aprendizaje', '99887766', 'Prof. Sánchez', 'Evaluación de posibles dificultades en el procesamiento de información. Remisión a psicología educativa.', 'estudiante', 58, NULL, '07:45:00'),
(12422, '2025-06-14 16:20:00', 'Excelencia académica', '44556677', 'Prof. González', 'Reconocimiento por destacado desempeño. Conversación sobre oportunidades de participación en olimpiadas académicas.', 'estudiante', 59, NULL, '11:20:00'),
(12423, '2025-06-15 18:00:00', 'Adaptación escolar', '77889900', 'Prof. Hernández', 'Seguimiento del proceso de adaptación al nuevo grado. El estudiante muestra buena integración social.', 'estudiante', 60, NULL, '13:00:00'),
(12424, '2025-06-15 20:30:00', 'Proyecto de investigación', '33445566', 'Prof. Pérez', 'Asesoría para desarrollo de proyecto de ciencias naturales. Definición de metodología y cronograma.', 'estudiante', 61, NULL, '15:30:00'),
(12425, '2025-06-16 13:00:00', 'Bullying escolar', '66778899', 'Prof. Ramírez', 'Atención a situación de acoso escolar. Activación de protocolo y seguimiento psicosocial.', 'estudiante', 62, NULL, '08:00:00'),
(12426, '2025-06-16 15:45:00', 'Habilidades sociales', '22334455', 'Prof. Torres', 'Trabajo en desarrollo de habilidades de comunicación y trabajo en equipo. Estrategias de integración grupal.', 'estudiante', 63, NULL, '10:45:00'),
(12427, '2025-06-17 21:00:00', 'Reunión informativa', '88990011', 'Prof. Flores', 'Informe sobre el rendimiento académico y comportamental del estudiante. Establecimiento de estrategias de apoyo en casa.', 'acudiente', NULL, 19, '16:00:00'),
(12428, '2025-06-17 22:30:00', 'Seguimiento disciplinario', '55443322', 'Prof. Díaz', 'Conversación sobre comportamiento del estudiante en clase. Acuerdos de colaboración familia-escuela.', 'acudiente', NULL, 20, '17:30:00'),
(12429, '2025-06-18 14:30:00', 'Apoyo psicológico', '77665544', 'Prof. Morales', 'Remisión a psicología por dificultades emocionales. Orientación a la familia sobre manejo de situaciones.', 'acudiente', NULL, 21, '09:30:00'),
(12430, '2025-06-18 16:00:00', 'Excelencia académica', '99887755', 'Prof. Castillo', 'Felicitación por el destacado desempeño del estudiante. Sugerencias para mantener la motivación académica.', 'acudiente', NULL, 22, '11:00:00'),
(12431, '2025-06-19 19:15:00', 'Dificultades de aprendizaje', '11229988', 'Prof. Ortiz', 'Explicación sobre evaluación psicopedagógica. Estrategias de apoyo y adaptaciones curriculares necesarias.', 'acudiente', NULL, 23, '14:15:00'),
(12432, '2025-06-19 20:45:00', 'Participación familiar', '66554433', 'Prof. Ramos', 'Invitación a mayor participación en actividades escolares. Importancia del acompañamiento familiar.', 'acudiente', NULL, 24, '15:45:00'),
(12433, '2025-06-20 13:15:00', 'Transición de grado', '44332211', 'Prof. Rivera', 'Preparación para el cambio de grado. Recomendaciones para apoyar la adaptación del estudiante.', 'acudiente', NULL, 25, '08:15:00'),
(12434, '2025-06-20 15:30:00', 'Proyecto pedagógico', '77889966', 'Prof. Cruz', 'Invitación a participar en proyecto de lectura familiar. Explicación de metodología y beneficios.', 'acudiente', NULL, 26, '10:30:00'),
(12435, '2025-06-21 18:45:00', 'Salud escolar', '55667799', 'Prof. Gómez', 'Conversación sobre cuidados de salud y prevención en el ambiente escolar. Protocolos de bioseguridad.', 'acudiente', NULL, 27, '13:45:00'),
(12436, '2025-06-21 21:30:00', 'Evaluación integral', '33445577', 'Prof. Reyes', 'Revisión del proceso académico y convivencial del estudiante. Establecimiento de metas para el siguiente período.', 'acudiente', NULL, 28, '16:30:00'),
(12437, '2025-06-22 14:00:00', 'Talleres de refuerzo', '88776655', 'Prof. Vargas', 'Inscripción en talleres de refuerzo académico. Horarios y metodología de trabajo.', 'estudiante', 48, NULL, '09:00:00'),
(12438, '2025-06-22 19:00:00', 'Compromiso académico', '22113344', 'Prof. Jiménez', 'Firma de compromiso académico por bajo rendimiento. Establecimiento de metas y plazos.', 'acudiente', NULL, 29, '14:00:00'),
(12439, '2025-06-23 12:30:00', 'Orientación profesional', '66559988', 'Prof. Gutiérrez', 'Asesoría sobre opciones de educación superior. Exploración de programas académicos afines.', 'estudiante', 53, NULL, '07:30:00'),
(12440, '2025-06-23 16:15:00', 'Convivencia escolar', '44227766', 'Prof. Álvarez', 'Trabajo en resolución de conflictos. Desarrollo de habilidades para la sana convivencia.', 'estudiante', 64, NULL, '11:15:00'),
(12441, '2025-06-24 20:00:00', 'Actividades extracurriculares', '77664422', 'Prof. Castro', 'Información sobre clubes y actividades deportivas. Beneficios de la participación integral.', 'acudiente', NULL, 20, '15:00:00');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `docente`
--

CREATE TABLE `docente` (
  `nom_docente` varchar(8) NOT NULL,
  `n_estudiantes` smallint(4) NOT NULL,
  `an_docente` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `docente_materia`
--

CREATE TABLE `docente_materia` (
  `nom_docente` varchar(8) NOT NULL,
  `nm_materia` varchar(8) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `estudiantes`
--

CREATE TABLE `estudiantes` (
  `cod_estudiante` int(8) UNSIGNED NOT NULL,
  `nombres` char(50) NOT NULL,
  `apellidos` char(50) NOT NULL,
  `edad` tinyint(3) NOT NULL,
  `n_grado` varchar(50) NOT NULL,
  `correo_estudiante` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `estudiantes`
--

INSERT INTO `estudiantes` (`cod_estudiante`, `nombres`, `apellidos`, `edad`, `n_grado`, `correo_estudiante`) VALUES
(48, 'Angel Tomas', 'Amaya Correa', 15, '10B', 'angel.amaya@acupro.edu.co'),
(53, 'Juan Felipe', 'Perea Cuaran', 15, '10A', 'juan.perea@acupro.edu.co'),
(54, 'María', 'González López', 7, '1A', 'maria.gonzalez@acupro.edu.co'),
(55, 'José', 'Rodríguez Sánchez', 7, '1B', 'jose.rodriguez@acupro.edu.co'),
(56, 'Ana', 'Martínez Pérez', 7, '1C', 'ana.martinez@acupro.edu.co'),
(57, 'Carlos', 'López Torres', 8, '2A', NULL),
(58, 'Laura', 'Sánchez Flores', 8, '2B', NULL),
(59, 'Daniel', 'Pérez Díaz', 8, '2C', NULL),
(60, 'Sofía', 'Fernández Morales', 9, '3A', NULL),
(61, 'Diego', 'Torres Castillo', 9, '3B', NULL),
(62, 'Valentina', 'Ramírez Ortiz', 9, '3C', NULL),
(63, 'Javier', 'Gómez Ramos', 10, '4A', NULL),
(64, 'Camila', 'Díaz Rivera', 10, '4B', NULL),
(65, 'Alejandro', 'Castro Cruz', 10, '4C', NULL),
(66, 'Isabella', 'Ruiz Gómez', 11, '5A', NULL),
(67, 'Sebastián', 'Hernández Reyes', 11, '5B', NULL),
(68, 'Valeria', 'Jiménez Vargas', 11, '5C', NULL),
(69, 'Mateo', 'Morales Jiménez', 12, '6A', NULL),
(70, 'Gabriela', 'Álvarez Gutiérrez', 12, '6B', NULL),
(71, 'Santiago', 'Silva Álvarez', 12, '6C', NULL),
(72, 'Natalia', 'Vargas Castro', 13, '7A', NULL),
(73, 'Samuel', 'Romero Fernández', 13, '7B', NULL),
(74, 'Lucía', 'Medina Medina', 13, '7C', NULL),
(75, 'David', 'Ortiz Rojas', 14, '8A', NULL),
(76, 'Mariana', 'Rojas Navarro', 14, '8B', NULL),
(77, 'Lucas', 'Navarro Moreno', 14, '8C', NULL),
(78, 'Daniela', 'Moreno Romero', 15, '9A', NULL),
(79, 'Andrés', 'Acosta Acosta', 15, '9B', NULL),
(80, 'Paula', 'Herrera Herrera', 15, '9C', NULL),
(81, 'Nicolás', 'Flores Flores', 16, '10A', NULL),
(82, 'Catalina', 'Rivera Rivera', 16, '10B', NULL),
(83, 'Felipe', 'Cruz Cruz', 16, '10C', NULL),
(84, 'Antonia', 'Reyes Reyes', 17, '11A', NULL),
(85, 'Tomás', 'Gutiérrez Gutiérrez', 17, '11B', NULL),
(86, 'Carolina', 'Álvarez Álvarez', 17, '11C', NULL),
(87, 'Samuel', 'Amaya', 9, '10A', NULL),
(92, 'Samuel', 'Amaya', 1, '10A', NULL),
(93, 'Angel Tomas', 'Amaya Amaya', 15, '9B', NULL),
(94, 'Angel Tomas', 'gyu', 6, '9B', NULL),
(95, 'pok', 'pĺ', 2, '12', NULL),
(96, 'xdgf', 'xdgfs', 2, '12', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `grado`
--

CREATE TABLE `grado` (
  `n_grado` varchar(8) NOT NULL,
  `n_estudiantes` smallint(4) NOT NULL,
  `an_docente` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `grado`
--

INSERT INTO `grado` (`n_grado`, `n_estudiantes`, `an_docente`) VALUES
('10A', 28, 'Prof. Rojas'),
('10B', 26, 'Prof. Navarro'),
('10C', 24, 'Prof. Moreno'),
('11A', 27, 'Prof. Romero'),
('11B', 25, 'Prof. Acosta'),
('11C', 23, 'Prof. Herrera'),
('1A', 25, 'Prof. García'),
('1B', 28, 'Prof. López'),
('1C', 22, 'Prof. Martínez'),
('2A', 30, 'Prof. Rodríguez'),
('2B', 27, 'Prof. Sánchez'),
('2C', 26, 'Prof. González'),
('3A', 24, 'Prof. Hernández'),
('3B', 29, 'Prof. Pérez'),
('3C', 25, 'Prof. Ramírez'),
('4A', 23, 'Prof. Torres'),
('4B', 26, 'Prof. Flores'),
('4C', 24, 'Prof. Díaz'),
('5A', 31, 'Prof. Morales'),
('5B', 29, 'Prof. Castillo'),
('5C', 27, 'Prof. Ortiz'),
('6A', 32, 'Prof. Ramos'),
('6B', 30, 'Prof. Rivera'),
('6C', 28, 'Prof. Cruz'),
('7A', 33, 'Prof. Gómez'),
('7B', 31, 'Prof. Reyes'),
('7C', 29, 'Prof. Vargas'),
('8A', 30, 'Prof. Jiménez'),
('8B', 28, 'Prof. Gutiérrez'),
('8C', 26, 'Prof. Álvarez'),
('9A', 29, 'Prof. Castro'),
('9B', 27, 'Prof. Fernández'),
('9C', 25, 'Prof. Medina');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `hcitas`
--

CREATE TABLE `hcitas` (
  `num_hcita` int(8) NOT NULL,
  `cod_estudiante` int(8) UNSIGNED NOT NULL,
  `fecha` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `observaciones` varchar(800) NOT NULL,
  `duracion` varchar(400) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `materia`
--

CREATE TABLE `materia` (
  `nm_materia` varchar(8) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `profesionales`
--

CREATE TABLE `profesionales` (
  `cod_profesional` int(8) NOT NULL,
  `nom_completo` char(50) NOT NULL,
  `celular` char(12) NOT NULL,
  `email` char(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `secretarios`
--

CREATE TABLE `secretarios` (
  `cod_secretario` int(8) NOT NULL,
  `nom_completo` char(50) NOT NULL,
  `hora_turno` int(8) NOT NULL,
  `celular` char(12) NOT NULL,
  `email` char(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id_usuario` int(11) NOT NULL,
  `usuario` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id_usuario`, `usuario`, `email`, `password`) VALUES
(1, '123', 'nome@gmail.com', '$2y$10$I07BQSdImeXR.ia0aV5fleaGIdknCsz5bDhBNRQTNmTwv5s0cZOQ6'),
(7, 'johnlark1', 'johnlark1@gmail.com', '$2y$10$yahic0AgtXoP3e9wdpKdteUceOR2rPgPXifo5TzElaNOPtHHrWCwu'),
(8, '12345', '12345@gmail.com', '$2y$10$tu06x7ap1KU.mr59mMHQNehtjp4M4U3OPKakzPZqsFjXwZ/NG03jS'),
(9, 'Mamá', '12@gmail.com', '$2y$10$16HeH7z9Q3vnFBhvPMx.QuCBKjn.mrJs9KGl2W55FLsNnXJ38wd46'),
(10, 'Jaramillo php', 'Jaramillo@gmail.com', '$2y$10$iPlokbRiDgUhb7Fel3V.H.7eTUXxajp02xXnm3VGN9zzqYo/anzmy'),
(11, 'Aleara', 'Si@gmail.com', '$2y$10$SKkGV3VfBsJE98F8Itnjgea4j4mwaWjvKx88sVWY7jFghKM05S/J2'),
(12, 'Ñamñamñam', 'Sigmaboy@gmail.com', '$2y$10$zR6LlH15ZdS/.SvNN7owLORZ4zsH/5JQ2MGp.lAbPi4JcL8McgGuW'),
(13, 'dsfgdsfg', 'dgdstghdfsghdfsghdgfh', '$2y$10$e9MQzdrORZb3MLleblVtU.h7/4i6uJYViAg.hVjOwBGG7hMEgUBf.');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `acudientes`
--
ALTER TABLE `acudientes`
  ADD PRIMARY KEY (`num_acudiente`),
  ADD KEY `fk_acudientes_estudiantes` (`cod_estudiante`);

--
-- Indices de la tabla `citas`
--
ALTER TABLE `citas`
  ADD PRIMARY KEY (`num_cita`),
  ADD KEY `num_cita` (`num_cita`),
  ADD KEY `nom_docente` (`nom_docente`),
  ADD KEY `nom_docente_2` (`nom_docente`),
  ADD KEY `fk_citas_acudientes` (`num_acudiente`),
  ADD KEY `fk_citas_estudiantes` (`cod_estudiante`);

--
-- Indices de la tabla `docente`
--
ALTER TABLE `docente`
  ADD PRIMARY KEY (`nom_docente`),
  ADD UNIQUE KEY `nom_docente_2` (`nom_docente`),
  ADD KEY `nom_docente` (`nom_docente`);

--
-- Indices de la tabla `docente_materia`
--
ALTER TABLE `docente_materia`
  ADD PRIMARY KEY (`nom_docente`,`nm_materia`),
  ADD KEY `nom_docente` (`nom_docente`,`nm_materia`),
  ADD KEY `fk_docente_materia_materia` (`nm_materia`);

--
-- Indices de la tabla `estudiantes`
--
ALTER TABLE `estudiantes`
  ADD PRIMARY KEY (`cod_estudiante`),
  ADD KEY `cod_estudiante` (`cod_estudiante`,`n_grado`),
  ADD KEY `grado_2` (`n_grado`),
  ADD KEY `n_grado` (`n_grado`),
  ADD KEY `cod_estudiante_2` (`cod_estudiante`);

--
-- Indices de la tabla `grado`
--
ALTER TABLE `grado`
  ADD PRIMARY KEY (`n_grado`),
  ADD KEY `n_grado` (`n_grado`),
  ADD KEY `n_grado_2` (`n_grado`);

--
-- Indices de la tabla `hcitas`
--
ALTER TABLE `hcitas`
  ADD PRIMARY KEY (`num_hcita`),
  ADD KEY `num_hcita` (`num_hcita`,`cod_estudiante`),
  ADD KEY `fk_hcitas_estudiantes` (`cod_estudiante`);

--
-- Indices de la tabla `materia`
--
ALTER TABLE `materia`
  ADD PRIMARY KEY (`nm_materia`),
  ADD KEY `nm_materia` (`nm_materia`);

--
-- Indices de la tabla `profesionales`
--
ALTER TABLE `profesionales`
  ADD PRIMARY KEY (`cod_profesional`),
  ADD KEY `cod_profesional` (`cod_profesional`);

--
-- Indices de la tabla `secretarios`
--
ALTER TABLE `secretarios`
  ADD PRIMARY KEY (`cod_secretario`),
  ADD KEY `cod_secretario` (`cod_secretario`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id_usuario`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `acudientes`
--
ALTER TABLE `acudientes`
  MODIFY `num_acudiente` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT de la tabla `citas`
--
ALTER TABLE `citas`
  MODIFY `num_cita` int(8) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12442;

--
-- AUTO_INCREMENT de la tabla `estudiantes`
--
ALTER TABLE `estudiantes`
  MODIFY `cod_estudiante` int(8) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=97;

--
-- AUTO_INCREMENT de la tabla `hcitas`
--
ALTER TABLE `hcitas`
  MODIFY `num_hcita` int(8) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `profesionales`
--
ALTER TABLE `profesionales`
  MODIFY `cod_profesional` int(8) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `secretarios`
--
ALTER TABLE `secretarios`
  MODIFY `cod_secretario` int(8) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id_usuario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `acudientes`
--
ALTER TABLE `acudientes`
  ADD CONSTRAINT `fk_acudientes_estudiantes` FOREIGN KEY (`cod_estudiante`) REFERENCES `estudiantes` (`cod_estudiante`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Filtros para la tabla `citas`
--
ALTER TABLE `citas`
  ADD CONSTRAINT `fk_citas_acudientes` FOREIGN KEY (`num_acudiente`) REFERENCES `acudientes` (`num_acudiente`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_citas_estudiantes` FOREIGN KEY (`cod_estudiante`) REFERENCES `estudiantes` (`cod_estudiante`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `docente_materia`
--
ALTER TABLE `docente_materia`
  ADD CONSTRAINT `fk_docente_materia_docente` FOREIGN KEY (`nom_docente`) REFERENCES `docente` (`nom_docente`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_docente_materia_materia` FOREIGN KEY (`nm_materia`) REFERENCES `materia` (`nm_materia`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `hcitas`
--
ALTER TABLE `hcitas`
  ADD CONSTRAINT `fk_hcitas_citas` FOREIGN KEY (`num_hcita`) REFERENCES `citas` (`num_cita`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_hcitas_estudiantes` FOREIGN KEY (`cod_estudiante`) REFERENCES `estudiantes` (`cod_estudiante`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
