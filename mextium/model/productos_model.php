<?php
class ProductosModel {
    /**
     * Eliminar un producto por su ID
     */
    public function eliminarProductoPorId($id) {
        try {
            // Eliminar relaciones en ordenes_productos primero
            $stmt_rel = $this->pdo->prepare("DELETE FROM ordenes_productos WHERE producto_id = ?");
            $stmt_rel->execute([$id]);

            // Ahora eliminar el producto
            $stmt = $this->pdo->prepare("DELETE FROM productos WHERE id = ?");
            $stmt->execute([$id]);
            if ($stmt->rowCount() === 0) {
                return false;
            }
            return true;
        } catch (PDOException $e) {
            echo '<pre style="color:red;">Error al eliminar producto: ' . htmlspecialchars($e->getMessage()) . '</pre>';
            return false;
        }
    }
    private $pdo;
    public function __construct() {
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
            echo '<pre style="color:red;">Error: No se pudo inicializar la conexión a la base de datos en ProductosModel.</pre>';
        }
    }

    /**
     * Registrar un nuevo producto
     * $datos = [nombre, descripcion, precio, imagen, stock, categoria_id, vendedor_id, destacado, estado]
     */
    public function registrarProducto($datos) {
        try {
            // Asegurar que especificaciones_tecnicas sea JSON válido o NULL
            if (empty($datos['especificaciones_tecnicas']) || trim($datos['especificaciones_tecnicas']) === '') {
                $datos['especificaciones_tecnicas'] = null;
            } else {
                // Si ya es JSON válido, lo dejamos; si no, lo convertimos a JSON
                $json = json_decode($datos['especificaciones_tecnicas']);
                if (json_last_error() !== JSON_ERROR_NONE) {
                    $datos['especificaciones_tecnicas'] = json_encode([$datos['especificaciones_tecnicas']]);
                }
            }
            $stmt = $this->pdo->prepare("INSERT INTO productos (
                nombre, descripcion, precio, imagen, stock, categoria_id, vendedor_id, destacado, estado,
                peso, peso_unidad, largo, ancho, alto, dimensiones_unidad, volumen, volumen_unidad,
                material, color, marca, modelo, codigo_barras, sku, garantia_meses, origen_pais, condicion,
                tags, especificaciones_tecnicas, instrucciones_uso, ingredientes, fecha_vencimiento, temperatura_almacenamiento,
                fragil, requiere_refrigeracion, edad_minima, edad_maxima, genero, talla, sistema_talla,
                fecha_creacion, fecha_actualizacion
            ) VALUES (
                ?, ?, ?, ?, ?, ?, ?, ?, ?,
                ?, ?, ?, ?, ?, ?, ?, ?,
                ?, ?, ?, ?, ?, ?, ?, ?, ?,
                ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?,
                NOW(), NOW()
            )");
            $resultado = $stmt->execute([
                $datos['nombre'],
                $datos['descripcion'],
                $datos['precio'],
                $datos['imagen'],
                $datos['stock'],
                $datos['categoria_id'],
                $datos['vendedor_id'],
                $datos['destacado'],
                $datos['estado'],
                $datos['peso'],
                $datos['peso_unidad'],
                $datos['largo'],
                $datos['ancho'],
                $datos['alto'],
                $datos['dimensiones_unidad'],
                $datos['volumen'],
                $datos['volumen_unidad'],
                $datos['material'],
                $datos['color'],
                $datos['marca'],
                $datos['modelo'],
                $datos['codigo_barras'],
                $datos['sku'],
                $datos['garantia_meses'],
                $datos['origen_pais'],
                $datos['condicion'],
                $datos['tags'],
                $datos['especificaciones_tecnicas'],
                $datos['instrucciones_uso'],
                $datos['ingredientes'],
                $datos['fecha_vencimiento'],
                $datos['temperatura_almacenamiento'],
                $datos['fragil'],
                $datos['requiere_refrigeracion'],
                $datos['edad_minima'],
                $datos['edad_maxima'],
                $datos['genero'],
                $datos['talla'],
                $datos['sistema_talla']
            ]);
            if ($resultado) {
                return [
                    'success' => true,
                    'message' => 'Producto registrado exitosamente',
                    'producto_id' => $this->pdo->lastInsertId()
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Error al registrar el producto'
                ];
            }
        } catch (PDOException $e) {
            return [
                'success' => false,
                'message' => 'Error del sistema: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Obtener todos los productos de un vendedor
     */
    public function obtenerProductosPorVendedor($vendedor_id) {
        try {
            $stmt = $this->pdo->prepare("SELECT p.*, c.nombre AS categoria FROM productos p LEFT JOIN categorias c ON p.categoria_id = c.id WHERE p.vendedor_id = ?");
            $stmt->execute([$vendedor_id]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }

    /**
     * Obtener productos por nombre de categoría (y filtros opcionales)
     */
    public function obtenerProductosPorCategoria($nombreCategoria, $filtros = []) {
        try {
            $sql = "SELECT p.*, c.nombre AS categoria FROM productos p LEFT JOIN categorias c ON p.categoria_id = c.id WHERE 1=1 ";
            $params = [];
            if ($nombreCategoria && strtolower($nombreCategoria) !== 'todas') {
                $sql .= " AND c.nombre = ? ";
                $params[] = $nombreCategoria;
            }
            if (!empty($filtros['busqueda'])) {
                $sql .= " AND (p.nombre LIKE ? OR p.descripcion LIKE ?) ";
                $params[] = "%" . $filtros['busqueda'] . "%";
                $params[] = "%" . $filtros['busqueda'] . "%";
            }
            if (!empty($filtros['precio_min'])) {
                $sql .= " AND p.precio >= ? ";
                $params[] = $filtros['precio_min'];
            }
            if (!empty($filtros['precio_max'])) {
                $sql .= " AND p.precio <= ? ";
                $params[] = $filtros['precio_max'];
            }
            // Solo productos con imagen no vacía
            $sql .= " AND p.imagen IS NOT NULL AND p.imagen != '' ";
            // Ordenamiento
            $orden = "p.id DESC";
            if (!empty($filtros['ordenar'])) {
                switch ($filtros['ordenar']) {
                    case 'precio_asc': $orden = "p.precio ASC"; break;
                    case 'precio_desc': $orden = "p.precio DESC"; break;
                    case 'nombre_asc': $orden = "p.nombre ASC"; break;
                    case 'nombre_desc': $orden = "p.nombre DESC"; break;
                }
            }
            $sql .= " ORDER BY $orden";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }
}
