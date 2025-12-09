-- Script para corregir el estado de las asignaciones devueltas
-- Problema: Varios registros tienen estado vacío ('') en lugar de 'devuelta'
-- cuando tienen fecha_devolucion

-- Actualizar registros con estado vacío que tienen fecha_devolucion
UPDATE asignaciones
SET estado = 'devuelta'
WHERE estado = ''
  AND fecha_devolucion IS NOT NULL;

-- Verificar los cambios
SELECT id, equipo_id, colaborador_id, fecha_asignacion, fecha_devolucion, estado, observaciones
FROM asignaciones
WHERE fecha_devolucion IS NOT NULL
ORDER BY fecha_devolucion DESC;
