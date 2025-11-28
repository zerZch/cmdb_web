# 🔒 Sistema de Seguridad - CMDB v2

## Resumen Ejecutivo

Este proyecto implementa un sistema de autenticación y seguridad profesional de nivel empresarial utilizando **Programación Orientada a Objetos (POO)** y las mejores prácticas de seguridad recomendadas por OWASP y normativas internacionales.

## ✅ Validaciones Implementadas

### 1. Validaciones del Lado del Cliente (Frontend)

#### 1.1 Campos de Email
- ✅ Campo obligatorio
- ✅ Formato de email válido (regex)
- ✅ Sin espacios automáticos
- ✅ Longitud máxima (255 caracteres)
- ✅ Sanitización automática

#### 1.2 Campos de Contraseña
- ✅ Campo obligatorio
- ✅ Longitud mínima (6 caracteres)
- ✅ Longitud máxima (128 caracteres)
- ✅ Sin espacios al inicio/final (trim)

#### 1.3 UX/Feedback Visual
- ✅ Mensajes de error claros
- ✅ Loading state durante autenticación
- ✅ Doble click para mostrar/ocultar password
- ✅ SweetAlert2 para notificaciones profesionales

### 2. Validaciones del Lado del Servidor (Backend - PHP)

#### 2.1 Clase `Validator`
Ubicación: `src/Core/Validator.php`

**Reglas de validación disponibles:**
- `required` - Campo obligatorio
- `email` - Formato de email válido
- `min:X` - Longitud mínima
- `max:X` - Longitud máxima
- `username` - Solo alfanuméricos, guiones y puntos
- `no_spaces` - Sin espacios
- `alpha` - Solo letras
- `alphanumeric` - Letras y números
- `numeric` - Solo números
- `integer` - Números enteros
- `url` - URL válida
- `ip` - Dirección IP válida
- `date` - Fecha válida
- `strong_password` - Contraseña fuerte (8+ chars, mayúsculas, minúsculas, números, especiales)

**Métodos de sanitización:**
- `sanitize()` - Limpieza general de strings
- `sanitizeEmail()` - Limpieza específica de emails
- `sanitizeUrl()` - Limpieza de URLs
- `sanitizeInt()` - Conversión segura a integer
- `cleanInput()` - Sanitización automática según tipo

#### 2.2 Protección contra Inyección SQL
- ✅ Uso exclusivo de **Prepared Statements**
- ✅ Binding de parámetros con tipos (`bind_param`)
- ✅ Singleton pattern para conexión a BD
- ✅ Sin interpolación directa de variables en queries

#### 2.3 Hashing de Contraseñas
- ✅ Uso de `password_hash()` con **bcrypt** (algoritmo $2y$)
- ✅ Verificación con `password_verify()`
- ✅ Cost factor configurable (default: 12)
- ✅ NUNCA se almacenan contraseñas en texto plano

### 3. Seguridad Avanzada

#### 3.1 Clase `SecurityManager`
Ubicación: `src/Core/SecurityManager.php`

**Funcionalidades:**

**CSRF Protection:**
- ✅ Generación de tokens seguros (64 bytes)
- ✅ Validación con `hash_equals()` (timing-attack resistant)
- ✅ Expiración de tokens (1 hora)
- ✅ Renovación automática

**Sesiones Seguras:**
- ✅ `HttpOnly` cookies (no accesibles por JavaScript)
- ✅ `Secure` cookies (solo HTTPS en producción)
- ✅ `SameSite=Strict` (protección CSRF)
- ✅ Regeneración de ID después del login
- ✅ Timeout de inactividad (30 minutos configurable)

**Headers de Seguridad:**
```php
X-Frame-Options: DENY                    // Anti-clickjacking
X-Content-Type-Options: nosniff          // Anti-MIME sniffing
X-XSS-Protection: 1; mode=block         // Protección XSS
Content-Security-Policy: ...             // CSP básico
Referrer-Policy: strict-origin...        // Política de referrer
Permissions-Policy: ...                  // Permisos limitados
```

**Device Fingerprinting:**
- ✅ Hash SHA-256 de User-Agent + IP + Headers
- ✅ Detección de dispositivos nuevos
- ✅ Verificación de consistencia de IP

**Detección de Amenazas:**
- ✅ Detección de bots conocidos
- ✅ Logging de eventos sospechosos
- ✅ Prevención de XSS con escape automático

#### 3.2 Clase `RateLimiter`
Ubicación: `src/Core/RateLimiter.php`

**Protección contra Fuerza Bruta:**
- ✅ Límite de intentos por email (5 intentos)
- ✅ Límite de intentos por IP (5 intentos)
- ✅ Ventana de tiempo (15 minutos de bloqueo)
- ✅ Delay progresivo en intentos fallidos
- ✅ Limpieza automática de intentos antiguos
- ✅ Rate limiting global (20 requests/hora por IP)

**Tabla de BD:** `intentos_login`

**Funcionalidades:**
- `checkAttempts()` - Verifica si está bloqueado
- `recordAttempt()` - Registra intento fallido
- `clearAttempts()` - Limpia intentos después de login exitoso
- `getStatistics()` - Estadísticas de intentos
- `getTopAttackingIps()` - IPs más activas
- `manualBlock()` - Bloqueo manual de IP/email

#### 3.3 Clase `AuditLogger`
Ubicación: `src/Core/AuditLogger.php`

**Cumplimiento Normativo:**
- ✅ Registro de TODOS los accesos (exitosos y fallidos)
- ✅ Cumple con Ley 81 de Panamá
- ✅ Logs inmutables con timestamp
- ✅ Geolocalización de IPs
- ✅ Metadata en formato JSON

**Tabla de BD:** `logs_acceso`

**Campos registrados:**
- Usuario (email)
- IP Address (IPv4/IPv6)
- User Agent
- País (código ISO)
- Fingerprint del dispositivo
- Resultado (exitoso/fallido)
- Motivo del fallo
- Metadata adicional (JSON)
- Timestamp

**Funcionalidades:**
- `logSuccessfulLogin()` - Registra login exitoso
- `logFailedLogin()` - Registra intento fallido
- `logLogout()` - Registra cierre de sesión
- `getLogs()` - Consulta de logs con filtros
- `getStatistics()` - Estadísticas de accesos
- `detectSuspiciousActivity()` - Detección de IPs sospechosas
- `exportToCsv()` - Exportación para auditorías

#### 3.4 Principio de Mínimo Privilegio

**Control de Acceso Basado en Roles (RBAC):**
- ✅ Roles definidos: `admin` y `colaborador`
- ✅ Verificación de rol en cada página protegida
- ✅ Métodos de autorización en `AuthService`
- ✅ Helpers globales: `hasRole()`, `isAuthenticated()`

**Validación de Sesión:**
- ✅ Verificación en cada request
- ✅ Timeout de inactividad
- ✅ Verificación de consistencia de IP
- ✅ Regeneración periódica de ID de sesión

### 4. Flujo de Autenticación Completo

```
1. Usuario ingresa credenciales en frontend
   ├─ Validación de formato (JavaScript)
   ├─ CSRF token incluido automáticamente
   └─ Submit del formulario

2. Backend recibe POST
   ├─ Verifica método HTTP (solo POST)
   ├─ Detecta bots conocidos → bloquea
   ├─ Valida CSRF token → excepción si falla
   └─ Continúa al paso 3

3. Sanitización y validación de datos
   ├─ Limpia email con `Validator::cleanInput()`
   ├─ Trim de password (sin sanitizar)
   ├─ Valida con reglas: required, email, min, max
   └─ Si falla → registra en audit log + redirige

4. Rate Limiting por Email
   ├─ Consulta tabla `intentos_login`
   ├─ Si >= 5 intentos → bloqueo temporal (15 min)
   ├─ Muestra tiempo restante al usuario
   └─ Continúa si está dentro del límite

5. Rate Limiting por IP
   ├─ Consulta intentos desde esta IP
   ├─ Si >= 5 intentos → bloqueo temporal
   ├─ Logs de seguridad
   └─ Continúa si está dentro del límite

6. Rate Limiting Global
   ├─ Verifica total de requests de la IP (20/hora)
   ├─ HTTP 429 si excede
   └─ Continúa si está dentro del límite

7. Delay Progresivo
   ├─ sleep(intentos_previos) hasta máx 3 segundos
   └─ Dificulta ataques automatizados

8. Autenticación en Base de Datos
   ├─ Busca usuario por email (prepared statement)
   ├─ Verifica que existe
   ├─ Verifica que está activo
   ├─ Verifica password con `password_verify()`
   └─ Retorna resultado con código de razón

9a. Si LOGIN EXITOSO:
    ├─ Limpia intentos fallidos (email + IP)
    ├─ Registra en `logs_acceso` (exitoso)
    ├─ Crea sesión segura
    │  ├─ Almacena datos del usuario
    │  ├─ Guarda IP, fingerprint, timestamps
    │  └─ Regenera session_id
    └─ Redirige al dashboard según rol

9b. Si LOGIN FALLIDO:
    ├─ Registra intento en `intentos_login` (email + IP)
    ├─ Registra en `logs_acceso` (fallido + motivo)
    ├─ Log de seguridad si >= 3 intentos
    ├─ Mensaje genérico (NO revela si user existe)
    └─ Redirige a login

10. En cada página protegida:
    ├─ Verifica autenticación
    ├─ Verifica timeout de sesión (30 min)
    ├─ Verifica consistencia de IP
    ├─ Actualiza last_activity
    └─ Permite acceso o redirige a login
```

### 5. Base de Datos

#### Tablas de Seguridad

**tabla: `logs_acceso`**
```sql
- id (BIGINT, auto_increment)
- usuario (VARCHAR 255) - Email o username
- ip_address (VARCHAR 45) - IPv4/IPv6
- user_agent (TEXT) - Info del navegador
- pais (VARCHAR 10) - Código ISO
- fingerprint (VARCHAR 64) - Hash SHA-256
- exitoso (BOOLEAN) - 1=exitoso, 0=fallido
- motivo (VARCHAR 255) - Razón del fallo
- metadata (JSON) - Datos adicionales
- created_at (TIMESTAMP)

Índices: usuario, ip_address, exitoso, created_at, fingerprint
```

**tabla: `intentos_login`**
```sql
- id (BIGINT, auto_increment)
- identifier (VARCHAR 255) - Email o IP
- type (ENUM: 'email', 'ip')
- ip_address (VARCHAR 45)
- user_agent (TEXT)
- metadata (JSON)
- created_at (TIMESTAMP)

Índices: identifier + type, created_at, ip_address
```

#### Vistas SQL
- `v_accesos_diarios` - Resumen diario de accesos
- `v_ips_sospechosas` - IPs con múltiples fallos (24h)
- `v_accesos_recientes` - Últimos 100 accesos con detalles

### 6. Arquitectura POO

```
src/
├── Controllers/
│   ├── AuthController.php        ← Orquesta el proceso de autenticación
│   └── BaseController.php        ← Helpers de validación básica
├── Core/
│   ├── AuthService.php           ← Lógica de autenticación y sesiones
│   ├── Validator.php             ← Validaciones y sanitización
│   ├── SecurityManager.php       ← CSRF, headers, fingerprinting
│   ├── RateLimiter.php           ← Control de intentos
│   ├── AuditLogger.php           ← Logging y auditoría
│   └── Database.php              ← Singleton de conexión BD
└── Models/
    └── Usuario.php               ← Modelo de usuario (ORM-like)
```

**Principios SOLID aplicados:**
- **S** - Single Responsibility: Cada clase tiene una responsabilidad única
- **O** - Open/Closed: Extensible sin modificar código existente
- **L** - Liskov Substitution: BaseController es sustituible
- **I** - Interface Segregation: Interfaces específicas por funcionalidad
- **D** - Dependency Inversion: Dependencia de abstracciones (Database)

### 7. Configuración

#### Sesiones Seguras
```php
session.cookie_httponly = 1      // No accesible por JS
session.use_only_cookies = 1     // Solo cookies
session.cookie_samesite = Strict // CSRF protection
session.cookie_secure = 1        // Solo HTTPS (producción)
session.gc_maxlifetime = 1800    // 30 minutos
session.cookie_lifetime = 0      // Borrar al cerrar navegador
```

#### Parámetros Configurables
```php
// Rate Limiting
MAX_ATTEMPTS = 5                 // Intentos permitidos
LOCKOUT_TIME = 900               // 15 minutos de bloqueo
WINDOW_TIME = 3600               // Ventana de 1 hora

// Sesiones
SESSION_TIMEOUT = 1800           // 30 minutos de inactividad
MAX_SESSION_DURATION = 28800     // 8 horas máximo

// Global Rate Limit
MAX_REQUESTS_PER_HOUR = 20       // Por IP
```

### 8. Checklist de Validaciones (OWASP Top 10)

| # | Vulnerabilidad | Protección Implementada | Estado |
|---|---------------|------------------------|--------|
| 1 | Injection (SQL) | Prepared Statements + Sanitización | ✅ |
| 2 | Broken Authentication | Rate limiting + Audit logs + 2FA ready | ✅ |
| 3 | Sensitive Data Exposure | Bcrypt + HTTPS + Secure cookies | ✅ |
| 4 | XML External Entities | N/A (no usamos XML) | - |
| 5 | Broken Access Control | RBAC + Sesiones seguras | ✅ |
| 6 | Security Misconfiguration | Headers de seguridad + Config óptima | ✅ |
| 7 | XSS | Escape automático + CSP | ✅ |
| 8 | Insecure Deserialization | Validación de JSON + No unserialize | ✅ |
| 9 | Using Components with Known Vulnerabilities | Composer autoload + Updates | ⚠️ |
| 10 | Insufficient Logging & Monitoring | AuditLogger completo + Security logs | ✅ |

### 9. Pruebas de Seguridad Recomendadas

#### Pruebas Manuales
1. **SQL Injection**
   - Intentar: `admin@cmdb.com' OR '1'='1`
   - Resultado esperado: Bloqueado por prepared statements

2. **XSS**
   - Intentar: `<script>alert('XSS')</script>` en email
   - Resultado esperado: Sanitizado automáticamente

3. **CSRF**
   - Hacer POST sin token
   - Resultado esperado: 403 Forbidden

4. **Brute Force**
   - Intentar 6+ veces con password incorrecta
   - Resultado esperado: Cuenta bloqueada 15 minutos

5. **Session Hijacking**
   - Cambiar IP durante sesión activa
   - Resultado esperado: Logout forzado

#### Herramientas Recomendadas
- **OWASP ZAP** - Scanner de vulnerabilidades
- **Burp Suite** - Pruebas de penetración
- **SQLMap** - Test de SQL injection
- **Nikto** - Scanner web
- **Nmap** - Port scanning

### 10. Mantenimiento y Monitoreo

#### Logs a Revisar Regularmente
```bash
# Logs de seguridad
tail -f logs/security.log

# Ver IPs sospechosas (SQL)
SELECT * FROM v_ips_sospechosas;

# Estadísticas de acceso (SQL)
SELECT * FROM v_accesos_diarios LIMIT 30;

# Intentos de login recientes
SELECT * FROM intentos_login ORDER BY created_at DESC LIMIT 100;
```

#### Tareas de Mantenimiento
- [ ] Limpiar logs antiguos (>90 días): `AuditLogger::cleanOldLogs(90)`
- [ ] Revisar IPs bloqueadas manualmente
- [ ] Analizar patrones de ataques
- [ ] Actualizar dependencias (Composer)
- [ ] Backup de base de datos semanal
- [ ] Revisar headers de seguridad con securityheaders.com

### 11. Mejoras Futuras Recomendadas

#### Autenticación de Dos Factores (2FA)
- Google Authenticator (TOTP)
- SMS/Email con código
- Backup codes

#### Notificaciones por Email
- Login desde nueva ubicación/dispositivo
- Cambios de contraseña
- Intentos de acceso bloqueados

#### Machine Learning (Detección de Anomalías)
- Entrenar modelo con patrones de acceso normales
- Alertas en comportamientos anómalos
- Scoring de riesgo por sesión

#### WAF (Web Application Firewall)
- Cloudflare
- ModSecurity
- AWS WAF

#### Honeypots
- Campos ocultos para detectar bots
- Endpoints trampa

---

## 📄 Licencia

Proyecto académico - Universidad Tecnológica de Panamá
Todos los derechos reservados © 2025

## 👨‍💻 Autor

Sistema de seguridad implementado siguiendo las mejores prácticas de OWASP, CWE Top 25 y normativas de privacidad (Ley 81 de Panamá, GDPR-ready).

**¡IMPORTANTE!** Siempre mantén actualizadas las dependencias y revisa los logs de seguridad regularmente.
