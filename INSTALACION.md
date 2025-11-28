# 🚀 Guía de Instalación CMDB v2

## ⚠️ IMPORTANTE: Tu problema de login

Si no puedes entrar con las credenciales, es porque **la base de datos no está configurada correctamente**. Sigue estos pasos:

---

## 📋 Paso 1: Requisitos Previos

Asegúrate de tener instalado:
- **XAMPP** o **WAMP** (incluye Apache, MySQL y PHP)
- Navegador web

---

## 🔧 Paso 2: Configurar el Proyecto

### 2.1 Ubicar el proyecto
1. Copia la carpeta `cmdb_project` a:
   - **XAMPP**: `C:\xampp\htdocs\`
   - **WAMP**: `C:\wamp64\www\`

La ruta final debe ser:
- XAMPP: `C:\xampp\htdocs\cmdb_project\`
- WAMP: `C:\wamp64\www\cmdb_project\`

### 2.2 Iniciar servicios
1. Abre el Panel de Control de XAMPP/WAMP
2. Inicia **Apache**
3. Inicia **MySQL**

---

## 💾 Paso 3: Crear la Base de Datos

### Opción A: Usando phpMyAdmin (Recomendado)

1. **Abre phpMyAdmin**:
   - En tu navegador ve a: `http://localhost/phpmyadmin`

2. **Crear la base de datos**:
   - Clic en "Nueva" (o "New") en el panel izquierdo
   - Nombre de la base de datos: `cmdb_v2_db`
   - Cotejamiento: `utf8mb4_unicode_ci`
   - Clic en "Crear"

3. **Importar las tablas y datos**:
   - Selecciona la base de datos `cmdb_v2_db` que acabas de crear
   - Clic en la pestaña "Importar" (o "Import")
   - Clic en "Seleccionar archivo" (o "Choose File")
   - Busca y selecciona: `cmdb_project/config/database.sql`
   - Clic en "Continuar" o "Go" al final de la página

4. **Verificar**:
   - Deberías ver un mensaje "Importación finalizada correctamente"
   - En el panel izquierdo verás las tablas: `usuarios`, `categorias`, `equipos`, `asignaciones`

### Opción B: Usando línea de comandos

```bash
# En Windows (desde la carpeta de XAMPP/WAMP)
cd C:\xampp\mysql\bin
mysql -u root -p

# Luego ejecuta:
CREATE DATABASE cmdb_v2_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE cmdb_v2_db;
SOURCE C:/xampp/htdocs/cmdb_project/config/database.sql;
exit;
```

---

## ⚙️ Paso 4: Configurar la Conexión

1. **Abrir el archivo de configuración**:
   - Archivo: `cmdb_project/config/database.php`

2. **Ajustar credenciales** (si es necesario):

```php
<?php
define('DB_HOST', 'localhost');
define('DB_NAME', 'cmdb_v2_db');
define('DB_USER', 'root');
define('DB_PASS', '');  // Vacío para XAMPP, o tu contraseña de MySQL
define('DB_CHARSET', 'utf8mb4');
```

3. **Configurar URL base**:
   - Archivo: `cmdb_project/config/app.php`
   - Ajusta la línea:

```php
define('BASE_URL', 'http://localhost/cmdb_project/public/');
```

---

## 🧪 Paso 5: Verificar la Instalación

1. **Script de verificación** (desde la carpeta del proyecto):

```bash
# Desde terminal/cmd en la carpeta del proyecto
php setup_database.php
```

Este script te dirá:
- ✓ Si la conexión a MySQL funciona
- ✓ Si la base de datos existe
- ✓ Qué tablas están creadas
- ✓ Qué usuarios existen

2. **Si el script reporta que NO hay usuarios**, te los creará automáticamente.

---

## 🌐 Paso 6: Acceder al Sistema

1. **Abre tu navegador**

2. **Ve a la URL**:
   ```
   http://localhost/cmdb_project/public/
   ```

3. **Credenciales de acceso**:

   **👨‍💼 Administrador:**
   - Email: `admin@cmdb.com`
   - Password: `admin123`

   **👤 Colaborador:**
   - Email: `colaborador@cmdb.com`
   - Password: `colab123`

---

## 🐛 Solución de Problemas

### ❌ Error: "Página en blanco"

**Causa**: Errores PHP no se muestran o falta configuración

**Solución**:
1. Verifica que Apache esté corriendo
2. Verifica que accedes a `/public/` en la URL
3. Revisa los logs de Apache:
   - XAMPP: `C:\xampp\apache\logs\error.log`

### ❌ Error: "No puedo hacer login"

**Causa**: Base de datos no configurada o contraseñas incorrectas

**Solución**:
1. Ejecuta `php setup_database.php` para verificar
2. Ve a phpMyAdmin → `cmdb_v2_db` → tabla `usuarios`
3. Verifica que existan los usuarios
4. Si no existen, ejecuta manualmente:

```sql
-- Borrar usuarios antiguos (si existen)
DELETE FROM usuarios;

-- Crear usuario admin
INSERT INTO usuarios (nombre, apellido, email, password, rol, estado)
VALUES ('Admin', 'Sistema', 'admin@cmdb.com', '$2y$12$aG9QDC/sgwzKULAVzazsGulYqHazTGxHMm0mviuFbSlnPoFi.6g.i', 'admin', 'activo');

-- Crear usuario colaborador
INSERT INTO usuarios (nombre, apellido, email, password, rol, estado)
VALUES ('Juan', 'Pérez', 'colaborador@cmdb.com', '$2y$12$s2oP1y.OLpNAxQWZr60mU.HuHRX6Rg2KVP8K61XBojNJ96cP5qqZ2', 'colaborador', 'activo');
```

### ❌ Error: "Access denied for user"

**Causa**: Credenciales de MySQL incorrectas

**Solución**:
1. Edita `config/database.php`
2. Verifica usuario y contraseña de MySQL
3. Por defecto en XAMPP:
   - Usuario: `root`
   - Password: `` (vacío)

### ❌ Error: "Base de datos no encontrada"

**Causa**: La base de datos no fue creada

**Solución**:
1. Abre phpMyAdmin
2. Crea la base de datos `cmdb_v2_db`
3. Importa el archivo `config/database.sql`

### ❌ Error 404 en todas las páginas

**Causa**: Archivo `.htaccess` no está funcionando

**Solución**:
1. Verifica que `mod_rewrite` esté habilitado en Apache
2. En XAMPP, edita `C:\xampp\apache\conf\httpd.conf`
3. Busca y descomenta (quita el #):
   ```
   LoadModule rewrite_module modules/mod_rewrite.so
   ```
4. Reinicia Apache

---

## ✅ Checklist de Instalación

- [ ] XAMPP/WAMP instalado y corriendo
- [ ] Proyecto en htdocs/www
- [ ] Apache iniciado
- [ ] MySQL iniciado
- [ ] Base de datos `cmdb_v2_db` creada
- [ ] Archivo `database.sql` importado
- [ ] Configuración en `config/database.php` correcta
- [ ] URL base en `config/app.php` correcta
- [ ] Script `setup_database.php` ejecutado sin errores
- [ ] Puedo acceder a `http://localhost/cmdb_project/public/`
- [ ] Puedo hacer login con admin@cmdb.com / admin123

---

## 📞 Si Aún Tienes Problemas

1. **Verifica los logs de PHP**:
   - Temporal: Agrega al inicio de `public/index.php`:
   ```php
   ini_set('display_errors', 1);
   error_reporting(E_ALL);
   ```

2. **Verifica la conexión a la base de datos**:
   - Crea un archivo `test_conexion.php` en la raíz:
   ```php
   <?php
   require 'config/database.php';
   try {
       $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME;
       $pdo = new PDO($dsn, DB_USER, DB_PASS);
       echo "✓ Conexión exitosa a la base de datos!";
   } catch (PDOException $e) {
       echo "✗ Error: " . $e->getMessage();
   }
   ```
   - Accede a: `http://localhost/cmdb_project/test_conexion.php`

3. **Verifica que las tablas existen**:
   - En phpMyAdmin, selecciona `cmdb_v2_db`
   - Deberías ver 4 tablas: `asignaciones`, `categorias`, `equipos`, `usuarios`

---

## 🎉 ¡Listo!

Una vez completados todos los pasos, deberías poder:
- ✅ Acceder al sistema de login
- ✅ Iniciar sesión con las credenciales de prueba
- ✅ Ver el dashboard con métricas
- ✅ Gestionar usuarios (como admin)
- ✅ Gestionar categorías (como admin)

**¡Disfruta del sistema CMDB v2!**
