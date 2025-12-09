# Solución al Problema de Historial de Asignaciones

## Problema Identificado

El historial de asignaciones no mostraba los equipos devueltos debido a tres problemas:

### 1. **Error en el código de devolución** (`src/Models/Asignacion.php`)
   - El método `devolverEquipo()` guardaba el estado como `'inactiva'` en lugar de `'devuelta'`
   - **✅ CORREGIDO**: Ahora guarda correctamente como `'devuelta'`

### 2. **Error en la consulta del historial** (`src/Models/Asignacion.php`)
   - El método `getHistorialPorColaborador()` buscaba estado `'inactiva'` en lugar de `'devuelta'`
   - **✅ CORREGIDO**: Ahora busca correctamente `'devuelta'`

### 3. **Datos incorrectos en la base de datos**
   - Varios registros tienen estado vacío (`''`) en lugar de `'devuelta'`
   - **⚠️ REQUIERE ACCIÓN**: Debes ejecutar el script de corrección

## Archivos Modificados

1. `/home/user/cmdb_web/src/Models/Asignacion.php`
   - Línea 78: Cambiado de `estado = 'inactiva'` a `estado = 'devuelta'`
   - Línea 217: Cambiado WHERE de `a.estado = 'inactiva'` a `a.estado = 'devuelta'`

## Corrección de Datos Históricos

Para corregir los registros existentes en la base de datos, tienes **2 opciones**:

### Opción 1: Ejecutar desde phpMyAdmin (MÁS FÁCIL)

1. Abre phpMyAdmin en tu navegador: http://localhost/phpmyadmin
2. Selecciona la base de datos `cmdb_v2_db`
3. Ve a la pestaña "SQL"
4. Copia y pega este código:

```sql
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
```

5. Haz clic en "Continuar"
6. Deberías ver un mensaje indicando cuántos registros fueron actualizados

### Opción 2: Ejecutar el script PHP

1. Abre una terminal/símbolo del sistema
2. Navega a la carpeta del proyecto:
   ```bash
   cd C:\xampp\htdocs\cmdb_web
   ```
   (Ajusta la ruta según tu instalación)

3. Ejecuta el script:
   ```bash
   php fix_asignaciones.php
   ```

## Verificación

Después de ejecutar cualquiera de las opciones anteriores:

1. Inicia sesión como Juan Pérez (colaborador@cmdb.com)
2. Ve a la sección "Historial de Asignaciones"
3. Deberías ver todos los equipos devueltos, incluyendo el equipo ID 4 que fue devuelto

## Registros que Serán Corregidos

Estos son los registros con estado vacío que serán actualizados:

- **ID 7**: Equipo 1, Colaborador 1, Devuelto: 2025-12-09 08:16:04
- **ID 16**: Equipo 2, Colaborador 3, Devuelto: 2025-12-09 08:17:02
- **ID 17**: Equipo 1, Colaborador 3, Devuelto: 2025-12-09 08:15:21
- **ID 18**: Equipo 4, Colaborador 3, Devuelto: 2025-12-09 08:10:04 ⭐ (Juan Pérez)
- **ID 19**: Equipo 2, Colaborador 2, Devuelto: 2025-12-09 08:14:09
- **ID 21**: Equipo 10, Colaborador 3, Devuelto: 2025-12-09 03:37:25
- **ID 22**: Equipo 1, Colaborador 3, Devuelto: 2025-12-09 08:03:58
- **ID 24**: Equipo 4, Colaborador 12, Devuelto: 2025-12-09 09:05:12 ⭐ (Juan Pérez)

## Nota Importante

Los cambios en el código (`src/Models/Asignacion.php`) ya están aplicados y funcionarán para **nuevas devoluciones**. Sin embargo, para ver el historial de **devoluciones anteriores**, es necesario ejecutar el script de corrección de base de datos.

## Resultado Esperado

Una vez completados todos los pasos:
- ✅ Las nuevas devoluciones se guardarán correctamente con estado 'devuelta'
- ✅ El historial mostrará todas las devoluciones pasadas
- ✅ Juan Pérez podrá ver su equipo ID 4 y ID 24 en su historial de devoluciones
