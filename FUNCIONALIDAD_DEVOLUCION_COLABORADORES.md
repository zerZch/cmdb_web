# Sistema de Devolución de Equipos por Colaboradores

## Descripción General

Esta funcionalidad permite a los colaboradores solicitar la devolución de equipos asignados a través del sistema, con validación por parte de los administradores. Los equipos devueltos pasan automáticamente a un estado de revisión técnica antes de poder ser reasignados.

## Flujo Completo del Proceso

### 1. COLABORADOR: Solicitar Devolución

**Ruta:** Asignaciones → Mis Equipos → Botón "Devolver"

1. El colaborador ve sus equipos asignados
2. Hace clic en "Devolver" en el equipo que desea devolver
3. Completa el formulario con:
   - **Motivo de devolución** (obligatorio):
     - Traslado de ubicación/departamento
     - Salida de la empresa (Renuncia/Despido)
     - Equipo en mal estado / No funciona correctamente
     - Fin de proyecto
     - Otro motivo
   - **Observaciones** (opcional): Descripción del estado o motivo adicional
4. Envía la solicitud
5. La solicitud queda en estado "solicitada" y pendiente de validación

**Vista:** `src/Views/asignaciones/solicitar_devolucion.php`
**Acción:** `AsignacionController::solicitarDevolucionForm()` y `guardarSolicitudDevolucion()`

---

### 2. ADMIN: Recibir Notificación

**Ruta:** Asignaciones → Devoluciones Pendientes

- El admin ve una lista de todas las solicitudes pendientes
- Información mostrada:
  - Colaborador (nombre, email)
  - Equipo (nombre, marca, modelo, número de serie)
  - Fecha de solicitud
  - Motivo de devolución
  - Departamento
- Ordenadas por fecha (más antiguas primero)

**Vista:** `src/Views/asignaciones/devoluciones_pendientes.php`
**Acción:** `AsignacionController::devolucionesPendientes()`

---

### 3. ADMIN: Validar Devolución

**Ruta:** Devoluciones Pendientes → Botón "Validar"

1. El admin hace clic en "Validar" en una solicitud
2. Ve el formulario de validación con:
   - Información del colaborador
   - Información del equipo
   - Detalles de la solicitud
   - Observaciones del colaborador
3. Completa el formulario de validación:
   - **Estado del equipo recibido** (obligatorio):
     - En Revisión (default)
     - Disponible (si está en buen estado)
     - Dañado (si tiene problemas)
     - Mantenimiento (requiere reparación)
   - **Observaciones de validación** (obligatorio): Describe el estado del equipo
4. Opciones disponibles:
   - **Validar y Recibir Equipo**: Acepta la devolución
   - **Rechazar Solicitud**: Rechaza la devolución (el colaborador debe seguir con el equipo)

**Vista:** `src/Views/asignaciones/validar_devolucion.php`
**Acción:** `AsignacionController::validarDevolucionForm()` y `procesarValidacion()`

---

### 4. SISTEMA: Procesamiento Automático

Al validar la devolución, el sistema realiza las siguientes acciones automáticamente:

1. **Actualiza la asignación:**
   - `estado` → 'devuelta'
   - `estado_solicitud` → 'validada'
   - `fecha_devolucion` → Fecha/hora actual
   - `usuario_validador_id` → ID del admin que validó
   - `observaciones_validacion` → Comentarios del admin

2. **Actualiza el equipo:**
   - `estado` → 'en_revision' (o el estado seleccionado por el admin)

3. **Registra en historial:**
   - Tipo de movimiento: 'devolucion'
   - Estado anterior: 'asignado'
   - Estado nuevo: 'en_revision' (o el seleccionado)
   - Incluye motivo y observaciones

---

### 5. ADMIN: Revisión Técnica y Reasignación

**Flujo futuro/manual:**

1. El admin/técnico revisa físicamente el equipo
2. Desde la gestión de equipos, cambia el estado:
   - A "disponible" si está bien → puede ser reasignado
   - A "dañado" si tiene problemas → requiere reparación
   - A "mantenimiento" si necesita mantenimiento

---

## Archivos Modificados/Creados

### Base de Datos

**Script SQL:** `add_devolucion_colaborador.sql`

Campos agregados a la tabla `asignaciones`:
- `motivo_devolucion` ENUM('traslado', 'salida', 'mal_estado', 'fin_proyecto', 'otro')
- `fecha_solicitud_devolucion` DATETIME
- `usuario_validador_id` INT (FK a usuarios)
- `estado_solicitud` ENUM('sin_solicitud', 'solicitada', 'validada', 'rechazada')
- `observaciones_validacion` TEXT

### Modelo

**Archivo:** `src/Models/Asignacion.php`

Métodos agregados:
- `solicitarDevolucion()` - Colaborador solicita devolución
- `getSolicitudesPendientes()` - Obtener solicitudes pendientes
- `validarDevolucion()` - Admin valida y recibe equipo
- `rechazarSolicitudDevolucion()` - Admin rechaza solicitud
- `contarSolicitudesPendientes()` - Contador para notificaciones

### Controlador

**Archivo:** `src/Controllers/AsignacionController.php`

Métodos agregados:
- `solicitarDevolucionForm()` - Muestra formulario de solicitud
- `guardarSolicitudDevolucion()` - Procesa solicitud del colaborador
- `devolucionesPendientes()` - Lista de solicitudes para admin
- `validarDevolucionForm()` - Formulario de validación para admin
- `procesarValidacion()` - Procesa validación o rechazo

### Vistas

1. **`src/Views/asignaciones/mis_equipos.php`** (modificada)
   - Agregado botón "Devolver" en cada equipo

2. **`src/Views/asignaciones/solicitar_devolucion.php`** (nueva)
   - Formulario para solicitar devolución

3. **`src/Views/asignaciones/devoluciones_pendientes.php`** (nueva)
   - Lista de solicitudes pendientes para admin

4. **`src/Views/asignaciones/validar_devolucion.php`** (nueva)
   - Formulario de validación para admin

---

## Instrucciones de Implementación

### 1. Ejecutar Script SQL

Ejecuta el script en phpMyAdmin o MySQL:

```bash
mysql -u root cmdb_v2_db < add_devolucion_colaborador.sql
```

O en phpMyAdmin:
1. Abre http://localhost/phpmyadmin
2. Selecciona `cmdb_v2_db`
3. Pestaña "SQL"
4. Copia y pega el contenido de `add_devolucion_colaborador.sql`
5. Ejecuta

### 2. Verificar Permisos

Asegúrate de que:
- Los colaboradores pueden acceder a `asignaciones/misEquipos`
- Los administradores pueden acceder a `asignaciones/devolucionesPendientes`

### 3. Probar el Flujo

**Como Colaborador:**
1. Inicia sesión como colaborador (ej: colaborador@cmdb.com)
2. Ve a "Mis Equipos"
3. Haz clic en "Devolver" en un equipo
4. Completa y envía el formulario

**Como Admin:**
1. Inicia sesión como admin (admin@cmdb.com)
2. Ve a "Asignaciones" → "Devoluciones Pendientes"
3. Haz clic en "Validar" en una solicitud
4. Completa el formulario y valida

**Verificar:**
1. El equipo debería estar en estado "en_revision"
2. La asignación debería mostrar `estado = 'devuelta'`
3. Debería haber un registro en `historial_movimientos`

---

## Casos de Uso

### Caso 1: Colaborador se va de la empresa

1. Colaborador solicita devolución con motivo "Salida"
2. Admin valida y recibe el equipo
3. Equipo pasa a "en_revision"
4. Técnico revisa → cambia a "disponible"
5. Equipo puede ser reasignado a otro colaborador

### Caso 2: Equipo con problemas

1. Colaborador solicita devolución con motivo "Mal Estado"
2. Describe el problema en observaciones
3. Admin valida y selecciona estado "dañado"
4. Equipo va a reparación
5. Después de reparar → cambiar a "disponible"

### Caso 3: Fin de proyecto

1. Colaborador solicita devolución con motivo "Fin de Proyecto"
2. Admin valida y selecciona estado "disponible" (si está bien)
3. Equipo queda listo para reasignar inmediatamente

### Caso 4: Solicitud rechazada

1. Colaborador solicita devolución
2. Admin revisa y decide que NO debe devolverse
3. Admin hace clic en "Rechazar Solicitud"
4. Indica el motivo del rechazo
5. Colaborador conserva el equipo

---

## Ventajas de esta Implementación

1. **Trazabilidad completa:**
   - Se registra quién solicita, cuándo, por qué
   - Se registra quién recibe/valida
   - Todo queda en el historial

2. **Control de estados:**
   - Los equipos devueltos van automáticamente a revisión
   - Previene reasignación de equipos en mal estado

3. **Validación obligatoria:**
   - Un admin debe recibir físicamente el equipo
   - Se verifica el estado del equipo

4. **Flexibilidad:**
   - El admin puede decidir el estado final del equipo
   - Permite rechazar solicitudes si es necesario

5. **Transparencia:**
   - El colaborador sabe que su solicitud está pendiente
   - El admin ve todas las solicitudes en un solo lugar

---

## Mejoras Futuras Sugeridas

1. **Notificaciones por email:**
   - Al colaborador cuando se valida/rechaza su solicitud
   - Al admin cuando hay nuevas solicitudes

2. **Dashboard con métricas:**
   - Solicitudes pendientes (widget)
   - Equipos en revisión (widget)
   - Tiempo promedio de validación

3. **Recordatorios automáticos:**
   - Si una solicitud lleva más de X días sin validar
   - Si un equipo lleva mucho tiempo en revisión

4. **Checklist de revisión:**
   - Lista de verificación para técnicos
   - Registro de componentes/accesorios

5. **Firmas digitales:**
   - Firma del colaborador al devolver
   - Firma del admin al recibir

---

## Soporte y Resolución de Problemas

### Problema: No aparece el botón "Devolver"

**Solución:** Verifica que:
- El colaborador ha iniciado sesión correctamente
- La tabla `asignaciones` tiene la columna `estado_solicitud`
- El equipo tiene estado = 'activa'

### Problema: Error al solicitar devolución

**Solución:** Verifica que:
- La columna `estado_solicitud` existe y tiene el valor default 'sin_solicitud'
- El colaborador_id en la sesión coincide con el de la asignación

### Problema: No se actualiza el estado del equipo

**Solución:** Verifica que:
- El modelo `Equipo` tiene el método `updateEstado()`
- Hay permisos de UPDATE en la tabla `equipos`

---

**Fecha de Implementación:** 2025-12-09
**Versión:** 1.0
**Autor:** Sistema CMDB v2
