-- ============================================================
-- SCRIPT: Corregir Vista de Equipos por Colaborador
-- ============================================================
-- Problema: La vista v_equipos_por_colaborador no existe o está mal definida
-- Solución: Recrear la vista con la estructura correcta
-- ============================================================

-- 1. Eliminar la tabla/vista si existe
DROP TABLE IF EXISTS `v_equipos_por_colaborador`;
DROP VIEW IF EXISTS `v_equipos_por_colaborador`;

-- 2. Crear la vista correctamente
CREATE VIEW `v_equipos_por_colaborador` AS
SELECT
    c.id AS colaborador_id,
    CONCAT(c.nombre, ' ', c.apellido) AS colaborador_nombre,
    c.nombre AS colaborador,
    c.cedula,
    c.cargo,
    c.departamento,
    c.departamento AS departamento_nombre,
    c.ubicacion,
    COUNT(a.id) AS total_equipos_asignados,
    GROUP_CONCAT(
        CONCAT(e.nombre, ' (', e.numero_serie, ')')
        ORDER BY a.fecha_asignacion DESC
        SEPARATOR ', '
    ) AS equipos
FROM colaboradores c
LEFT JOIN asignaciones a ON c.id = a.colaborador_id AND a.estado = 'activa'
LEFT JOIN equipos e ON a.equipo_id = e.id
WHERE c.estado = 'activo'
GROUP BY c.id, c.nombre, c.apellido, c.cedula, c.cargo, c.departamento, c.ubicacion
HAVING total_equipos_asignados > 0
ORDER BY total_equipos_asignados DESC;

-- 3. Verificar que la vista se creó correctamente
SELECT * FROM v_equipos_por_colaborador;

-- 4. Verificar colaboradores con equipos
SELECT
    '=== VERIFICACIÓN: Colaboradores con Equipos Asignados ===' AS info;

SELECT
    c.id,
    CONCAT(c.nombre, ' ', c.apellido) AS colaborador,
    c.departamento,
    COUNT(a.id) AS total_equipos_activos
FROM colaboradores c
LEFT JOIN asignaciones a ON c.id = a.colaborador_id AND a.estado = 'activa'
GROUP BY c.id, c.nombre, c.apellido, c.departamento
HAVING total_equipos_activos > 0
ORDER BY total_equipos_activos DESC;
