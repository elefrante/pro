create database soluciones_celeste;
use soluciones_celeste;

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `bdcooprs`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `administrativo`
--

CREATE TABLE `administrativo` (
  `id_admin` int(11) NOT NULL,
  `nombre_apellidos` varchar(200) DEFAULT NULL,
  `contrasena` varchar(200) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `administrativo`
--

INSERT INTO `administrativo` (`id_admin`, `nombre_apellidos`, `contrasena`) VALUES
(4, 'Richie Silver', '$2y$10$g930iFIJoDQRtRD.dc.SYejANcUg7pXW5pEsx40c5VTLEwZq1JTDK');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `comprobante`
--

CREATE TABLE `comprobante` (
  `id_comprobante` int(11) NOT NULL,
  `nombre_comprobante` varchar(200) DEFAULT NULL,
  `fecha_optativa` date DEFAULT NULL,
  `tipo` varchar(100) DEFAULT NULL,
  `descripcion` text DEFAULT NULL,
  `estado` varchar(50) DEFAULT NULL,
  `fecha` date DEFAULT NULL,
  `cedula` varchar(15) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `comprobante`
--

INSERT INTO `comprobante` (`id_comprobante`, `nombre_comprobante`, `fecha_optativa`, `tipo`, `descripcion`, `estado`, `fecha`, `cedula`) VALUES
(30, 'comprobantes/68d67a70a63a2_comprobante3.jpg', '2025-09-26', 'inicial', 'kajdnakwjdnawkjd', 'En proceso', '2025-09-26', '5432765-3'),
(31, 'comprobantes/68d680dc27bd2', '2025-09-17', 'mensual', '', 'En proceso', '2025-09-26', '5432765-3'),
(32, 'comprobantes/68e7c88c127ad_comprobante2.jpg', '2025-10-09', 'inicial', 'jyghyvjhv', 'En proceso', '2025-10-09', '12345678'),
(36, 'comprobantes/68eecbced8d7c_Screenshot_45.png', '2025-10-09', 'compensatorio', '', 'En proceso', '2025-10-15', '12345678'),
(39, 'comprobantes/690a0df60a2d5_Screenshot_49_1762113906 (4).png', '2025-11-04', 'inicial', 'jehfgkjfbjehf', 'Aceptado', '2025-11-04', '5.322.948-7'),
(40, 'comprobantes/690a0e2cee0c8_Screenshot_49_1762113906 (2).png', '2025-11-06', 'mensual', 'ytdhjfn', 'Comprobante Inválido', '2025-11-04', '5.322.948-7'),
(41, 'comprobantes/690c9edeeeb24_Mini_Manual_Trabajo_Equipo_Git_GitHub_limpio (1).pdf', '2222-02-22', 'mensual', '22', 'En proceso', '2025-11-06', '5.322.948-7');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `exonera`
--

CREATE TABLE `exonera` (
  `id_admin` int(11) NOT NULL,
  `fecha` date NOT NULL,
  `cedula` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `exonera`
--

INSERT INTO `exonera` (`id_admin`, `fecha`, `cedula`) VALUES
(4, '2025-10-13', '5.322.948-7'),
(4, '2025-10-20', '5.322.948-7');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `gestiona`
--

CREATE TABLE `gestiona` (
  `id_admin` int(11) NOT NULL,
  `id_comprobante` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `gestiona`
--

INSERT INTO `gestiona` (`id_admin`, `id_comprobante`) VALUES
(4, 39),
(4, 40);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `horas_semanales`
--

CREATE TABLE `horas_semanales` (
  `fecha` date NOT NULL,
  `solicitud` tinyint(1) DEFAULT NULL,
  `cantidad_hs` varchar(20) DEFAULT NULL,
  `motivo` text DEFAULT NULL,
  `estado` varchar(50) DEFAULT NULL,
  `cedula` varchar(15) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `horas_semanales`
--

INSERT INTO `horas_semanales` (`fecha`, `solicitud`, `cantidad_hs`, `motivo`, `estado`, `cedula`) VALUES
('2025-09-29', 0, '21', '', 'Completado', '5432765-3'),
('2025-10-06', 0, '10', '', 'Incompleto', '12345678'),
('2025-10-13', 0, '22', '', 'Completado', '12345678'),
('2025-10-13', 1, '1', 'ksbsejfbewvyfuyfb', 'Exoneración Denegada - Incompleto', '5.322.948-7'),
('2025-10-20', 1, '0', 'quiero exonerar porque soy un boldo', 'Exonerado', '5.322.948-7'),
('2025-10-27', 0, '10', '', 'Incompleto', '5.322.948-7'),
('2025-11-03', 0, '1', '', 'Incompleto', '5.322.948-7');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `notificacion`
--

CREATE TABLE `notificacion` (
  `id_noti` int(11) NOT NULL,
  `descripcion_breve` varchar(200) DEFAULT NULL,
  `titulo` varchar(200) DEFAULT NULL,
  `id_usuario` varchar(15) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `reporte`
--

CREATE TABLE `reporte` (
  `id_reporte` int(11) NOT NULL,
  `titulo` varchar(200) DEFAULT NULL,
  `nombre_arch` varchar(200) DEFAULT NULL,
  `fecha` date DEFAULT NULL,
  `descripcion` text DEFAULT NULL,
  `id_admin` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `reporte`
--

INSERT INTO `reporte` (`id_reporte`, `titulo`, `nombre_arch`, `fecha`, `descripcion`, `id_admin`) VALUES
(9, 'Asamblea General Ordinaria – Convocatoria Oficial', NULL, '2025-11-05', 'Se convoca a todos los socios a participar de la Asamblea General Ordinaria a realizarse el día sábado 23 de noviembre a las 18:00 hs en el Salón Comunal.Se tratarán temas referentes al avance de obras, estado financiero, próximas actividades y asuntos varios.La participación de los socios es fundamental para la toma de decisiones colectivas.', 4),
(10, 'Entrega de Plano Actualizado de Unidades Habitacionales', 'planoej_1762357635.jpg', '2025-11-05', 'Se informa a todos los socios que ya se encuentra disponible el plano actualizado de la distribución de las unidades habitacionales, el cual incluye las últimas modificaciones aprobadas por la comisión técnica y la Intendencia.\\r\\nEste documento contiene:\\r\\nNuevas referencias de espacios comunes\\r\\nAjustes en superficies habitacionales\\r\\nActualización de numeración de viviendas\\r\\nUbicación de áreas de circulación y servicios\\r\\nEl archivo podrá ser consultado o descargado desde la plataforma, o solicitado en formato impreso en la oficina administrativa. Se recomienda a cada socio revisar cuidadosamente su unidad asignada.', 4),
(11, 'w', NULL, '2025-11-06', 'w', 4);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuario`
--

CREATE TABLE `usuario` (
  `cedula` varchar(15) NOT NULL,
  `nombre` varchar(100) DEFAULT NULL,
  `apellido` varchar(100) DEFAULT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `email` varchar(150) DEFAULT NULL,
  `contrasena` varchar(200) DEFAULT NULL,
  `aceptado` tinyint(1) DEFAULT NULL,
  `perfil` varchar(100) DEFAULT NULL,
  `unidad_habitacional` varchar(50) DEFAULT NULL,
  `fecha_nacimiento` date DEFAULT NULL,
  `fecha_registro` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuario`
--

INSERT INTO `usuario` (`cedula`, `nombre`, `apellido`, `telefono`, `email`, `contrasena`, `aceptado`, `perfil`, `unidad_habitacional`, `fecha_nacimiento`, `fecha_registro`) VALUES
('12345678', 'Kun', 'Aguero', '000000001', 'cumdeabuel@gmail.com', '$2y$10$6GZ9qB1rsk9g/qZOc8L5uemiWR2n7XJmRkZZuZloRUkmPhcaC5YzO', 1, 'fotosPerfiles/68e7c6b2d6c22.png', '8', '2025-10-17', '2025-10-09'),
('43210985', 'Emanuel', 'Ginóbili', '010101010', 'emanuelginobili@gmail.com', '$2y$10$jWVOR3WI2uG1KedSuhabm.V4pl6vRcUJfL.Kmkbbo9HXIOXr36gmW', 1, 'fotosPerfiles/69049e0aa76f0.webp', NULL, '1978-07-20', '2025-10-31'),
('5.322.948-7', 'Tiago', 'Veras', '092648492', 'tiago@gmail.com', '$2y$10$RXeQYdoIyePfJfPL.y1dN.ShoFrUqTrXquNEbl1nnHXbGWQMcyMvm', 1, 'fotosPerfiles/68c64385caac6.jpeg', '6', '2007-09-22', '2025-09-14'),
('5432765-3', 'Roberto', 'Gonzales', '099999999', 'robertog@gmail.com', '$2y$10$OH2STE5cbHflHb/KjwZqgemxExaL12xZb/vokfam3EjKAjDX5ljJO', 1, 'fotosPerfiles/68d679edb9ff6.jpeg', '3', '1997-06-21', '2025-09-26');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `administrativo`
--
ALTER TABLE `administrativo`
  ADD PRIMARY KEY (`id_admin`);

--
-- Indices de la tabla `comprobante`
--
ALTER TABLE `comprobante`
  ADD PRIMARY KEY (`id_comprobante`),
  ADD KEY `cedula` (`cedula`);

--
-- Indices de la tabla `exonera`
--
ALTER TABLE `exonera`
  ADD PRIMARY KEY (`id_admin`,`fecha`),
  ADD KEY `fecha` (`fecha`);

--
-- Indices de la tabla `gestiona`
--
ALTER TABLE `gestiona`
  ADD PRIMARY KEY (`id_admin`,`id_comprobante`),
  ADD KEY `id_comprobante` (`id_comprobante`);

--
-- Indices de la tabla `horas_semanales`
--
ALTER TABLE `horas_semanales`
  ADD PRIMARY KEY (`fecha`,`cedula`),
  ADD KEY `cedula` (`cedula`);

--
-- Indices de la tabla `notificacion`
--
ALTER TABLE `notificacion`
  ADD PRIMARY KEY (`id_noti`),
  ADD KEY `id_usuario` (`id_usuario`);

--
-- Indices de la tabla `reporte`
--
ALTER TABLE `reporte`
  ADD PRIMARY KEY (`id_reporte`),
  ADD KEY `id_admin` (`id_admin`);

--
-- Indices de la tabla `usuario`
--
ALTER TABLE `usuario`
  ADD PRIMARY KEY (`cedula`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `administrativo`
--
ALTER TABLE `administrativo`
  MODIFY `id_admin` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de la tabla `comprobante`
--
ALTER TABLE `comprobante`
  MODIFY `id_comprobante` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;

--
-- AUTO_INCREMENT de la tabla `notificacion`
--
ALTER TABLE `notificacion`
  MODIFY `id_noti` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `reporte`
--
ALTER TABLE `reporte`
  MODIFY `id_reporte` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `comprobante`
--
ALTER TABLE `comprobante`
  ADD CONSTRAINT `comprobante_ibfk_1` FOREIGN KEY (`cedula`) REFERENCES `usuario` (`cedula`);

--
-- Filtros para la tabla `exonera`
--
ALTER TABLE `exonera`
  ADD CONSTRAINT `exonera_ibfk_1` FOREIGN KEY (`id_admin`) REFERENCES `administrativo` (`id_admin`),
  ADD CONSTRAINT `exonera_ibfk_2` FOREIGN KEY (`fecha`) REFERENCES `horas_semanales` (`fecha`);

--
-- Filtros para la tabla `gestiona`
--
ALTER TABLE `gestiona`
  ADD CONSTRAINT `gestiona_ibfk_1` FOREIGN KEY (`id_admin`) REFERENCES `administrativo` (`id_admin`),
  ADD CONSTRAINT `gestiona_ibfk_2` FOREIGN KEY (`id_comprobante`) REFERENCES `comprobante` (`id_comprobante`);

--
-- Filtros para la tabla `horas_semanales`
--
ALTER TABLE `horas_semanales`
  ADD CONSTRAINT `horas_semanales_ibfk_1` FOREIGN KEY (`cedula`) REFERENCES `usuario` (`cedula`);

--
-- Filtros para la tabla `notificacion`
--
ALTER TABLE `notificacion`
  ADD CONSTRAINT `notificacion_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`cedula`);

--
-- Filtros para la tabla `reporte`
--
ALTER TABLE `reporte`
  ADD CONSTRAINT `reporte_ibfk_1` FOREIGN KEY (`id_admin`) REFERENCES `administrativo` (`id_admin`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
