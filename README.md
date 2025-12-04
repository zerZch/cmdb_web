# CMDB - Sistema de Gestión de Inventario v2.0

![PHP Version](https://img.shields.io/badge/PHP-7.4%2B-blue)
![MySQL](https://img.shields.io/badge/MySQL-5.7%2B-orange)
![License](https://img.shields.io/badge/License-Academic-green)

Sistema profesional de gestión de inventario de equipos desarrollado en **PHP puro** con arquitectura **MVC** y **POO**, implementando las mejores prácticas de seguridad según **OWASP Top 10** y normativas internacionales.

---

## 📖 Tabla de Contenidos

- [Características](#-características)
- [Tecnologías](#-tecnologías)
- [Arquitectura](#-arquitectura)
- [Requisitos](#-requisitos)
- [Instalación](#-instalación)
- [Configuración](#-configuración)
- [Uso del Sistema](#-uso-del-sistema)
- [Seguridad](#-seguridad)
- [Base de Datos](#-base-de-datos)
- [Estructura del Proyecto](#-estructura-del-proyecto)
- [API/Endpoints](#-apiendpoints)
- [Mantenimiento](#-mantenimiento)
- [Solución de Problemas](#-solución-de-problemas)
- [Roadmap](#-roadmap)
- [Contribución](#-contribución)
- [Licencia](#-licencia)

---

## 🚀 Características

### ✅ Módulo de Autenticación y Usuarios

#### Backend:
- ✅ **Sistema de Login** con validación de correo, contraseña y estado de usuario
- ✅ **Logout seguro** con destrucción completa de sesión
- ✅ **CRUD completo de Usuarios** con validación de datos
- ✅ **Sistema de roles** (Administrador / Colaborador)
- ✅ **Gestión de perfiles** con foto de usuario
- ✅ **Activación/desactivación** de usuarios
- ✅ **Cambio de contraseñas** con encriptación

#### Frontend:
- ✅ **Pantalla de Login** moderna y responsiva
- ✅ **Dashboard con métricas** en tiempo real
- ✅ **Gráficos interactivos** (Chart.js)
- ✅ **DataTables** para gestión eficiente de datos
- ✅ **SweetAlert2** para notificaciones profesionales
- ✅ **Interfaz responsiva** compatible con móviles

### ✅ Módulo de Dashboard

- ✅ **Métricas en tiempo real:**
  - Total de Equipos
  - Equipos Disponibles
  - Equipos Asignados
  - Equipos Dañados
  - Equipos en Mantenimiento
- ✅ **Gráficos de distribución:**
  - Por categoría (Gráfico de pastel)
  - Por estado (Gráfico de barras)
- ✅ **Panel administrativo** con acceso basado en roles

### ✅ Módulo de Categorías

- ✅ **CRUD completo de categorías**
- ✅ **Validación de nombres únicos**
- ✅ **Conteo de equipos** asociados por categoría
- ✅ **Protección de eliminación** (no se pueden eliminar categorías con equipos)
- ✅ **Estados** activa/inactiva

### ✅ Sistema de Seguridad Avanzado

- ✅ **Protección contra SQL Injection** (Prepared Statements)
- ✅ **Protección contra XSS** (Sanitización automática)
- ✅ **Protección CSRF** con tokens seguros
- ✅ **Rate Limiting** contra ataques de fuerza bruta
- ✅ **Audit Logging** completo de accesos
- ✅ **Device Fingerprinting** para detección de dispositivos
- ✅ **Sesiones seguras** con HttpOnly, Secure, SameSite
- ✅ **Headers de seguridad** (CSP, X-Frame-Options, etc.)
- ✅ **Detección de bots** y IPs sospechosas
- ✅ **Geolocalización** de accesos por IP

---

## 🎨 Tecnologías

### Backend

| Tecnología | Versión | Descripción |
|-----------|---------|-------------|
| **PHP** | 7.4+ | Lenguaje principal (sin frameworks) |
| **MySQL** | 5.7+ | Base de datos relacional |
| **PDO** | - | Acceso seguro a base de datos |
| **Composer** | 2.x | Gestor de dependencias |
| **PSR-4** | - | Autoloading de clases |

### Frontend

| Tecnología | Versión | Descripción |
|-----------|---------|-------------|
| **Bootstrap** | 5.3 | Framework CSS |
| **Font Awesome** | 6.x | Iconografía |
| **jQuery** | 3.7 | Librería JavaScript |
| **DataTables** | 1.13 | Tablas interactivas |
| **SweetAlert2** | 11.x | Alertas modernas |
| **Chart.js** | 4.x | Gráficos interactivos |

### Arquitectura

- **MVC** (Model-View-Controller)
- **POO** (Programación Orientada a Objetos)
- **Singleton Pattern** para conexión a BD
- **SOLID Principles**
- **PSR-4 Autoloading**
- **Separation of Concerns**

---

## 🏗️ Arquitectura

```
┌─────────────────────────────────────────────────────────┐
│                    USUARIO (Browser)                     │
└──────────────────────┬──────────────────────────────────┘
                       │
                       ▼
┌─────────────────────────────────────────────────────────┐
│                  PUBLIC/INDEX.PHP                        │
│  (Front Controller - URL Routing - CSRF Protection)      │
└──────────────────────┬──────────────────────────────────┘
                       │
                       ▼
┌─────────────────────────────────────────────────────────┐
│                   CONTROLLERS                            │
│  ┌────────────┐  ┌──────────┐  ┌────────────┐          │
│  │   Auth     │  │Dashboard │  │  Usuario   │          │
│  │Controller  │  │Controller│  │ Controller │          │
│  └────────────┘  └──────────┘  └────────────┘          │
└──────────┬────────────┬────────────┬────────────────────┘
           │            │            │
           ▼            ▼            ▼
┌─────────────────────────────────────────────────────────┐
│                    CORE SERVICES                         │
│  ┌────────────┐  ┌──────────┐  ┌────────────┐          │
│  │   Auth     │  │ Security │  │    Rate    │          │
│  │  Service   │  │ Manager  │  │  Limiter   │          │
│  └────────────┘  └──────────┘  └────────────┘          │
│  ┌────────────┐  ┌──────────┐  ┌────────────┐          │
│  │   Audit    │  │Validator │  │  Database  │          │
│  │  Logger    │  │          │  │ (Singleton)│          │
│  └────────────┘  └──────────┘  └────────────┘          │
└──────────┬────────────┬────────────┬────────────────────┘
           │            │            │
           ▼            ▼            ▼
┌─────────────────────────────────────────────────────────┐
│                      MODELS                              │
│  ┌────────────┐  ┌──────────┐  ┌────────────┐          │
│  │  Usuario   │  │Categoria │  │   Equipo   │          │
│  │   Model    │  │  Model   │  │   Model    │          │
│  └────────────┘  └──────────┘  └────────────┘          │
└──────────┬────────────┬────────────┬────────────────────┘
           │            │            │
           ▼            ▼            ▼
┌─────────────────────────────────────────────────────────┐
│                   MYSQL DATABASE                         │
│  ┌────────────┐  ┌──────────┐  ┌────────────┐          │
│  │  usuarios  │  │categorias│  │  equipos   │          │
│  └────────────┘  └──────────┘  └────────────┘          │
│  ┌────────────┐  ┌──────────┐  ┌────────────┐          │
│  │logs_acceso │  │intentos_ │  │asignaciones│          │
│  │            │  │  login   │  │            │          │
│  └────────────┘  └──────────┘  └────────────┘          │
└─────────────────────────────────────────────────────────┘
```

---

## 📋 Requisitos

### Requisitos del Sistema

- **PHP:** 7.4 o superior
- **MySQL:** 5.7 o superior
- **Apache:** 2.4+ con `mod_rewrite` habilitado
- **Composer:** 2.x
- **Extensiones PHP requeridas:**
  - PDO
  - pdo_mysql
  - mbstring
  - session
  - json

### Requisitos Opcionales

- **phpMyAdmin:** Para gestión visual de BD
- **Git:** Para control de versiones

---

## 🚀 Instalación

### 1. Clonar el Repositorio

```bash
git clone <url-del-repositorio>
cd cmdb_web
```

### 2. Instalar Dependencias

```bash
composer install
```

Si no tienes Composer instalado, descárgalo desde [getcomposer.org](https://getcomposer.org/)

### 3. Configurar Base de Datos

#### Opción A: Usando phpMyAdmin (Recomendado)

1. Abre phpMyAdmin en `http://localhost/phpmyadmin`
2. Crea una nueva base de datos llamada `cmdb_v2_db`
3. Selecciona cotejamiento: `utf8mb4_unicode_ci`
4. Ve a la pestaña "Importar"
5. Selecciona el archivo `config/database.sql`
6. Haz clic en "Continuar"

#### Opción B: Usando línea de comandos

```bash
# Crear la base de datos e importar el schema
mysql -u root -p < config/database.sql
```

### 4. Configurar Credenciales

Edita el archivo `config/database.php`:

```php
<?php
define('DB_HOST', 'localhost');
define('DB_NAME', 'cmdb_v2_db');
define('DB_USER', 'root');
define('DB_PASS', ''); // Contraseña de MySQL
define('DB_CHARSET', 'utf8mb4');
```

### 5. Configurar URL Base

Edita el archivo `config/app.php`:

```php
<?php
define('BASE_URL', 'http://localhost/cmdb_web/public/');
```

Ajusta la URL según tu configuración.

### 6. Configurar Servidor Web

#### XAMPP / WAMP

1. Copia la carpeta del proyecto a:
   - **XAMPP:** `C:\xampp\htdocs\cmdb_web\`
   - **WAMP:** `C:\wamp64\www\cmdb_web\`
2. Inicia Apache y MySQL
3. Accede a: `http://localhost/cmdb_web/public/`

#### Apache Virtual Host (Opcional)

```apache
<VirtualHost *:80>
    ServerName cmdb.local
    DocumentRoot "/ruta/a/cmdb_web/public"

    <Directory "/ruta/a/cmdb_web/public">
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

No olvides agregar `127.0.0.1 cmdb.local` a tu archivo `hosts`.

### 7. Verificar Instalación

Ejecuta el script de verificación:

```bash
php setup_database.php
```

Este script verificará:
- ✓ Conexión a MySQL
- ✓ Existencia de la base de datos
- ✓ Tablas creadas correctamente
- ✓ Usuarios de prueba

Si no existen usuarios, los creará automáticamente.

---

## ⚙️ Configuración

### Configuración de Sesiones

Las sesiones están configuradas de forma segura en `src/Core/SecurityManager.php`:

```php
// Configuración aplicada automáticamente
session.cookie_httponly = 1      // No accesible desde JavaScript
session.use_only_cookies = 1     // Solo usar cookies
session.cookie_samesite = Strict // Protección CSRF
session.cookie_secure = 0        // Cambiar a 1 en producción (HTTPS)
session.gc_maxlifetime = 1800    // 30 minutos de inactividad
```

### Configuración de Rate Limiting

Edita las constantes en `src/Core/RateLimiter.php`:

```php
const MAX_ATTEMPTS = 5;           // Intentos permitidos
const LOCKOUT_TIME = 900;         // 15 minutos de bloqueo
const WINDOW_TIME = 3600;         // Ventana de 1 hora
const MAX_REQUESTS_PER_HOUR = 20; // Límite global por IP
```

### Configuración de Seguridad

Headers de seguridad en `src/Core/SecurityManager.php`:

```php
X-Frame-Options: DENY
X-Content-Type-Options: nosniff
X-XSS-Protection: 1; mode=block
Content-Security-Policy: default-src 'self'
Referrer-Policy: strict-origin-when-cross-origin
```

---

## 🔐 Uso del Sistema

### Credenciales de Acceso

#### 👨‍💼 Administrador

- **Email:** `admin@cmdb.com`
- **Contraseña:** `admin123`

**Permisos:**
- Dashboard completo con métricas
- Gestión de usuarios
- Gestión de categorías
- Gestión de equipos
- Asignaciones
- Reportes

#### 👤 Colaborador

- **Email:** `colaborador@cmdb.com`
- **Contraseña:** `colab123`

**Permisos:**
- Dashboard con métricas limitadas
- Ver equipos
- Ver asignaciones propias

### Funcionalidades por Módulo

#### 1. Gestión de Usuarios (Solo Admin)

**Crear Usuario:**
1. Dashboard → Usuarios → Nuevo Usuario
2. Completar formulario:
   - Nombre y Apellido
   - Email (único)
   - Contraseña
   - Rol (admin/colaborador)
   - Estado (activo/inactivo)
   - Foto (opcional)
3. Guardar

**Editar Usuario:**
1. Listado de usuarios → Botón "Editar"
2. Modificar campos necesarios
3. Opcionalmente cambiar contraseña
4. Guardar cambios

**Eliminar Usuario:**
1. Listado de usuarios → Botón "Eliminar"
2. Confirmar acción
3. **Nota:** No puedes eliminar tu propio usuario

#### 2. Gestión de Categorías (Solo Admin)

**Crear Categoría:**
1. Dashboard → Categorías → Nueva Categoría
2. Ingresar nombre y descripción
3. Guardar

**Editar Categoría:**
1. Listado → Botón "Editar"
2. Modificar datos
3. Guardar

**Eliminar Categoría:**
1. Listado → Botón "Eliminar"
2. Solo se puede eliminar si no tiene equipos asociados

#### 3. Dashboard

**Métricas visualizadas:**
- Total de equipos en el inventario
- Equipos disponibles para asignación
- Equipos actualmente asignados
- Equipos dañados
- Equipos en mantenimiento

**Gráficos:**
- Distribución por categoría (pie chart)
- Distribución por estado (bar chart)

---

## 🔒 Seguridad

### Características de Seguridad Implementadas

#### 1. Autenticación Segura

- ✅ Contraseñas hasheadas con **bcrypt** (cost factor 12)
- ✅ Validación de estado de usuario (activo/inactivo)
- ✅ Sesiones seguras con HttpOnly cookies
- ✅ Regeneración de session ID después del login
- ✅ Timeout de inactividad (30 minutos)

#### 2. Protección contra Ataques

| Ataque | Protección |
|--------|-----------|
| **SQL Injection** | Prepared Statements + PDO |
| **XSS** | Sanitización automática + escape de HTML |
| **CSRF** | Tokens seguros con hash_equals() |
| **Brute Force** | Rate limiting (5 intentos, 15 min bloqueo) |
| **Session Hijacking** | Fingerprinting + IP validation |
| **Clickjacking** | X-Frame-Options: DENY |

#### 3. Rate Limiting

**Por Email:**
- Máximo 5 intentos fallidos
- Bloqueo temporal de 15 minutos

**Por IP:**
- Máximo 5 intentos fallidos
- Bloqueo temporal de 15 minutos

**Global:**
- Máximo 20 requests por hora por IP

**Delay progresivo:**
- Incrementa el tiempo de respuesta con cada intento fallido

#### 4. Audit Logging

Todos los accesos son registrados en la tabla `logs_acceso`:

```sql
- Usuario (email)
- IP Address (IPv4/IPv6)
- User Agent
- País (geolocalización)
- Fingerprint del dispositivo
- Resultado (exitoso/fallido)
- Motivo del fallo
- Metadata adicional
- Timestamp
```

#### 5. Device Fingerprinting

Genera un hash SHA-256 único basado en:
- User Agent
- IP Address
- Accept Headers
- Accept Language

Detecta cuando un usuario accede desde un dispositivo nuevo.

#### 6. Validaciones

**Clase Validator** (`src/Core/Validator.php`):

Reglas disponibles:
- `required` - Campo obligatorio
- `email` - Email válido
- `min:X` - Longitud mínima
- `max:X` - Longitud máxima
- `username` - Alfanuméricos + guiones
- `strong_password` - Contraseña fuerte
- `numeric` - Solo números
- `url` - URL válida
- `ip` - IP válida
- Y más...

#### 7. Cumplimiento Normativo

✅ **OWASP Top 10** - Protección completa
✅ **Ley 81 de Panamá** - Logging de accesos
✅ **GDPR-ready** - Protección de datos personales

### Monitoreo de Seguridad

**Vistas SQL disponibles:**

```sql
-- Accesos diarios
SELECT * FROM v_accesos_diarios;

-- IPs sospechosas (múltiples fallos en 24h)
SELECT * FROM v_ips_sospechosas;

-- Accesos recientes
SELECT * FROM v_accesos_recientes LIMIT 100;
```

**Métodos de auditoría:**

```php
// En PHP
$auditLogger = new AuditLogger();
$stats = $auditLogger->getStatistics('7 days');
$suspicious = $auditLogger->detectSuspiciousActivity(5, '1 hour');
$auditLogger->exportToCsv('audit_log.csv');
```

---

## 🗄️ Base de Datos

### Diagrama ER

```
┌─────────────┐       ┌──────────────┐       ┌─────────────┐
│  usuarios   │       │   equipos    │       │ categorias  │
├─────────────┤       ├──────────────┤       ├─────────────┤
│ id (PK)     │       │ id (PK)      │       │ id (PK)     │
│ nombre      │       │ nombre       │       │ nombre      │
│ apellido    │       │ codigo       │       │ descripcion │
│ email       │       │ categoria_id─┼──────▶│ estado      │
│ password    │       │ estado       │       │ created_at  │
│ rol         │       │ descripcion  │       │ updated_at  │
│ estado      │       │ fecha_compra │       └─────────────┘
│ foto        │       │ costo        │
│ created_at  │       │ created_at   │
│ updated_at  │       │ updated_at   │
└──────┬──────┘       └───────┬──────┘
       │                      │
       │    ┌─────────────────┘
       │    │
       │    ▼
       │  ┌──────────────┐
       │  │ asignaciones │
       │  ├──────────────┤
       │  │ id (PK)      │
       └─▶│ equipo_id    │
          │ usuario_id   │
          │ fecha_asig   │
          │ fecha_dev    │
          │ observaciones│
          │ created_at   │
          └──────────────┘

┌──────────────┐       ┌──────────────┐
│ logs_acceso  │       │intentos_login│
├──────────────┤       ├──────────────┤
│ id (PK)      │       │ id (PK)      │
│ usuario      │       │ identifier   │
│ ip_address   │       │ type         │
│ user_agent   │       │ ip_address   │
│ pais         │       │ user_agent   │
│ fingerprint  │       │ metadata     │
│ exitoso      │       │ created_at   │
│ motivo       │       └──────────────┘
│ metadata     │
│ created_at   │
└──────────────┘
```

### Tablas Principales

#### 1. usuarios

```sql
CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    apellido VARCHAR(100) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    rol ENUM('admin', 'colaborador') DEFAULT 'colaborador',
    estado ENUM('activo', 'inactivo') DEFAULT 'activo',
    foto VARCHAR(255) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_email (email),
    INDEX idx_estado (estado)
);
```

#### 2. categorias

```sql
CREATE TABLE categorias (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL UNIQUE,
    descripcion TEXT NULL,
    estado ENUM('activa', 'inactiva') DEFAULT 'activa',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_nombre (nombre)
);
```

#### 3. equipos

```sql
CREATE TABLE equipos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(200) NOT NULL,
    codigo VARCHAR(50) NOT NULL UNIQUE,
    categoria_id INT NOT NULL,
    estado ENUM('disponible','asignado','mantenimiento','dañado') DEFAULT 'disponible',
    descripcion TEXT NULL,
    fecha_compra DATE NULL,
    costo DECIMAL(10,2) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (categoria_id) REFERENCES categorias(id),
    INDEX idx_codigo (codigo),
    INDEX idx_estado (estado)
);
```

#### 4. asignaciones

```sql
CREATE TABLE asignaciones (
    id INT AUTO_INCREMENT PRIMARY KEY,
    equipo_id INT NOT NULL,
    usuario_id INT NOT NULL,
    fecha_asignacion DATE NOT NULL,
    fecha_devolucion DATE NULL,
    observaciones TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (equipo_id) REFERENCES equipos(id),
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id),
    INDEX idx_equipo (equipo_id),
    INDEX idx_usuario (usuario_id)
);
```

#### 5. logs_acceso (Auditoría)

```sql
CREATE TABLE logs_acceso (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    usuario VARCHAR(255) NOT NULL,
    ip_address VARCHAR(45) NOT NULL,
    user_agent TEXT NULL,
    pais VARCHAR(10) NULL,
    fingerprint VARCHAR(64) NULL,
    exitoso BOOLEAN DEFAULT 0,
    motivo VARCHAR(255) NULL,
    metadata JSON NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_usuario (usuario),
    INDEX idx_ip (ip_address),
    INDEX idx_exitoso (exitoso),
    INDEX idx_created (created_at),
    INDEX idx_fingerprint (fingerprint)
);
```

#### 6. intentos_login (Rate Limiting)

```sql
CREATE TABLE intentos_login (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    identifier VARCHAR(255) NOT NULL,
    type ENUM('email', 'ip') NOT NULL,
    ip_address VARCHAR(45) NOT NULL,
    user_agent TEXT NULL,
    metadata JSON NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_identifier_type (identifier, type),
    INDEX idx_created (created_at),
    INDEX idx_ip (ip_address)
);
```

### Vistas SQL

#### v_accesos_diarios

Resumen diario de accesos exitosos y fallidos:

```sql
CREATE VIEW v_accesos_diarios AS
SELECT
    DATE(created_at) as fecha,
    COUNT(*) as total_accesos,
    SUM(exitoso) as exitosos,
    COUNT(*) - SUM(exitoso) as fallidos
FROM logs_acceso
GROUP BY DATE(created_at)
ORDER BY fecha DESC;
```

#### v_ips_sospechosas

IPs con múltiples intentos fallidos en las últimas 24 horas:

```sql
CREATE VIEW v_ips_sospechosas AS
SELECT
    ip_address,
    COUNT(*) as intentos_fallidos,
    MAX(created_at) as ultimo_intento
FROM logs_acceso
WHERE exitoso = 0
    AND created_at > DATE_SUB(NOW(), INTERVAL 24 HOUR)
GROUP BY ip_address
HAVING intentos_fallidos >= 3
ORDER BY intentos_fallidos DESC;
```

---

## 📁 Estructura del Proyecto

```
cmdb_web/
├── .git/                           # Control de versiones Git
├── .gitattributes                  # Configuración Git
├── config/                         # Configuración
│   ├── app.php                     # Configuración general (URL base)
│   ├── database.php                # Credenciales de BD
│   └── database.sql                # Schema SQL e inserts iniciales
├── public/                         # Carpeta pública (Document Root)
│   ├── .htaccess                   # Configuración Apache (URL rewriting)
│   ├── index.php                   # Front Controller (punto de entrada)
│   ├── assets/                     # Recursos estáticos
│   │   └── css/
│   │       └── custom.css          # Estilos personalizados
│   └── uploads/                    # Archivos subidos (fotos de perfil)
├── src/                            # Código fuente
│   ├── Controllers/                # Controladores (MVC)
│   │   ├── BaseController.php      # Controlador base con helpers
│   │   ├── AuthController.php      # Login, logout, validaciones
│   │   ├── DashboardController.php # Dashboard con métricas
│   │   ├── UsuarioController.php   # CRUD de usuarios
│   │   └── CategoriaController.php # CRUD de categorías
│   ├── Models/                     # Modelos (MVC)
│   │   ├── Model.php               # Modelo base (conexión BD)
│   │   ├── Usuario.php             # Modelo de usuarios
│   │   ├── Categoria.php           # Modelo de categorías
│   │   └── Equipo.php              # Modelo de equipos
│   ├── Views/                      # Vistas (MVC)
│   │   ├── layouts/
│   │   │   └── main.php            # Layout principal
│   │   ├── auth/
│   │   │   └── login.php           # Pantalla de login
│   │   ├── dashboard/
│   │   │   └── index.php           # Dashboard principal
│   │   ├── usuarios/
│   │   │   ├── index.php           # Listado de usuarios
│   │   │   ├── crear.php           # Formulario crear
│   │   │   └── editar.php          # Formulario editar
│   │   └── categorias/
│   │       ├── index.php           # Listado de categorías
│   │       ├── crear.php           # Formulario crear
│   │       └── editar.php          # Formulario editar
│   └── Core/                       # Núcleo del sistema
│       ├── Database.php            # Singleton de conexión BD
│       ├── AuthService.php         # Servicio de autenticación
│       ├── SecurityManager.php     # CSRF, headers, fingerprinting
│       ├── RateLimiter.php         # Control de rate limiting
│       ├── AuditLogger.php         # Logging de auditoría
│       ├── Validator.php           # Validaciones y sanitización
│       └── helpers.php             # Funciones auxiliares globales
├── vendor/                         # Dependencias de Composer
│   ├── autoload.php                # Autoloader PSR-4
│   └── composer/                   # Metadata de Composer
├── composer.json                   # Definición de dependencias
├── composer.lock                   # Versiones exactas instaladas
├── setup_database.php              # Script de verificación/setup de BD
├── README.md                       # Este archivo
├── INSTALACION.md                  # Guía detallada de instalación
└── SECURITY.md                     # Documentación de seguridad
```

---

## 🌐 API/Endpoints

### Rutas de Autenticación

| Método | Ruta | Descripción | Autenticación |
|--------|------|-------------|---------------|
| GET | `/` | Pantalla de login | No |
| POST | `/auth/login` | Procesar login | No |
| GET | `/auth/logout` | Cerrar sesión | Sí |

### Rutas de Dashboard

| Método | Ruta | Descripción | Rol Requerido |
|--------|------|-------------|---------------|
| GET | `/dashboard` | Dashboard principal | Cualquiera |
| GET | `/dashboard/metrics` | Métricas JSON | Cualquiera |

### Rutas de Usuarios

| Método | Ruta | Descripción | Rol Requerido |
|--------|------|-------------|---------------|
| GET | `/usuarios` | Listar usuarios | Admin |
| GET | `/usuarios/crear` | Formulario crear | Admin |
| POST | `/usuarios/store` | Guardar usuario | Admin |
| GET | `/usuarios/editar/:id` | Formulario editar | Admin |
| POST | `/usuarios/update/:id` | Actualizar usuario | Admin |
| POST | `/usuarios/eliminar/:id` | Eliminar usuario | Admin |

### Rutas de Categorías

| Método | Ruta | Descripción | Rol Requerido |
|--------|------|-------------|---------------|
| GET | `/categorias` | Listar categorías | Admin |
| GET | `/categorias/crear` | Formulario crear | Admin |
| POST | `/categorias/store` | Guardar categoría | Admin |
| GET | `/categorias/editar/:id` | Formulario editar | Admin |
| POST | `/categorias/update/:id` | Actualizar categoría | Admin |
| POST | `/categorias/eliminar/:id` | Eliminar categoría | Admin |

---

## 🔧 Mantenimiento

### Tareas Diarias

- [ ] Revisar logs de acceso fallidos
- [ ] Monitorear IPs sospechosas
- [ ] Verificar que todos los servicios estén corriendo

### Tareas Semanales

- [ ] Revisar estadísticas de acceso
- [ ] Analizar patrones de uso
- [ ] Verificar integridad de respaldos

### Tareas Mensuales

- [ ] Limpiar logs antiguos (>90 días)
- [ ] Actualizar dependencias (Composer)
- [ ] Revisar y actualizar contraseñas
- [ ] Analizar métricas de seguridad

### Comandos Útiles

```bash
# Limpiar logs antiguos (PHP)
php -r "require 'src/Core/AuditLogger.php'; (new AuditLogger())->cleanOldLogs(90);"

# Ver estadísticas
php -r "require 'src/Core/AuditLogger.php'; print_r((new AuditLogger())->getStatistics('30 days'));"

# Exportar logs a CSV
php -r "require 'src/Core/AuditLogger.php'; (new AuditLogger())->exportToCsv('audit_'.date('Y-m-d').'.csv');"

# Backup de base de datos
mysqldump -u root -p cmdb_v2_db > backup_$(date +%Y%m%d).sql
```

### Logs del Sistema

**Ubicación de logs:**

- **PHP errors:** `{htdocs}/error.log` o configurado en `php.ini`
- **Apache errors:** `C:\xampp\apache\logs\error.log` (XAMPP)
- **MySQL errors:** `C:\xampp\mysql\data\*.err` (XAMPP)
- **Audit logs:** Tabla `logs_acceso` en base de datos

---

## 🐛 Solución de Problemas

### Error: "Página en blanco"

**Causa:** Errores PHP no se muestran

**Solución:**
1. Edita `public/index.php` y agrega al inicio:
```php
ini_set('display_errors', 1);
error_reporting(E_ALL);
```
2. Revisa el log de errores de Apache

### Error: "No puedo hacer login"

**Causa:** Base de datos no configurada o usuarios no existen

**Solución:**
```bash
# Ejecutar script de verificación
php setup_database.php

# O manualmente en MySQL:
USE cmdb_v2_db;
SELECT * FROM usuarios;

# Si no hay usuarios, el script los creará
```

### Error: "Access denied for user"

**Causa:** Credenciales MySQL incorrectas

**Solución:**
1. Verifica usuario/contraseña en `config/database.php`
2. En XAMPP por defecto: usuario=`root`, password=`` (vacío)

### Error: "Base de datos no encontrada"

**Causa:** La BD no fue creada

**Solución:**
```bash
# Crear base de datos
mysql -u root -p -e "CREATE DATABASE cmdb_v2_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

# Importar schema
mysql -u root -p cmdb_v2_db < config/database.sql
```

### Error 404 en todas las rutas

**Causa:** `mod_rewrite` no habilitado

**Solución:**
1. Edita `C:\xampp\apache\conf\httpd.conf` (XAMPP)
2. Busca y descomenta:
```apache
LoadModule rewrite_module modules/mod_rewrite.so
```
3. Reinicia Apache

### Error: "CSRF token validation failed"

**Causa:** Sesión expirada o cookies bloqueadas

**Solución:**
1. Limpia cookies del navegador
2. Verifica que tu navegador acepta cookies
3. Recarga la página de login

### Error: "Too many requests"

**Causa:** Rate limit excedido

**Solución:**
1. Espera 15 minutos
2. Si el problema persiste, limpia la tabla `intentos_login`:
```sql
DELETE FROM intentos_login WHERE identifier = 'tu_email@example.com';
```

---

## 🗺️ Roadmap

### En Desarrollo

- [ ] **Módulo de Equipos:** CRUD completo de equipos de inventario
- [ ] **Módulo de Asignaciones:** Asignar/devolver equipos a colaboradores
- [ ] **Portal del Colaborador:** Vista limitada para colaboradores

### Próximas Características (v2.1)

- [ ] **Reportes:**
  - Reporte de equipos por categoría
  - Reporte de asignaciones por usuario
  - Historial de asignaciones
  - Exportación a PDF/Excel

- [ ] **Notificaciones:**
  - Email al asignar equipo
  - Recordatorios de devolución
  - Alertas de equipos dañados

- [ ] **Búsqueda Avanzada:**
  - Filtros múltiples
  - Búsqueda por código QR
  - Búsqueda por fecha

### Características Futuras (v3.0)

- [ ] **Autenticación de Dos Factores (2FA)**
- [ ] **API REST** para integraciones
- [ ] **Aplicación Móvil** (React Native)
- [ ] **Escaneo de códigos QR/Barras**
- [ ] **Sistema de tickets** para soporte técnico
- [ ] **Dashboard de analíticas avanzadas**
- [ ] **Integración con Active Directory**
- [ ] **Notificaciones push**

---

## 🤝 Contribución

Este proyecto es parte de un trabajo académico, pero se aceptan contribuciones.

### Cómo Contribuir

1. Fork el repositorio
2. Crea una rama para tu feature: `git checkout -b feature/nueva-funcionalidad`
3. Commit tus cambios: `git commit -m 'Add: nueva funcionalidad'`
4. Push a la rama: `git push origin feature/nueva-funcionalidad`
5. Abre un Pull Request

### Convenciones de Código

- **PSR-4** para autoloading
- **PSR-12** para estilo de código
- **Nombres de clases:** PascalCase
- **Nombres de métodos:** camelCase
- **Nombres de BD:** snake_case
- **Comentarios:** Español
- **Commits:** Inglés (Add/Fix/Update/Remove)

### Reportar Bugs

Abre un issue en GitHub incluyendo:
- Descripción detallada del problema
- Pasos para reproducir
- Comportamiento esperado vs actual
- Screenshots (si aplica)
- Versión de PHP y MySQL

---

## 📄 Licencia

Este proyecto es parte de un trabajo académico para la **Universidad Tecnológica de Panamá**.

**Todos los derechos reservados © 2025**

---

## 👥 Autor

### Integrante 1: David

**Módulos desarrollados:**
- ✅ Sistema de Autenticación y Sesiones
- ✅ Gestión de Usuarios (CRUD completo)
- ✅ Dashboard con Métricas en Tiempo Real
- ✅ Gestión de Categorías (CRUD completo)
- ✅ Sistema de Seguridad Avanzado
  - Rate Limiting
  - Audit Logging
  - CSRF Protection
  - Device Fingerprinting
  - Headers de Seguridad

**Contacto:**
- Email: david@example.com
- GitHub: [usuario]

---

## 📚 Documentación Adicional

- **[INSTALACION.md](INSTALACION.md)** - Guía detallada de instalación paso a paso
- **[SECURITY.md](SECURITY.md)** - Documentación completa del sistema de seguridad
- **config/database.sql** - Schema SQL con comentarios detallados

---

## 🎓 Referencias

- [OWASP Top 10](https://owasp.org/www-project-top-ten/)
- [PHP Security Cheat Sheet](https://cheatsheetseries.owasp.org/cheatsheets/PHP_Configuration_Cheat_Sheet.html)
- [PSR-4 Autoloader](https://www.php-fig.org/psr/psr-4/)
- [PSR-12 Coding Style](https://www.php-fig.org/psr/psr-12/)
- [Chart.js Documentation](https://www.chartjs.org/docs/latest/)
- [DataTables Documentation](https://datatables.net/manual/)

---

## 🆕 Versión Actual

**v2.0.0** - Implementación completa desde cero

**Fecha de creación:** Noviembre 2025
**Última actualización:** Diciembre 2025

---

## ⭐ Agradecimientos

- Universidad Tecnológica de Panamá
- Profesores del curso
- Compañeros de equipo
- Comunidad de desarrolladores PHP

---

**¿Tienes preguntas?** Abre un issue en GitHub o contacta al equipo de desarrollo.

**¡Gracias por usar CMDB v2!** 🚀