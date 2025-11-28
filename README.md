# CMDB - Sistema de Gestión de Inventario v2

Sistema de gestión de inventario de equipos desarrollado en PHP puro con arquitectura MVC.

## 📋 Características Implementadas

### ✅ Integrante 1: Autenticación + Usuarios + Dashboard

#### Backend:
- ✅ Login con validación de correo, contraseña y estado
- ✅ Logout (destrucción de sesión)
- ✅ CRUD completo de Usuarios del Sistema
- ✅ Manejo de roles (Administrador / Colaborador)
- ✅ CRUD completo de Categorías
- ✅ Protección de rutas según rol
- ✅ Dashboard del Administrador con métricas:
  - Total de Equipos
  - Equipos Disponibles
  - Equipos Asignados
  - Equipos Dañados
  - Equipos en Mantenimiento

#### Frontend:
- ✅ Pantalla de Login moderna y responsiva
- ✅ Dashboard con tarjetas de resumen
- ✅ Gráficos interactivos (Chart.js)
- ✅ Interfaz administrativa completa
- ✅ DataTables para gestión de datos
- ✅ SweetAlert2 para mensajes

## 🚀 Instalación

### Requisitos Previos
- PHP 7.4 o superior
- MySQL 5.7 o superior
- Apache con mod_rewrite habilitado
- Composer

### Pasos de Instalación

1. **Clonar el repositorio**
```bash
git clone <url-del-repositorio>
cd cmdb_project
```

2. **Instalar dependencias**
```bash
composer install
```

3. **Configurar la base de datos**

Editar el archivo `config/database.php` con tus credenciales:
```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'cmdb_v2_db');
define('DB_USER', 'root');
define('DB_PASS', '');
```

4. **Crear la base de datos**

Ejecutar el script SQL ubicado en `config/database.sql`:
```bash
mysql -u root -p < config/database.sql
```

O importarlo manualmente desde phpMyAdmin.

5. **Configurar la URL base**

Editar el archivo `config/app.php`:
```php
define('BASE_URL', 'http://localhost/cmdb_project/public/');
```

Ajustar según tu configuración local.

6. **Configurar el servidor web**

Si usas XAMPP/WAMP, colocar el proyecto en la carpeta `htdocs`.

El archivo `.htaccess` ya está configurado en `public/.htaccess`.

## 🔐 Credenciales de Acceso

### Administrador
- **Email:** admin@cmdb.com
- **Contraseña:** admin123

### Colaborador
- **Email:** colaborador@cmdb.com
- **Contraseña:** colab123

## 📁 Estructura del Proyecto

```
cmdb_project/
├── config/
│   ├── app.php              # Configuración general
│   ├── database.php         # Configuración de BD
│   └── database.sql         # Script SQL
├── public/
│   ├── index.php           # Punto de entrada
│   ├── .htaccess           # Configuración Apache
│   ├── assets/             # CSS, JS, imágenes
│   └── uploads/            # Archivos subidos
├── src/
│   ├── Controllers/        # Controladores
│   │   ├── AuthController.php
│   │   ├── DashboardController.php
│   │   ├── UsuarioController.php
│   │   ├── CategoriaController.php
│   │   └── BaseController.php
│   ├── Models/             # Modelos
│   │   ├── Usuario.php
│   │   ├── Categoria.php
│   │   ├── Equipo.php
│   │   └── Model.php
│   ├── Views/              # Vistas
│   │   ├── auth/
│   │   ├── dashboard/
│   │   ├── usuarios/
│   │   ├── categorias/
│   │   └── layouts/
│   └── Core/               # Núcleo del sistema
│       ├── Database.php
│       ├── AuthService.php
│       └── helpers.php
├── vendor/                 # Dependencias de Composer
├── composer.json
└── README.md
```

## 🎨 Tecnologías Utilizadas

### Backend
- PHP 7.4+ (Puro, sin frameworks)
- MySQL
- PDO para acceso a base de datos
- PSR-4 Autoloading

### Frontend
- Bootstrap 5
- Font Awesome 6
- jQuery 3.7
- DataTables
- SweetAlert2
- Chart.js

### Arquitectura
- MVC (Model-View-Controller)
- Patrón Singleton para BD
- Sistema de routing personalizado
- Separación de responsabilidades

## 🔒 Seguridad Implementada

- ✅ Contraseñas encriptadas con `password_hash()`
- ✅ Validación de entrada de datos
- ✅ Protección contra SQL Injection (PDO prepared statements)
- ✅ Protección XSS (escape de HTML)
- ✅ Validación de roles para acceso a rutas
- ✅ Sesiones seguras con regeneración de ID
- ✅ Validación de estado de usuario (activo/inactivo)

## 📊 Funcionalidades del Dashboard

### Métricas en Tiempo Real
- Total de equipos en el sistema
- Equipos disponibles para asignación
- Equipos actualmente asignados
- Equipos dañados que requieren atención
- Equipos en mantenimiento

### Gráficos Interactivos
- Distribución de equipos por categoría (Gráfico de pastel)
- Estado actual de equipos (Gráfico de barras)

### Panel Administrativo
Solo visible para usuarios con rol **Administrador**:
- Gestión completa de usuarios
- Gestión de categorías
- Activación/desactivación de usuarios
- Asignación de roles

## 🛠️ Gestión de Usuarios

### Funcionalidades CRUD:
- **Crear:** Nuevo usuario con validación de email único
- **Leer:** Listado con DataTables (búsqueda, ordenamiento, paginación)
- **Actualizar:** Edición de datos y cambio de contraseña opcional
- **Eliminar:** Eliminación con confirmación (no se puede auto-eliminar)

### Campos del Usuario:
- Nombre y apellido
- Email (único)
- Contraseña (encriptada)
- Rol (admin/colaborador)
- Estado (activo/inactivo)
- Foto de perfil (opcional)

## 📦 Gestión de Categorías

### Funcionalidades CRUD:
- **Crear:** Nueva categoría con nombre único
- **Leer:** Listado con conteo de equipos asociados
- **Actualizar:** Edición de nombre y descripción
- **Eliminar:** Solo si no tiene equipos asociados

### Protección de Datos:
- No se pueden eliminar categorías con equipos asociados
- Validación de nombre único
- Estados activa/inactiva

## 🗄️ Base de Datos

### Tablas Creadas:
1. **usuarios** - Gestión de usuarios del sistema
2. **categorias** - Categorías de equipos
3. **equipos** - Inventario de equipos
4. **asignaciones** - Registro de asignaciones

### Relaciones:
- Equipos → Categorías (Foreign Key)
- Asignaciones → Equipos (Foreign Key)
- Asignaciones → Usuarios (Foreign Key)

## 🎯 Próximas Características

Las siguientes funcionalidades serán implementadas por otros integrantes:

- **Integrante 2:** Gestión de Inventario de Equipos
- **Integrante 3:** Asignaciones y Portal del Colaborador
- **Integrante 4:** Reportes y Exportación

## 📝 Notas de Desarrollo

### Convenciones de Código:
- PSR-4 para autoloading
- Nombres de clases en PascalCase
- Nombres de métodos en camelCase
- Nombres de tablas y columnas en snake_case
- Comentarios en español

### Buenas Prácticas:
- Separación de lógica de negocio (Controllers)
- Modelos para acceso a datos
- Vistas con escape de HTML
- Validación en backend y frontend
- Mensajes de usuario amigables

## 🐛 Solución de Problemas

### Error de conexión a BD:
```
Error de conexión a la base de datos
```
**Solución:** Verificar credenciales en `config/database.php`

### Página en blanco:
**Solución:** Verificar que mod_rewrite está habilitado en Apache

### Error 404 en todas las rutas:
**Solución:** Verificar que `.htaccess` existe en `public/`

### Las sesiones no funcionan:
**Solución:** Verificar permisos de escritura en carpeta de sesiones de PHP

## 👥 Autor

**David** - Integrante 1
- Autenticación y Seguridad
- Gestión de Usuarios
- Dashboard con Métricas
- Gestión de Categorías

## 📄 Licencia

Este proyecto es parte de un trabajo académico.

## 🆕 Versión

**v2.0** - Nueva implementación desde cero

---

**Fecha de creación:** Noviembre 2025
**Última actualización:** <?= date('d/m/Y') ?>
