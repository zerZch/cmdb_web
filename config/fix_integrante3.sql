-- ============================================================================
-- SCRIPT DE CORRECCIÓN PARA INTEGRANTE 3
-- Crea la vista SQL faltante para el Reporte de Inventario
-- ============================================================================

USE cmdb_v2_db;

-- Eliminar vista si existe (para poder recrearla)
DROP VIEW IF EXISTS v_inventario_completo;

-- Crear vista de inventario completo
CREATE VIEW v_inventario_completo AS
SELECT
    e.id,
    e.codigo_inventario,
    COALESCE(NULLIF(e.nombre, ''), CONCAT(e.marca, ' ', e.modelo)) AS nombre,
    e.numero_serie,
    e.marca,
    e.modelo,
    e.descripcion,
    c.nombre AS categoria,
    e.estado,
    e.condicion,
    e.ubicacion,
    e.fecha_adquisicion,
    e.costo_adquisicion,
    e.vida_util_anos,
    e.valor_residual,

    -- Información de asignación actual
    CASE
        WHEN e.estado = 'asignado' THEN
            (SELECT CONCAT(col.nombre, ' ', col.apellido)
             FROM asignaciones a
             LEFT JOIN colaboradores col ON a.colaborador_id = col.id
             WHERE a.equipo_id = e.id AND a.estado = 'activa'
             ORDER BY a.fecha_asignacion DESC
             LIMIT 1)
        ELSE NULL
    END AS asignado_a,

    -- Calcular depreciación
    TIMESTAMPDIFF(MONTH, e.fecha_adquisicion, CURRENT_DATE) AS meses_uso,
    ROUND((e.costo_adquisicion - COALESCE(e.valor_residual, 0)) / (e.vida_util_anos * 12), 2) AS depreciacion_mensual,
    ROUND(
        LEAST(
            (e.costo_adquisicion - COALESCE(e.valor_residual, 0)) / (e.vida_util_anos * 12) * TIMESTAMPDIFF(MONTH, e.fecha_adquisicion, CURRENT_DATE),
            e.costo_adquisicion - COALESCE(e.valor_residual, 0)
        ),
        2
    ) AS depreciacion_acumulada,
    ROUND(
        GREATEST(
            e.costo_adquisicion - (
                (e.costo_adquisicion - COALESCE(e.valor_residual, 0)) / (e.vida_util_anos * 12) * TIMESTAMPDIFF(MONTH, e.fecha_adquisicion, CURRENT_DATE)
            ),
            COALESCE(e.valor_residual, 0)
        ),
        2
    ) AS valor_actual,

    e.created_at,
    e.updated_at
FROM equipos e
LEFT JOIN categorias c ON e.categoria_id = c.id
WHERE e.estado NOT IN ('dado_de_baja', 'donado')
ORDER BY e.id DESC;

-- Verificar que se creó correctamente
SELECT 'Vista v_inventario_completo creada exitosamente' AS Resultado;

-- Mostrar primeros registros
SELECT * FROM v_inventario_completo LIMIT 5;
