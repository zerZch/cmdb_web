-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Dec 09, 2025 at 01:26 AM
-- Server version: 9.1.0
-- PHP Version: 8.3.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `cmdb_v2_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `asignaciones`
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Triggers `asignaciones`
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
-- Table structure for table `bajas_equipos`
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Registro de bajas y descartes de equipos con criterio técnico';

--
-- Triggers `bajas_equipos`
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
-- Table structure for table `categorias`
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
-- Dumping data for table `categorias`
--

INSERT INTO `categorias` (`id`, `nombre`, `descripcion`, `estado`, `created_at`, `updated_at`) VALUES
(1, 'Software', 'Licencias de software, aplicaciones y sistemas operativos', 'activa', '2025-12-09 01:00:03', '2025-12-09 01:00:03'),
(2, 'Hardware', 'Componentes de hardware (discos duros, memorias RAM, tarjetas, etc.)', 'activa', '2025-12-09 01:00:03', '2025-12-09 01:00:03'),
(3, 'Equipo de Red', 'Routers, switches, firewalls, access points y equipos de comunicación', 'activa', '2025-12-09 01:00:03', '2025-12-09 01:00:03'),
(4, 'Equipo de Cómputo', 'Computadoras, laptops, tablets y dispositivos de cómputo', 'activa', '2025-12-09 01:00:03', '2025-12-09 01:00:03'),
(5, 'Equipo de Telefonía', 'Teléfonos IP, centrales telefónicas y equipos de comunicación telefónica', 'activa', '2025-12-09 01:00:03', '2025-12-09 01:00:03');

-- --------------------------------------------------------

--
-- Table structure for table `colaboradores`
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
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Empleados de la empresa que pueden recibir equipos';

--
-- Dumping data for table `colaboradores`
--

INSERT INTO `colaboradores` (`id`, `nombre`, `apellido`, `cedula`, `email`, `telefono`, `cargo`, `departamento`, `ubicacion`, `foto_perfil`, `fecha_ingreso`, `estado`, `observaciones`, `created_at`, `updated_at`) VALUES
(1, 'María', 'González', '8-123-4567', 'maria.gonzalez@empresa.com', '6789-0123', 'Gerente de Ventas', 'Ventas', 'Oficina 201', NULL, '2020-01-15', 'activo', NULL, '2025-11-30 05:08:39', '2025-11-30 05:08:39'),
(2, 'Carlos', 'Rodríguez', '8-234-5678', 'carlos.rodriguez@empresa.com', '6789-1234', 'Desarrollador Senior', 'TI', 'Oficina 105', NULL, '2019-06-10', 'activo', NULL, '2025-11-30 05:08:39', '2025-11-30 05:08:39'),
(3, 'Ana', 'Martínez', '8-345-6789', 'ana.martinez@empresa.com', '6789-2345', 'Contadora', 'Finanzas', 'Oficina 302', NULL, '2021-03-20', 'activo', NULL, '2025-11-30 05:08:39', '2025-11-30 05:08:39'),
(4, 'Roberto', 'López', '8-456-7890', 'roberto.lopez@empresa.com', '6789-3456', 'Diseñador Gráfico', 'Marketing', 'Oficina 203', NULL, '2020-09-05', 'activo', NULL, '2025-11-30 05:08:39', '2025-11-30 05:08:39'),
(5, 'Laura', 'Sánchez', '8-567-8901', 'laura.sanchez@empresa.com', '6789-4567', 'Asistente Administrativa', 'Administración', 'Oficina 101', NULL, '2022-01-10', 'activo', NULL, '2025-11-30 05:08:39', '2025-11-30 05:08:39');

-- --------------------------------------------------------

--
-- Table structure for table `donaciones_equipos`
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Registro de donaciones de equipos a entidades externas';

--
-- Triggers `donaciones_equipos`
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
-- Table structure for table `equipos`
--

DROP TABLE IF EXISTS `equipos`;
CREATE TABLE IF NOT EXISTS `equipos` (
  `id` int NOT NULL AUTO_INCREMENT,
  `codigo_inventario` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `categoria_id` int NOT NULL,
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
) ENGINE=InnoDB AUTO_INCREMENT=114 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `equipos`
--

INSERT INTO `equipos` (`id`, `codigo_inventario`, `categoria_id`, `nombre`, `descripcion`, `foto`, `codigo_qr`, `numero_serie`, `modelo`, `marca`, `estado`, `condicion`, `ultima_revision`, `ubicacion`, `fecha_adquisicion`, `costo_adquisicion`, `vida_util_anos`, `valor_residual`, `observaciones`, `created_at`, `updated_at`) VALUES
(15, 'SW-2025-0001', 1, 'Windows 11 Pro', 'Licencia Windows 11 Profesional', 'https://images.unsplash.com/photo-1633419461186-7d40a38105ec?w=500', NULL, 'WIN11-PRO-001', 'Windows 11 Pro', 'Microsoft', 'disponible', 'excelente', NULL, 'Almacén Licencias', '2024-01-15', 199.99, 3, 0.00, NULL, '2025-12-09 01:25:22', '2025-12-09 01:25:22'),
(16, 'SW-2025-0002', 1, 'Windows 11 Pro', 'Licencia Windows 11 Profesional', 'https://images.unsplash.com/photo-1633419461186-7d40a38105ec?w=500', NULL, 'WIN11-PRO-002', 'Windows 11 Pro', 'Microsoft', 'asignado', 'excelente', NULL, 'PC Contabilidad', '2024-01-16', 199.99, 3, 0.00, NULL, '2025-12-09 01:25:22', '2025-12-09 01:25:22'),
(17, 'SW-2025-0003', 1, 'Windows Server 2022', 'Licencia Windows Server Standard', 'https://images.unsplash.com/photo-1633419461186-7d40a38105ec?w=500', NULL, 'WINSRV2022-001', 'Server 2022 Standard', 'Microsoft', 'disponible', 'excelente', NULL, 'Sala Servidores', '2024-02-01', 799.99, 5, 0.00, NULL, '2025-12-09 01:25:22', '2025-12-09 01:25:22'),
(18, 'SW-2025-0004', 1, 'Ubuntu Server 22.04 LTS', 'Sistema operativo Linux para servidores', 'https://images.unsplash.com/photo-1629654297299-c8506221ca97?w=500', NULL, 'UBUNTU-SRV-001', 'Ubuntu 22.04 LTS', 'Canonical', 'disponible', 'excelente', NULL, 'Sala Servidores', '2024-01-10', 0.00, 5, 0.00, NULL, '2025-12-09 01:25:22', '2025-12-09 01:25:22'),
(19, 'SW-2025-0005', 1, 'Microsoft Office 365 Business', 'Suite Office 365 - Licencia anual', 'https://images.unsplash.com/photo-1618761714954-0b8cd0026356?w=500', NULL, 'O365-BUS-001', 'Office 365 Business', 'Microsoft', 'disponible', 'excelente', NULL, 'Almacén Licencias', '2024-01-20', 99.99, 1, 0.00, NULL, '2025-12-09 01:25:22', '2025-12-09 01:25:22'),
(20, 'SW-2025-0006', 1, 'Microsoft Office 365 Business', 'Suite Office 365 - Licencia anual', 'https://images.unsplash.com/photo-1618761714954-0b8cd0026356?w=500', NULL, 'O365-BUS-002', 'Office 365 Business', 'Microsoft', 'asignado', 'excelente', NULL, 'Usuario Marketing', '2024-01-21', 99.99, 1, 0.00, NULL, '2025-12-09 01:25:22', '2025-12-09 01:25:22'),
(21, 'SW-2025-0007', 1, 'Microsoft Office 2021 Professional', 'Office 2021 - Licencia perpetua', 'https://images.unsplash.com/photo-1618761714954-0b8cd0026356?w=500', NULL, 'OFF2021-PRO-001', 'Office 2021 Pro', 'Microsoft', 'disponible', 'excelente', NULL, 'Almacén Licencias', '2024-02-10', 439.99, 5, 0.00, NULL, '2025-12-09 01:25:22', '2025-12-09 01:25:22'),
(22, 'SW-2025-0008', 1, 'Google Workspace Business', 'Suite Google Workspace - Licencia anual', 'https://images.unsplash.com/photo-1573804633927-bfcbcd909acd?w=500', NULL, 'GWORK-BUS-001', 'Workspace Business', 'Google', 'asignado', 'excelente', NULL, 'Usuario Ventas', '2024-01-25', 144.00, 1, 0.00, NULL, '2025-12-09 01:25:22', '2025-12-09 01:25:22'),
(23, 'SW-2025-0009', 1, 'Adobe Creative Cloud', 'Suite completa Adobe CC - Todas las apps', 'https://images.unsplash.com/photo-1626785774573-4b799315345d?w=500', NULL, 'ACC-ALL-001', 'Creative Cloud All Apps', 'Adobe', 'disponible', 'excelente', NULL, 'Almacén Licencias', '2024-01-15', 599.99, 1, 0.00, NULL, '2025-12-09 01:25:22', '2025-12-09 01:25:22'),
(24, 'SW-2025-0010', 1, 'Adobe Creative Cloud', 'Suite completa Adobe CC - Todas las apps', 'https://images.unsplash.com/photo-1626785774573-4b799315345d?w=500', NULL, 'ACC-ALL-002', 'Creative Cloud All Apps', 'Adobe', 'asignado', 'excelente', NULL, 'Depto Diseño', '2024-01-16', 599.99, 1, 0.00, NULL, '2025-12-09 01:25:22', '2025-12-09 01:25:22'),
(25, 'SW-2025-0011', 1, 'Adobe Photoshop', 'Licencia individual Photoshop', 'https://images.unsplash.com/photo-1626785774573-4b799315345d?w=500', NULL, 'APS-IND-001', 'Photoshop CC', 'Adobe', 'disponible', 'excelente', NULL, 'Almacén Licencias', '2024-02-01', 239.88, 1, 0.00, NULL, '2025-12-09 01:25:22', '2025-12-09 01:25:22'),
(26, 'SW-2025-0012', 1, 'CorelDRAW Graphics Suite', 'Suite de diseño gráfico vectorial', 'https://images.unsplash.com/photo-1609921212029-bb5a28e60960?w=500', NULL, 'CDR-GS-001', 'CorelDRAW 2024', 'Corel', 'asignado', 'bueno', NULL, 'Depto Diseño', '2023-12-10', 549.00, 3, 0.00, NULL, '2025-12-09 01:25:22', '2025-12-09 01:25:22'),
(27, 'SW-2025-0013', 1, 'Visual Studio Professional', 'IDE para desarrollo profesional', 'https://images.unsplash.com/photo-1461749280684-dccba630e2f6?w=500', NULL, 'VSP-2022-001', 'VS Pro 2022', 'Microsoft', 'disponible', 'excelente', NULL, 'Almacén Licencias', '2024-01-18', 499.00, 3, 0.00, NULL, '2025-12-09 01:25:22', '2025-12-09 01:25:22'),
(28, 'SW-2025-0014', 1, 'JetBrains IntelliJ IDEA', 'IDE para desarrollo Java', 'https://images.unsplash.com/photo-1555066931-4365d14bab8c?w=500', NULL, 'IJ-IDEA-001', 'IntelliJ Ultimate', 'JetBrains', 'asignado', 'excelente', NULL, 'Depto TI', '2024-01-22', 169.00, 1, 0.00, NULL, '2025-12-09 01:25:22', '2025-12-09 01:25:22'),
(29, 'SW-2025-0015', 1, 'GitHub Enterprise', 'Plataforma de desarrollo colaborativo', 'https://images.unsplash.com/photo-1618401471353-b98afee0b2eb?w=500', NULL, 'GHENT-001', 'Enterprise Cloud', 'GitHub', 'disponible', 'excelente', NULL, 'Depto TI', '2024-02-05', 231.00, 1, 0.00, NULL, '2025-12-09 01:25:22', '2025-12-09 01:25:22'),
(30, 'SW-2025-0016', 1, 'AutoCAD 2024', 'Software de diseño asistido por computadora', 'https://images.unsplash.com/photo-1503387762-592deb58ef4e?w=500', NULL, 'ACAD-2024-001', 'AutoCAD 2024', 'Autodesk', 'asignado', 'excelente', NULL, 'Depto Ingeniería', '2024-01-12', 1850.00, 1, 0.00, NULL, '2025-12-09 01:25:22', '2025-12-09 01:25:22'),
(31, 'SW-2025-0017', 1, 'SolidWorks Premium', 'Software de diseño 3D mecánico', 'https://images.unsplash.com/photo-1581092160562-40aa08e78837?w=500', NULL, 'SW-PREM-001', 'SolidWorks 2024', 'Dassault', 'disponible', 'excelente', NULL, 'Almacén Licencias', '2024-02-08', 3995.00, 1, 0.00, NULL, '2025-12-09 01:25:22', '2025-12-09 01:25:22'),
(32, 'SW-2025-0018', 1, 'Revit 2024', 'Software BIM para arquitectura', 'https://images.unsplash.com/photo-1503387762-592deb58ef4e?w=500', NULL, 'REVIT-2024-001', 'Revit 2024', 'Autodesk', 'asignado', 'excelente', NULL, 'Depto Arquitectura', '2024-01-15', 2690.00, 1, 0.00, NULL, '2025-12-09 01:25:22', '2025-12-09 01:25:22'),
(33, 'SW-2025-0019', 1, 'Kaspersky Endpoint Security', 'Antivirus empresarial - 50 licencias', 'https://images.unsplash.com/photo-1563013544-824ae1b704d3?w=500', NULL, 'KES-50-001', 'Endpoint Security', 'Kaspersky', 'disponible', 'excelente', NULL, 'Depto TI', '2024-01-10', 899.00, 1, 0.00, NULL, '2025-12-09 01:25:22', '2025-12-09 01:25:22'),
(34, 'SW-2025-0020', 1, 'Acronis Cyber Protect', 'Solución de backup y ciberseguridad', 'https://images.unsplash.com/photo-1614064641938-3bbee52942c7?w=500', NULL, 'ACP-STD-001', 'Cyber Protect Standard', 'Acronis', 'disponible', 'excelente', NULL, 'Sala Servidores', '2024-02-01', 599.00, 1, 0.00, NULL, '2025-12-09 01:25:22', '2025-12-09 01:25:22'),
(35, 'HW-2025-0001', 2, 'SSD Samsung 970 EVO Plus 1TB', 'Disco sólido NVMe M.2 1TB', 'https://images.unsplash.com/photo-1597872200969-2b65d56bd16b?w=500', NULL, 'SSD-970EP-001', '970 EVO Plus 1TB', 'Samsung', 'disponible', 'excelente', NULL, 'Almacén TI', '2024-01-10', 120.00, 5, 0.00, NULL, '2025-12-09 01:25:22', '2025-12-09 01:25:22'),
(36, 'HW-2025-0002', 2, 'SSD Samsung 970 EVO Plus 1TB', 'Disco sólido NVMe M.2 1TB', 'https://images.unsplash.com/photo-1597872200969-2b65d56bd16b?w=500', NULL, 'SSD-970EP-002', '970 EVO Plus 1TB', 'Samsung', 'disponible', 'excelente', NULL, 'Almacén TI', '2024-01-10', 120.00, 5, 0.00, NULL, '2025-12-09 01:25:22', '2025-12-09 01:25:22'),
(37, 'HW-2025-0003', 2, 'SSD Kingston A400 480GB', 'Disco sólido SATA 2.5\" 480GB', 'https://images.unsplash.com/photo-1597872200969-2b65d56bd16b?w=500', NULL, 'SSD-KA400-001', 'A400 480GB', 'Kingston', 'disponible', 'bueno', NULL, 'Almacén TI', '2023-11-15', 45.00, 5, 0.00, NULL, '2025-12-09 01:25:22', '2025-12-09 01:25:22'),
(38, 'HW-2025-0004', 2, 'HDD Seagate Barracuda 2TB', 'Disco duro mecánico 3.5\" 2TB 7200RPM', 'https://images.unsplash.com/photo-1531492746076-161ca9bcad58?w=500', NULL, 'HDD-BARRA-001', 'Barracuda 2TB', 'Seagate', 'disponible', 'bueno', NULL, 'Almacén TI', '2023-12-01', 55.00, 5, 0.00, NULL, '2025-12-09 01:25:22', '2025-12-09 01:25:22'),
(39, 'HW-2025-0005', 2, 'HDD WD Red Plus 4TB', 'Disco duro NAS 3.5\" 4TB', 'https://images.unsplash.com/photo-1531492746076-161ca9bcad58?w=500', NULL, 'HDD-WDRED-001', 'WD Red Plus 4TB', 'Western Digital', 'disponible', 'excelente', NULL, 'Almacén TI', '2024-01-20', 110.00, 5, 0.00, NULL, '2025-12-09 01:25:22', '2025-12-09 01:25:22'),
(40, 'HW-2025-0006', 2, 'RAM Corsair Vengeance 16GB DDR4', 'Memoria RAM DDR4 16GB 3200MHz', 'https://images.unsplash.com/photo-1541403890281-e27927f8a4e6?w=500', NULL, 'RAM-VENG-001', 'Vengeance LPX 16GB', 'Corsair', 'disponible', 'excelente', NULL, 'Almacén TI', '2024-01-15', 75.00, 5, 0.00, NULL, '2025-12-09 01:25:22', '2025-12-09 01:25:22'),
(41, 'HW-2025-0007', 2, 'RAM Corsair Vengeance 16GB DDR4', 'Memoria RAM DDR4 16GB 3200MHz', 'https://images.unsplash.com/photo-1541403890281-e27927f8a4e6?w=500', NULL, 'RAM-VENG-002', 'Vengeance LPX 16GB', 'Corsair', 'disponible', 'excelente', NULL, 'Almacén TI', '2024-01-15', 75.00, 5, 0.00, NULL, '2025-12-09 01:25:22', '2025-12-09 01:25:22'),
(42, 'HW-2025-0008', 2, 'RAM Kingston Fury 32GB DDR4', 'Memoria RAM DDR4 32GB 3200MHz Kit', 'https://images.unsplash.com/photo-1541403890281-e27927f8a4e6?w=500', NULL, 'RAM-FURY-001', 'Fury Beast 32GB', 'Kingston', 'disponible', 'excelente', NULL, 'Almacén TI', '2024-01-18', 125.00, 5, 0.00, NULL, '2025-12-09 01:25:22', '2025-12-09 01:25:22'),
(43, 'HW-2025-0009', 2, 'RAM G.Skill Trident Z 64GB DDR4', 'Memoria RAM DDR4 64GB 3600MHz Kit', 'https://images.unsplash.com/photo-1541403890281-e27927f8a4e6?w=500', NULL, 'RAM-TRID-001', 'Trident Z RGB 64GB', 'G.Skill', 'asignado', 'excelente', NULL, 'Workstation Diseño', '2024-01-22', 280.00, 5, 0.00, NULL, '2025-12-09 01:25:22', '2025-12-09 01:25:22'),
(44, 'HW-2025-0010', 2, 'RAM Crucial 8GB DDR4', 'Memoria RAM DDR4 8GB 2666MHz', 'https://images.unsplash.com/photo-1541403890281-e27927f8a4e6?w=500', NULL, 'RAM-CRUC-001', 'Crucial 8GB', 'Crucial', 'disponible', 'bueno', NULL, 'Almacén TI', '2023-10-10', 35.00, 5, 0.00, NULL, '2025-12-09 01:25:22', '2025-12-09 01:25:22'),
(45, 'HW-2025-0011', 2, 'GPU NVIDIA RTX 4060 Ti', 'Tarjeta gráfica RTX 4060 Ti 8GB', 'https://images.unsplash.com/photo-1591488320449-011701bb6704?w=500', NULL, 'GPU-RTX4060TI-001', 'GeForce RTX 4060 Ti', 'NVIDIA', 'disponible', 'excelente', NULL, 'Almacén TI', '2024-02-01', 499.00, 5, 0.00, NULL, '2025-12-09 01:25:22', '2025-12-09 01:25:22'),
(46, 'HW-2025-0012', 2, 'GPU NVIDIA RTX 3060', 'Tarjeta gráfica RTX 3060 12GB', 'https://images.unsplash.com/photo-1591488320449-011701bb6704?w=500', NULL, 'GPU-RTX3060-001', 'GeForce RTX 3060', 'NVIDIA', 'asignado', 'bueno', NULL, 'PC Diseño 01', '2023-11-15', 350.00, 5, 0.00, NULL, '2025-12-09 01:25:22', '2025-12-09 01:25:22'),
(47, 'HW-2025-0013', 2, 'GPU AMD Radeon RX 7600', 'Tarjeta gráfica RX 7600 8GB', 'https://images.unsplash.com/photo-1591488320449-011701bb6704?w=500', NULL, 'GPU-RX7600-001', 'Radeon RX 7600', 'AMD', 'disponible', 'excelente', NULL, 'Almacén TI', '2024-01-25', 299.00, 5, 0.00, NULL, '2025-12-09 01:25:22', '2025-12-09 01:25:22'),
(48, 'HW-2025-0014', 2, 'Monitor Dell UltraSharp 27\" 4K', 'Monitor profesional 27\" IPS 4K', 'https://images.unsplash.com/photo-1527443224154-c4a3942d3acf?w=500', NULL, 'MON-U2720Q-001', 'U2720Q', 'Dell', 'disponible', 'excelente', NULL, 'Almacén TI', '2024-02-05', 520.00, 5, 0.00, NULL, '2025-12-09 01:25:22', '2025-12-09 01:25:22'),
(49, 'HW-2025-0015', 2, 'Monitor Dell UltraSharp 27\" 4K', 'Monitor profesional 27\" IPS 4K', 'https://images.unsplash.com/photo-1527443224154-c4a3942d3acf?w=500', NULL, 'MON-U2720Q-002', 'U2720Q', 'Dell', 'asignado', 'excelente', NULL, 'Oficina Gerencia', '2024-02-06', 520.00, 5, 0.00, NULL, '2025-12-09 01:25:22', '2025-12-09 01:25:22'),
(50, 'HW-2025-0016', 2, 'Monitor LG UltraWide 34\"', 'Monitor ultrawide 34\" 21:9 QHD', 'https://images.unsplash.com/photo-1527443224154-c4a3942d3acf?w=500', NULL, 'MON-LG34-001', '34WN80C-B', 'LG', 'disponible', 'excelente', NULL, 'Almacén TI', '2024-01-28', 450.00, 5, 0.00, NULL, '2025-12-09 01:25:22', '2025-12-09 01:25:22'),
(51, 'HW-2025-0017', 2, 'Monitor Samsung 24\" Full HD', 'Monitor básico 24\" 1920x1080', 'https://images.unsplash.com/photo-1527443224154-c4a3942d3acf?w=500', NULL, 'MON-SAM24-001', 'S24R350', 'Samsung', 'disponible', 'bueno', NULL, 'Almacén TI', '2023-12-10', 149.00, 5, 0.00, NULL, '2025-12-09 01:25:22', '2025-12-09 01:25:22'),
(52, 'HW-2025-0018', 2, 'Monitor BenQ 32\" 4K', 'Monitor diseñador 32\" IPS 4K', 'https://images.unsplash.com/photo-1527443224154-c4a3942d3acf?w=500', NULL, 'MON-BENQ32-001', 'PD3220U', 'BenQ', 'asignado', 'excelente', NULL, 'Depto Diseño', '2024-01-30', 899.00, 5, 0.00, NULL, '2025-12-09 01:25:22', '2025-12-09 01:25:22'),
(53, 'HW-2025-0019', 2, 'Procesador Intel Core i7-13700K', 'CPU Intel i7 13th Gen 16 núcleos', 'https://images.unsplash.com/photo-1555617981-dac3880eac6e?w=500', NULL, 'CPU-I713700K-001', 'Core i7-13700K', 'Intel', 'disponible', 'excelente', NULL, 'Almacén TI', '2024-01-20', 409.00, 5, 0.00, NULL, '2025-12-09 01:25:22', '2025-12-09 01:25:22'),
(54, 'HW-2025-0020', 2, 'Procesador AMD Ryzen 7 7700X', 'CPU AMD Ryzen 7 8 núcleos', 'https://images.unsplash.com/photo-1555617981-dac3880eac6e?w=500', NULL, 'CPU-R77700X-001', 'Ryzen 7 7700X', 'AMD', 'disponible', 'excelente', NULL, 'Almacén TI', '2024-01-22', 349.00, 5, 0.00, NULL, '2025-12-09 01:25:22', '2025-12-09 01:25:22'),
(55, 'HW-2025-0021', 2, 'Fuente Corsair RM850x 850W', 'PSU modular 80+ Gold 850W', 'https://images.unsplash.com/photo-1587202372616-b43abea06c2a?w=500', NULL, 'PSU-RM850X-001', 'RM850x', 'Corsair', 'disponible', 'excelente', NULL, 'Almacén TI', '2024-01-15', 139.00, 7, 0.00, NULL, '2025-12-09 01:25:22', '2025-12-09 01:25:22'),
(56, 'HW-2025-0022', 2, 'Fuente EVGA SuperNOVA 750W', 'PSU modular 80+ Gold 750W', 'https://images.unsplash.com/photo-1587202372616-b43abea06c2a?w=500', NULL, 'PSU-SN750-001', 'SuperNOVA 750 G5', 'EVGA', 'disponible', 'bueno', NULL, 'Almacén TI', '2023-11-20', 109.00, 7, 0.00, NULL, '2025-12-09 01:25:22', '2025-12-09 01:25:22'),
(57, 'HW-2025-0023', 2, 'Motherboard ASUS ROG Strix B550', 'Placa madre AMD AM4 ATX', 'https://images.unsplash.com/photo-1562976540-1502c2145186?w=500', NULL, 'MB-ROGB550-001', 'ROG STRIX B550-F', 'ASUS', 'disponible', 'excelente', NULL, 'Almacén TI', '2024-01-18', 189.00, 5, 0.00, NULL, '2025-12-09 01:25:22', '2025-12-09 01:25:22'),
(58, 'HW-2025-0024', 2, 'Motherboard MSI MPG Z690', 'Placa madre Intel LGA1700 ATX', 'https://images.unsplash.com/photo-1562976540-1502c2145186?w=500', NULL, 'MB-Z690-001', 'MPG Z690 Edge', 'MSI', 'disponible', 'excelente', NULL, 'Almacén TI', '2024-01-20', 269.00, 5, 0.00, NULL, '2025-12-09 01:25:22', '2025-12-09 01:25:22'),
(59, 'HW-2025-0025', 2, 'Teclado Logitech MX Keys', 'Teclado inalámbrico profesional', 'https://images.unsplash.com/photo-1587829741301-dc798b83add3?w=500', NULL, 'KBD-MXKEYS-001', 'MX Keys', 'Logitech', 'disponible', 'excelente', NULL, 'Almacén TI', '2024-01-12', 99.00, 5, 0.00, NULL, '2025-12-09 01:25:22', '2025-12-09 01:25:22'),
(60, 'RED-2025-0001', 3, 'Router Cisco ISR 4331', 'Router empresarial Cisco ISR 4000 Series', 'https://images.unsplash.com/photo-1606904825846-647eb07f5be2?w=500', NULL, 'CSC-ISR4331-001', 'ISR 4331', 'Cisco', 'disponible', 'excelente', NULL, 'Sala Servidores', '2024-01-05', 2800.00, 7, 0.00, NULL, '2025-12-09 01:25:22', '2025-12-09 01:25:22'),
(61, 'RED-2025-0002', 3, 'Router MikroTik RB5009', 'Router empresarial 8 puertos Gigabit', 'https://images.unsplash.com/photo-1606904825846-647eb07f5be2?w=500', NULL, 'MTK-RB5009-001', 'RB5009UG+S+IN', 'MikroTik', 'disponible', 'excelente', NULL, 'Sucursal Norte', '2024-01-10', 299.00, 7, 0.00, NULL, '2025-12-09 01:25:22', '2025-12-09 01:25:22'),
(62, 'RED-2025-0003', 3, 'Router Ubiquiti EdgeRouter', 'Router avanzado 4 puertos Gigabit', 'https://images.unsplash.com/photo-1606904825846-647eb07f5be2?w=500', NULL, 'UBI-ER4-001', 'EdgeRouter 4', 'Ubiquiti', 'disponible', 'bueno', NULL, 'Sucursal Sur', '2023-12-15', 199.00, 7, 0.00, NULL, '2025-12-09 01:25:22', '2025-12-09 01:25:22'),
(63, 'RED-2025-0004', 3, 'Router TP-Link ER7206', 'Router VPN Multi-WAN Gigabit', 'https://images.unsplash.com/photo-1606904825846-647eb07f5be2?w=500', NULL, 'TPL-ER7206-001', 'ER7206', 'TP-Link', 'disponible', 'excelente', NULL, 'Almacén TI', '2024-02-01', 139.00, 5, 0.00, NULL, '2025-12-09 01:25:22', '2025-12-09 01:25:22'),
(64, 'RED-2025-0005', 3, 'Switch Cisco Catalyst 2960X 48 puertos', 'Switch administrable 48p Gigabit PoE+', 'https://images.unsplash.com/photo-1622279457486-62dcc4a431d6?w=500', NULL, 'CSC-2960X-001', 'Catalyst 2960X-48FPD', 'Cisco', 'disponible', 'excelente', NULL, 'Sala Servidores', '2024-01-08', 3500.00, 7, 0.00, NULL, '2025-12-09 01:25:22', '2025-12-09 01:25:22'),
(65, 'RED-2025-0006', 3, 'Switch HP 1950 48 puertos', 'Switch administrable 48p Gigabit', 'https://images.unsplash.com/photo-1622279457486-62dcc4a431d6?w=500', NULL, 'HP-1950-001', '1950-48G-2SFP', 'HP', 'disponible', 'bueno', NULL, 'Sala Servidores', '2023-12-10', 850.00, 7, 0.00, NULL, '2025-12-09 01:25:22', '2025-12-09 01:25:22'),
(66, 'RED-2025-0007', 3, 'Switch Ubiquiti USW-24-PoE', 'Switch administrable 24p Gigabit PoE+', 'https://images.unsplash.com/photo-1622279457486-62dcc4a431d6?w=500', NULL, 'UBI-USW24-001', 'UniFi Switch 24 PoE', 'Ubiquiti', 'disponible', 'excelente', NULL, 'Oficina Piso 2', '2024-01-15', 399.00, 7, 0.00, NULL, '2025-12-09 01:25:22', '2025-12-09 01:25:22'),
(67, 'RED-2025-0008', 3, 'Switch TP-Link TL-SG1048', 'Switch no administrable 48p Gigabit', 'https://images.unsplash.com/photo-1622279457486-62dcc4a431d6?w=500', NULL, 'TPL-SG1048-001', 'TL-SG1048', 'TP-Link', 'disponible', 'bueno', NULL, 'Oficina Piso 3', '2023-11-20', 189.00, 7, 0.00, NULL, '2025-12-09 01:25:22', '2025-12-09 01:25:22'),
(68, 'RED-2025-0009', 3, 'Switch Netgear GS724T', 'Switch administrable 24p Gigabit Smart', 'https://images.unsplash.com/photo-1622279457486-62dcc4a431d6?w=500', NULL, 'NET-GS724T-001', 'GS724Tv4', 'Netgear', 'disponible', 'excelente', NULL, 'Almacén TI', '2024-01-20', 299.00, 7, 0.00, NULL, '2025-12-09 01:25:22', '2025-12-09 01:25:22'),
(69, 'RED-2025-0010', 3, 'Access Point Ubiquiti U6 Long Range', 'AP WiFi 6 de largo alcance', 'https://images.unsplash.com/photo-1544197150-b99a580bb7a8?w=500', NULL, 'UBI-U6LR-001', 'UniFi 6 LR', 'Ubiquiti', 'disponible', 'excelente', NULL, 'Oficina Piso 1', '2024-01-12', 179.00, 5, 0.00, NULL, '2025-12-09 01:25:22', '2025-12-09 01:25:22'),
(70, 'RED-2025-0011', 3, 'Access Point Cisco Aironet 2802', 'AP empresarial WiFi 5 Wave 2', 'https://images.unsplash.com/photo-1544197150-b99a580bb7a8?w=500', NULL, 'CSC-AIR2802-001', 'Aironet 2802i', 'Cisco', 'disponible', 'excelente', NULL, 'Sala Conferencias', '2024-01-15', 599.00, 7, 0.00, NULL, '2025-12-09 01:25:22', '2025-12-09 01:25:22'),
(71, 'RED-2025-0012', 3, 'Access Point TP-Link EAP660 HD', 'AP WiFi 6 empresarial', 'https://images.unsplash.com/photo-1544197150-b99a580bb7a8?w=500', NULL, 'TPL-EAP660-001', 'EAP660 HD', 'TP-Link', 'asignado', 'excelente', NULL, 'Oficina Piso 2', '2024-01-18', 149.00, 5, 0.00, NULL, '2025-12-09 01:25:22', '2025-12-09 01:25:22'),
(72, 'RED-2025-0013', 3, 'Access Point Aruba AP-505', 'AP WiFi 6 empresarial indoor', 'https://images.unsplash.com/photo-1544197150-b99a580bb7a8?w=500', NULL, 'ARB-AP505-001', 'AP-505', 'Aruba', 'disponible', 'excelente', NULL, 'Almacén TI', '2024-01-22', 495.00, 7, 0.00, NULL, '2025-12-09 01:25:22', '2025-12-09 01:25:22'),
(73, 'RED-2025-0014', 3, 'Access Point Ruckus R650', 'AP WiFi 6 alto rendimiento', 'https://images.unsplash.com/photo-1544197150-b99a580bb7a8?w=500', NULL, 'RCK-R650-001', 'R650', 'Ruckus', 'disponible', 'excelente', NULL, 'Cafetería', '2024-02-01', 650.00, 7, 0.00, NULL, '2025-12-09 01:25:22', '2025-12-09 01:25:22'),
(74, 'RED-2025-0015', 3, 'Firewall Fortinet FortiGate 60F', 'Firewall NGFW de sobremesa', 'https://images.unsplash.com/photo-1558494949-ef010cbdcc31?w=500', NULL, 'FTN-FG60F-001', 'FortiGate 60F', 'Fortinet', 'disponible', 'excelente', NULL, 'Sala Servidores', '2024-01-10', 1250.00, 5, 0.00, NULL, '2025-12-09 01:25:23', '2025-12-09 01:25:23'),
(75, 'RED-2025-0016', 3, 'Firewall Cisco ASA 5516-X', 'Firewall empresarial avanzado', 'https://images.unsplash.com/photo-1558494949-ef010cbdcc31?w=500', NULL, 'CSC-ASA5516-001', 'ASA 5516-X', 'Cisco', 'disponible', 'excelente', NULL, 'Sala Servidores', '2024-01-12', 2900.00, 7, 0.00, NULL, '2025-12-09 01:25:23', '2025-12-09 01:25:23'),
(76, 'RED-2025-0017', 3, 'Firewall Palo Alto PA-220', 'Firewall NGFW compacto', 'https://images.unsplash.com/photo-1558494949-ef010cbdcc31?w=500', NULL, 'PAL-PA220-001', 'PA-220', 'Palo Alto', 'disponible', 'excelente', NULL, 'Sucursal Norte', '2024-01-20', 1850.00, 5, 0.00, NULL, '2025-12-09 01:25:23', '2025-12-09 01:25:23'),
(77, 'RED-2025-0018', 3, 'Firewall pfSense SG-3100', 'Firewall open-source appliance', 'https://images.unsplash.com/photo-1558494949-ef010cbdcc31?w=500', NULL, 'PFS-SG3100-001', 'SG-3100', 'Netgate', 'disponible', 'bueno', NULL, 'Sucursal Sur', '2023-12-15', 499.00, 5, 0.00, NULL, '2025-12-09 01:25:23', '2025-12-09 01:25:23'),
(78, 'RED-2025-0019', 3, 'Firewall SonicWall TZ370', 'Firewall para pequeñas empresas', 'https://images.unsplash.com/photo-1558494949-ef010cbdcc31?w=500', NULL, 'SNW-TZ370-001', 'TZ370', 'SonicWall', 'asignado', 'excelente', NULL, 'Oficina Regional', '2024-01-25', 1150.00, 5, 0.00, NULL, '2025-12-09 01:25:23', '2025-12-09 01:25:23'),
(79, 'COMP-2025-0001', 4, 'Laptop Dell Latitude 7420', 'Intel i7 11th Gen 16GB RAM 512GB SSD', 'https://images.unsplash.com/photo-1496181133206-80ce9b88a853?w=500', NULL, 'DELL-LAT-2024-001', 'Latitude 7420', 'Dell', 'disponible', 'excelente', NULL, 'Almacén TI', '2024-01-15', 1250.00, 5, 0.00, NULL, '2025-12-09 01:25:23', '2025-12-09 01:25:23'),
(80, 'COMP-2025-0002', 4, 'Laptop HP EliteBook 840 G8', 'Intel i5 11th Gen 8GB RAM 256GB SSD', 'https://images.unsplash.com/photo-1588872657578-7efd1f1555ed?w=500', NULL, 'HP-ELB-2024-001', 'EliteBook 840 G8', 'HP', 'asignado', 'excelente', NULL, 'Usuario Contabilidad', '2024-01-20', 1180.00, 5, 0.00, NULL, '2025-12-09 01:25:23', '2025-12-09 01:25:23'),
(81, 'COMP-2025-0003', 4, 'Laptop Lenovo ThinkPad X1 Carbon', 'Intel i7 16GB RAM 1TB SSD Pantalla 14 4K', 'https://images.unsplash.com/photo-1525547719571-a2d4ac8945e2?w=500', NULL, 'LEN-X1C-2024-001', 'ThinkPad X1 Carbon Gen 9', 'Lenovo', 'asignado', 'excelente', NULL, 'Gerencia General', '2024-02-10', 1450.00, 5, 0.00, NULL, '2025-12-09 01:25:23', '2025-12-09 01:25:23'),
(82, 'COMP-2025-0004', 4, 'Laptop Apple MacBook Pro 14', 'M1 Pro 16GB 512GB SSD Pantalla Retina', 'https://images.unsplash.com/photo-1517336714731-489689fd1ca8?w=500', NULL, 'MAC-MBP-2024-001', 'MacBook Pro 14', 'Apple', 'asignado', 'excelente', NULL, 'Depto Diseño', '2024-02-15', 2200.00, 5, 0.00, NULL, '2025-12-09 01:25:23', '2025-12-09 01:25:23'),
(83, 'COMP-2025-0005', 4, 'Laptop ASUS ZenBook 14', 'Intel i5 8GB RAM 512GB SSD', 'https://images.unsplash.com/photo-1484788984921-03950022c9ef?w=500', NULL, 'ASUS-ZEN-2024-001', 'ZenBook 14', 'ASUS', 'disponible', 'excelente', NULL, 'Almacén TI', '2024-03-01', 980.00, 5, 0.00, NULL, '2025-12-09 01:25:23', '2025-12-09 01:25:23'),
(84, 'COMP-2025-0006', 4, 'Desktop Dell OptiPlex 7090', 'Intel i7 11th Gen 16GB RAM 512GB SSD', 'https://images.unsplash.com/photo-1587202372775-e229f172b9d7?w=500', NULL, 'DELL-OPT-2024-001', 'OptiPlex 7090 MT', 'Dell', 'disponible', 'excelente', NULL, 'Almacén TI', '2024-01-18', 1150.00, 5, 0.00, NULL, '2025-12-09 01:25:23', '2025-12-09 01:25:23'),
(85, 'COMP-2025-0007', 4, 'Desktop HP ProDesk 600 G6', 'Intel i5 10th Gen 8GB RAM 256GB SSD', 'https://images.unsplash.com/photo-1587202372775-e229f172b9d7?w=500', NULL, 'HP-PRD-2024-001', 'ProDesk 600 G6', 'HP', 'asignado', 'bueno', NULL, 'Recepción', '2024-01-22', 890.00, 5, 0.00, NULL, '2025-12-09 01:25:23', '2025-12-09 01:25:23'),
(86, 'COMP-2025-0008', 4, 'Desktop Lenovo ThinkCentre M720', 'Intel i5 9th Gen 8GB RAM 1TB HDD', 'https://images.unsplash.com/photo-1587202372775-e229f172b9d7?w=500', NULL, 'LEN-TC-2024-001', 'ThinkCentre M720 Tower', 'Lenovo', 'asignado', 'bueno', NULL, 'Contabilidad', '2024-02-01', 750.00, 5, 0.00, NULL, '2025-12-09 01:25:23', '2025-12-09 01:25:23'),
(87, 'COMP-2025-0009', 4, 'Workstation HP Z2 G5', 'Intel Xeon 32GB RAM 1TB SSD NVIDIA Quadro', 'https://images.unsplash.com/photo-1587202372775-e229f172b9d7?w=500', NULL, 'HP-Z2-2024-001', 'Z2 Tower G5', 'HP', 'asignado', 'excelente', NULL, 'Depto Ingeniería', '2024-02-05', 2850.00, 5, 0.00, NULL, '2025-12-09 01:25:23', '2025-12-09 01:25:23'),
(88, 'COMP-2025-0010', 4, 'Desktop Apple Mac Mini M2', 'Apple M2 8GB RAM 256GB SSD', 'https://images.unsplash.com/photo-1591370874773-6702e8f12fd8?w=500', NULL, 'MAC-MINI-2024-001', 'Mac Mini M2', 'Apple', 'disponible', 'excelente', NULL, 'Almacén TI', '2024-02-10', 799.00, 5, 0.00, NULL, '2025-12-09 01:25:23', '2025-12-09 01:25:23'),
(89, 'COMP-2025-0011', 4, 'Tablet Apple iPad Pro 12.9', 'M2 256GB WiFi + Cellular', 'https://images.unsplash.com/photo-1544244015-0df4b3ffc6b0?w=500', NULL, 'IPAD-PRO-2024-001', 'iPad Pro 12.9 6th Gen', 'Apple', 'asignado', 'excelente', NULL, 'Gerencia Ventas', '2024-01-25', 1299.00, 4, 0.00, NULL, '2025-12-09 01:25:23', '2025-12-09 01:25:23'),
(90, 'COMP-2025-0012', 4, 'Tablet Samsung Galaxy Tab S9', 'Snapdragon 8 Gen 2 128GB 5G', 'https://images.unsplash.com/photo-1544244015-0df4b3ffc6b0?w=500', NULL, 'SAM-TABS9-2024-001', 'Galaxy Tab S9', 'Samsung', 'disponible', 'excelente', NULL, 'Almacén TI', '2024-02-01', 899.00, 4, 0.00, NULL, '2025-12-09 01:25:23', '2025-12-09 01:25:23'),
(91, 'COMP-2025-0013', 4, 'Tablet Microsoft Surface Pro 9', 'Intel i5 12th Gen 8GB RAM 256GB SSD', 'https://images.unsplash.com/photo-1544244015-0df4b3ffc6b0?w=500', NULL, 'MS-SURP9-2024-001', 'Surface Pro 9', 'Microsoft', 'asignado', 'excelente', NULL, 'Ejecutivo Comercial', '2024-02-08', 1099.00, 4, 0.00, NULL, '2025-12-09 01:25:23', '2025-12-09 01:25:23'),
(92, 'COMP-2025-0014', 4, 'Tablet Lenovo Tab P12 Pro', 'Snapdragon 870 8GB RAM 256GB', 'https://images.unsplash.com/photo-1544244015-0df4b3ffc6b0?w=500', NULL, 'LEN-TABP12-2024-001', 'Tab P12 Pro', 'Lenovo', 'disponible', 'bueno', NULL, 'Almacén TI', '2024-02-12', 649.00, 4, 0.00, NULL, '2025-12-09 01:25:23', '2025-12-09 01:25:23'),
(93, 'COMP-2025-0015', 4, 'Tablet Amazon Fire HD 10', 'Tablet básica 32GB para uso general', 'https://images.unsplash.com/photo-1544244015-0df4b3ffc6b0?w=500', NULL, 'AMZ-FIREHD10-2024-001', 'Fire HD 10', 'Amazon', 'disponible', 'bueno', NULL, 'Sala Espera', '2024-02-15', 149.00, 3, 0.00, NULL, '2025-12-09 01:25:23', '2025-12-09 01:25:23'),
(94, 'COMP-2025-0016', 4, 'Servidor Dell PowerEdge R750', 'Intel Xeon Silver 64GB RAM 4x2TB RAID', 'https://images.unsplash.com/photo-1558494949-ef010cbdcc31?w=500', NULL, 'DELL-R750-2024-001', 'PowerEdge R750', 'Dell', 'disponible', 'excelente', NULL, 'Sala Servidores', '2024-01-10', 5500.00, 7, 0.00, NULL, '2025-12-09 01:25:23', '2025-12-09 01:25:23'),
(95, 'COMP-2025-0017', 4, 'Servidor HP ProLiant DL380 Gen10', 'Intel Xeon Gold 128GB RAM 8x1TB SSD', 'https://images.unsplash.com/photo-1558494949-ef010cbdcc31?w=500', NULL, 'HP-DL380-2024-001', 'ProLiant DL380 Gen10', 'HP', 'disponible', 'excelente', NULL, 'Sala Servidores', '2024-01-15', 7200.00, 7, 0.00, NULL, '2025-12-09 01:25:23', '2025-12-09 01:25:23'),
(96, 'COMP-2025-0018', 4, 'Servidor Lenovo ThinkSystem SR650', 'Intel Xeon Silver 96GB RAM 6x2TB RAID', 'https://images.unsplash.com/photo-1558494949-ef010cbdcc31?w=500', NULL, 'LEN-SR650-2024-001', 'ThinkSystem SR650', 'Lenovo', 'disponible', 'excelente', NULL, 'Sala Servidores', '2024-01-20', 6100.00, 7, 0.00, NULL, '2025-12-09 01:25:23', '2025-12-09 01:25:23'),
(97, 'COMP-2025-0019', 4, 'Servidor Supermicro SuperServer', 'AMD EPYC 256GB RAM 12x4TB SSD NVMe', 'https://images.unsplash.com/photo-1558494949-ef010cbdcc31?w=500', NULL, 'SPM-SS-2024-001', 'SuperServer 1029P', 'Supermicro', 'disponible', 'excelente', NULL, 'Sala Servidores', '2024-02-01', 9800.00, 7, 0.00, NULL, '2025-12-09 01:25:23', '2025-12-09 01:25:23'),
(98, 'COMP-2025-0020', 4, 'NAS Synology DiskStation DS1821+', 'NAS 8 bahías Intel Atom 4GB RAM', 'https://images.unsplash.com/photo-1558494949-ef010cbdcc31?w=500', NULL, 'SYN-DS1821-2024-001', 'DiskStation DS1821+', 'Synology', 'disponible', 'excelente', NULL, 'Sala Servidores', '2024-02-05', 1299.00, 7, 0.00, NULL, '2025-12-09 01:25:23', '2025-12-09 01:25:23'),
(99, 'COMP-2025-0021', 4, 'Impresora HP LaserJet Pro M404dn', 'Impresora láser monocromática duplex red', 'https://images.unsplash.com/photo-1612815154858-60aa4c59eaa6?w=500', NULL, 'HP-LJP-M404-001', 'LaserJet Pro M404dn', 'HP', 'asignado', 'excelente', NULL, 'Oficina Piso 1', '2024-01-12', 329.00, 5, 0.00, NULL, '2025-12-09 01:25:23', '2025-12-09 01:25:23'),
(100, 'COMP-2025-0022', 4, 'Multifuncional Canon imageRUNNER 2625', 'Copiadora/impresora/escáner láser B/N', 'https://images.unsplash.com/photo-1612815154858-60aa4c59eaa6?w=500', NULL, 'CAN-IR2625-001', 'imageRUNNER 2625', 'Canon', 'asignado', 'bueno', NULL, 'Oficina Piso 2', '2024-01-18', 1850.00, 7, 0.00, NULL, '2025-12-09 01:25:23', '2025-12-09 01:25:23'),
(101, 'COMP-2025-0023', 4, 'Impresora Epson EcoTank L3250', 'Impresora multifuncional tinta continua', 'https://images.unsplash.com/photo-1612815154858-60aa4c59eaa6?w=500', '/cmdb_web/public/uploads/qr/qr_equipo_101.png', 'EPS-L3250-001', 'EcoTank L3250', 'Epson', 'disponible', 'bueno', NULL, 'Almacén TI', '2024-01-25', 269.00, 5, 0.00, NULL, '2025-12-09 01:25:23', '2025-12-09 01:25:51'),
(102, 'COMP-2025-0024', 4, 'Impresora Brother HL-L2350DW', 'Impresora láser monocromática WiFi', 'https://images.unsplash.com/photo-1612815154858-60aa4c59eaa6?w=500', NULL, 'BRO-HLL2350-001', 'HL-L2350DW', 'Brother', 'asignado', 'excelente', NULL, 'Contabilidad', '2024-02-01', 199.00, 5, 0.00, NULL, '2025-12-09 01:25:23', '2025-12-09 01:25:23'),
(103, 'COMP-2025-0025', 4, 'Plotter HP DesignJet T650', 'Impresora gran formato 36 pulgadas', 'https://images.unsplash.com/photo-1612815154858-60aa4c59eaa6?w=500', NULL, 'HP-DJT650-001', 'DesignJet T650', 'HP', 'disponible', 'excelente', NULL, 'Depto Arquitectura', '2024-02-10', 2199.00, 7, 0.00, NULL, '2025-12-09 01:25:23', '2025-12-09 01:25:23'),
(104, 'TEL-2025-0001', 5, 'Teléfono IP Cisco 8841', 'Teléfono IP empresarial con pantalla color', 'https://images.unsplash.com/photo-1557825835-70d97c4aa401?w=500', NULL, 'CSC-8841-001', 'IP Phone 8841', 'Cisco', 'asignado', 'excelente', NULL, 'Gerencia General', '2024-01-10', 349.00, 7, 0.00, NULL, '2025-12-09 01:25:23', '2025-12-09 01:25:23'),
(105, 'TEL-2025-0002', 5, 'Teléfono IP Yealink T46S', 'Teléfono IP con pantalla táctil color', 'https://images.unsplash.com/photo-1557825835-70d97c4aa401?w=500', NULL, 'YEA-T46S-001', 'SIP-T46S', 'Yealink', 'asignado', 'excelente', NULL, 'Recepción', '2024-01-15', 189.00, 7, 0.00, NULL, '2025-12-09 01:25:23', '2025-12-09 01:25:23'),
(106, 'TEL-2025-0003', 5, 'Teléfono IP Grandstream GXP2170', 'Teléfono IP 12 líneas LCD color', 'https://images.unsplash.com/photo-1557825835-70d97c4aa401?w=500', NULL, 'GRD-GXP2170-001', 'GXP2170', 'Grandstream', 'disponible', 'excelente', NULL, 'Almacén TI', '2024-01-20', 145.00, 7, 0.00, NULL, '2025-12-09 01:25:23', '2025-12-09 01:25:23'),
(107, 'TEL-2025-0004', 5, 'Teléfono IP Polycom VVX 450', 'Teléfono IP ejecutivo 12 líneas', 'https://images.unsplash.com/photo-1557825835-70d97c4aa401?w=500', NULL, 'PLY-VVX450-001', 'VVX 450', 'Polycom', 'asignado', 'bueno', NULL, 'Oficina Ventas', '2023-12-10', 299.00, 7, 0.00, NULL, '2025-12-09 01:25:23', '2025-12-09 01:25:23'),
(108, 'TEL-2025-0005', 5, 'Teléfono IP Fanvil X7C', 'Teléfono IP touchscreen Android', 'https://images.unsplash.com/photo-1557825835-70d97c4aa401?w=500', NULL, 'FAN-X7C-001', 'X7C', 'Fanvil', 'disponible', 'excelente', NULL, 'Almacén TI', '2024-02-01', 259.00, 7, 0.00, NULL, '2025-12-09 01:25:23', '2025-12-09 01:25:23'),
(109, 'TEL-2025-0006', 5, 'Central Telefónica Grandstream UCM6304', 'IP PBX hasta 500 usuarios 45 llamadas', 'https://images.unsplash.com/photo-1606904825846-647eb07f5be2?w=500', NULL, 'GRD-UCM6304-001', 'UCM6304', 'Grandstream', 'disponible', 'excelente', NULL, 'Sala Servidores', '2024-01-08', 1499.00, 7, 0.00, NULL, '2025-12-09 01:25:23', '2025-12-09 01:25:23'),
(110, 'TEL-2025-0007', 5, 'Central Telefónica 3CX', 'Sistema PBX software licencia 64 ext', 'https://images.unsplash.com/photo-1606904825846-647eb07f5be2?w=500', NULL, '3CX-ENT64-001', '3CX Enterprise 64SC', '3CX', 'disponible', 'excelente', NULL, 'Sala Servidores', '2024-01-12', 899.00, 3, 0.00, NULL, '2025-12-09 01:25:23', '2025-12-09 01:25:23'),
(111, 'TEL-2025-0008', 5, 'Central Telefónica Elastix', 'PBX open-source appliance 100 ext', 'https://images.unsplash.com/photo-1606904825846-647eb07f5be2?w=500', NULL, 'ELX-APP-001', 'Elastix MT 100', 'Elastix', 'disponible', 'bueno', NULL, 'Sucursal Norte', '2023-11-20', 650.00, 7, 0.00, NULL, '2025-12-09 01:25:23', '2025-12-09 01:25:23'),
(112, 'TEL-2025-0009', 5, 'Central Telefónica Cisco UC560', 'Sistema unificado para 48 usuarios', 'https://images.unsplash.com/photo-1606904825846-647eb07f5be2?w=500', NULL, 'CSC-UC560-001', 'UC560', 'Cisco', 'disponible', 'excelente', NULL, 'Almacén TI', '2024-01-25', 2850.00, 7, 0.00, NULL, '2025-12-09 01:25:23', '2025-12-09 01:25:23'),
(113, 'TEL-2025-0010', 5, 'Gateway VoIP Grandstream HT814', 'Adaptador analógico 4 puertos FXS', 'https://images.unsplash.com/photo-1606904825846-647eb07f5be2?w=500', NULL, 'GRD-HT814-001', 'HT814', 'Grandstream', 'disponible', 'excelente', NULL, 'Almacén TI', '2024-02-05', 89.00, 7, 0.00, NULL, '2025-12-09 01:25:23', '2025-12-09 01:25:23');

--
-- Triggers `equipos`
--
DROP TRIGGER IF EXISTS `trg_equipos_cambio_estado`;
DELIMITER $$
CREATE TRIGGER `trg_equipos_cambio_estado` AFTER UPDATE ON `equipos` FOR EACH ROW BEGIN
    IF OLD.estado != NEW.estado THEN
        INSERT INTO historial_movimientos (
            equipo_id, 
            tipo_movimiento, 
            estado_anterior, 
            estado_nuevo, 
            observaciones,
            created_at
        ) VALUES (
            NEW.id,
            'cambio_estado',
            OLD.estado,
            NEW.estado,
            CONCAT('Cambio automático de estado de ', OLD.estado, ' a ', NEW.estado),
            NOW()
        );
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `historial_movimientos`
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
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Trazabilidad completa de todos los movimientos de equipos';

--
-- Dumping data for table `historial_movimientos`
--

INSERT INTO `historial_movimientos` (`id`, `equipo_id`, `tipo_movimiento`, `usuario_id`, `colaborador_id`, `estado_anterior`, `estado_nuevo`, `observaciones`, `metadata`, `created_at`) VALUES
(1, 1, 'compra', 1, NULL, NULL, 'disponible', 'Compra inicial del equipo', NULL, '2025-11-30 05:08:39'),
(2, 2, 'compra', 1, NULL, NULL, 'disponible', 'Compra inicial del equipo', NULL, '2025-11-30 05:08:39'),
(3, 4, 'compra', 1, NULL, NULL, 'disponible', 'Compra inicial del equipo', NULL, '2025-11-30 05:08:39'),
(4, 3, 'compra', 1, NULL, NULL, 'disponible', 'Compra inicial del equipo', NULL, '2025-11-30 05:08:39'),
(5, 5, 'compra', 1, NULL, NULL, 'disponible', 'Compra inicial del equipo', NULL, '2025-11-30 05:08:39'),
(8, 14, 'cambio_estado', NULL, NULL, 'disponible', '', 'Cambio automático de estado de disponible a ', NULL, '2025-12-04 05:42:13'),
(9, 13, 'cambio_estado', NULL, NULL, 'disponible', '', 'Cambio automático de estado de disponible a ', NULL, '2025-12-04 12:16:04');

-- --------------------------------------------------------

--
-- Table structure for table `intentos_login`
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Control de intentos de login para prevenir fuerza bruta';

-- --------------------------------------------------------

--
-- Table structure for table `logs_acceso`
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
) ENGINE=InnoDB AUTO_INCREMENT=49 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Registro de auditoría de todos los accesos al sistema';

--
-- Dumping data for table `logs_acceso`
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
(14, 'admin@cmdb.com', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'Local', '757cefdae6f3993029c771b8c7f3e4f7f75222751bcc3c6bc2c4ddcdd7e49533', 1, NULL, '{\"rol\": \"admin\", \"referer\": \"http://localhost/cmdb_web/public/index.php?route=login&action=index\", \"user_id\": 1, \"dispositivo\": \"757cefdae6f3993029c771b8c7f3e4f7f75222751bcc3c6bc2c4ddcdd7e49533\", \"request_uri\": \"/cmdb_web/public/index.php?route=login&action=login\", \"request_method\": \"POST\", \"accept_language\": \"en-US,en;q=0.9,es-US;q=0.8,es;q=0.7\"}', '2025-12-08 16:10:29'),
(15, 'admin@cmdb.com', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'Local', '757cefdae6f3993029c771b8c7f3e4f7f75222751bcc3c6bc2c4ddcdd7e49533', 1, 'logout', '{\"action\": \"logout\", \"referer\": \"http://localhost/cmdb_web/public/index.php?route=reportes&action=inventarioPorCategoria\", \"user_id\": 1, \"request_uri\": \"/cmdb_web/public/index.php?route=logout&action=logout\", \"request_method\": \"GET\", \"accept_language\": \"en-US,en;q=0.9,es-US;q=0.8,es;q=0.7\"}', '2025-12-08 16:11:19'),
(16, 'colaborador@cmdb.com', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'Local', '757cefdae6f3993029c771b8c7f3e4f7f75222751bcc3c6bc2c4ddcdd7e49533', 1, NULL, '{\"rol\": \"colaborador\", \"referer\": \"http://localhost/cmdb_web/public/index.php?route=login&action=index\", \"user_id\": 2, \"dispositivo\": \"757cefdae6f3993029c771b8c7f3e4f7f75222751bcc3c6bc2c4ddcdd7e49533\", \"request_uri\": \"/cmdb_web/public/index.php?route=login&action=login\", \"request_method\": \"POST\", \"accept_language\": \"en-US,en;q=0.9,es-US;q=0.8,es;q=0.7\"}', '2025-12-08 16:11:22'),
(17, 'colaborador@cmdb.com', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'Local', '757cefdae6f3993029c771b8c7f3e4f7f75222751bcc3c6bc2c4ddcdd7e49533', 1, 'logout', '{\"action\": \"logout\", \"referer\": \"http://localhost/cmdb_web/public/index.php?route=reportes\", \"user_id\": 2, \"request_uri\": \"/cmdb_web/public/index.php?route=logout&action=logout\", \"request_method\": \"GET\", \"accept_language\": \"en-US,en;q=0.9,es-US;q=0.8,es;q=0.7\"}', '2025-12-08 16:11:43'),
(18, 'colaborador@cmdb.com', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'Local', '757cefdae6f3993029c771b8c7f3e4f7f75222751bcc3c6bc2c4ddcdd7e49533', 1, NULL, '{\"rol\": \"colaborador\", \"referer\": \"http://localhost/cmdb_web/public/index.php?route=login&action=index\", \"user_id\": 2, \"dispositivo\": \"757cefdae6f3993029c771b8c7f3e4f7f75222751bcc3c6bc2c4ddcdd7e49533\", \"request_uri\": \"/cmdb_web/public/index.php?route=login&action=login\", \"request_method\": \"POST\", \"accept_language\": \"en-US,en;q=0.9,es-US;q=0.8,es;q=0.7\"}', '2025-12-08 16:45:29'),
(19, 'colaborador@cmdb.com', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'Local', '757cefdae6f3993029c771b8c7f3e4f7f75222751bcc3c6bc2c4ddcdd7e49533', 1, 'logout', '{\"action\": \"logout\", \"referer\": \"http://localhost/cmdb_web/public/index.php?route=reportes\", \"user_id\": 2, \"request_uri\": \"/cmdb_web/public/index.php?route=logout&action=logout\", \"request_method\": \"GET\", \"accept_language\": \"en-US,en;q=0.9,es-US;q=0.8,es;q=0.7\"}', '2025-12-08 16:45:42'),
(20, 'colaborador@cmdb.com', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'Local', '757cefdae6f3993029c771b8c7f3e4f7f75222751bcc3c6bc2c4ddcdd7e49533', 1, NULL, '{\"rol\": \"colaborador\", \"referer\": \"http://localhost/cmdb_web/public/index.php?route=login&action=index\", \"user_id\": 2, \"dispositivo\": \"757cefdae6f3993029c771b8c7f3e4f7f75222751bcc3c6bc2c4ddcdd7e49533\", \"request_uri\": \"/cmdb_web/public/index.php?route=login&action=login\", \"request_method\": \"POST\", \"accept_language\": \"en-US,en;q=0.9,es-US;q=0.8,es;q=0.7\"}', '2025-12-08 17:08:55'),
(21, 'colaborador@cmdb.com', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'Local', '757cefdae6f3993029c771b8c7f3e4f7f75222751bcc3c6bc2c4ddcdd7e49533', 1, 'logout', '{\"action\": \"logout\", \"referer\": \"http://localhost/cmdb_web/public/index.php?route=reportes\", \"user_id\": 2, \"request_uri\": \"/cmdb_web/public/index.php?route=logout&action=logout\", \"request_method\": \"GET\", \"accept_language\": \"en-US,en;q=0.9,es-US;q=0.8,es;q=0.7\"}', '2025-12-08 17:09:12'),
(22, 'admin@cmdb.com', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'Local', '757cefdae6f3993029c771b8c7f3e4f7f75222751bcc3c6bc2c4ddcdd7e49533', 1, NULL, '{\"rol\": \"admin\", \"referer\": \"http://localhost/cmdb_web/public/index.php?route=login&action=index\", \"user_id\": 1, \"dispositivo\": \"757cefdae6f3993029c771b8c7f3e4f7f75222751bcc3c6bc2c4ddcdd7e49533\", \"request_uri\": \"/cmdb_web/public/index.php?route=login&action=login\", \"request_method\": \"POST\", \"accept_language\": \"en-US,en;q=0.9,es-US;q=0.8,es;q=0.7\"}', '2025-12-08 17:09:16'),
(23, 'admin@cmdb.com', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'Local', '757cefdae6f3993029c771b8c7f3e4f7f75222751bcc3c6bc2c4ddcdd7e49533', 1, 'logout', '{\"action\": \"logout\", \"referer\": \"http://localhost/cmdb_web/public/index.php?route=colaboradores\", \"user_id\": 1, \"request_uri\": \"/cmdb_web/public/index.php?route=logout&action=logout\", \"request_method\": \"GET\", \"accept_language\": \"en-US,en;q=0.9,es-US;q=0.8,es;q=0.7\"}', '2025-12-08 21:04:21'),
(24, 'admin@cmdb.com', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'Local', '757cefdae6f3993029c771b8c7f3e4f7f75222751bcc3c6bc2c4ddcdd7e49533', 1, NULL, '{\"rol\": \"admin\", \"referer\": \"http://localhost/cmdb_web/public/index.php?route=login&action=index\", \"user_id\": 1, \"dispositivo\": \"757cefdae6f3993029c771b8c7f3e4f7f75222751bcc3c6bc2c4ddcdd7e49533\", \"request_uri\": \"/cmdb_web/public/index.php?route=login&action=login\", \"request_method\": \"POST\", \"accept_language\": \"en-US,en;q=0.9,es-US;q=0.8,es;q=0.7\"}', '2025-12-08 21:04:27'),
(25, 'admin@cmdb.com', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'Local', '757cefdae6f3993029c771b8c7f3e4f7f75222751bcc3c6bc2c4ddcdd7e49533', 1, 'logout', '{\"action\": \"logout\", \"referer\": \"http://localhost/cmdb_web/public/index.php?route=reportes\", \"user_id\": 1, \"request_uri\": \"/cmdb_web/public/index.php?route=logout&action=logout\", \"request_method\": \"GET\", \"accept_language\": \"en-US,en;q=0.9,es-US;q=0.8,es;q=0.7\"}', '2025-12-09 00:04:40'),
(26, 'colaborador@cmdb.com', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'Local', '757cefdae6f3993029c771b8c7f3e4f7f75222751bcc3c6bc2c4ddcdd7e49533', 1, NULL, '{\"rol\": \"colaborador\", \"referer\": \"http://localhost/cmdb_web/public/index.php?route=login&action=index\", \"user_id\": 2, \"dispositivo\": \"757cefdae6f3993029c771b8c7f3e4f7f75222751bcc3c6bc2c4ddcdd7e49533\", \"request_uri\": \"/cmdb_web/public/index.php?route=login&action=login\", \"request_method\": \"POST\", \"accept_language\": \"en-US,en;q=0.9,es-US;q=0.8,es;q=0.7\"}', '2025-12-09 00:04:47'),
(27, 'colaborador@cmdb.com', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'Local', '757cefdae6f3993029c771b8c7f3e4f7f75222751bcc3c6bc2c4ddcdd7e49533', 1, 'logout', '{\"action\": \"logout\", \"referer\": \"http://localhost/cmdb_web/public/index.php?route=colaboradores\", \"user_id\": 2, \"request_uri\": \"/cmdb_web/public/index.php?route=logout&action=logout\", \"request_method\": \"GET\", \"accept_language\": \"en-US,en;q=0.9,es-US;q=0.8,es;q=0.7\"}', '2025-12-09 00:06:18'),
(28, 'colaborador@cmdb.com', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'Local', '757cefdae6f3993029c771b8c7f3e4f7f75222751bcc3c6bc2c4ddcdd7e49533', 1, NULL, '{\"rol\": \"colaborador\", \"referer\": \"http://localhost/cmdb_web/public/index.php?route=login&action=index\", \"user_id\": 2, \"dispositivo\": \"757cefdae6f3993029c771b8c7f3e4f7f75222751bcc3c6bc2c4ddcdd7e49533\", \"request_uri\": \"/cmdb_web/public/index.php?route=login&action=login\", \"request_method\": \"POST\", \"accept_language\": \"en-US,en;q=0.9,es-US;q=0.8,es;q=0.7\"}', '2025-12-09 00:06:30'),
(29, 'colaborador@cmdb.com', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'Local', '757cefdae6f3993029c771b8c7f3e4f7f75222751bcc3c6bc2c4ddcdd7e49533', 1, 'logout', '{\"action\": \"logout\", \"referer\": \"http://localhost/cmdb_web/public/index.php?route=equipos\", \"user_id\": 2, \"request_uri\": \"/cmdb_web/public/index.php?route=logout&action=logout\", \"request_method\": \"GET\", \"accept_language\": \"en-US,en;q=0.9,es-US;q=0.8,es;q=0.7\"}', '2025-12-09 00:13:27'),
(30, 'admin@cmdb.com', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'Local', '757cefdae6f3993029c771b8c7f3e4f7f75222751bcc3c6bc2c4ddcdd7e49533', 1, NULL, '{\"rol\": \"admin\", \"referer\": \"http://localhost/cmdb_web/public/index.php?route=login&action=index\", \"user_id\": 1, \"dispositivo\": \"757cefdae6f3993029c771b8c7f3e4f7f75222751bcc3c6bc2c4ddcdd7e49533\", \"request_uri\": \"/cmdb_web/public/index.php?route=login&action=login\", \"request_method\": \"POST\", \"accept_language\": \"en-US,en;q=0.9,es-US;q=0.8,es;q=0.7\"}', '2025-12-09 00:13:34'),
(31, 'admin@cmdb.com', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'Local', '757cefdae6f3993029c771b8c7f3e4f7f75222751bcc3c6bc2c4ddcdd7e49533', 1, 'logout', '{\"action\": \"logout\", \"referer\": \"http://localhost/cmdb_web/public/index.php?route=usuarios\", \"user_id\": 1, \"request_uri\": \"/cmdb_web/public/index.php?route=logout&action=logout\", \"request_method\": \"GET\", \"accept_language\": \"en-US,en;q=0.9,es-US;q=0.8,es;q=0.7\"}', '2025-12-09 00:15:14'),
(32, 'admin@cmdb.com', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'Local', '757cefdae6f3993029c771b8c7f3e4f7f75222751bcc3c6bc2c4ddcdd7e49533', 1, NULL, '{\"rol\": \"admin\", \"referer\": \"http://localhost/cmdb_web/public/index.php?route=login&action=index\", \"user_id\": 1, \"dispositivo\": \"757cefdae6f3993029c771b8c7f3e4f7f75222751bcc3c6bc2c4ddcdd7e49533\", \"request_uri\": \"/cmdb_web/public/index.php?route=login&action=login\", \"request_method\": \"POST\", \"accept_language\": \"en-US,en;q=0.9,es-US;q=0.8,es;q=0.7\"}', '2025-12-09 00:40:30'),
(33, 'admin@cmdb.com', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'Local', '757cefdae6f3993029c771b8c7f3e4f7f75222751bcc3c6bc2c4ddcdd7e49533', 1, 'logout', '{\"action\": \"logout\", \"referer\": \"http://localhost/cmdb_web/public/index.php?route=asignaciones\", \"user_id\": 1, \"request_uri\": \"/cmdb_web/public/index.php?route=logout&action=logout\", \"request_method\": \"GET\", \"accept_language\": \"en-US,en;q=0.9,es-US;q=0.8,es;q=0.7\"}', '2025-12-09 00:40:57'),
(34, 'colaborador@cmdb.com', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'Local', '757cefdae6f3993029c771b8c7f3e4f7f75222751bcc3c6bc2c4ddcdd7e49533', 1, NULL, '{\"rol\": \"colaborador\", \"referer\": \"http://localhost/cmdb_web/public/index.php?route=login&action=index\", \"user_id\": 2, \"dispositivo\": \"757cefdae6f3993029c771b8c7f3e4f7f75222751bcc3c6bc2c4ddcdd7e49533\", \"request_uri\": \"/cmdb_web/public/index.php?route=login&action=login\", \"request_method\": \"POST\", \"accept_language\": \"en-US,en;q=0.9,es-US;q=0.8,es;q=0.7\"}', '2025-12-09 00:42:24'),
(35, 'colaborador@cmdb.com', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'Local', '757cefdae6f3993029c771b8c7f3e4f7f75222751bcc3c6bc2c4ddcdd7e49533', 1, 'logout', '{\"action\": \"logout\", \"referer\": \"http://localhost/cmdb_web/public/\", \"user_id\": 2, \"request_uri\": \"/cmdb_web/public/index.php?route=logout&action=logout\", \"request_method\": \"GET\", \"accept_language\": \"en-US,en;q=0.9,es-US;q=0.8,es;q=0.7\"}', '2025-12-09 00:42:59'),
(36, 'colaborador@cmdb.com', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'Local', '757cefdae6f3993029c771b8c7f3e4f7f75222751bcc3c6bc2c4ddcdd7e49533', 1, NULL, '{\"rol\": \"colaborador\", \"referer\": \"http://localhost/cmdb_web/public/index.php?route=login&action=index\", \"user_id\": 2, \"dispositivo\": \"757cefdae6f3993029c771b8c7f3e4f7f75222751bcc3c6bc2c4ddcdd7e49533\", \"request_uri\": \"/cmdb_web/public/index.php?route=login&action=login\", \"request_method\": \"POST\", \"accept_language\": \"en-US,en;q=0.9,es-US;q=0.8,es;q=0.7\"}', '2025-12-09 00:43:02'),
(37, 'colaborador@cmdb.com', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'Local', '757cefdae6f3993029c771b8c7f3e4f7f75222751bcc3c6bc2c4ddcdd7e49533', 1, 'logout', '{\"action\": \"logout\", \"referer\": \"http://localhost/cmdb_web/public/index.php?route=dashboard&action=index\", \"user_id\": 2, \"request_uri\": \"/cmdb_web/public/index.php?route=logout&action=logout\", \"request_method\": \"GET\", \"accept_language\": \"en-US,en;q=0.9,es-US;q=0.8,es;q=0.7\"}', '2025-12-09 00:43:14'),
(38, 'admin@cmdb.com', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'Local', '757cefdae6f3993029c771b8c7f3e4f7f75222751bcc3c6bc2c4ddcdd7e49533', 1, NULL, '{\"rol\": \"admin\", \"referer\": \"http://localhost/cmdb_web/public/index.php?route=login&action=index\", \"user_id\": 1, \"dispositivo\": \"757cefdae6f3993029c771b8c7f3e4f7f75222751bcc3c6bc2c4ddcdd7e49533\", \"request_uri\": \"/cmdb_web/public/index.php?route=login&action=login\", \"request_method\": \"POST\", \"accept_language\": \"en-US,en;q=0.9,es-US;q=0.8,es;q=0.7\"}', '2025-12-09 00:43:16'),
(39, 'admin@cmdb.com', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'Local', '757cefdae6f3993029c771b8c7f3e4f7f75222751bcc3c6bc2c4ddcdd7e49533', 1, 'logout', '{\"action\": \"logout\", \"referer\": \"http://localhost/cmdb_web/public/index.php?route=equipos&action=edit&id=11\", \"user_id\": 1, \"request_uri\": \"/cmdb_web/public/index.php?route=logout&action=logout\", \"request_method\": \"GET\", \"accept_language\": \"en-US,en;q=0.9,es-US;q=0.8,es;q=0.7\"}', '2025-12-09 00:44:01'),
(40, 'colaborador@cmdb.com', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'Local', '757cefdae6f3993029c771b8c7f3e4f7f75222751bcc3c6bc2c4ddcdd7e49533', 1, NULL, '{\"rol\": \"colaborador\", \"referer\": \"http://localhost/cmdb_web/public/index.php?route=login&action=index\", \"user_id\": 2, \"dispositivo\": \"757cefdae6f3993029c771b8c7f3e4f7f75222751bcc3c6bc2c4ddcdd7e49533\", \"request_uri\": \"/cmdb_web/public/index.php?route=login&action=login\", \"request_method\": \"POST\", \"accept_language\": \"en-US,en;q=0.9,es-US;q=0.8,es;q=0.7\"}', '2025-12-09 00:44:09'),
(41, 'colaborador@cmdb.com', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'Local', '757cefdae6f3993029c771b8c7f3e4f7f75222751bcc3c6bc2c4ddcdd7e49533', 1, 'logout', '{\"action\": \"logout\", \"referer\": \"http://localhost/cmdb_web/public/index.php?route=asignaciones\", \"user_id\": 2, \"request_uri\": \"/cmdb_web/public/index.php?route=logout&action=logout\", \"request_method\": \"GET\", \"accept_language\": \"en-US,en;q=0.9,es-US;q=0.8,es;q=0.7\"}', '2025-12-09 00:44:19'),
(42, 'admin@cmdb.com', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'Local', '757cefdae6f3993029c771b8c7f3e4f7f75222751bcc3c6bc2c4ddcdd7e49533', 1, NULL, '{\"rol\": \"admin\", \"referer\": \"http://localhost/cmdb_web/public/index.php?route=login&action=index\", \"user_id\": 1, \"dispositivo\": \"757cefdae6f3993029c771b8c7f3e4f7f75222751bcc3c6bc2c4ddcdd7e49533\", \"request_uri\": \"/cmdb_web/public/index.php?route=login&action=login\", \"request_method\": \"POST\", \"accept_language\": \"en-US,en;q=0.9,es-US;q=0.8,es;q=0.7\"}', '2025-12-09 00:44:23'),
(43, 'admin@cmdb.com', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'Local', '757cefdae6f3993029c771b8c7f3e4f7f75222751bcc3c6bc2c4ddcdd7e49533', 1, 'logout', '{\"action\": \"logout\", \"referer\": \"http://localhost/cmdb_web/public/index.php?route=equipos\", \"user_id\": 1, \"request_uri\": \"/cmdb_web/public/index.php?route=logout&action=logout\", \"request_method\": \"GET\", \"accept_language\": \"en-US,en;q=0.9,es-US;q=0.8,es;q=0.7\"}', '2025-12-09 01:03:08'),
(44, 'colaborador@cmdb.com', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'Local', '757cefdae6f3993029c771b8c7f3e4f7f75222751bcc3c6bc2c4ddcdd7e49533', 1, NULL, '{\"rol\": \"colaborador\", \"referer\": \"http://localhost/cmdb_web/public/index.php?route=login&action=index\", \"user_id\": 2, \"dispositivo\": \"757cefdae6f3993029c771b8c7f3e4f7f75222751bcc3c6bc2c4ddcdd7e49533\", \"request_uri\": \"/cmdb_web/public/index.php?route=login&action=login\", \"request_method\": \"POST\", \"accept_language\": \"en-US,en;q=0.9,es-US;q=0.8,es;q=0.7\"}', '2025-12-09 01:03:45'),
(45, 'colaborador@cmdb.com', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'Local', '757cefdae6f3993029c771b8c7f3e4f7f75222751bcc3c6bc2c4ddcdd7e49533', 1, 'logout', '{\"action\": \"logout\", \"referer\": \"http://localhost/cmdb_web/public/index.php?route=dashboard&action=index\", \"user_id\": 2, \"request_uri\": \"/cmdb_web/public/index.php?route=logout&action=logout\", \"request_method\": \"GET\", \"accept_language\": \"en-US,en;q=0.9,es-US;q=0.8,es;q=0.7\"}', '2025-12-09 01:03:50'),
(46, 'colaborador@cmdb.com', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'Local', '757cefdae6f3993029c771b8c7f3e4f7f75222751bcc3c6bc2c4ddcdd7e49533', 1, NULL, '{\"rol\": \"colaborador\", \"referer\": \"http://localhost/cmdb_web/public/index.php?route=login&action=index\", \"user_id\": 2, \"dispositivo\": \"757cefdae6f3993029c771b8c7f3e4f7f75222751bcc3c6bc2c4ddcdd7e49533\", \"request_uri\": \"/cmdb_web/public/index.php?route=login&action=login\", \"request_method\": \"POST\", \"accept_language\": \"en-US,en;q=0.9,es-US;q=0.8,es;q=0.7\"}', '2025-12-09 01:03:56'),
(47, 'colaborador@cmdb.com', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'Local', '757cefdae6f3993029c771b8c7f3e4f7f75222751bcc3c6bc2c4ddcdd7e49533', 1, 'logout', '{\"action\": \"logout\", \"referer\": \"http://localhost/cmdb_web/public/index.php?route=donaciones\", \"user_id\": 2, \"request_uri\": \"/cmdb_web/public/index.php?route=logout&action=logout\", \"request_method\": \"GET\", \"accept_language\": \"en-US,en;q=0.9,es-US;q=0.8,es;q=0.7\"}', '2025-12-09 01:08:48'),
(48, 'admin@cmdb.com', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'Local', '757cefdae6f3993029c771b8c7f3e4f7f75222751bcc3c6bc2c4ddcdd7e49533', 1, NULL, '{\"rol\": \"admin\", \"referer\": \"http://localhost/cmdb_web/public/index.php?route=login&action=index\", \"user_id\": 1, \"dispositivo\": \"757cefdae6f3993029c771b8c7f3e4f7f75222751bcc3c6bc2c4ddcdd7e49533\", \"request_uri\": \"/cmdb_web/public/index.php?route=login&action=login\", \"request_method\": \"POST\", \"accept_language\": \"en-US,en;q=0.9,es-US;q=0.8,es;q=0.7\"}', '2025-12-09 01:08:55');

-- --------------------------------------------------------

--
-- Table structure for table `usuarios`
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
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `usuarios`
--

INSERT INTO `usuarios` (`id`, `nombre`, `apellido`, `email`, `password`, `rol`, `estado`, `foto_perfil`, `created_at`, `updated_at`) VALUES
(1, 'Admin', 'Sistema', 'admin@cmdb.com', '$2y$12$aG9QDC/sgwzKULAVzazsGulYqHazTGxHMm0mviuFbSlnPoFi.6g.i', 'admin', 'activo', NULL, '2025-11-30 04:57:53', '2025-11-30 04:57:53'),
(2, 'Juan', 'Pérez', 'colaborador@cmdb.com', '$2y$12$s2oP1y.OLpNAxQWZr60mU.HuHRX6Rg2KVP8K61XBojNJ96cP5qqZ2', 'colaborador', 'activo', NULL, '2025-11-30 04:57:53', '2025-11-30 04:57:53');

-- --------------------------------------------------------

--
-- Table structure for table `v_accesos_diarios`
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
-- Table structure for table `v_accesos_recientes`
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
-- Table structure for table `v_equipos_por_colaborador`
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
-- Table structure for table `v_equipos_proximos_baja`
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
-- Stand-in structure for view `v_inventario_completo`
-- (See below for the actual view)
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
-- Stand-in structure for view `v_ips_sospechosas`
-- (See below for the actual view)
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
-- Stand-in structure for view `v_reporte_depreciacion`
-- (See below for the actual view)
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
-- Structure for view `v_inventario_completo`
--
DROP TABLE IF EXISTS `v_inventario_completo`;

DROP VIEW IF EXISTS `v_inventario_completo`;
CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_inventario_completo`  AS SELECT `e`.`id` AS `id`, `e`.`codigo_inventario` AS `codigo_inventario`, coalesce(nullif(`e`.`nombre`,''),concat(`e`.`marca`,' ',`e`.`modelo`)) AS `nombre`, `e`.`numero_serie` AS `numero_serie`, `e`.`marca` AS `marca`, `e`.`modelo` AS `modelo`, `e`.`descripcion` AS `descripcion`, `c`.`nombre` AS `categoria`, `e`.`estado` AS `estado`, `e`.`condicion` AS `condicion`, `e`.`ubicacion` AS `ubicacion`, `e`.`fecha_adquisicion` AS `fecha_adquisicion`, `e`.`costo_adquisicion` AS `costo_adquisicion`, `e`.`vida_util_anos` AS `vida_util_anos`, `e`.`valor_residual` AS `valor_residual`, (case when (`e`.`estado` = 'asignado') then (select concat(`col`.`nombre`,' ',`col`.`apellido`) from (`asignaciones` `a` left join `colaboradores` `col` on((`a`.`colaborador_id` = `col`.`id`))) where ((`a`.`equipo_id` = `e`.`id`) and (`a`.`estado` = 'activa')) order by `a`.`fecha_asignacion` desc limit 1) else NULL end) AS `asignado_a`, timestampdiff(MONTH,`e`.`fecha_adquisicion`,curdate()) AS `meses_uso`, round(((`e`.`costo_adquisicion` - coalesce(`e`.`valor_residual`,0)) / (`e`.`vida_util_anos` * 12)),2) AS `depreciacion_mensual`, round(least((((`e`.`costo_adquisicion` - coalesce(`e`.`valor_residual`,0)) / (`e`.`vida_util_anos` * 12)) * timestampdiff(MONTH,`e`.`fecha_adquisicion`,curdate())),(`e`.`costo_adquisicion` - coalesce(`e`.`valor_residual`,0))),2) AS `depreciacion_acumulada`, round(greatest((`e`.`costo_adquisicion` - (((`e`.`costo_adquisicion` - coalesce(`e`.`valor_residual`,0)) / (`e`.`vida_util_anos` * 12)) * timestampdiff(MONTH,`e`.`fecha_adquisicion`,curdate()))),coalesce(`e`.`valor_residual`,0)),2) AS `valor_actual`, `e`.`created_at` AS `created_at`, `e`.`updated_at` AS `updated_at` FROM (`equipos` `e` left join `categorias` `c` on((`e`.`categoria_id` = `c`.`id`))) WHERE (`e`.`estado` not in ('dado_de_baja','donado')) ORDER BY `e`.`id` DESC ;

-- --------------------------------------------------------

--
-- Structure for view `v_ips_sospechosas`
--
DROP TABLE IF EXISTS `v_ips_sospechosas`;

DROP VIEW IF EXISTS `v_ips_sospechosas`;
CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_ips_sospechosas`  AS SELECT `logs_acceso`.`ip_address` AS `ip_address`, count(0) AS `intentos_fallidos`, min(`logs_acceso`.`created_at`) AS `primer_intento`, max(`logs_acceso`.`created_at`) AS `ultimo_intento`, group_concat(distinct `logs_acceso`.`usuario` order by `logs_acceso`.`created_at` DESC separator ', ') AS `usuarios_intentados` FROM `logs_acceso` WHERE ((`logs_acceso`.`exitoso` = 0) AND (`logs_acceso`.`created_at` > (now() - interval 24 hour))) GROUP BY `logs_acceso`.`ip_address` HAVING (`intentos_fallidos` >= 3) ORDER BY `intentos_fallidos` DESC ;

-- --------------------------------------------------------

--
-- Structure for view `v_reporte_depreciacion`
--
DROP TABLE IF EXISTS `v_reporte_depreciacion`;

DROP VIEW IF EXISTS `v_reporte_depreciacion`;
CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_reporte_depreciacion`  AS SELECT `e`.`id` AS `id_equipo`, `e`.`codigo_inventario` AS `codigo_inventario`, `e`.`nombre` AS `nombre_equipo`, `e`.`descripcion` AS `descripcion`, `e`.`categoria_id` AS `categoria_id`, `c`.`nombre` AS `categoria`, `e`.`fecha_adquisicion` AS `fecha_adquisicion`, `e`.`costo_adquisicion` AS `costo_adquisicion`, `e`.`vida_util_anos` AS `vida_util_anos`, timestampdiff(YEAR,`e`.`fecha_adquisicion`,curdate()) AS `anos_uso`, (`e`.`costo_adquisicion` / `e`.`vida_util_anos`) AS `depreciacion_anual`, (`e`.`costo_adquisicion` / (`e`.`vida_util_anos` * 12)) AS `depreciacion_mensual`, round(((`e`.`costo_adquisicion` / `e`.`vida_util_anos`) * timestampdiff(YEAR,`e`.`fecha_adquisicion`,curdate())),2) AS `depreciacion_acumulada`, round((`e`.`costo_adquisicion` - ((`e`.`costo_adquisicion` / `e`.`vida_util_anos`) * timestampdiff(YEAR,`e`.`fecha_adquisicion`,curdate()))),2) AS `valor_libro`, round(((((`e`.`costo_adquisicion` / `e`.`vida_util_anos`) * timestampdiff(YEAR,`e`.`fecha_adquisicion`,curdate())) / `e`.`costo_adquisicion`) * 100),2) AS `porcentaje_depreciado` FROM (`equipos` `e` left join `categorias` `c` on((`c`.`id` = `e`.`categoria_id`))) ;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
