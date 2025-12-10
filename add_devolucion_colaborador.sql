-- ============================================================
-- SCRIPT: Sistema de Devolución de Equipos por Colaboradores
-- ============================================================
-- Permite a colaboradores solicitar devolución de equipos
-- Los admins validan la devolución y los equipos pasan a revisión
-- ============================================================

-- 1. Modificar tabla asignaciones para agregar campos de devolución
ALTER TABLE `asignaciones`
ADD COLUMN `motivo_devolucion` ENUM('traslado', 'salida', 'mal_estado', 'fin_proyecto', 'otro') DEFAULT NULL
    COMMENT 'Motivo por el cual se devuelve el equipo' AFTER `observaciones`,
ADD COLUMN `fecha_solicitud_devolucion` DATETIME DEFAULT NULL
    COMMENT 'Fecha en que el colaborador solicita la devolución' AFTER `motivo_devolucion`,
ADD COLUMN `usuario_validador_id` INT DEFAULT NULL
    COMMENT 'Usuario del sistema que valida/recibe el equipo' AFTER `fecha_solicitud_devolucion`,
ADD COLUMN `estado_solicitud` ENUM('sin_solicitud', 'solicitada', 'validada', 'rechazada') DEFAULT 'sin_solicitud'
    COMMENT 'Estado de la solicitud de devolución' AFTER `usuario_validador_id`,
ADD COLUMN `observaciones_validacion` TEXT DEFAULT NULL
    COMMENT 'Observaciones del admin al validar' AFTER `estado_solicitud`;

-- 2. Agregar índice para búsquedas rápidas de solicitudes pendientes
ALTER TABLE `asignaciones`
ADD INDEX `idx_estado_solicitud` (`estado_solicitud`),
ADD INDEX `idx_usuario_validador` (`usuario_validador_id`);

-- 3. Agregar foreign key para usuario validador
ALTER TABLE `asignaciones`
ADD CONSTRAINT `fk_asignaciones_validador`
    FOREIGN KEY (`usuario_validador_id`)
    REFERENCES `usuarios` (`id`)
    ON DELETE SET NULL;

-- 4. Verificar la estructura actualizada
DESCRIBE asignaciones;

-- 5. Consulta de ejemplo para ver solicitudes pendientes
SELECT
    a.id,
    a.fecha_asignacion,
    a.fecha_solicitud_devolucion,
    a.motivo_devolucion,
    a.estado_solicitud,
    e.nombre AS equipo,
    e.numero_serie,
    CONCAT(c.nombre, ' ', c.apellido) AS colaborador,
    c.departamento
FROM asignaciones a
INNER JOIN equipos e ON a.equipo_id = e.id
INNER JOIN colaboradores c ON a.colaborador_id = c.id
WHERE a.estado = 'activa'
  AND a.estado_solicitud = 'solicitada'
ORDER BY a.fecha_solicitud_devolucion ASC;

-- 6. Consulta para ver equipos en revisión
SELECT
    e.id,
    e.nombre,
    e.numero_serie,
    e.marca,
    e.modelo,
    e.estado,
    e.updated_at AS fecha_cambio_estado
FROM equipos e
WHERE e.estado = 'en_revision'
ORDER BY e.updated_at DESC;
