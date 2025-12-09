-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1:3306
-- Tiempo de generación: 09-12-2025 a las 14:22:26
-- Versión del servidor: 9.1.0
-- Versión de PHP: 8.3.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `cmdb_v2_db`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `asignaciones`
--

DROP TABLE IF EXISTS `asignaciones`;
CREATE TABLE IF NOT EXISTS `asignaciones` (
  `id` int NOT NULL AUTO_INCREMENT,
  `equipo_id` int NOT NULL,
  `usuario_id` int NOT NULL,
  `colaborador_id` int DEFAULT NULL COMMENT 'Colaborador al que se asigna',
  `fecha_asignacion` datetime NOT NULL,
  `fecha_devolucion` datetime DEFAULT NULL,
  `estado` enum('activa','devuelta') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'activa',
  `observaciones` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_equipo` (`equipo_id`),
  KEY `idx_usuario` (`usuario_id`),
  KEY `idx_estado` (`estado`),
  KEY `idx_colaborador` (`colaborador_id`)
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `asignaciones`
--

INSERT INTO `asignaciones` (`id`, `equipo_id`, `usuario_id`, `colaborador_id`, `fecha_asignacion`, `fecha_devolucion`, `estado`, `observaciones`, `created_at`, `updated_at`) VALUES
(1, 1, 2, 3, '2025-12-08 00:00:00', NULL, 'activa', '', '2025-12-08 20:40:29', '2025-12-08 20:40:29'),
(2, 2, 2, 3, '2025-12-08 00:00:00', NULL, 'activa', '', '2025-12-08 20:41:49', '2025-12-08 20:41:49'),
(3, 2, 2, 1, '2025-12-08 00:00:00', NULL, 'activa', '', '2025-12-08 21:18:20', '2025-12-08 21:18:20'),
(4, 1, 2, 2, '2025-12-08 00:00:00', NULL, 'activa', '', '2025-12-08 21:48:10', '2025-12-08 21:48:10'),
(5, 6, 2, 3, '2025-12-08 00:00:00', NULL, 'activa', '', '2025-12-08 21:48:21', '2025-12-08 21:48:21'),
(6, 4, 2, 2, '2025-12-07 00:00:00', NULL, 'activa', '', '2025-12-08 21:52:38', '2025-12-08 21:52:38'),
(7, 1, 2, 1, '2025-12-08 00:00:00', '2025-12-09 08:16:04', '', 'Sin observaciones al devolver.', '2025-12-08 22:06:19', '2025-12-09 13:16:04'),
(8, 1, 1, 1, '2025-12-08 00:00:00', NULL, 'activa', '', '2025-12-08 22:07:10', '2025-12-08 22:07:10'),
(9, 2, 1, 2, '2025-12-08 00:00:00', NULL, 'activa', '', '2025-12-08 22:07:49', '2025-12-08 22:07:49'),
(10, 1, 1, 4, '2025-12-08 00:00:00', NULL, 'activa', '', '2025-12-08 22:14:55', '2025-12-08 22:14:55'),
(11, 10, 1, 5, '2025-12-08 00:00:00', NULL, 'activa', '', '2025-12-08 22:17:27', '2025-12-08 22:17:27'),
(12, 1, 1, 2, '2025-12-08 00:00:00', NULL, 'activa', '', '2025-12-08 22:39:27', '2025-12-08 22:39:27'),
(13, 1, 1, 4, '2025-12-08 00:00:00', NULL, 'activa', '', '2025-12-08 22:49:01', '2025-12-08 22:49:01'),
(14, 1, 1, 1, '2025-12-08 00:00:00', NULL, 'activa', '', '2025-12-08 22:57:41', '2025-12-08 22:57:41'),
(15, 2, 1, 3, '2025-12-08 00:00:00', NULL, 'activa', '', '2025-12-08 22:57:51', '2025-12-08 22:57:51'),
(16, 2, 1, 3, '2025-12-08 00:00:00', '2025-12-09 08:17:02', '', 'Sin observaciones al devolver.', '2025-12-08 23:16:36', '2025-12-09 13:17:02'),
(17, 1, 1, 3, '2025-12-08 00:00:00', '2025-12-09 08:15:21', '', 'Sin observaciones al devolver.', '2025-12-09 00:08:20', '2025-12-09 13:15:21'),
(18, 4, 1, 3, '2025-12-08 00:00:00', '2025-12-09 08:10:04', '', 'Sin observaciones al devolver.', '2025-12-09 00:08:34', '2025-12-09 13:10:04'),
(19, 2, 1, 2, '2025-12-08 00:00:00', '2025-12-09 08:14:09', '', 'Sin observaciones al devolver.', '2025-12-09 00:16:34', '2025-12-09 13:14:09'),
(20, 10, 1, 2, '2025-12-16 00:00:00', '2025-12-08 00:00:00', 'devuelta', 'todo correcto en la compu para su devolucion, mas nada que añadird\nMotivo: fin_proyecto', '2025-12-09 00:16:54', '2025-12-09 00:38:56'),
(21, 10, 1, 3, '2025-12-09 00:00:00', '2025-12-09 03:37:25', '', 'Sin observaciones al devolver.', '2025-12-09 08:36:56', '2025-12-09 08:37:25'),
(22, 1, 1, 3, '2025-12-17 00:00:00', '2025-12-09 08:03:58', '', 'Sin observaciones al devolver.', '2025-12-09 08:39:00', '2025-12-09 13:03:58'),
(23, 1, 1, 12, '2025-12-09 00:00:00', NULL, 'activa', 'Uso en oficina para Juan Perez.', '2025-12-09 14:01:30', '2025-12-09 14:01:30'),
(24, 4, 1, 12, '2025-12-09 00:00:00', '2025-12-09 09:05:12', '', 'Sin observaciones al devolver.', '2025-12-09 14:04:08', '2025-12-09 14:05:12');

--
-- Disparadores `asignaciones`
--
DROP TRIGGER IF EXISTS `trg_asignaciones_devolucion`;
DELIMITER $$
CREATE TRIGGER `trg_asignaciones_devolucion` AFTER UPDATE ON `asignaciones` FOR EACH ROW BEGIN
    IF OLD.estado = 'activa' AND NEW.estado = 'devuelta' THEN
        INSERT INTO historial_movimientos (
            equipo_id,
            tipo_movimiento,
            usuario_id,
            colaborador_id,
            estado_anterior,
            estado_nuevo,
            observaciones,
            created_at
        ) VALUES (
            NEW.equipo_id,
            'devolucion',
            NEW.usuario_id,
            NEW.colaborador_id,
            'asignado',
            'disponible',
            NEW.observaciones,
            NEW.fecha_devolucion
        );
    END IF;
END
$$
DELIMITER ;
DROP TRIGGER IF EXISTS `trg_asignaciones_insert`;
DELIMITER $$
CREATE TRIGGER `trg_asignaciones_insert` AFTER INSERT ON `asignaciones` FOR EACH ROW BEGIN
    INSERT INTO historial_movimientos (
        equipo_id,
        tipo_movimiento,
        usuario_id,
        colaborador_id,
        estado_anterior,
        estado_nuevo,
        observaciones,
        created_at
    ) VALUES (
        NEW.equipo_id,
        'asignacion',
        NEW.usuario_id,
        NEW.colaborador_id,
        NULL,
        'asignado',
        NEW.observaciones,
        NEW.fecha_asignacion
    );
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `bajas_equipos`
--

DROP TABLE IF EXISTS `bajas_equipos`;
CREATE TABLE IF NOT EXISTS `bajas_equipos` (
  `id` int NOT NULL AUTO_INCREMENT,
  `equipo_id` int NOT NULL,
  `usuario_responsable_id` int NOT NULL COMMENT 'Usuario que realizó la baja',
  `fecha_baja` date NOT NULL,
  `motivo_baja` enum('obsolescencia','daño_irreparable','fin_vida_util','reemplazo','perdida','robo','otro') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `criterio_tecnico` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'OBLIGATORIO: Justificación técnica de la baja',
  `valor_residual` decimal(10,2) DEFAULT NULL COMMENT 'Valor residual del equipo al momento de la baja',
  `metodo_disposicion` enum('reciclaje','destruccion','venta','donacion','almacenamiento') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `empresa_disposicion` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Empresa encargada de la disposición',
  `numero_acta` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Número de acta de baja',
  `documentos_adjuntos` json DEFAULT NULL COMMENT 'Rutas de documentos escaneados',
  `observaciones` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `estado` enum('pendiente','aprobada','rechazada','ejecutada') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pendiente',
  `aprobado_por` int DEFAULT NULL COMMENT 'Usuario que aprobó la baja',
  `fecha_aprobacion` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `usuario_responsable_id` (`usuario_responsable_id`),
  KEY `aprobado_por` (`aprobado_por`),
  KEY `idx_equipo` (`equipo_id`),
  KEY `idx_fecha` (`fecha_baja`),
  KEY `idx_estado` (`estado`),
  KEY `idx_motivo` (`motivo_baja`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Registro de bajas y descartes de equipos con criterio técnico';

--
-- Volcado de datos para la tabla `bajas_equipos`
--

INSERT INTO `bajas_equipos` (`id`, `equipo_id`, `usuario_responsable_id`, `fecha_baja`, `motivo_baja`, `criterio_tecnico`, `valor_residual`, `metodo_disposicion`, `empresa_disposicion`, `numero_acta`, `documentos_adjuntos`, `observaciones`, `estado`, `aprobado_por`, `fecha_aprobacion`, `created_at`, `updated_at`) VALUES
(1, 7, 1, '2025-12-08', 'daño_irreparable', 'el equipo presentA fallas', 230.00, 'venta', 'Fundacion R', '12893003', NULL, 'nada', 'pendiente', NULL, NULL, '2025-12-08 23:29:18', '2025-12-08 23:29:18');

--
-- Disparadores `bajas_equipos`
--
DROP TRIGGER IF EXISTS `trg_bajas_insert`;
DELIMITER $$
CREATE TRIGGER `trg_bajas_insert` AFTER INSERT ON `bajas_equipos` FOR EACH ROW BEGIN
    INSERT INTO historial_movimientos (
        equipo_id,
        tipo_movimiento,
        usuario_id,
        estado_anterior,
        estado_nuevo,
        observaciones,
        metadata,
        created_at
    ) VALUES (
        NEW.equipo_id,
        'baja',
        NEW.usuario_responsable_id,
        NULL,
        'dado_de_baja',
        NEW.criterio_tecnico,
        JSON_OBJECT(
            'motivo', NEW.motivo_baja,
            'numero_acta', NEW.numero_acta
        ),
        NEW.fecha_baja
    );
    
    -- Actualizar estado del equipo
    UPDATE equipos SET estado = 'dado_de_baja' WHERE id = NEW.equipo_id;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `categorias`
--

DROP TABLE IF EXISTS `categorias`;
CREATE TABLE IF NOT EXISTS `categorias` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `descripcion` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `estado` enum('activa','inactiva') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'activa',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `nombre` (`nombre`),
  KEY `idx_nombre` (`nombre`),
  KEY `idx_estado` (`estado`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `categorias`
--

INSERT INTO `categorias` (`id`, `nombre`, `descripcion`, `estado`, `created_at`, `updated_at`) VALUES
(1, 'Computadoras', 'Equipos de cómputo (PCs, Laptops)', 'activa', '2025-11-29 03:47:15', '2025-11-29 03:47:15'),
(2, 'Impresoras', 'Equipos de impresión', 'activa', '2025-11-29 03:47:15', '2025-11-29 03:47:15'),
(3, 'Servidores', 'Servidores y equipos de red', 'activa', '2025-11-29 03:47:15', '2025-11-29 03:47:15'),
(4, 'Monitores', 'Pantallas y monitores', 'activa', '2025-11-29 03:47:15', '2025-11-29 03:47:15'),
(5, 'Periféricos', 'Teclados, ratones y otros accesorios', 'activa', '2025-11-29 03:47:15', '2025-11-29 03:47:15');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `colaboradores`
--

DROP TABLE IF EXISTS `colaboradores`;
CREATE TABLE IF NOT EXISTS `colaboradores` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `apellido` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `cedula` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Cédula o identificación del colaborador',
  `email` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `telefono` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cargo` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Puesto o cargo del colaborador',
  `departamento` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Departamento al que pertenece',
  `ubicacion` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Ubicación física (oficina, piso, etc.)',
  `foto_perfil` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Ruta de la foto del colaborador',
  `fecha_ingreso` date DEFAULT NULL COMMENT 'Fecha de ingreso a la empresa',
  `estado` enum('activo','inactivo','suspendido') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'activo',
  `observaciones` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `cedula` (`cedula`),
  KEY `idx_nombre` (`nombre`,`apellido`),
  KEY `idx_cedula` (`cedula`),
  KEY `idx_estado` (`estado`),
  KEY `idx_departamento` (`departamento`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Empleados de la empresa que pueden recibir equipos';

--
-- Volcado de datos para la tabla `colaboradores`
--

INSERT INTO `colaboradores` (`id`, `nombre`, `apellido`, `cedula`, `email`, `telefono`, `cargo`, `departamento`, `ubicacion`, `foto_perfil`, `fecha_ingreso`, `estado`, `observaciones`, `created_at`, `updated_at`) VALUES
(1, 'María', 'González', '8-123-4567', 'maria.gonzalez@empresa.com', '6789-0123', 'Gerente de Ventas', 'Ventas', 'Oficina 201', NULL, '2020-01-15', 'activo', NULL, '2025-11-30 05:08:39', '2025-11-30 05:08:39'),
(2, 'Carlos', 'Rodríguez', '8-234-5678', 'carlos.rodriguez@empresa.com', '6789-1234', 'Desarrollador Senior', 'TI', 'Oficina 105', NULL, '2019-06-10', 'activo', NULL, '2025-11-30 05:08:39', '2025-11-30 05:08:39'),
(3, 'Ana', 'Martínez', '8-345-6789', 'ana.martinez@empresa.com', '6789-2345', 'Contadora', 'Finanzas', 'Oficina 302', NULL, '2021-03-20', 'activo', NULL, '2025-11-30 05:08:39', '2025-11-30 05:08:39'),
(4, 'Roberto', 'López', '8-456-7890', 'roberto.lopez@empresa.com', '6789-3456', 'Diseñador Gráfico', 'Marketing', 'Oficina 203', NULL, '2020-09-05', 'activo', NULL, '2025-11-30 05:08:39', '2025-11-30 05:08:39'),
(5, 'Laura', 'Sánchez', '8-567-8901', 'laura.sanchez@empresa.com', '6789-4567', 'Asistente Administrativa', 'Administración', 'Oficina 101', NULL, '2022-01-10', 'activo', NULL, '2025-11-30 05:08:39', '2025-11-30 05:08:39'),
(12, 'Juan', 'Pérez', '6-143-4061', 'juan.perez@gmail.com', '', 'Técnico de Soporte', 'TI', '', NULL, '2025-12-10', 'activo', '', '2025-12-09 13:51:12', '2025-12-09 14:00:52');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `donaciones_equipos`
--

DROP TABLE IF EXISTS `donaciones_equipos`;
CREATE TABLE IF NOT EXISTS `donaciones_equipos` (
  `id` int NOT NULL AUTO_INCREMENT,
  `equipo_id` int NOT NULL,
  `usuario_responsable_id` int NOT NULL COMMENT 'Usuario que gestionó la donación',
  `fecha_donacion` date NOT NULL,
  `entidad_beneficiada` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Nombre de la entidad que recibe la donación',
  `tipo_entidad` enum('ong','fundacion','escuela','universidad','gobierno','comunidad','otra') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ruc_entidad` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'RUC o identificación de la entidad',
  `contacto_nombre` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `contacto_telefono` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `contacto_email` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `direccion_entidad` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `valor_donacion` decimal(10,2) DEFAULT NULL COMMENT 'Valor estimado de la donación',
  `motivo_donacion` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `condicion_equipo` enum('excelente','bueno','regular','funcional') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `numero_acta` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Número de acta de donación',
  `certificado_donacion` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Ruta del certificado de donación',
  `observaciones` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `usuario_responsable_id` (`usuario_responsable_id`),
  KEY `idx_equipo` (`equipo_id`),
  KEY `idx_fecha` (`fecha_donacion`),
  KEY `idx_entidad` (`entidad_beneficiada`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Registro de donaciones de equipos a entidades externas';

--
-- Volcado de datos para la tabla `donaciones_equipos`
--

INSERT INTO `donaciones_equipos` (`id`, `equipo_id`, `usuario_responsable_id`, `fecha_donacion`, `entidad_beneficiada`, `tipo_entidad`, `ruc_entidad`, `contacto_nombre`, `contacto_telefono`, `contacto_email`, `direccion_entidad`, `valor_donacion`, `motivo_donacion`, `condicion_equipo`, `numero_acta`, `certificado_donacion`, `observaciones`, `created_at`, `updated_at`) VALUES
(1, 6, 1, '2025-12-08', 'Fundacion manos a la obra', 'fundacion', '8-1020-1086', 'Wanytchy', '6062-3436', 'waytchyfranncois@gmail.com', '266 huxley Dr brick', 30.00, 'motivo', 'bueno', '12893003', 'uploads/donaciones/certificado_1765236508_69375f1cbedb2.jpeg', '', '2025-12-08 23:28:28', '2025-12-08 23:28:28');

--
-- Disparadores `donaciones_equipos`
--
DROP TRIGGER IF EXISTS `trg_donaciones_insert`;
DELIMITER $$
CREATE TRIGGER `trg_donaciones_insert` AFTER INSERT ON `donaciones_equipos` FOR EACH ROW BEGIN
    INSERT INTO historial_movimientos (
        equipo_id,
        tipo_movimiento,
        usuario_id,
        estado_anterior,
        estado_nuevo,
        observaciones,
        metadata,
        created_at
    ) VALUES (
        NEW.equipo_id,
        'donacion',
        NEW.usuario_responsable_id,
        NULL,
        'donado',
        CONCAT('Donado a: ', NEW.entidad_beneficiada),
        JSON_OBJECT(
            'entidad', NEW.entidad_beneficiada,
            'tipo_entidad', NEW.tipo_entidad,
            'valor', NEW.valor_donacion
        ),
        NEW.fecha_donacion
    );
    
    -- Actualizar estado del equipo
    UPDATE equipos SET estado = 'donado' WHERE id = NEW.equipo_id;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `equipos`
--

DROP TABLE IF EXISTS `equipos`;
CREATE TABLE IF NOT EXISTS `equipos` (
  `id` int NOT NULL AUTO_INCREMENT,
  `codigo_inventario` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `categoria_id` int DEFAULT NULL,
  `nombre` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `descripcion` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `foto` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `codigo_qr` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `numero_serie` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `modelo` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `marca` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `estado` enum('disponible','asignado','dañado','mantenimiento','en_revision','dado_de_baja','donado') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'disponible',
  `condicion` enum('excelente','bueno','regular','malo') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'bueno',
  `ultima_revision` date DEFAULT NULL COMMENT 'Fecha de última revisión técnica',
  `ubicacion` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fecha_adquisicion` date DEFAULT NULL,
  `costo_adquisicion` decimal(10,2) DEFAULT NULL,
  `vida_util_anos` int DEFAULT '5',
  `valor_residual` decimal(10,2) DEFAULT '0.00',
  `observaciones` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `numero_serie` (`numero_serie`),
  UNIQUE KEY `codigo_inventario` (`codigo_inventario`),
  KEY `idx_categoria` (`categoria_id`),
  KEY `idx_estado` (`estado`),
  KEY `idx_numero_serie` (`numero_serie`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `equipos`
--

INSERT INTO `equipos` (`id`, `codigo_inventario`, `categoria_id`, `nombre`, `descripcion`, `foto`, `codigo_qr`, `numero_serie`, `modelo`, `marca`, `estado`, `condicion`, `ultima_revision`, `ubicacion`, `fecha_adquisicion`, `costo_adquisicion`, `vida_util_anos`, `valor_residual`, `observaciones`, `created_at`, `updated_at`) VALUES
(1, 'EQ-2025-0001', 1, 'Laptop Dell XPS 15', 'Laptop de alto rendimiento', 'https://images.unsplash.com/photo-1593642632823-8f785ba67e45?w=500', '/cmdb_web/public/uploads/qr/qr_equipo_1.png', 'DELL-001', 'XPS 15', 'Dell', 'asignado', 'bueno', NULL, 'Almacén TI', NULL, NULL, 5, 0.00, NULL, '2025-11-30 04:57:53', '2025-12-09 14:01:30'),
(2, 'EQ-2025-0002', 1, 'Desktop HP ProDesk', 'Computadora de escritorio', 'https://images.unsplash.com/photo-1587202372775-e229f172b9d7?w=500', NULL, 'HP-001', 'ProDesk 400', 'HP', 'disponible', 'bueno', NULL, 'Oficina 101', NULL, NULL, 5, 0.00, NULL, '2025-11-30 04:57:53', '2025-12-09 13:14:09'),
(3, 'EQ-2025-0003', 2, 'Impresora HP LaserJet', 'Impresora láser B/N', 'https://images.unsplash.com/photo-1612815154858-60aa4c59eaa6?w=500', NULL, 'HP-PRN-001', 'LaserJet Pro M404', 'HP', 'asignado', 'bueno', NULL, 'Oficina Principal', NULL, NULL, 5, 0.00, NULL, '2025-11-30 04:57:53', '2025-12-04 03:47:37'),
(4, 'EQ-2025-0004', 3, 'Servidor Dell PowerEdge', 'Servidor rack', 'https://images.unsplash.com/photo-1558494949-ef010cbdcc31?w=500', '/cmdb_web/public/uploads/qr/qr_equipo_4.png', 'DELL-SRV-001', 'PowerEdge R740', 'Dell', 'disponible', 'bueno', NULL, 'Sala de Servidores', NULL, NULL, 5, 0.00, NULL, '2025-11-30 04:57:53', '2025-12-09 14:05:12'),
(5, 'EQ-2025-0005', 4, 'Monitor LG 27\"', 'Monitor 4K', 'https://images.unsplash.com/photo-1527443224154-c4a3942d3acf?w=500', NULL, 'LG-MON-001', '27UK850-W', 'LG', 'dañado', 'bueno', NULL, 'Taller Reparación', NULL, NULL, 5, 0.00, NULL, '2025-11-30 04:57:53', '2025-12-04 03:47:37'),
(6, 'EQ-2025-0006', 1, '', 'Intel i7 11th Gen 16GB RAM 512GB SSD', 'https://images.unsplash.com/photo-1496181133206-80ce9b88a853?w=500', NULL, 'DELL-LAT-2023-001', 'Latitude 7420', 'Dell', 'donado', 'bueno', NULL, 'Oficina 301', '2023-01-15', 1250.00, 5, 120.00, NULL, '2025-12-04 02:48:15', '2025-12-08 23:28:28'),
(7, 'EQ-2025-0007', 1, '', 'Intel i5 11th Gen 8GB RAM 256GB SSD', 'https://images.unsplash.com/photo-1588872657578-7efd1f1555ed?w=500', NULL, 'HP-ELB-2023-002', 'EliteBook 840 G8', 'HP', 'dado_de_baja', 'bueno', NULL, 'Oficina 302', '2023-01-20', 1180.00, 5, 110.00, NULL, '2025-12-04 02:51:01', '2025-12-08 23:29:18'),
(8, 'EQ-2025-0008', 1, '', 'Intel i7 16GB RAM 1TB SSD Pantalla 14 4K', 'https://images.unsplash.com/photo-1525547719571-a2d4ac8945e2?w=500', NULL, 'LEN-X1C-2023-003', 'ThinkPad X1 Carbon', 'Lenovo', 'asignado', 'bueno', NULL, 'Oficina 303', '2023-02-10', 1450.00, 5, 140.00, NULL, '2025-12-04 02:51:14', '2025-12-04 03:47:37'),
(9, 'EQ-2025-0009', 1, '', 'M1 Pro 16GB 512GB SSD Pantalla Retina', 'https://images.unsplash.com/photo-1517336714731-489689fd1ca8?w=500', NULL, 'MAC-MBP-2023-004', 'MacBook Pro 14', 'Apple', 'asignado', 'bueno', NULL, 'Oficina 304', '2023-02-15', 2200.00, 5, 250.00, NULL, '2025-12-04 02:53:41', '2025-12-04 03:47:37'),
(10, 'EQ-2025-0010', 1, '', 'Intel i5 8GB RAM 512GB SSD', 'https://images.unsplash.com/photo-1484788984921-03950022c9ef?w=500', NULL, 'ASUS-ZEN-2023-005', 'ZenBook 14', 'ASUS', 'disponible', 'bueno', NULL, 'Almacén TI', '2023-03-01', 980.00, 5, 90.00, NULL, '2025-12-04 03:00:12', '2025-12-09 08:37:25'),
(11, 'EQ-2025-0011', 1, '', 'Intel i7 32GB RAM 1TB SSD GTX 3050', 'https://images.unsplash.com/photo-1593642632823-8f785ba67e45?w=500', NULL, 'DELL-XPS-2023-006', 'XPS 15', 'Dell', 'asignado', 'bueno', NULL, 'Oficina 305', '2023-03-10', 1650.00, 5, 160.00, NULL, '2025-12-04 03:00:18', '2025-12-04 03:47:37');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `historial_movimientos`
--

DROP TABLE IF EXISTS `historial_movimientos`;
CREATE TABLE IF NOT EXISTS `historial_movimientos` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `equipo_id` int NOT NULL,
  `tipo_movimiento` enum('compra','asignacion','devolucion','mantenimiento','reparacion','baja','donacion','cambio_estado') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `usuario_id` int DEFAULT NULL COMMENT 'Usuario del sistema que realizó el movimiento',
  `colaborador_id` int DEFAULT NULL COMMENT 'Colaborador involucrado en el movimiento',
  `estado_anterior` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `estado_nuevo` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `observaciones` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `metadata` json DEFAULT NULL COMMENT 'Datos adicionales del movimiento',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `usuario_id` (`usuario_id`),
  KEY `idx_equipo` (`equipo_id`),
  KEY `idx_tipo` (`tipo_movimiento`),
  KEY `idx_fecha` (`created_at`),
  KEY `idx_colaborador` (`colaborador_id`)
) ENGINE=InnoDB AUTO_INCREMENT=44 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Trazabilidad completa de todos los movimientos de equipos';

--
-- Volcado de datos para la tabla `historial_movimientos`
--

INSERT INTO `historial_movimientos` (`id`, `equipo_id`, `tipo_movimiento`, `usuario_id`, `colaborador_id`, `estado_anterior`, `estado_nuevo`, `observaciones`, `metadata`, `created_at`) VALUES
(1, 1, 'compra', 1, NULL, NULL, 'disponible', 'Compra inicial del equipo', NULL, '2025-11-30 05:08:39'),
(2, 2, 'compra', 1, NULL, NULL, 'disponible', 'Compra inicial del equipo', NULL, '2025-11-30 05:08:39'),
(3, 4, 'compra', 1, NULL, NULL, 'disponible', 'Compra inicial del equipo', NULL, '2025-11-30 05:08:39'),
(4, 3, 'compra', 1, NULL, NULL, 'disponible', 'Compra inicial del equipo', NULL, '2025-11-30 05:08:39'),
(5, 5, 'compra', 1, NULL, NULL, 'disponible', 'Compra inicial del equipo', NULL, '2025-11-30 05:08:39'),
(8, 14, 'cambio_estado', NULL, NULL, 'disponible', '', 'Cambio automático de estado de disponible a ', NULL, '2025-12-04 05:42:13'),
(9, 13, 'cambio_estado', NULL, NULL, 'disponible', '', 'Cambio automático de estado de disponible a ', NULL, '2025-12-04 12:16:04'),
(10, 1, 'asignacion', 2, 3, NULL, 'asignado', '', NULL, '2025-12-08 05:00:00'),
(11, 2, 'asignacion', 2, 3, NULL, 'asignado', '', NULL, '2025-12-08 05:00:00'),
(12, 2, 'asignacion', 2, 1, NULL, 'asignado', '', NULL, '2025-12-08 05:00:00'),
(13, 1, 'asignacion', 2, 2, NULL, 'asignado', '', NULL, '2025-12-08 05:00:00'),
(14, 6, 'asignacion', 2, 3, NULL, 'asignado', '', NULL, '2025-12-08 05:00:00'),
(15, 4, 'asignacion', 2, 2, NULL, 'asignado', '', NULL, '2025-12-07 05:00:00'),
(16, 1, 'asignacion', 2, 1, NULL, 'asignado', '', NULL, '2025-12-08 05:00:00'),
(17, 1, 'asignacion', 1, 1, NULL, 'asignado', '', NULL, '2025-12-08 05:00:00'),
(18, 2, 'asignacion', 1, 2, NULL, 'asignado', '', NULL, '2025-12-08 05:00:00'),
(19, 1, 'asignacion', 1, 4, NULL, 'asignado', '', NULL, '2025-12-08 05:00:00'),
(20, 10, 'asignacion', 1, 5, NULL, 'asignado', '', NULL, '2025-12-08 05:00:00'),
(21, 1, 'asignacion', 1, 2, NULL, 'asignado', '', NULL, '2025-12-08 05:00:00'),
(22, 1, 'asignacion', 1, 4, NULL, 'asignado', '', NULL, '2025-12-08 05:00:00'),
(23, 1, 'asignacion', 1, 1, NULL, 'asignado', '', NULL, '2025-12-08 05:00:00'),
(24, 2, 'asignacion', 1, 3, NULL, 'asignado', '', NULL, '2025-12-08 05:00:00'),
(25, 2, 'asignacion', 1, 3, NULL, 'asignado', '', NULL, '2025-12-08 05:00:00'),
(26, 6, 'donacion', 1, NULL, NULL, 'donado', 'Donado a: Fundacion manos a la obra', '{\"valor\": 30.0, \"entidad\": \"Fundacion manos a la obra\", \"tipo_entidad\": \"fundacion\"}', '2025-12-08 05:00:00'),
(27, 7, 'baja', 1, NULL, NULL, 'dado_de_baja', 'el equipo presentA fallas', '{\"motivo\": \"daño_irreparable\", \"numero_acta\": \"12893003\"}', '2025-12-08 05:00:00'),
(28, 1, 'asignacion', 1, 3, NULL, 'asignado', '', NULL, '2025-12-08 05:00:00'),
(29, 4, 'asignacion', 1, 3, NULL, 'asignado', '', NULL, '2025-12-08 05:00:00'),
(30, 2, 'asignacion', 1, 2, NULL, 'asignado', '', NULL, '2025-12-08 05:00:00'),
(31, 2, 'asignacion', 1, 2, 'disponible', 'asignado', '', NULL, '2025-12-09 00:16:34'),
(32, 10, 'asignacion', 1, 2, NULL, 'asignado', '', NULL, '2025-12-16 05:00:00'),
(33, 10, 'asignacion', 1, 2, 'disponible', 'asignado', '', NULL, '2025-12-09 00:16:54'),
(34, 10, 'devolucion', 1, 2, 'asignado', 'disponible', 'todo correcto en la compu para su devolucion, mas nada que añadird\nMotivo: fin_proyecto', NULL, '2025-12-08 05:00:00'),
(35, 10, 'devolucion', 1, 2, 'asignado', 'disponible', 'todo correcto en la compu para su devolucion, mas nada que añadird\nMotivo: fin_proyecto', NULL, '2025-12-09 00:38:56'),
(36, 10, 'asignacion', 1, 3, NULL, 'asignado', '', NULL, '2025-12-09 05:00:00'),
(37, 10, 'asignacion', 1, 3, 'disponible', 'asignado', '', NULL, '2025-12-09 08:36:56'),
(38, 1, 'asignacion', 1, 3, NULL, 'asignado', '', NULL, '2025-12-17 05:00:00'),
(39, 1, 'asignacion', 1, 3, 'disponible', 'asignado', '', NULL, '2025-12-09 08:39:01'),
(40, 1, 'asignacion', 1, 12, NULL, 'asignado', 'Uso en oficina para Juan Perez.', NULL, '2025-12-09 05:00:00'),
(41, 1, 'asignacion', 1, 12, 'disponible', 'asignado', 'Uso en oficina para Juan Perez.', NULL, '2025-12-09 14:01:30'),
(42, 4, 'asignacion', 1, 12, NULL, 'asignado', '', NULL, '2025-12-09 05:00:00'),
(43, 4, 'asignacion', 1, 12, 'disponible', 'asignado', '', NULL, '2025-12-09 14:04:08');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `intentos_login`
--

DROP TABLE IF EXISTS `intentos_login`;
CREATE TABLE IF NOT EXISTS `intentos_login` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `identifier` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Email o IP',
  `type` enum('email','ip') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'email' COMMENT 'Tipo de identificador',
  `ip_address` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'IP del intento',
  `user_agent` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci COMMENT 'User agent del cliente',
  `metadata` json DEFAULT NULL COMMENT 'Información adicional',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_identifier` (`identifier`,`type`),
  KEY `idx_created` (`created_at`),
  KEY `idx_ip` (`ip_address`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Control de intentos de login para prevenir fuerza bruta';

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `logs_acceso`
--

DROP TABLE IF EXISTS `logs_acceso`;
CREATE TABLE IF NOT EXISTS `logs_acceso` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `usuario` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Email o username del usuario',
  `ip_address` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'IPv4 o IPv6',
  `user_agent` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci COMMENT 'Información del navegador/dispositivo',
  `pais` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Código ISO del país',
  `fingerprint` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Huella digital del dispositivo',
  `exitoso` tinyint(1) NOT NULL DEFAULT '0' COMMENT '1=exitoso, 0=fallido',
  `motivo` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Razón del fallo o acción',
  `metadata` json DEFAULT NULL COMMENT 'Datos adicionales en formato JSON',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_usuario` (`usuario`),
  KEY `idx_ip` (`ip_address`),
  KEY `idx_exitoso` (`exitoso`),
  KEY `idx_created` (`created_at`),
  KEY `idx_fingerprint` (`fingerprint`)
) ENGINE=InnoDB AUTO_INCREMENT=47 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Registro de auditoría de todos los accesos al sistema';

--
-- Volcado de datos para la tabla `logs_acceso`
--

INSERT INTO `logs_acceso` (`id`, `usuario`, `ip_address`, `user_agent`, `pais`, `fingerprint`, `exitoso`, `motivo`, `metadata`, `created_at`) VALUES
(1, 'admin@cmdb.com', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', 'Local', '2e4f5ae6d4f5e95537e12c998733517fbc5f365a16d6cdb443d803b745f2888f', 1, NULL, '{\"rol\": \"admin\", \"referer\": \"http://localhost/cmdb_web/public/index.php?route=login&action=index\", \"user_id\": 1, \"dispositivo\": \"2e4f5ae6d4f5e95537e12c998733517fbc5f365a16d6cdb443d803b745f2888f\", \"request_uri\": \"/cmdb_web/public/index.php?route=login&action=login\", \"request_method\": \"POST\", \"accept_language\": \"en-US,en;q=0.9,es;q=0.8\"}', '2025-11-30 22:37:59'),
(2, 'admin@cmdb.com', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', 'Local', '887da1b633488aa55bbec9682bbc60764f06d4f8a2a67d2bbb9c5dd6b70b4658', 1, NULL, '{\"rol\": \"admin\", \"referer\": \"http://localhost/cmdb_web/public/index.php?route=login&action=index\", \"user_id\": 1, \"dispositivo\": \"887da1b633488aa55bbec9682bbc60764f06d4f8a2a67d2bbb9c5dd6b70b4658\", \"request_uri\": \"/cmdb_web/public/index.php?route=login&action=login\", \"request_method\": \"POST\", \"accept_language\": \"en-US,en;q=0.9,es-US;q=0.8,es;q=0.7\"}', '2025-12-01 04:24:48'),
(3, 'admin@cmdb.com', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', 'Local', '887da1b633488aa55bbec9682bbc60764f06d4f8a2a67d2bbb9c5dd6b70b4658', 1, NULL, '{\"rol\": \"admin\", \"referer\": \"http://localhost/cmdb_web/public/index.php?route=login&action=index\", \"user_id\": 1, \"dispositivo\": \"887da1b633488aa55bbec9682bbc60764f06d4f8a2a67d2bbb9c5dd6b70b4658\", \"request_uri\": \"/cmdb_web/public/index.php?route=login&action=login\", \"request_method\": \"POST\", \"accept_language\": \"en-US,en;q=0.9,es-US;q=0.8,es;q=0.7\"}', '2025-12-01 05:06:22'),
(4, 'admin@cmdb.com', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', 'Local', '887da1b633488aa55bbec9682bbc60764f06d4f8a2a67d2bbb9c5dd6b70b4658', 1, NULL, '{\"rol\": \"admin\", \"referer\": \"http://localhost/cmdb_web/public/index.php?route=login&action=index\", \"user_id\": 1, \"dispositivo\": \"887da1b633488aa55bbec9682bbc60764f06d4f8a2a67d2bbb9c5dd6b70b4658\", \"request_uri\": \"/cmdb_web/public/index.php?route=login&action=login\", \"request_method\": \"POST\", \"accept_language\": \"en-US,en;q=0.9,es-US;q=0.8,es;q=0.7\"}', '2025-12-01 13:30:39'),
(5, 'admin@cmdb.com', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', 'Local', '887da1b633488aa55bbec9682bbc60764f06d4f8a2a67d2bbb9c5dd6b70b4658', 1, NULL, '{\"rol\": \"admin\", \"referer\": \"http://localhost/cmdb_web/public/index.php?route=login&action=index\", \"user_id\": 1, \"dispositivo\": \"887da1b633488aa55bbec9682bbc60764f06d4f8a2a67d2bbb9c5dd6b70b4658\", \"request_uri\": \"/cmdb_web/public/index.php?route=login&action=login\", \"request_method\": \"POST\", \"accept_language\": \"en-US,en;q=0.9,es-US;q=0.8,es;q=0.7\"}', '2025-12-01 13:51:57'),
(6, 'admin@cmdb.com', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', 'Local', '887da1b633488aa55bbec9682bbc60764f06d4f8a2a67d2bbb9c5dd6b70b4658', 1, NULL, '{\"rol\": \"admin\", \"referer\": \"http://localhost/cmdb_web/public/index.php?route=login&action=index\", \"user_id\": 1, \"dispositivo\": \"887da1b633488aa55bbec9682bbc60764f06d4f8a2a67d2bbb9c5dd6b70b4658\", \"request_uri\": \"/cmdb_web/public/index.php?route=login&action=login\", \"request_method\": \"POST\", \"accept_language\": \"en-US,en;q=0.9,es-US;q=0.8,es;q=0.7\"}', '2025-12-02 21:20:59'),
(7, 'admin@cmdb.com', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', 'Local', '887da1b633488aa55bbec9682bbc60764f06d4f8a2a67d2bbb9c5dd6b70b4658', 1, NULL, '{\"rol\": \"admin\", \"referer\": \"http://localhost/cmdb_web/public/index.php?route=login&action=index\", \"user_id\": 1, \"dispositivo\": \"887da1b633488aa55bbec9682bbc60764f06d4f8a2a67d2bbb9c5dd6b70b4658\", \"request_uri\": \"/cmdb_web/public/index.php?route=login&action=login\", \"request_method\": \"POST\", \"accept_language\": \"en-US,en;q=0.9,es-US;q=0.8,es;q=0.7\"}', '2025-12-02 21:26:16'),
(8, 'admin@cmdb.com', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', 'Local', '887da1b633488aa55bbec9682bbc60764f06d4f8a2a67d2bbb9c5dd6b70b4658', 1, NULL, '{\"rol\": \"admin\", \"referer\": \"http://localhost/cmdb_web/public/index.php?route=login&action=index\", \"user_id\": 1, \"dispositivo\": \"887da1b633488aa55bbec9682bbc60764f06d4f8a2a67d2bbb9c5dd6b70b4658\", \"request_uri\": \"/cmdb_web/public/index.php?route=login&action=login\", \"request_method\": \"POST\", \"accept_language\": \"en-US,en;q=0.9,es-US;q=0.8,es;q=0.7\"}', '2025-12-02 21:51:20'),
(9, 'admin@cmdb.com', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', 'Local', '887da1b633488aa55bbec9682bbc60764f06d4f8a2a67d2bbb9c5dd6b70b4658', 1, NULL, '{\"rol\": \"admin\", \"referer\": \"http://localhost/cmdb_web/public/index.php?route=login&action=index\", \"user_id\": 1, \"dispositivo\": \"887da1b633488aa55bbec9682bbc60764f06d4f8a2a67d2bbb9c5dd6b70b4658\", \"request_uri\": \"/cmdb_web/public/index.php?route=login&action=login\", \"request_method\": \"POST\", \"accept_language\": \"en-US,en;q=0.9,es-US;q=0.8,es;q=0.7\"}', '2025-12-03 20:07:29'),
(10, 'admin@cmdb.com', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', 'Local', '887da1b633488aa55bbec9682bbc60764f06d4f8a2a67d2bbb9c5dd6b70b4658', 1, NULL, '{\"rol\": \"admin\", \"referer\": \"http://localhost/cmdb_web/public/index.php?route=login&action=index\", \"user_id\": 1, \"dispositivo\": \"887da1b633488aa55bbec9682bbc60764f06d4f8a2a67d2bbb9c5dd6b70b4658\", \"request_uri\": \"/cmdb_web/public/index.php?route=login&action=login\", \"request_method\": \"POST\", \"accept_language\": \"en-US,en;q=0.9,es-US;q=0.8,es;q=0.7\"}', '2025-12-03 23:10:43'),
(11, 'admin@cmdb.com', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', 'Local', '887da1b633488aa55bbec9682bbc60764f06d4f8a2a67d2bbb9c5dd6b70b4658', 1, NULL, '{\"rol\": \"admin\", \"referer\": \"http://localhost/cmdb_web/public/index.php?route=login&action=index\", \"user_id\": 1, \"dispositivo\": \"887da1b633488aa55bbec9682bbc60764f06d4f8a2a67d2bbb9c5dd6b70b4658\", \"request_uri\": \"/cmdb_web/public/index.php?route=login&action=login\", \"request_method\": \"POST\", \"accept_language\": \"en-US,en;q=0.9,es-US;q=0.8,es;q=0.7\"}', '2025-12-04 00:31:13'),
(12, 'admin@cmdb.com', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', 'Local', '887da1b633488aa55bbec9682bbc60764f06d4f8a2a67d2bbb9c5dd6b70b4658', 1, NULL, '{\"rol\": \"admin\", \"referer\": \"http://localhost/cmdb_web/public/index.php?route=login&action=index\", \"user_id\": 1, \"dispositivo\": \"887da1b633488aa55bbec9682bbc60764f06d4f8a2a67d2bbb9c5dd6b70b4658\", \"request_uri\": \"/cmdb_web/public/index.php?route=login&action=login\", \"request_method\": \"POST\", \"accept_language\": \"en-US,en;q=0.9,es-US;q=0.8,es;q=0.7\"}', '2025-12-04 03:10:15'),
(13, 'admin@cmdb.com', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'Local', '757cefdae6f3993029c771b8c7f3e4f7f75222751bcc3c6bc2c4ddcdd7e49533', 1, NULL, '{\"rol\": \"admin\", \"referer\": \"http://localhost/cmdb_web/public/index.php?route=login&action=index\", \"user_id\": 1, \"dispositivo\": \"757cefdae6f3993029c771b8c7f3e4f7f75222751bcc3c6bc2c4ddcdd7e49533\", \"request_uri\": \"/cmdb_web/public/index.php?route=login&action=login\", \"request_method\": \"POST\", \"accept_language\": \"en-US,en;q=0.9,es-US;q=0.8,es;q=0.7\"}', '2025-12-04 12:05:47'),
(14, 'admin@cmdb.com', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', 'Local', '2e4f5ae6d4f5e95537e12c998733517fbc5f365a16d6cdb443d803b745f2888f', 1, NULL, '{\"rol\": \"admin\", \"referer\": \"http://localhost/cmdb_web/public/index.php?route=login&action=index\", \"user_id\": 1, \"dispositivo\": \"2e4f5ae6d4f5e95537e12c998733517fbc5f365a16d6cdb443d803b745f2888f\", \"request_uri\": \"/cmdb_web/public/index.php?route=login&action=login\", \"request_method\": \"POST\", \"accept_language\": \"en-US,en;q=0.9,es;q=0.8\"}', '2025-12-08 22:06:38'),
(15, 'admin@cmdb.com', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', 'Local', '2e4f5ae6d4f5e95537e12c998733517fbc5f365a16d6cdb443d803b745f2888f', 1, 'logout', '{\"action\": \"logout\", \"referer\": \"http://localhost/cmdb_web/public/index.php?route=asignaciones&action=crear\", \"user_id\": 1, \"request_uri\": \"/cmdb_web/public/index.php?route=logout&action=logout\", \"request_method\": \"GET\", \"accept_language\": \"en-US,en;q=0.9,es;q=0.8\"}', '2025-12-08 22:17:03'),
(16, 'admin@cmdb.com', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', 'Local', '2e4f5ae6d4f5e95537e12c998733517fbc5f365a16d6cdb443d803b745f2888f', 1, NULL, '{\"rol\": \"admin\", \"referer\": \"http://localhost/cmdb_web/public/index.php?route=login&action=index\", \"user_id\": 1, \"dispositivo\": \"2e4f5ae6d4f5e95537e12c998733517fbc5f365a16d6cdb443d803b745f2888f\", \"request_uri\": \"/cmdb_web/public/index.php?route=login&action=login\", \"request_method\": \"POST\", \"accept_language\": \"en-US,en;q=0.9,es;q=0.8\"}', '2025-12-08 22:17:14'),
(17, 'admin@cmdb.com', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', 'Local', '2e4f5ae6d4f5e95537e12c998733517fbc5f365a16d6cdb443d803b745f2888f', 1, 'logout', '{\"action\": \"logout\", \"referer\": \"http://localhost/cmdb_web/public/index.php?route=asignaciones&action=crear\", \"user_id\": 1, \"request_uri\": \"/cmdb_web/public/index.php?route=logout&action=logout\", \"request_method\": \"GET\", \"accept_language\": \"en-US,en;q=0.9,es;q=0.8\"}', '2025-12-08 22:57:19'),
(18, 'admin@cmdb.com', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', 'Local', '2e4f5ae6d4f5e95537e12c998733517fbc5f365a16d6cdb443d803b745f2888f', 1, NULL, '{\"rol\": \"admin\", \"referer\": \"http://localhost/cmdb_web/public/index.php?route=login&action=index\", \"user_id\": 1, \"dispositivo\": \"2e4f5ae6d4f5e95537e12c998733517fbc5f365a16d6cdb443d803b745f2888f\", \"request_uri\": \"/cmdb_web/public/index.php?route=login&action=login\", \"request_method\": \"POST\", \"accept_language\": \"en-US,en;q=0.9,es;q=0.8\"}', '2025-12-08 22:57:30'),
(19, 'admin@cmdb.com', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'Local', '00128885cb9c76ac0256cecac4c5eb49d79481cd3985ce4c738aec60d244a61a', 1, NULL, '{\"rol\": \"admin\", \"referer\": \"http://localhost/cmdb_web/public/index.php?route=login&action=index\", \"user_id\": 1, \"dispositivo\": \"00128885cb9c76ac0256cecac4c5eb49d79481cd3985ce4c738aec60d244a61a\", \"request_uri\": \"/cmdb_web/public/index.php?route=login&action=login\", \"request_method\": \"POST\", \"accept_language\": \"es-ES,es;q=0.9\"}', '2025-12-09 04:02:38'),
(20, 'admin@cmdb.com', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'Local', '00128885cb9c76ac0256cecac4c5eb49d79481cd3985ce4c738aec60d244a61a', 1, 'logout', '{\"action\": \"logout\", \"referer\": \"http://localhost/cmdb_web/public/index.php?route=asignaciones\", \"user_id\": 1, \"request_uri\": \"/cmdb_web/public/index.php?route=logout&action=logout\", \"request_method\": \"GET\", \"accept_language\": \"es-ES,es;q=0.9\"}', '2025-12-09 04:06:57'),
(21, 'colaborador@cmdb.com', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'Local', '00128885cb9c76ac0256cecac4c5eb49d79481cd3985ce4c738aec60d244a61a', 1, NULL, '{\"rol\": \"colaborador\", \"referer\": \"http://localhost/cmdb_web/public/index.php?route=login&action=index\", \"user_id\": 2, \"dispositivo\": \"00128885cb9c76ac0256cecac4c5eb49d79481cd3985ce4c738aec60d244a61a\", \"request_uri\": \"/cmdb_web/public/index.php?route=login&action=login\", \"request_method\": \"POST\", \"accept_language\": \"es-ES,es;q=0.9\"}', '2025-12-09 04:07:20'),
(22, 'colaborador@cmdb.com', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'Local', '00128885cb9c76ac0256cecac4c5eb49d79481cd3985ce4c738aec60d244a61a', 1, 'logout', '{\"action\": \"logout\", \"referer\": \"http://localhost/cmdb_web/public/index.php?route=dashboard\", \"user_id\": 2, \"request_uri\": \"/cmdb_web/public/index.php?route=logout&action=logout\", \"request_method\": \"GET\", \"accept_language\": \"es-ES,es;q=0.9\"}', '2025-12-09 06:03:39'),
(23, 'admin@cmdb.com', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'Local', '00128885cb9c76ac0256cecac4c5eb49d79481cd3985ce4c738aec60d244a61a', 0, 'Contraseña incorrecta', '{\"referer\": \"http://localhost/cmdb_web/public/index.php?route=login&action=index\", \"request_uri\": \"/cmdb_web/public/index.php?route=login&action=login\", \"request_method\": \"POST\", \"accept_language\": \"es-ES,es;q=0.9\"}', '2025-12-09 06:04:06'),
(24, 'colaborador@cmdb.com', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'Local', '00128885cb9c76ac0256cecac4c5eb49d79481cd3985ce4c738aec60d244a61a', 0, 'Contraseña incorrecta', '{\"referer\": \"http://localhost/cmdb_web/public/index.php?route=login&action=index\", \"request_uri\": \"/cmdb_web/public/index.php?route=login&action=login\", \"request_method\": \"POST\", \"accept_language\": \"es-ES,es;q=0.9\"}', '2025-12-09 06:04:12'),
(25, 'admin@cmdb.com', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'Local', '00128885cb9c76ac0256cecac4c5eb49d79481cd3985ce4c738aec60d244a61a', 1, NULL, '{\"rol\": \"admin\", \"referer\": \"http://localhost/cmdb_web/public/index.php?route=login&action=index\", \"user_id\": 1, \"dispositivo\": \"00128885cb9c76ac0256cecac4c5eb49d79481cd3985ce4c738aec60d244a61a\", \"request_uri\": \"/cmdb_web/public/index.php?route=login&action=login\", \"request_method\": \"POST\", \"accept_language\": \"es-ES,es;q=0.9\"}', '2025-12-09 06:04:28'),
(26, 'admin@cmdb.com', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'Local', '00128885cb9c76ac0256cecac4c5eb49d79481cd3985ce4c738aec60d244a61a', 1, 'logout', '{\"action\": \"logout\", \"referer\": \"http://localhost/cmdb_web/public/index.php?route=bajas&action=crear\", \"user_id\": 1, \"request_uri\": \"/cmdb_web/public/index.php?route=logout&action=logout\", \"request_method\": \"GET\", \"accept_language\": \"es-ES,es;q=0.9\"}', '2025-12-09 07:56:43'),
(27, 'colaborador@cmdb.com', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'Local', '00128885cb9c76ac0256cecac4c5eb49d79481cd3985ce4c738aec60d244a61a', 1, NULL, '{\"rol\": \"colaborador\", \"referer\": \"http://localhost/cmdb_web/public/index.php?route=login&action=index\", \"user_id\": 2, \"dispositivo\": \"00128885cb9c76ac0256cecac4c5eb49d79481cd3985ce4c738aec60d244a61a\", \"request_uri\": \"/cmdb_web/public/index.php?route=login&action=login\", \"request_method\": \"POST\", \"accept_language\": \"es-ES,es;q=0.9\"}', '2025-12-09 07:56:49'),
(28, 'colaborador@cmdb.com', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'Local', '00128885cb9c76ac0256cecac4c5eb49d79481cd3985ce4c738aec60d244a61a', 1, 'logout', '{\"action\": \"logout\", \"referer\": \"http://localhost/cmdb_web/public/index.php?route=reportes\", \"user_id\": 2, \"request_uri\": \"/cmdb_web/public/index.php?route=logout&action=logout\", \"request_method\": \"GET\", \"accept_language\": \"es-ES,es;q=0.9\"}', '2025-12-09 08:02:21'),
(29, 'admin@cmdb.com', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'Local', '00128885cb9c76ac0256cecac4c5eb49d79481cd3985ce4c738aec60d244a61a', 1, NULL, '{\"rol\": \"admin\", \"referer\": \"http://localhost/cmdb_web/public/index.php?route=login&action=index\", \"user_id\": 1, \"dispositivo\": \"00128885cb9c76ac0256cecac4c5eb49d79481cd3985ce4c738aec60d244a61a\", \"request_uri\": \"/cmdb_web/public/index.php?route=login&action=login\", \"request_method\": \"POST\", \"accept_language\": \"es-ES,es;q=0.9\"}', '2025-12-09 08:02:43'),
(30, 'admin@cmdb.com', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'Local', '00128885cb9c76ac0256cecac4c5eb49d79481cd3985ce4c738aec60d244a61a', 1, 'logout', '{\"action\": \"logout\", \"referer\": \"http://localhost/cmdb_web/public/index.php?route=reportes\", \"user_id\": 1, \"request_uri\": \"/cmdb_web/public/index.php?route=logout&action=logout\", \"request_method\": \"GET\", \"accept_language\": \"es-ES,es;q=0.9\"}', '2025-12-09 08:20:44'),
(31, 'admin@cmdb.com', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'Local', '00128885cb9c76ac0256cecac4c5eb49d79481cd3985ce4c738aec60d244a61a', 1, NULL, '{\"rol\": \"admin\", \"referer\": \"http://localhost/cmdb_web/public/index.php?route=login&action=index\", \"user_id\": 1, \"dispositivo\": \"00128885cb9c76ac0256cecac4c5eb49d79481cd3985ce4c738aec60d244a61a\", \"request_uri\": \"/cmdb_web/public/index.php?route=login&action=login\", \"request_method\": \"POST\", \"accept_language\": \"es-ES,es;q=0.9\"}', '2025-12-09 08:20:57'),
(32, 'admin@cmdb.com', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'Local', '00128885cb9c76ac0256cecac4c5eb49d79481cd3985ce4c738aec60d244a61a', 1, 'logout', '{\"action\": \"logout\", \"referer\": \"http://localhost/cmdb_web/public/index.php?route=dashboard&action=index\", \"user_id\": 1, \"request_uri\": \"/cmdb_web/public/index.php?route=logout&action=logout\", \"request_method\": \"GET\", \"accept_language\": \"es-ES,es;q=0.9\"}', '2025-12-09 08:21:04'),
(33, 'colaborador@cmdb.com', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'Local', '00128885cb9c76ac0256cecac4c5eb49d79481cd3985ce4c738aec60d244a61a', 1, NULL, '{\"rol\": \"colaborador\", \"referer\": \"http://localhost/cmdb_web/public/index.php?route=login&action=index\", \"user_id\": 2, \"dispositivo\": \"00128885cb9c76ac0256cecac4c5eb49d79481cd3985ce4c738aec60d244a61a\", \"request_uri\": \"/cmdb_web/public/index.php?route=login&action=login\", \"request_method\": \"POST\", \"accept_language\": \"es-ES,es;q=0.9\"}', '2025-12-09 08:21:07'),
(34, 'colaborador@cmdb.com', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'Local', '00128885cb9c76ac0256cecac4c5eb49d79481cd3985ce4c738aec60d244a61a', 1, 'logout', '{\"action\": \"logout\", \"referer\": \"http://localhost/cmdb_web/public/index.php?route=necesidades&action=misSolicitudes\", \"user_id\": 2, \"request_uri\": \"/cmdb_web/public/index.php?route=logout&action=logout\", \"request_method\": \"GET\", \"accept_language\": \"es-ES,es;q=0.9\"}', '2025-12-09 08:34:50'),
(35, 'admin@cmdb.com', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'Local', '00128885cb9c76ac0256cecac4c5eb49d79481cd3985ce4c738aec60d244a61a', 0, 'Contraseña incorrecta', '{\"referer\": \"http://localhost/cmdb_web/public/index.php?route=login&action=index\", \"request_uri\": \"/cmdb_web/public/index.php?route=login&action=login\", \"request_method\": \"POST\", \"accept_language\": \"es-ES,es;q=0.9\"}', '2025-12-09 08:35:20'),
(36, 'admin@cmdb.com', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'Local', '00128885cb9c76ac0256cecac4c5eb49d79481cd3985ce4c738aec60d244a61a', 1, NULL, '{\"rol\": \"admin\", \"referer\": \"http://localhost/cmdb_web/public/index.php?route=login&action=index\", \"user_id\": 1, \"dispositivo\": \"00128885cb9c76ac0256cecac4c5eb49d79481cd3985ce4c738aec60d244a61a\", \"request_uri\": \"/cmdb_web/public/index.php?route=login&action=login\", \"request_method\": \"POST\", \"accept_language\": \"es-ES,es;q=0.9\"}', '2025-12-09 08:35:41'),
(37, 'admin@cmdb.com', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'Local', '00128885cb9c76ac0256cecac4c5eb49d79481cd3985ce4c738aec60d244a61a', 1, 'logout', '{\"action\": \"logout\", \"referer\": \"http://localhost/cmdb_web/public/index.php?route=asignaciones\", \"user_id\": 1, \"request_uri\": \"/cmdb_web/public/index.php?route=logout&action=logout\", \"request_method\": \"GET\", \"accept_language\": \"es-ES,es;q=0.9\"}', '2025-12-09 13:40:05'),
(38, 'colaborador@cmdb.com', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'Local', '00128885cb9c76ac0256cecac4c5eb49d79481cd3985ce4c738aec60d244a61a', 1, NULL, '{\"rol\": \"colaborador\", \"referer\": \"http://localhost/cmdb_web/public/index.php?route=login&action=index\", \"user_id\": 2, \"dispositivo\": \"00128885cb9c76ac0256cecac4c5eb49d79481cd3985ce4c738aec60d244a61a\", \"request_uri\": \"/cmdb_web/public/index.php?route=login&action=login\", \"request_method\": \"POST\", \"accept_language\": \"es-ES,es;q=0.9\"}', '2025-12-09 13:40:09'),
(39, 'colaborador@cmdb.com', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'Local', '00128885cb9c76ac0256cecac4c5eb49d79481cd3985ce4c738aec60d244a61a', 1, 'logout', '{\"action\": \"logout\", \"referer\": \"http://localhost/cmdb_web/public/index.php?route=asignaciones&action=historialColaborador\", \"user_id\": 2, \"request_uri\": \"/cmdb_web/public/index.php?route=logout&action=logout\", \"request_method\": \"GET\", \"accept_language\": \"es-ES,es;q=0.9\"}', '2025-12-09 13:42:10'),
(40, 'admin@cmdb.com', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'Local', '00128885cb9c76ac0256cecac4c5eb49d79481cd3985ce4c738aec60d244a61a', 1, NULL, '{\"rol\": \"admin\", \"referer\": \"http://localhost/cmdb_web/public/index.php?route=login&action=index\", \"user_id\": 1, \"dispositivo\": \"00128885cb9c76ac0256cecac4c5eb49d79481cd3985ce4c738aec60d244a61a\", \"request_uri\": \"/cmdb_web/public/index.php?route=login&action=login\", \"request_method\": \"POST\", \"accept_language\": \"es-ES,es;q=0.9\"}', '2025-12-09 13:42:25'),
(41, 'admin@cmdb.com', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'Local', '00128885cb9c76ac0256cecac4c5eb49d79481cd3985ce4c738aec60d244a61a', 1, 'logout', '{\"action\": \"logout\", \"referer\": \"http://localhost/cmdb_web/public/index.php?route=asignaciones&action=index\", \"user_id\": 1, \"request_uri\": \"/cmdb_web/public/index.php?route=logout&action=logout\", \"request_method\": \"GET\", \"accept_language\": \"es-ES,es;q=0.9\"}', '2025-12-09 14:01:35'),
(42, 'colaborador@cmdb.com', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'Local', '00128885cb9c76ac0256cecac4c5eb49d79481cd3985ce4c738aec60d244a61a', 1, NULL, '{\"rol\": \"colaborador\", \"referer\": \"http://localhost/cmdb_web/public/index.php?route=login&action=index\", \"user_id\": 12, \"dispositivo\": \"00128885cb9c76ac0256cecac4c5eb49d79481cd3985ce4c738aec60d244a61a\", \"request_uri\": \"/cmdb_web/public/index.php?route=login&action=login\", \"request_method\": \"POST\", \"accept_language\": \"es-ES,es;q=0.9\"}', '2025-12-09 14:01:38'),
(43, 'colaborador@cmdb.com', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'Local', '00128885cb9c76ac0256cecac4c5eb49d79481cd3985ce4c738aec60d244a61a', 1, 'logout', '{\"action\": \"logout\", \"referer\": \"http://localhost/cmdb_web/public/index.php?route=asignaciones&action=misEquipos\", \"user_id\": 12, \"request_uri\": \"/cmdb_web/public/index.php?route=logout&action=logout\", \"request_method\": \"GET\", \"accept_language\": \"es-ES,es;q=0.9\"}', '2025-12-09 14:02:51'),
(44, 'admin@cmdb.com', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'Local', '00128885cb9c76ac0256cecac4c5eb49d79481cd3985ce4c738aec60d244a61a', 1, NULL, '{\"rol\": \"admin\", \"referer\": \"http://localhost/cmdb_web/public/index.php?route=login&action=index\", \"user_id\": 1, \"dispositivo\": \"00128885cb9c76ac0256cecac4c5eb49d79481cd3985ce4c738aec60d244a61a\", \"request_uri\": \"/cmdb_web/public/index.php?route=login&action=login\", \"request_method\": \"POST\", \"accept_language\": \"es-ES,es;q=0.9\"}', '2025-12-09 14:03:53'),
(45, 'admin@cmdb.com', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'Local', '00128885cb9c76ac0256cecac4c5eb49d79481cd3985ce4c738aec60d244a61a', 1, 'logout', '{\"action\": \"logout\", \"referer\": \"http://localhost/cmdb_web/public/index.php?route=asignaciones&action=index\", \"user_id\": 1, \"request_uri\": \"/cmdb_web/public/index.php?route=logout&action=logout\", \"request_method\": \"GET\", \"accept_language\": \"es-ES,es;q=0.9\"}', '2025-12-09 14:05:26'),
(46, 'colaborador@cmdb.com', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'Local', '00128885cb9c76ac0256cecac4c5eb49d79481cd3985ce4c738aec60d244a61a', 1, NULL, '{\"rol\": \"colaborador\", \"referer\": \"http://localhost/cmdb_web/public/index.php?route=login&action=index\", \"user_id\": 12, \"dispositivo\": \"00128885cb9c76ac0256cecac4c5eb49d79481cd3985ce4c738aec60d244a61a\", \"request_uri\": \"/cmdb_web/public/index.php?route=login&action=login\", \"request_method\": \"POST\", \"accept_language\": \"es-ES,es;q=0.9\"}', '2025-12-09 14:05:29');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `necesidades`
--

DROP TABLE IF EXISTS `necesidades`;
CREATE TABLE IF NOT EXISTS `necesidades` (
  `id` int NOT NULL AUTO_INCREMENT,
  `colaborador_id` int NOT NULL,
  `categoria_id` int DEFAULT NULL,
  `tipo_equipo` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Ej: Laptop, Mouse, Teclado',
  `justificacion` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Por qué necesita el equipo',
  `urgencia` enum('baja','normal','alta') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'normal',
  `estado` enum('pendiente','aprobada','rechazada','completada') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'pendiente',
  `administrador_id` int DEFAULT NULL COMMENT 'Admin que aprobó/rechazó',
  `observaciones_admin` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci COMMENT 'Comentarios del administrador',
  `equipo_asignado_id` int DEFAULT NULL COMMENT 'Equipo que se le asignó',
  `fecha_solicitud` date NOT NULL,
  `fecha_respuesta` date DEFAULT NULL,
  `fecha_completada` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_colaborador` (`colaborador_id`),
  KEY `idx_estado` (`estado`),
  KEY `idx_categoria` (`categoria_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `necesidades`
--

INSERT INTO `necesidades` (`id`, `colaborador_id`, `categoria_id`, `tipo_equipo`, `justificacion`, `urgencia`, `estado`, `administrador_id`, `observaciones_admin`, `equipo_asignado_id`, `fecha_solicitud`, `fecha_respuesta`, `fecha_completada`, `created_at`, `updated_at`) VALUES
(1, 1, 2, 'Laptop', 'Trabajo adicional', 'normal', 'aprobada', 1, '', NULL, '2025-12-08', '2025-12-08', NULL, '2025-12-09 00:10:34', '2025-12-09 00:48:37'),
(2, 2, 5, 'Mouse Logitech', 'Para la mejora de las entregas de mis asignaciones.', 'baja', 'pendiente', NULL, NULL, NULL, '2025-12-09', NULL, NULL, '2025-12-09 08:23:47', '2025-12-09 08:23:47');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `solicitudes`
--

DROP TABLE IF EXISTS `solicitudes`;
CREATE TABLE IF NOT EXISTS `solicitudes` (
  `id` int NOT NULL AUTO_INCREMENT,
  `colaborador_id` int NOT NULL,
  `equipo_id` int NOT NULL,
  `descripcion` text,
  `estado` enum('pendiente','aprobado','rechazado') DEFAULT 'pendiente',
  `fecha_solicitud` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

DROP TABLE IF EXISTS `usuarios`;
CREATE TABLE IF NOT EXISTS `usuarios` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `apellido` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `rol` enum('admin','colaborador') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'colaborador',
  `estado` enum('activo','inactivo') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'activo',
  `foto_perfil` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  KEY `idx_email` (`email`),
  KEY `idx_rol` (`rol`),
  KEY `idx_estado` (`estado`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `nombre`, `apellido`, `email`, `password`, `rol`, `estado`, `foto_perfil`, `created_at`, `updated_at`) VALUES
(1, 'Admin', 'Sistema', 'admin@cmdb.com', '$2y$12$aG9QDC/sgwzKULAVzazsGulYqHazTGxHMm0mviuFbSlnPoFi.6g.i', 'admin', 'activo', NULL, '2025-11-30 04:57:53', '2025-11-30 04:57:53'),
(12, 'Juan', 'Pérez', 'colaborador@cmdb.com', '$2y$12$s2oP1y.OLpNAxQWZr60mU.HuHRX6Rg2KVP8K61XBojNJ96cP5qqZ2', 'colaborador', 'activo', NULL, '2025-11-30 04:57:53', '2025-12-09 13:59:31');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `v_accesos_diarios`
--

DROP TABLE IF EXISTS `v_accesos_diarios`;
CREATE TABLE IF NOT EXISTS `v_accesos_diarios` (
  `fecha` date DEFAULT NULL,
  `total_accesos` bigint DEFAULT NULL,
  `exitosos` decimal(23,0) DEFAULT NULL,
  `fallidos` decimal(23,0) DEFAULT NULL,
  `usuarios_unicos` bigint DEFAULT NULL,
  `ips_unicas` bigint DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `v_accesos_recientes`
--

DROP TABLE IF EXISTS `v_accesos_recientes`;
CREATE TABLE IF NOT EXISTS `v_accesos_recientes` (
  `id` bigint DEFAULT NULL,
  `usuario` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ip_address` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pais` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `resultado` varchar(7) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `motivo` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `navegador` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `v_equipos_por_colaborador`
--

DROP TABLE IF EXISTS `v_equipos_por_colaborador`;
CREATE TABLE IF NOT EXISTS `v_equipos_por_colaborador` (
  `colaborador_id` int DEFAULT NULL,
  `colaborador` varchar(201) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cedula` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cargo` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `departamento` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ubicacion` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `total_equipos_asignados` bigint DEFAULT NULL,
  `equipos` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `v_equipos_proximos_baja`
--

DROP TABLE IF EXISTS `v_equipos_proximos_baja`;
CREATE TABLE IF NOT EXISTS `v_equipos_proximos_baja` (
  `id` int DEFAULT NULL,
  `nombre` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `numero_serie` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `marca` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `modelo` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fecha_adquisicion` date DEFAULT NULL,
  `valor_adquisicion` decimal(10,2) DEFAULT NULL,
  `años_uso` bigint DEFAULT NULL,
  `estado` enum('disponible','asignado','dañado','mantenimiento','en_revision','dado_de_baja','donado') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `condicion` enum('excelente','bueno','regular','malo') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `categoria` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `v_inventario_completo`
-- (Véase abajo para la vista actual)
--
DROP VIEW IF EXISTS `v_inventario_completo`;
CREATE TABLE IF NOT EXISTS `v_inventario_completo` (
`id` int
,`codigo_inventario` varchar(50)
,`nombre` varchar(201)
,`numero_serie` varchar(100)
,`marca` varchar(100)
,`modelo` varchar(100)
,`descripcion` text
,`categoria` varchar(100)
,`estado` enum('disponible','asignado','dañado','mantenimiento','en_revision','dado_de_baja','donado')
,`condicion` enum('excelente','bueno','regular','malo')
,`ubicacion` varchar(200)
,`fecha_adquisicion` date
,`costo_adquisicion` decimal(10,2)
,`vida_util_anos` int
,`valor_residual` decimal(10,2)
,`asignado_a` varchar(201)
,`meses_uso` bigint
,`depreciacion_mensual` decimal(12,2)
,`depreciacion_acumulada` decimal(32,2)
,`valor_actual` decimal(33,2)
,`created_at` timestamp
,`updated_at` timestamp
);

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `v_ips_sospechosas`
-- (Véase abajo para la vista actual)
--
DROP VIEW IF EXISTS `v_ips_sospechosas`;
CREATE TABLE IF NOT EXISTS `v_ips_sospechosas` (
`ip_address` varchar(45)
,`intentos_fallidos` bigint
,`primer_intento` timestamp
,`ultimo_intento` timestamp
,`usuarios_intentados` text
);

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `v_reporte_depreciacion`
-- (Véase abajo para la vista actual)
--
DROP VIEW IF EXISTS `v_reporte_depreciacion`;
CREATE TABLE IF NOT EXISTS `v_reporte_depreciacion` (
`id_equipo` int
,`codigo_inventario` varchar(50)
,`nombre_equipo` varchar(150)
,`descripcion` text
,`categoria_id` int
,`categoria` varchar(100)
,`fecha_adquisicion` date
,`costo_adquisicion` decimal(10,2)
,`vida_util_anos` int
,`anos_uso` bigint
,`depreciacion_anual` decimal(14,6)
,`depreciacion_mensual` decimal(14,6)
,`depreciacion_acumulada` decimal(31,2)
,`valor_libro` decimal(32,2)
,`porcentaje_depreciado` decimal(36,2)
);

-- --------------------------------------------------------

--
-- Estructura para la vista `v_inventario_completo`
--
DROP TABLE IF EXISTS `v_inventario_completo`;

DROP VIEW IF EXISTS `v_inventario_completo`;
CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_inventario_completo`  AS SELECT `e`.`id` AS `id`, `e`.`codigo_inventario` AS `codigo_inventario`, coalesce(nullif(`e`.`nombre`,''),concat(`e`.`marca`,' ',`e`.`modelo`)) AS `nombre`, `e`.`numero_serie` AS `numero_serie`, `e`.`marca` AS `marca`, `e`.`modelo` AS `modelo`, `e`.`descripcion` AS `descripcion`, `c`.`nombre` AS `categoria`, `e`.`estado` AS `estado`, `e`.`condicion` AS `condicion`, `e`.`ubicacion` AS `ubicacion`, `e`.`fecha_adquisicion` AS `fecha_adquisicion`, `e`.`costo_adquisicion` AS `costo_adquisicion`, `e`.`vida_util_anos` AS `vida_util_anos`, `e`.`valor_residual` AS `valor_residual`, (case when (`e`.`estado` = 'asignado') then (select concat(`col`.`nombre`,' ',`col`.`apellido`) from (`asignaciones` `a` left join `colaboradores` `col` on((`a`.`colaborador_id` = `col`.`id`))) where ((`a`.`equipo_id` = `e`.`id`) and (`a`.`estado` = 'activa')) order by `a`.`fecha_asignacion` desc limit 1) else NULL end) AS `asignado_a`, timestampdiff(MONTH,`e`.`fecha_adquisicion`,curdate()) AS `meses_uso`, round(((`e`.`costo_adquisicion` - coalesce(`e`.`valor_residual`,0)) / (`e`.`vida_util_anos` * 12)),2) AS `depreciacion_mensual`, round(least((((`e`.`costo_adquisicion` - coalesce(`e`.`valor_residual`,0)) / (`e`.`vida_util_anos` * 12)) * timestampdiff(MONTH,`e`.`fecha_adquisicion`,curdate())),(`e`.`costo_adquisicion` - coalesce(`e`.`valor_residual`,0))),2) AS `depreciacion_acumulada`, round(greatest((`e`.`costo_adquisicion` - (((`e`.`costo_adquisicion` - coalesce(`e`.`valor_residual`,0)) / (`e`.`vida_util_anos` * 12)) * timestampdiff(MONTH,`e`.`fecha_adquisicion`,curdate()))),coalesce(`e`.`valor_residual`,0)),2) AS `valor_actual`, `e`.`created_at` AS `created_at`, `e`.`updated_at` AS `updated_at` FROM (`equipos` `e` left join `categorias` `c` on((`e`.`categoria_id` = `c`.`id`))) WHERE (`e`.`estado` not in ('dado_de_baja','donado')) ORDER BY `e`.`id` DESC ;

-- --------------------------------------------------------

--
-- Estructura para la vista `v_ips_sospechosas`
--
DROP TABLE IF EXISTS `v_ips_sospechosas`;

DROP VIEW IF EXISTS `v_ips_sospechosas`;
CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_ips_sospechosas`  AS SELECT `logs_acceso`.`ip_address` AS `ip_address`, count(0) AS `intentos_fallidos`, min(`logs_acceso`.`created_at`) AS `primer_intento`, max(`logs_acceso`.`created_at`) AS `ultimo_intento`, group_concat(distinct `logs_acceso`.`usuario` order by `logs_acceso`.`created_at` DESC separator ', ') AS `usuarios_intentados` FROM `logs_acceso` WHERE ((`logs_acceso`.`exitoso` = 0) AND (`logs_acceso`.`created_at` > (now() - interval 24 hour))) GROUP BY `logs_acceso`.`ip_address` HAVING (`intentos_fallidos` >= 3) ORDER BY `intentos_fallidos` DESC ;

-- --------------------------------------------------------

--
-- Estructura para la vista `v_reporte_depreciacion`
--
DROP TABLE IF EXISTS `v_reporte_depreciacion`;

DROP VIEW IF EXISTS `v_reporte_depreciacion`;
CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_reporte_depreciacion`  AS SELECT `e`.`id` AS `id_equipo`, `e`.`codigo_inventario` AS `codigo_inventario`, `e`.`nombre` AS `nombre_equipo`, `e`.`descripcion` AS `descripcion`, `e`.`categoria_id` AS `categoria_id`, `c`.`nombre` AS `categoria`, `e`.`fecha_adquisicion` AS `fecha_adquisicion`, `e`.`costo_adquisicion` AS `costo_adquisicion`, `e`.`vida_util_anos` AS `vida_util_anos`, timestampdiff(YEAR,`e`.`fecha_adquisicion`,curdate()) AS `anos_uso`, (`e`.`costo_adquisicion` / `e`.`vida_util_anos`) AS `depreciacion_anual`, (`e`.`costo_adquisicion` / (`e`.`vida_util_anos` * 12)) AS `depreciacion_mensual`, round(((`e`.`costo_adquisicion` / `e`.`vida_util_anos`) * timestampdiff(YEAR,`e`.`fecha_adquisicion`,curdate())),2) AS `depreciacion_acumulada`, round((`e`.`costo_adquisicion` - ((`e`.`costo_adquisicion` / `e`.`vida_util_anos`) * timestampdiff(YEAR,`e`.`fecha_adquisicion`,curdate()))),2) AS `valor_libro`, round(((((`e`.`costo_adquisicion` / `e`.`vida_util_anos`) * timestampdiff(YEAR,`e`.`fecha_adquisicion`,curdate())) / `e`.`costo_adquisicion`) * 100),2) AS `porcentaje_depreciado` FROM (`equipos` `e` left join `categorias` `c` on((`c`.`id` = `e`.`categoria_id`))) ;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `necesidades`
--
ALTER TABLE `necesidades`
  ADD CONSTRAINT `fk_necesidad_categoria` FOREIGN KEY (`categoria_id`) REFERENCES `categorias` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_necesidad_colaborador` FOREIGN KEY (`colaborador_id`) REFERENCES `colaboradores` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
