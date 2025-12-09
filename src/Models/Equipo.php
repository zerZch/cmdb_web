<?php
// src/Models/Equipo.php

namespace App\Models;

use App\Core\Database;
use PDO;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelHigh;
use Endroid\QrCode\RoundBlockSizeMode\RoundBlockSizeModeMargin;
use Endroid\QrCode\Color\Color;

class Equipo extends Model
{
    protected $table = 'equipos';
    
    /**
     * Obtener todos los equipos con su categoría
     */
    public function getAllWithCategoria()
    {
        $sql = "SELECT e.*, c.nombre as categoria_nombre 
                FROM {$this->table} e
                LEFT JOIN categorias c ON e.categoria_id = c.id
                ORDER BY e.created_at DESC";
        
        return $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtener conexión a la base de datos
     */
    public function getConnection()
    {
        return $this->db;
    }

    /**
     * Obtener equipo por ID con categoría
     */
    public function getByIdWithCategoria($id)
    {
        $sql = "SELECT e.*, c.nombre as categoria_nombre 
                FROM {$this->table} e
                LEFT JOIN categorias c ON e.categoria_id = c.id
                WHERE e.id = ?";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    /**
     * ✅ NUEVO: Sobrescribir find() para SIEMPRE traer categoria_nombre
     */
    public function updateEstado($id, $nuevoEstado) {
    // 1. Obtener la conexión a la base de datos (Ejemplo: $this->db)
    //    Asegúrate de que tu modelo tenga acceso a la conexión DB.
    
    // 2. Definir la consulta SQL para actualizar la tabla 'equipos'
    $sql = "UPDATE equipos SET estado = :estado WHERE id = :id";
    
    // 3. Ejecutar la consulta con los parámetros
    //    (Este es un ejemplo, adáptalo a la forma en que ejecutas consultas)
    $stmt = $this->db->prepare($sql);
    
    // 4. Bind y ejecución
    $stmt->bindParam(':estado', $nuevoEstado);
    $stmt->bindParam(':id', $id);
    
    return $stmt->execute();
}
    public function find($id)
    {
        return $this->getByIdWithCategoria($id);
    }
    
    /**
     * Crear nuevo equipo
     */
    public function create($data)
    {
        // Generar código de inventario único
        $data['codigo_inventario'] = $this->generateCodigoInventario();
        
        $sql = "INSERT INTO {$this->table} 
                (codigo_inventario, categoria_id, marca, modelo, numero_serie, 
                 fecha_adquisicion, costo_adquisicion, vida_util_anos, valor_residual,
                 descripcion, foto, ubicacion, estado)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $this->db->prepare($sql);
        $result = $stmt->execute([
            $data['codigo_inventario'],
            $data['categoria_id'],
            $data['marca'],
            $data['modelo'],
            $data['numero_serie'],
            $data['fecha_adquisicion'],
            $data['costo_adquisicion'],
            $data['vida_util_anos'] ?? 5,
            $data['valor_residual'] ?? 0,
            $data['descripcion'] ?? null,
            $data['foto'] ?? null,
            $data['ubicacion'] ?? null,
            $data['estado'] ?? 'disponible'
        ]);
        
        return $result ? $this->db->lastInsertId() : false;
    }
    
    /**
     * Actualizar equipo
     */
    public function update($id, $data)
    {
        $sql = "UPDATE {$this->table} SET
                categoria_id = ?,
                marca = ?,
                modelo = ?,
                numero_serie = ?,
                fecha_adquisicion = ?,
                costo_adquisicion = ?,
                vida_util_anos = ?,
                valor_residual = ?,
                descripcion = ?,
                foto = ?,
                ubicacion = ?,
                estado = ?,
                updated_at = CURRENT_TIMESTAMP
                WHERE id = ?";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            $data['categoria_id'],
            $data['marca'],
            $data['modelo'],
            $data['numero_serie'],
            $data['fecha_adquisicion'],
            $data['costo_adquisicion'],
            $data['vida_util_anos'] ?? 5,
            $data['valor_residual'] ?? 0,
            $data['descripcion'] ?? null,
            $data['foto'] ?? null,
            $data['ubicacion'] ?? null,
            $data['estado'],
            $id
        ]);
    }
    
    /**
     * Actualizar código QR
     */
    public function updateQrCode($id, $qrPath)
    {
        $sql = "UPDATE {$this->table} SET codigo_qr = ? WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$qrPath, $id]);
    }
    
    /**
     * Generar código de inventario único
     */
    private function generateCodigoInventario()
    {
        $year = date('Y');
        $prefix = 'EQ';
        
        // Obtener el último número del año
        $sql = "SELECT codigo_inventario FROM {$this->table} 
                WHERE codigo_inventario LIKE ? 
                ORDER BY codigo_inventario DESC LIMIT 1";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute(["{$prefix}-{$year}-%"]);
        $last = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($last) {
            // Extraer número y sumar 1
            preg_match('/(\d+)$/', $last['codigo_inventario'], $matches);
            $number = isset($matches[1]) ? intval($matches[1]) + 1 : 1;
        } else {
            $number = 1;
        }
        
        return sprintf("%s-%s-%04d", $prefix, $year, $number);
    }
    
    /**
     * Verificar si número de serie ya existe
     */
    public function serieExists($serie, $excludeId = null)
    {
        $sql = "SELECT id FROM {$this->table} WHERE numero_serie = ?";
        $params = [$serie];
        
        if ($excludeId) {
            $sql .= " AND id != ?";
            $params[] = $excludeId;
        }
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetch(PDO::FETCH_ASSOC) !== false;
    }

    /**
     * ✅ CORREGIDO: Generar QR con validación de campos
     */
    public function generarQr($equipo)
    {
        try {
            // ✅ VALIDACIÓN: Si no tiene categoria_nombre, la obtenemos
            if (!isset($equipo['categoria_nombre'])) {
                $equipoCompleto = $this->getByIdWithCategoria($equipo['id']);
                if ($equipoCompleto) {
                    $equipo = $equipoCompleto;
                }
            }

            // ✅ Usar valores seguros con operador null coalescing
            $qrData = json_encode([
                'id'            => $equipo['id'] ?? 'N/A',
                'marca'         => $equipo['marca'] ?? 'Sin marca',
                'modelo'        => $equipo['modelo'] ?? 'Sin modelo',
                'numero_serie'  => $equipo['numero_serie'] ?? 'S/N',
                'categoria'     => $equipo['categoria_nombre'] ?? 'Sin categoría'
            ]);

            // Ruta donde se guardará el QR
            $qrPath = __DIR__ . "/../../public/qr/" . $equipo['id'] . ".png";

            // Crear directorio si no existe
            $qrDir = dirname($qrPath);
            if (!is_dir($qrDir)) {
                mkdir($qrDir, 0777, true);
            }

            // Crear QR
            $qrCode = QrCode::create($qrData)
                ->setEncoding(new Encoding('UTF-8'))
                ->setErrorCorrectionLevel(new ErrorCorrectionLevelHigh())
                ->setSize(300)
                ->setMargin(10)
                ->setRoundBlockSizeMode(new RoundBlockSizeModeMargin())
                ->setForegroundColor(new Color(0, 0, 0))
                ->setBackgroundColor(new Color(255, 255, 255));

            $writer = new PngWriter();
            $result = $writer->write($qrCode);

            // Guardar en archivo
            $result->saveToFile($qrPath);

            return [
                "success" => true,
                "path"    => "qr/" . $equipo['id'] . ".png"
            ];

        } catch (\Exception $e) {
            error_log("Error generando QR para equipo {$equipo['id']}: " . $e->getMessage());
            
            return [
                "success" => false,
                "message" => "Error generando QR: " . $e->getMessage()
            ];
        }
    }

    /**
     * Calcular depreciación de un equipo
     */
    public function calcularDepreciacion($equipo)
    {
        // ✅ VALIDACIÓN: Verificar que existan los datos necesarios
        if (empty($equipo['fecha_adquisicion']) || empty($equipo['costo_adquisicion'])) {
            return [
                'meses_transcurridos' => 0,
                'depreciacion_mensual' => 0,
                'depreciacion_acumulada' => 0,
                'valor_libro' => $equipo['costo_adquisicion'] ?? 0,
                'porcentaje_depreciado' => 0,
                'completamente_depreciado' => false
            ];
        }

        try {
            // Calcular meses transcurridos desde la adquisición
            $fechaAdquisicion = new \DateTime($equipo['fecha_adquisicion']);
            $fechaActual = new \DateTime();
            $intervalo = $fechaAdquisicion->diff($fechaActual);
            $mesesTranscurridos = ($intervalo->y * 12) + $intervalo->m;
            
            // Datos base
            $costoAdquisicion = (float) $equipo['costo_adquisicion'];
            $vidaUtil = !empty($equipo['vida_util_anos']) ? (int) $equipo['vida_util_anos'] : 5;
            $valorResidual = !empty($equipo['valor_residual']) ? (float) $equipo['valor_residual'] : 0;
            
            // Cálculos de depreciación
            $mesesVidaUtil = $vidaUtil * 12;
            $depreciacionTotal = $costoAdquisicion - $valorResidual;
            $depreciacionMensual = $mesesVidaUtil > 0 ? $depreciacionTotal / $mesesVidaUtil : 0;
            
            // Depreciación acumulada (no puede exceder la depreciación total)
            $depreciacionAcumulada = min($depreciacionMensual * $mesesTranscurridos, $depreciacionTotal);
            
            // Valor en libros (no puede ser menor al valor residual)
            $valorLibro = max($costoAdquisicion - $depreciacionAcumulada, $valorResidual);
            
            // Porcentaje depreciado
            $porcentajeDepreciado = $depreciacionTotal > 0 
                ? ($depreciacionAcumulada / $depreciacionTotal) * 100 
                : 0;
            
            // Verificar si está completamente depreciado
            $completamenteDepreciado = $mesesTranscurridos >= $mesesVidaUtil;
            
            return [
                'meses_transcurridos' => $mesesTranscurridos,
                'depreciacion_mensual' => round($depreciacionMensual, 2),
                'depreciacion_acumulada' => round($depreciacionAcumulada, 2),
                'valor_libro' => round($valorLibro, 2),
                'porcentaje_depreciado' => round($porcentajeDepreciado, 2),
                'completamente_depreciado' => $completamenteDepreciado
            ];
            
        } catch (\Exception $e) {
            error_log("Error en calcularDepreciacion: " . $e->getMessage());
            
            return [
                'meses_transcurridos' => 0,
                'depreciacion_mensual' => 0,
                'depreciacion_acumulada' => 0,
                'valor_libro' => $equipo['costo_adquisicion'] ?? 0,
                'porcentaje_depreciado' => 0,
                'completamente_depreciado' => false
            ];
        }
    }
    
    /**
     * Obtener reporte de depreciación
     */
    public function getReporteDepreciacion($filtros = [])
    {
        $sql = "SELECT 
                e.id,
                e.codigo_inventario,
                e.marca,
                e.modelo,
                e.numero_serie,
                c.nombre AS categoria,
                e.fecha_adquisicion,
                e.costo_adquisicion,
                e.vida_util_anos,
                e.valor_residual,
                e.estado,
                
                -- Calcular meses transcurridos
                TIMESTAMPDIFF(MONTH, e.fecha_adquisicion, CURRENT_DATE) AS meses_transcurridos,
                
                -- Depreciación mensual
                ROUND((e.costo_adquisicion - COALESCE(e.valor_residual, 0)) / (e.vida_util_anos * 12), 2) AS depreciacion_mensual,
                
                -- Depreciación acumulada
                ROUND(
                    LEAST(
                        (e.costo_adquisicion - COALESCE(e.valor_residual, 0)) / (e.vida_util_anos * 12) * TIMESTAMPDIFF(MONTH, e.fecha_adquisicion, CURRENT_DATE),
                        e.costo_adquisicion - COALESCE(e.valor_residual, 0)
                    ), 
                    2
                ) AS depreciacion_acumulada,
                
                -- Valor en libros
                ROUND(
                    GREATEST(
                        e.costo_adquisicion - (
                            (e.costo_adquisicion - COALESCE(e.valor_residual, 0)) / (e.vida_util_anos * 12) * TIMESTAMPDIFF(MONTH, e.fecha_adquisicion, CURRENT_DATE)
                        ),
                        COALESCE(e.valor_residual, 0)
                    ),
                    2
                ) AS valor_libro,
                
                -- Porcentaje depreciado
                ROUND(
                    LEAST(
                        ((e.costo_adquisicion - COALESCE(e.valor_residual, 0)) / (e.vida_util_anos * 12) * TIMESTAMPDIFF(MONTH, e.fecha_adquisicion, CURRENT_DATE)) / e.costo_adquisicion * 100,
                        100
                    ),
                    2
                ) AS porcentaje_depreciado
                
            FROM {$this->table} e
            LEFT JOIN categorias c ON e.categoria_id = c.id
            WHERE e.estado NOT IN ('dado_de_baja', 'donado')
            AND e.fecha_adquisicion IS NOT NULL
            AND e.costo_adquisicion IS NOT NULL";
        
        $params = [];
        
        // Aplicar filtros
        if (!empty($filtros['categoria_id'])) {
            $sql .= " AND e.categoria_id = ?";
            $params[] = $filtros['categoria_id'];
        }
        
        if (!empty($filtros['estado'])) {
            $sql .= " AND e.estado = ?";
            $params[] = $filtros['estado'];
        }
        
        if (!empty($filtros['ano'])) {
            $sql .= " AND YEAR(e.fecha_adquisicion) = ?";
            $params[] = $filtros['ano'];
        }
        
        $sql .= " ORDER BY porcentaje_depreciado DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
    
    /**
     * Obtener estadísticas de equipos
     */
    public function getEstadisticas()
    {
        $sql = "SELECT 
                COUNT(*) as total,
                SUM(CASE WHEN estado = 'disponible' THEN 1 ELSE 0 END) as disponibles,
                SUM(CASE WHEN estado = 'asignado' THEN 1 ELSE 0 END) as asignados,
                SUM(CASE WHEN estado = 'mantenimiento' THEN 1 ELSE 0 END) as mantenimiento,
                SUM(CASE WHEN estado = 'dañado' THEN 1 ELSE 0 END) as danados,
                SUM(costo_adquisicion) as valor_total
                FROM {$this->table}
                WHERE estado != 'baja'";
        
        return $this->db->query($sql)->fetch(PDO::FETCH_ASSOC);
    }
    
    /**
     * Obtener equipos por categoría para gráficos
     */
    public function getPorCategoria()
    {
        $sql = "SELECT c.nombre AS categoria, COUNT(e.id) AS total
                FROM categorias c
                LEFT JOIN {$this->table} e ON c.id = e.categoria_id AND e.estado != 'baja'
                WHERE c.estado = 'activa'
                GROUP BY c.id, c.nombre
                ORDER BY total DESC";

        return $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtener equipo por ID
     */
    public function getById($id)
    {
        $sql = "SELECT * FROM {$this->table} WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Obtener todos los equipos
     */
    public function getAll()
    {
        $sql = "SELECT * FROM {$this->table} ORDER BY created_at DESC";
        return $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Obtener todos los números de serie existentes
     */
    public function getAllSerials()
    {
        $sql = "SELECT numero_serie FROM {$this->table}";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    /**
     * Verificar si un equipo existe
     */
    public function exists($id)
    {
        $sql = "SELECT COUNT(*) FROM {$this->table} WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetchColumn() > 0;
    }

    /**
     * Eliminar equipo (eliminación lógica)
     */
    public function delete($id)
    {
        $sql = "DELETE FROM {$this->table} WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$id]);
    }
}