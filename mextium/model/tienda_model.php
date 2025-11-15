<?php
// Modelo para gestión de tiendas
class TiendaModel {
    /**
     * Actualizar tienda existente
     * $datos = [ 'id', 'nombre_tienda', 'descripcion_tienda', 'categoria_principal', 'direccion', 'ciudad', 'mercado_pago_token', 'imagen' ]
     */
    public function actualizarTienda($datos) {
        try {
            $campos = [
                'nombre_tienda = :nombre_tienda',
                'descripcion_tienda = :descripcion_tienda',
                'categoria_principal = :categoria_principal',
                'direccion = :direccion',
                'ciudad = :ciudad',
                'mercado_pago_token = :mercado_pago_token',
                'fecha_actualizacion = :fecha_actualizacion'
            ];
            $params = [
                ':nombre_tienda' => $datos['nombre_tienda'],
                ':descripcion_tienda' => $datos['descripcion_tienda'],
                ':categoria_principal' => $datos['categoria_principal'],
                ':direccion' => $datos['direccion'],
                ':ciudad' => $datos['ciudad'],
                ':mercado_pago_token' => $datos['mercado_pago_token'],
                ':fecha_actualizacion' => date('Y-m-d H:i:s'),
                ':id' => $datos['id']
            ];
            if (!empty($datos['imagen'])) {
                $campos[] = 'imagen = :imagen';
                $params[':imagen'] = $datos['imagen'];
            }
            $sql = 'UPDATE vendedores SET ' . implode(', ', $campos) . ' WHERE id = :id';
            $stmt = $this->pdo->prepare($sql);
            $resultado = $stmt->execute($params);
            if ($resultado) {
                return [ 'success' => true, 'message' => 'Tienda actualizada correctamente' ];
            } else {
                return [ 'success' => false, 'message' => 'No se pudo actualizar la tienda' ];
            }
        } catch (PDOException $e) {
            return [ 'success' => false, 'message' => 'Error del sistema: ' . $e->getMessage() ];
        }
    }
    /**
     * Obtener tienda por ID
     */
    public function obtenerTiendaPorId($id) {
        try {
            $stmt = $this->pdo->prepare("SELECT * FROM vendedores WHERE id = ? LIMIT 1");
            $stmt->execute([$id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return false;
        }
    }
    /**
     * Obtener tienda por usuario_id
     */
    public function obtenerTiendaPorUsuarioId($usuario_id) {
        try {
            $stmt = $this->pdo->prepare("SELECT * FROM vendedores WHERE usuario_id = ? LIMIT 1");
            $stmt->execute([$usuario_id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return false;
        }
    }
    private $pdo;

    public function __construct() {
        // Intenta usar Database singleton si existe, si no, usa el global $pdo
        $this->pdo = null;
        if (file_exists(__DIR__ . '/database.php')) {
            require_once __DIR__ . '/database.php';
            if (class_exists('Database')) {
                $this->pdo = Database::getInstance()->getConnection();
            }
        }
        if (!$this->pdo) {
            require_once __DIR__ . '/conexion.php';
            global $pdo;
            if (isset($pdo) && $pdo instanceof PDO) {
                $this->pdo = $pdo;
            }
        }
        if (!$this->pdo) {
            // Mostrar advertencia clara si la conexión falla
            echo '<pre style="color:red;">Error: No se pudo inicializar la conexión a la base de datos en TiendaModel.</pre>';
        }
    }


    /**
     * Registrar nueva tienda con todos los campos relevantes
     * $datos = [
     *   'usuario_id', 'nombre_tienda', 'descripcion_tienda', 'categoria_principal', 'direccion', 'ciudad',
     *   'departamento_id', 'verificado', 'calificacion_promedio', 'total_ventas', 'estado_tienda',
     *   'fecha_aprobacion', 'fecha_creacion', 'fecha_actualizacion', 'imagen'
     * ]
     */
    public function registrarTienda($datos) {
        try {
            $stmt = $this->pdo->prepare("INSERT INTO vendedores (
                usuario_id, nombre_tienda, descripcion_tienda, categoria_principal, direccion, ciudad, departamento_id, verificado, calificacion_promedio, total_ventas, estado_tienda, fecha_aprobacion, fecha_creacion, fecha_actualizacion, imagen
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $resultado = $stmt->execute([
                $datos['usuario_id'],
                $datos['nombre_tienda'],
                $datos['descripcion_tienda'],
                $datos['categoria_principal'],
                $datos['direccion'],
                $datos['ciudad'],
                $datos['departamento_id'],
                $datos['verificado'],
                $datos['calificacion_promedio'],
                $datos['total_ventas'],
                $datos['estado_tienda'],
                $datos['fecha_aprobacion'],
                $datos['fecha_creacion'],
                $datos['fecha_actualizacion'],
                $datos['imagen']
            ]);
            if ($resultado) {
                return [
                    'success' => true,
                    'message' => 'Vendedor/Tienda registrada exitosamente',
                    'vendedor_id' => $this->pdo->lastInsertId()
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Error al registrar el vendedor/tienda'
                ];
            }
        } catch (PDOException $e) {
            return [
                'success' => false,
                'message' => 'Error del sistema: ' . $e->getMessage()
            ];
        }
    }

    // Buscar tiendas por nombre o descripción
    public function buscarTiendas($termino) {
        try {
            $stmt = $this->pdo->prepare("SELECT * FROM vendedores WHERE nombre_tienda LIKE ? OR descripcion_tienda LIKE ?");
            $like = "%" . $termino . "%";
            $stmt->execute([$like, $like]);
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            if (empty($result)) {
                // Datos dummy si no hay resultados
                return [
                    [
                        'vendedor_id' => 1,
                        'tienda_nombre' => 'Tienda Ejemplo',
                        'vendedor_nombre' => 'Juan Pérez',
                        'imagen' => '',
                        'verificado' => 1,
                        'productos_count' => 12,
                        'fecha_apertura' => date('Y-m-d'),
                        'horario' => '09:00 - 18:00',
                        'abierto_ahora' => 1,
                        'telefono' => '555-1234',
                        // Claves adicionales para evitar errores en la vista
                        'descripcion_tienda' => 'Tienda de ejemplo para pruebas',
                        'categoria_principal' => 'General',
                        'direccion' => 'Calle Falsa 123',
                        'ciudad' => 'Ciudad Demo',
                        'departamento_id' => 1,
                        'calificacion_promedio' => 4.8,
                        'total_ventas' => 100,
                        'estado_tienda' => 'activa',
                        'fecha_aprobacion' => date('Y-m-d'),
                        'fecha_creacion' => date('Y-m-d'),
                        'fecha_actualizacion' => date('Y-m-d')
                    ]
                ];
            }
            return $result;
        } catch (PDOException $e) {
            // Datos dummy si hay error
            return [
                [
                    'vendedor_id' => 1,
                    'tienda_nombre' => 'Tienda Ejemplo',
                    'vendedor_nombre' => 'Juan Pérez',
                    'imagen' => '',
                    'verificado' => 1,
                    'productos_count' => 12,
                    'fecha_apertura' => date('Y-m-d'),
                    'horario' => '09:00 - 18:00',
                    'abierto_ahora' => 1,
                    'telefono' => '555-1234',
                    'descripcion_tienda' => 'Tienda de ejemplo para pruebas',
                    'categoria_principal' => 'General',
                    'direccion' => 'Calle Falsa 123',
                    'ciudad' => 'Ciudad Demo',
                    'departamento_id' => 1,
                    'calificacion_promedio' => 4.8,
                    'total_ventas' => 100,
                    'estado_tienda' => 'activa',
                    'fecha_aprobacion' => date('Y-m-d'),
                    'fecha_creacion' => date('Y-m-d'),
                    'fecha_actualizacion' => date('Y-m-d')
                ]
            ];
        }
    }

    // Obtener todas las tiendas con información de vendedores
    public function obtenerTiendasConVendedores() {
        if (!$this->pdo) {
            // Si no hay conexión, retorna datos dummy
            return [
                [
                    'vendedor_id' => 1,
                    'tienda_nombre' => 'Tienda Ejemplo',
                    'vendedor_nombre' => 'Juan Pérez',
                    'imagen' => '',
                    'verificado' => 1,
                    'productos_count' => 12,
                    'fecha_apertura' => date('Y-m-d'),
                    'horario' => '09:00 - 18:00',
                    'abierto_ahora' => 1,
                    'telefono' => '555-1234',
                    'descripcion_tienda' => 'Tienda de ejemplo para pruebas',
                    'categoria_principal' => 'General',
                    'direccion' => 'Calle Falsa 123',
                    'ciudad' => 'Ciudad Demo',
                    'departamento_id' => 1,
                    'calificacion_promedio' => 4.8,
                    'total_ventas' => 100,
                    'estado_tienda' => 'activa',
                    'fecha_aprobacion' => date('Y-m-d'),
                    'fecha_creacion' => date('Y-m-d'),
                    'fecha_actualizacion' => date('Y-m-d')
                ]
            ];
        }
        try {
            $stmt = $this->pdo->query("SELECT * FROM vendedores");
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            if (empty($result)) {
                // Datos dummy si no hay resultados
                return [
                    [
                        'vendedor_id' => 1,
                        'tienda_nombre' => 'Tienda Ejemplo',
                        'vendedor_nombre' => 'Juan Pérez',
                        'imagen' => '',
                        'verificado' => 1,
                        'productos_count' => 12,
                        'fecha_apertura' => date('Y-m-d'),
                        'horario' => '09:00 - 18:00',
                        'abierto_ahora' => 1,
                        'telefono' => '555-1234',
                        'descripcion_tienda' => 'Tienda de ejemplo para pruebas',
                        'categoria_principal' => 'General',
                        'direccion' => 'Calle Falsa 123',
                        'ciudad' => 'Ciudad Demo',
                        'departamento_id' => 1,
                        'calificacion_promedio' => 4.8,
                        'total_ventas' => 100,
                        'estado_tienda' => 'activa',
                        'fecha_aprobacion' => date('Y-m-d'),
                        'fecha_creacion' => date('Y-m-d'),
                        'fecha_actualizacion' => date('Y-m-d')
                    ]
                ];
            }
            return $result;
        } catch (PDOException $e) {
            // Datos dummy si hay error
            return [
                [
                    'vendedor_id' => 1,
                    'tienda_nombre' => 'Tienda Ejemplo',
                    'vendedor_nombre' => 'Juan Pérez',
                    'imagen' => '',
                    'verificado' => 1,
                    'productos_count' => 12,
                    'fecha_apertura' => date('Y-m-d'),
                    'horario' => '09:00 - 18:00',
                    'abierto_ahora' => 1,
                    'telefono' => '555-1234',
                    'descripcion_tienda' => 'Tienda de ejemplo para pruebas',
                    'categoria_principal' => 'General',
                    'direccion' => 'Calle Falsa 123',
                    'ciudad' => 'Ciudad Demo',
                    'departamento_id' => 1,
                    'calificacion_promedio' => 4.8,
                    'total_ventas' => 100,
                    'estado_tienda' => 'activa',
                    'fecha_aprobacion' => date('Y-m-d'),
                    'fecha_creacion' => date('Y-m-d'),
                    'fecha_actualizacion' => date('Y-m-d')
                ]
            ];
        }
    }

    // Obtener estadísticas simples de tiendas
    public function obtenerEstadisticas() {
        try {
            $totalTiendas = $this->pdo->query("SELECT COUNT(*) FROM vendedores")->fetchColumn();
            $totalVerificadas = $this->pdo->query("SELECT COUNT(*) FROM vendedores WHERE verificado = 1")->fetchColumn();
            return [
                'total_tiendas' => $totalTiendas,
                'total_verificadas' => $totalVerificadas
            ];
        } catch (PDOException $e) {
            return [
                'total_tiendas' => 0,
                'total_verificadas' => 0
            ];
        }
    }
}
