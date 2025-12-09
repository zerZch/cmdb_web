<?php
/**
 * Script para corregir el estado de las asignaciones devueltas
 * Ejecutar una sola vez para arreglar los datos históricos
 */

require_once __DIR__ . '/config/database.php';

try {
    // Conectar a la base de datos usando 127.0.0.1:3306
    $dsn = 'mysql:host=127.0.0.1;port=3306;dbname=' . DB_NAME . ';charset=' . DB_CHARSET;
    $pdo = new PDO($dsn, DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);

    echo "Conectado a la base de datos.\n\n";

    // 1. Mostrar registros con problema
    echo "=== REGISTROS CON ESTADO VACÍO Y FECHA DE DEVOLUCIÓN ===\n";
    $stmt = $pdo->query("
        SELECT id, equipo_id, colaborador_id, fecha_asignacion, fecha_devolucion, estado
        FROM asignaciones
        WHERE estado = '' AND fecha_devolucion IS NOT NULL
        ORDER BY id
    ");
    $registrosProblema = $stmt->fetchAll();

    if (empty($registrosProblema)) {
        echo "No se encontraron registros con problema.\n\n";
    } else {
        echo "Se encontraron " . count($registrosProblema) . " registros con estado vacío:\n";
        foreach ($registrosProblema as $reg) {
            echo "  ID: {$reg['id']}, Equipo: {$reg['equipo_id']}, Colaborador: {$reg['colaborador_id']}, ";
            echo "Fecha Dev: {$reg['fecha_devolucion']}, Estado actual: '{$reg['estado']}'\n";
        }
        echo "\n";

        // 2. Actualizar los registros
        echo "=== ACTUALIZANDO REGISTROS ===\n";
        $updateStmt = $pdo->prepare("
            UPDATE asignaciones
            SET estado = 'devuelta'
            WHERE estado = '' AND fecha_devolucion IS NOT NULL
        ");
        $updateStmt->execute();

        $rowsAffected = $updateStmt->rowCount();
        echo "✓ Se actualizaron {$rowsAffected} registros a estado 'devuelta'.\n\n";
    }

    // 3. Verificar el resultado
    echo "=== VERIFICACIÓN FINAL ===\n";
    $stmt = $pdo->query("
        SELECT id, equipo_id, colaborador_id, fecha_asignacion, fecha_devolucion, estado
        FROM asignaciones
        WHERE fecha_devolucion IS NOT NULL
        ORDER BY fecha_devolucion DESC
        LIMIT 10
    ");
    $registrosDevueltos = $stmt->fetchAll();

    echo "Últimas 10 asignaciones devueltas:\n";
    foreach ($registrosDevueltos as $reg) {
        echo "  ID: {$reg['id']}, Equipo: {$reg['equipo_id']}, Colaborador: {$reg['colaborador_id']}, ";
        echo "Fecha Dev: {$reg['fecha_devolucion']}, Estado: '{$reg['estado']}'\n";
    }

    echo "\n✓ Corrección completada exitosamente.\n";

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
    exit(1);
}
