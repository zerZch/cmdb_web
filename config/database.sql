-- Script de creación de base de datos CMDB v2
-- Ejecutar este script en MySQL/phpMyAdmin

CREATE DATABASE IF NOT EXISTS cmdb_v2_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE cmdb_v2_db;

-- =====================================================
-- Tabla de Usuarios del Sistema
-- =====================================================
CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    apellido VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    rol ENUM('admin', 'colaborador') NOT NULL DEFAULT 'colaborador',
    estado ENUM('activo', 'inactivo') NOT NULL DEFAULT 'activo',
    foto_perfil VARCHAR(255) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_email (email),
    INDEX idx_rol (rol),
    INDEX idx_estado (estado)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- Tabla de Categorías
-- =====================================================
CREATE TABLE categorias (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL UNIQUE,
    descripcion TEXT NULL,
    estado ENUM('activa', 'inactiva') NOT NULL DEFAULT 'activa',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_nombre (nombre),
    INDEX idx_estado (estado)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- Tabla de Equipos (Inventario)
-- =====================================================
CREATE TABLE equipos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    categoria_id INT NOT NULL,
    nombre VARCHAR(150) NOT NULL,
    descripcion TEXT NULL,
    numero_serie VARCHAR(100) NULL UNIQUE,
    modelo VARCHAR(100) NULL,
    marca VARCHAR(100) NULL,
    estado ENUM('disponible', 'asignado', 'dañado', 'mantenimiento') NOT NULL DEFAULT 'disponible',
    ubicacion VARCHAR(200) NULL,
    fecha_adquisicion DATE NULL,
    valor_adquisicion DECIMAL(10,2) NULL,
    observaciones TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (categoria_id) REFERENCES categorias(id) ON DELETE RESTRICT,
    INDEX idx_categoria (categoria_id),
    INDEX idx_estado (estado),
    INDEX idx_numero_serie (numero_serie)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- Tabla de Asignaciones de Equipos
-- =====================================================
CREATE TABLE asignaciones (
    id INT AUTO_INCREMENT PRIMARY KEY,
    equipo_id INT NOT NULL,
    usuario_id INT NOT NULL,
    fecha_asignacion DATETIME NOT NULL,
    fecha_devolucion DATETIME NULL,
    estado ENUM('activa', 'devuelta') NOT NULL DEFAULT 'activa',
    observaciones TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (equipo_id) REFERENCES equipos(id) ON DELETE RESTRICT,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE RESTRICT,
    INDEX idx_equipo (equipo_id),
    INDEX idx_usuario (usuario_id),
    INDEX idx_estado (estado)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- Datos de Prueba
-- =====================================================

-- Usuario Administrador por defecto
-- Email: admin@cmdb.com
-- Password: admin123
INSERT INTO usuarios (nombre, apellido, email, password, rol, estado) VALUES
('Admin', 'Sistema', 'admin@cmdb.com', '$2y$12$aG9QDC/sgwzKULAVzazsGulYqHazTGxHMm0mviuFbSlnPoFi.6g.i', 'admin', 'activo');

-- Categorías de ejemplo
INSERT INTO categorias (nombre, descripcion, estado) VALUES
('Computadoras', 'Equipos de cómputo (PCs, Laptops)', 'activa'),
('Impresoras', 'Equipos de impresión', 'activa'),
('Servidores', 'Servidores y equipos de red', 'activa'),
('Monitores', 'Pantallas y monitores', 'activa'),
('Periféricos', 'Teclados, ratones y otros accesorios', 'activa');

-- Equipos de ejemplo
INSERT INTO equipos (categoria_id, nombre, descripcion, numero_serie, modelo, marca, estado, ubicacion) VALUES
(1, 'Laptop Dell XPS 15', 'Laptop de alto rendimiento', 'DELL-001', 'XPS 15', 'Dell', 'disponible', 'Almacén TI'),
(1, 'Desktop HP ProDesk', 'Computadora de escritorio', 'HP-001', 'ProDesk 400', 'HP', 'disponible', 'Oficina 101'),
(2, 'Impresora HP LaserJet', 'Impresora láser B/N', 'HP-PRN-001', 'LaserJet Pro M404', 'HP', 'asignado', 'Oficina Principal'),
(3, 'Servidor Dell PowerEdge', 'Servidor rack', 'DELL-SRV-001', 'PowerEdge R740', 'Dell', 'disponible', 'Sala de Servidores'),
(4, 'Monitor LG 27"', 'Monitor 4K', 'LG-MON-001', '27UK850-W', 'LG', 'dañado', 'Taller Reparación');

-- Usuario colaborador de ejemplo
-- Email: colaborador@cmdb.com
-- Password: colab123
INSERT INTO usuarios (nombre, apellido, email, password, rol, estado) VALUES
('Juan', 'Pérez', 'colaborador@cmdb.com', '$2y$12$s2oP1y.OLpNAxQWZr60mU.HuHRX6Rg2KVP8K61XBojNJ96cP5qqZ2', 'colaborador', 'activo');

-- =====================================================
-- TABLAS DE SEGURIDAD Y AUDITORÍA
-- =====================================================

-- Tabla de Logs de Acceso (Auditoría completa)
-- Cumple con requisitos de Ley 81 de Panamá
CREATE TABLE logs_acceso (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    usuario VARCHAR(255) NULL COMMENT 'Email o username del usuario',
    ip_address VARCHAR(45) NOT NULL COMMENT 'IPv4 o IPv6',
    user_agent TEXT NULL COMMENT 'Información del navegador/dispositivo',
    pais VARCHAR(10) NULL COMMENT 'Código ISO del país',
    fingerprint VARCHAR(64) NULL COMMENT 'Huella digital del dispositivo',
    exitoso BOOLEAN NOT NULL DEFAULT 0 COMMENT '1=exitoso, 0=fallido',
    motivo VARCHAR(255) NULL COMMENT 'Razón del fallo o acción',
    metadata JSON NULL COMMENT 'Datos adicionales en formato JSON',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_usuario (usuario),
    INDEX idx_ip (ip_address),
    INDEX idx_exitoso (exitoso),
    INDEX idx_created (created_at),
    INDEX idx_fingerprint (fingerprint)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='Registro de auditoría de todos los accesos al sistema';

-- Tabla de Intentos de Login (Rate Limiting)
-- Protección contra ataques de fuerza bruta
CREATE TABLE intentos_login (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    identifier VARCHAR(255) NOT NULL COMMENT 'Email o IP',
    type ENUM('email', 'ip') NOT NULL DEFAULT 'email' COMMENT 'Tipo de identificador',
    ip_address VARCHAR(45) NOT NULL COMMENT 'IP del intento',
    user_agent TEXT NULL COMMENT 'User agent del cliente',
    metadata JSON NULL COMMENT 'Información adicional',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_identifier (identifier, type),
    INDEX idx_created (created_at),
    INDEX idx_ip (ip_address)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='Control de intentos de login para prevenir fuerza bruta';

-- =====================================================
-- VISTAS ÚTILES PARA REPORTES DE SEGURIDAD
-- =====================================================

-- Vista: Resumen de accesos por día
CREATE OR REPLACE VIEW v_accesos_diarios AS
SELECT
    DATE(created_at) as fecha,
    COUNT(*) as total_accesos,
    SUM(CASE WHEN exitoso = 1 THEN 1 ELSE 0 END) as exitosos,
    SUM(CASE WHEN exitoso = 0 THEN 1 ELSE 0 END) as fallidos,
    COUNT(DISTINCT usuario) as usuarios_unicos,
    COUNT(DISTINCT ip_address) as ips_unicas
FROM logs_acceso
GROUP BY DATE(created_at)
ORDER BY fecha DESC;

-- Vista: IPs sospechosas (múltiples fallos)
CREATE OR REPLACE VIEW v_ips_sospechosas AS
SELECT
    ip_address,
    COUNT(*) as intentos_fallidos,
    MIN(created_at) as primer_intento,
    MAX(created_at) as ultimo_intento,
    GROUP_CONCAT(DISTINCT usuario ORDER BY created_at DESC SEPARATOR ', ') as usuarios_intentados
FROM logs_acceso
WHERE exitoso = 0
AND created_at > DATE_SUB(NOW(), INTERVAL 24 HOUR)
GROUP BY ip_address
HAVING intentos_fallidos >= 3
ORDER BY intentos_fallidos DESC;

-- Vista: Accesos recientes con detalles
CREATE OR REPLACE VIEW v_accesos_recientes AS
SELECT
    l.id,
    l.usuario,
    l.ip_address,
    l.pais,
    CASE WHEN l.exitoso = 1 THEN 'Exitoso' ELSE 'Fallido' END as resultado,
    l.motivo,
    l.created_at,
    SUBSTRING(l.user_agent, 1, 100) as navegador
FROM logs_acceso l
WHERE l.created_at > DATE_SUB(NOW(), INTERVAL 7 DAY)
ORDER BY l.created_at DESC
LIMIT 100;
