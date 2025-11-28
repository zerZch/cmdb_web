<?php
/**
 * Script de configuración y verificación de base de datos
 */

echo "=== CMDB v2 - Configuración de Base de Datos ===\n\n";

// Cargar configuración
require_once 'config/database.php';

try {
    // Conectar a MySQL (sin seleccionar base de datos)
    $dsn = 'mysql:host=' . DB_HOST . ';charset=' . DB_CHARSET;
    $pdo = new PDO($dsn, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "✓ Conexión a MySQL exitosa\n";

    // Verificar si la base de datos existe
    $stmt = $pdo->query("SHOW DATABASES LIKE '" . DB_NAME . "'");
    $dbExists = $stmt->rowCount() > 0;

    if ($dbExists) {
        echo "✓ Base de datos '" . DB_NAME . "' encontrada\n";

        // Conectar a la base de datos específica
        $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=' . DB_CHARSET;
        $pdo = new PDO($dsn, DB_USER, DB_PASS);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Verificar tablas
        $stmt = $pdo->query("SHOW TABLES");
        $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);

        echo "\nTablas encontradas:\n";
        foreach ($tables as $table) {
            echo "  - $table\n";
        }

        // Verificar usuarios
        echo "\n=== Verificando usuarios ===\n";
        $stmt = $pdo->query("SELECT id, nombre, email, rol, estado FROM usuarios");
        $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (count($usuarios) > 0) {
            echo "Usuarios en la base de datos:\n";
            foreach ($usuarios as $usuario) {
                echo sprintf(
                    "  - ID: %d | %s | %s | Rol: %s | Estado: %s\n",
                    $usuario['id'],
                    $usuario['nombre'],
                    $usuario['email'],
                    $usuario['rol'],
                    $usuario['estado']
                );
            }
        } else {
            echo "⚠ No hay usuarios en la base de datos\n";
            echo "\nCreando usuarios de prueba...\n";

            // Crear usuario admin
            $adminPass = password_hash('admin123', PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO usuarios (nombre, apellido, email, password, rol, estado) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute(['Admin', 'Sistema', 'admin@cmdb.com', $adminPass, 'admin', 'activo']);
            echo "✓ Usuario admin creado: admin@cmdb.com / admin123\n";

            // Crear usuario colaborador
            $colabPass = password_hash('colab123', PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO usuarios (nombre, apellido, email, password, rol, estado) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute(['Juan', 'Pérez', 'colaborador@cmdb.com', $colabPass, 'colaborador', 'activo']);
            echo "✓ Usuario colaborador creado: colaborador@cmdb.com / colab123\n";
        }

        echo "\n=== Configuración completada ===\n";
        echo "Puedes acceder al sistema con:\n";
        echo "  Admin: admin@cmdb.com / admin123\n";
        echo "  Colaborador: colaborador@cmdb.com / colab123\n";

    } else {
        echo "⚠ Base de datos '" . DB_NAME . "' NO encontrada\n";
        echo "\nPor favor ejecuta el siguiente comando para crear la base de datos:\n";
        echo "mysql -u " . DB_USER . " -p < config/database.sql\n";
        echo "\nO importa el archivo config/database.sql desde phpMyAdmin\n";
    }

} catch (PDOException $e) {
    echo "\n✗ Error: " . $e->getMessage() . "\n";
    echo "\nVerifica:\n";
    echo "1. Que MySQL esté ejecutándose\n";
    echo "2. Las credenciales en config/database.php sean correctas\n";
    echo "3. El usuario tenga permisos para crear bases de datos\n";
}
