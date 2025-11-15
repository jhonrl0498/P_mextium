-- SQL para agregar campos de pesos, medidas y más detalles a la tabla productos

-- Agregar nuevos campos a la tabla productos existente
ALTER TABLE productos 
ADD COLUMN peso DECIMAL(10,2) NULL COMMENT 'Peso del producto en gramos',
ADD COLUMN peso_unidad ENUM('g', 'kg', 'lb', 'oz') DEFAULT 'g' COMMENT 'Unidad de medida del peso',
ADD COLUMN largo DECIMAL(10,2) NULL COMMENT 'Largo del producto en centímetros',
ADD COLUMN ancho DECIMAL(10,2) NULL COMMENT 'Ancho del producto en centímetros', 
ADD COLUMN alto DECIMAL(10,2) NULL COMMENT 'Alto del producto en centímetros',
ADD COLUMN dimensiones_unidad ENUM('cm', 'in', 'm', 'mm') DEFAULT 'cm' COMMENT 'Unidad de medida de las dimensiones',
ADD COLUMN volumen DECIMAL(10,3) NULL COMMENT 'Volumen del producto',
ADD COLUMN volumen_unidad ENUM('ml', 'l', 'cm3', 'in3', 'fl_oz') DEFAULT 'ml' COMMENT 'Unidad de medida del volumen',
ADD COLUMN material VARCHAR(100) NULL COMMENT 'Material principal del producto',
ADD COLUMN color VARCHAR(50) NULL COMMENT 'Color principal del producto',
ADD COLUMN marca VARCHAR(100) NULL COMMENT 'Marca del producto',
ADD COLUMN modelo VARCHAR(100) NULL COMMENT 'Modelo del producto',
ADD COLUMN codigo_barras VARCHAR(50) NULL COMMENT 'Código de barras del producto',
ADD COLUMN sku VARCHAR(50) NULL COMMENT 'SKU (Stock Keeping Unit) del producto',
ADD COLUMN garantia_meses INT NULL COMMENT 'Meses de garantía del producto',
ADD COLUMN origen_pais VARCHAR(50) NULL COMMENT 'País de origen del producto',
ADD COLUMN condicion ENUM('nuevo', 'usado', 'reacondicionado') DEFAULT 'nuevo' COMMENT 'Condición del producto',
ADD COLUMN tags TEXT NULL COMMENT 'Etiquetas separadas por comas para búsqueda',
ADD COLUMN especificaciones_tecnicas JSON NULL COMMENT 'Especificaciones técnicas en formato JSON',
ADD COLUMN instrucciones_uso TEXT NULL COMMENT 'Instrucciones de uso del producto',
ADD COLUMN ingredientes TEXT NULL COMMENT 'Ingredientes (para productos alimenticios/cosméticos)',
ADD COLUMN fecha_vencimiento DATE NULL COMMENT 'Fecha de vencimiento (si aplica)',
ADD COLUMN temperatura_almacenamiento VARCHAR(50) NULL COMMENT 'Temperatura recomendada de almacenamiento',
ADD COLUMN fragil BOOLEAN DEFAULT FALSE COMMENT 'Indica si el producto es frágil',
ADD COLUMN requiere_refrigeracion BOOLEAN DEFAULT FALSE COMMENT 'Indica si requiere refrigeración',
ADD COLUMN edad_minima INT NULL COMMENT 'Edad mínima recomendada para el producto',
ADD COLUMN edad_maxima INT NULL COMMENT 'Edad máxima recomendada para el producto',
ADD COLUMN genero ENUM('unisex', 'hombre', 'mujer', 'niño', 'niña') DEFAULT 'unisex' COMMENT 'Género target del producto',
ADD COLUMN talla VARCHAR(20) NULL COMMENT 'Talla del producto (ropa, calzado)',
ADD COLUMN sistema_talla ENUM('us', 'eu', 'uk', 'xl-xs', 'numerico') NULL COMMENT 'Sistema de tallas utilizado',
ADD COLUMN stock_minimo INT DEFAULT 5 COMMENT 'Stock mínimo antes de alerta',
ADD COLUMN stock_maximo INT NULL COMMENT 'Stock máximo recomendado',
ADD COLUMN precio_costo DECIMAL(10,2) NULL COMMENT 'Precio de costo del producto',
ADD COLUMN margen_ganancia DECIMAL(5,2) NULL COMMENT 'Margen de ganancia en porcentaje',
ADD COLUMN descuento_porcentaje DECIMAL(5,2) DEFAULT 0 COMMENT 'Descuento aplicado en porcentaje',
ADD COLUMN precio_oferta DECIMAL(10,2) NULL COMMENT 'Precio con descuento aplicado',
ADD COLUMN fecha_inicio_oferta DATETIME NULL COMMENT 'Fecha de inicio de la oferta',
ADD COLUMN fecha_fin_oferta DATETIME NULL COMMENT 'Fecha de fin de la oferta',
ADD COLUMN es_digital BOOLEAN DEFAULT FALSE COMMENT 'Indica si es un producto digital',
ADD COLUMN archivo_descarga VARCHAR(255) NULL COMMENT 'Ruta del archivo para productos digitales',
ADD COLUMN numero_descargas_permitidas INT NULL COMMENT 'Número de descargas permitidas para productos digitales',
ADD COLUMN requiere_envio BOOLEAN DEFAULT TRUE COMMENT 'Indica si el producto requiere envío físico',
ADD COLUMN peso_envio DECIMAL(10,2) NULL COMMENT 'Peso para cálculo de envío',
ADD COLUMN costo_envio_adicional DECIMAL(10,2) DEFAULT 0 COMMENT 'Costo adicional de envío específico del producto',
ADD COLUMN disponible_entrega_inmediata BOOLEAN DEFAULT FALSE COMMENT 'Disponible para entrega el mismo día',
ADD COLUMN tiempo_preparacion_dias INT DEFAULT 1 COMMENT 'Días necesarios para preparar el producto',
ADD COLUMN certificaciones TEXT NULL COMMENT 'Certificaciones del producto (separadas por comas)',
ADD COLUMN advertencias_seguridad TEXT NULL COMMENT 'Advertencias de seguridad',
ADD COLUMN video_url VARCHAR(255) NULL COMMENT 'URL de video demostrativo del producto',
ADD COLUMN imagenes_adicionales JSON NULL COMMENT 'URLs de imágenes adicionales en formato JSON',
ADD COLUMN valoracion_promedio DECIMAL(3,2) DEFAULT 0 COMMENT 'Valoración promedio de clientes',
ADD COLUMN total_valoraciones INT DEFAULT 0 COMMENT 'Total de valoraciones recibidas',
ADD COLUMN veces_vendido INT DEFAULT 0 COMMENT 'Número de veces que se ha vendido el producto',
ADD COLUMN vistas_producto INT DEFAULT 0 COMMENT 'Número de veces que se ha visto el producto',
ADD COLUMN seo_titulo VARCHAR(255) NULL COMMENT 'Título SEO personalizado',
ADD COLUMN seo_descripcion TEXT NULL COMMENT 'Descripción SEO personalizada',
ADD COLUMN seo_palabras_clave VARCHAR(255) NULL COMMENT 'Palabras clave SEO separadas por comas',
ADD INDEX idx_peso (peso),
ADD INDEX idx_marca (marca),
ADD INDEX idx_precio_oferta (precio_oferta),
ADD INDEX idx_condicion (condicion),
ADD INDEX idx_es_digital (es_digital),
ADD INDEX idx_valoracion (valoracion_promedio),
ADD INDEX idx_veces_vendido (veces_vendido),
ADD INDEX idx_fecha_oferta (fecha_inicio_oferta, fecha_fin_oferta);

-- Crear tabla para variantes de productos (tallas, colores, etc.)
CREATE TABLE IF NOT EXISTS producto_variantes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    producto_id INT NOT NULL,
    nombre VARCHAR(100) NOT NULL COMMENT 'Nombre de la variante (ej: Talla M - Rojo)',
    tipo_variante ENUM('talla', 'color', 'material', 'sabor', 'capacidad', 'otro') NOT NULL,
    valor VARCHAR(50) NOT NULL COMMENT 'Valor de la variante',
    precio_adicional DECIMAL(10,2) DEFAULT 0 COMMENT 'Precio adicional por esta variante',
    stock INT DEFAULT 0 COMMENT 'Stock específico para esta variante',
    sku_variante VARCHAR(50) NULL COMMENT 'SKU específico de la variante',
    imagen_variante VARCHAR(255) NULL COMMENT 'Imagen específica de la variante',
    estado BOOLEAN DEFAULT TRUE,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_actualizacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (producto_id) REFERENCES productos(id) ON DELETE CASCADE,
    INDEX idx_producto_variante (producto_id),
    INDEX idx_tipo_variante (tipo_variante),
    UNIQUE KEY unique_producto_tipo_valor (producto_id, tipo_variante, valor)
);

-- Crear tabla para especificaciones técnicas detalladas
CREATE TABLE IF NOT EXISTS producto_especificaciones (
    id INT AUTO_INCREMENT PRIMARY KEY,
    producto_id INT NOT NULL,
    nombre_especificacion VARCHAR(100) NOT NULL,
    valor_especificacion TEXT NOT NULL,
    unidad VARCHAR(20) NULL,
    categoria_especificacion VARCHAR(50) NULL COMMENT 'Categoría de la especificación (ej: dimensiones, rendimiento)',
    orden_visualizacion INT DEFAULT 0,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (producto_id) REFERENCES productos(id) ON DELETE CASCADE,
    INDEX idx_producto_especificacion (producto_id),
    INDEX idx_categoria_especificacion (categoria_especificacion)
);

-- Crear tabla para imágenes múltiples de productos
CREATE TABLE IF NOT EXISTS producto_imagenes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    producto_id INT NOT NULL,
    url_imagen VARCHAR(255) NOT NULL,
    alt_texto VARCHAR(255) NULL,
    es_principal BOOLEAN DEFAULT FALSE,
    orden_visualizacion INT DEFAULT 0,
    fecha_subida TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (producto_id) REFERENCES productos(id) ON DELETE CASCADE,
    INDEX idx_producto_imagen (producto_id),
    INDEX idx_imagen_principal (es_principal)
);

-- Crear tabla para valoraciones de productos
CREATE TABLE IF NOT EXISTS producto_valoraciones (
    id INT AUTO_INCREMENT PRIMARY KEY,
    producto_id INT NOT NULL,
    usuario_id INT NOT NULL,
    puntuacion INT NOT NULL CHECK (puntuacion >= 1 AND puntuacion <= 5),
    titulo_resena VARCHAR(255) NULL,
    comentario TEXT NULL,
    verificado BOOLEAN DEFAULT FALSE COMMENT 'Si la compra fue verificada',
    fecha_valoracion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (producto_id) REFERENCES productos(id) ON DELETE CASCADE,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    UNIQUE KEY unique_usuario_producto (usuario_id, producto_id),
    INDEX idx_producto_valoracion (producto_id),
    INDEX idx_puntuacion (puntuacion)
);

-- Crear tabla para preguntas frecuentes del producto
CREATE TABLE IF NOT EXISTS producto_preguntas_respuestas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    producto_id INT NOT NULL,
    usuario_pregunta_id INT NULL,
    pregunta TEXT NOT NULL,
    respuesta TEXT NULL,
    respondida_por_vendedor BOOLEAN DEFAULT FALSE,
    fecha_pregunta TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_respuesta TIMESTAMP NULL,
    estado ENUM('pendiente', 'respondida', 'oculta') DEFAULT 'pendiente',
    FOREIGN KEY (producto_id) REFERENCES productos(id) ON DELETE CASCADE,
    FOREIGN KEY (usuario_pregunta_id) REFERENCES usuarios(id) ON DELETE SET NULL,
    INDEX idx_producto_pregunta (producto_id),
    INDEX idx_estado_pregunta (estado)
);

-- Crear tabla para historial de precios
CREATE TABLE IF NOT EXISTS producto_historial_precios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    producto_id INT NOT NULL,
    precio_anterior DECIMAL(10,2) NOT NULL,
    precio_nuevo DECIMAL(10,2) NOT NULL,
    motivo VARCHAR(255) NULL,
    fecha_cambio TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (producto_id) REFERENCES productos(id) ON DELETE CASCADE,
    INDEX idx_producto_precio_historial (producto_id),
    INDEX idx_fecha_cambio (fecha_cambio)
);

-- Trigger para actualizar valoración promedio automáticamente
DELIMITER //
CREATE TRIGGER actualizar_valoracion_promedio 
AFTER INSERT ON producto_valoraciones
FOR EACH ROW
BEGIN
    UPDATE productos 
    SET valoracion_promedio = (
        SELECT AVG(puntuacion) 
        FROM producto_valoraciones 
        WHERE producto_id = NEW.producto_id
    ),
    total_valoraciones = (
        SELECT COUNT(*) 
        FROM producto_valoraciones 
        WHERE producto_id = NEW.producto_id
    )
    WHERE id = NEW.producto_id;
END//

-- Trigger para registrar cambios de precio
CREATE TRIGGER registrar_cambio_precio
AFTER UPDATE ON productos
FOR EACH ROW
BEGIN
    IF OLD.precio != NEW.precio THEN
        INSERT INTO producto_historial_precios (producto_id, precio_anterior, precio_nuevo, motivo)
        VALUES (NEW.id, OLD.precio, NEW.precio, 'Actualización manual');
    END IF;
END//

-- Trigger para actualizar contador de ventas
CREATE TRIGGER actualizar_contador_ventas
AFTER INSERT ON ordenes_productos
FOR EACH ROW
BEGIN
    UPDATE productos 
    SET veces_vendido = veces_vendido + NEW.cantidad
    WHERE id = NEW.producto_id;
END//

DELIMITER ;

-- Insertar datos de ejemplo para las nuevas características
-- (Opcional - solo para pruebas)

-- Ejemplo de variantes para un producto existente (asumiendo que existe un producto con ID 1)
/*
INSERT INTO producto_variantes (producto_id, nombre, tipo_variante, valor, precio_adicional, stock) VALUES
(1, 'Talla S', 'talla', 'S', 0, 10),
(1, 'Talla M', 'talla', 'M', 0, 15),
(1, 'Talla L', 'talla', 'L', 5000, 8),
(1, 'Color Rojo', 'color', 'Rojo', 0, 20),
(1, 'Color Azul', 'color', 'Azul', 2000, 12);
*/

-- Crear vista para productos con información completa
CREATE OR REPLACE VIEW vista_productos_completos AS
SELECT 
    p.*,
    c.nombre AS categoria_nombre,
    u.nombre AS vendedor_nombre,
    u.apellido AS vendedor_apellido,
    v.nombre_tienda AS tienda_nombre,
    CASE 
        WHEN p.fecha_fin_oferta IS NOT NULL AND p.fecha_fin_oferta > NOW() 
        THEN p.precio_oferta 
        ELSE p.precio 
    END AS precio_actual,
    CASE 
        WHEN p.fecha_fin_oferta IS NOT NULL AND p.fecha_fin_oferta > NOW() 
        THEN TRUE 
        ELSE FALSE 
    END AS en_oferta,
    CONCAT(p.largo, 'x', p.ancho, 'x', p.alto, ' ', p.dimensiones_unidad) AS dimensiones_completas,
    CONCAT(p.peso, ' ', p.peso_unidad) AS peso_completo,
    (SELECT COUNT(*) FROM producto_variantes pv WHERE pv.producto_id = p.id AND pv.estado = 1) AS tiene_variantes,
    (SELECT COUNT(*) FROM producto_imagenes pi WHERE pi.producto_id = p.id) AS total_imagenes
FROM productos p
LEFT JOIN categorias c ON p.categoria_id = c.id
LEFT JOIN usuarios u ON p.vendedor_id = u.id
LEFT JOIN vendedores v ON p.vendedor_id = v.usuario_id;

-- Comentarios adicionales para documentación
/*
CAMPOS AGREGADOS Y SU PROPÓSITO:

MEDIDAS Y PESO:
- peso, peso_unidad: Para cálculos de envío y especificaciones
- largo, ancho, alto, dimensiones_unidad: Dimensiones físicas
- volumen, volumen_unidad: Para productos líquidos o que requieran medida de volumen

INFORMACIÓN DETALLADA:
- material, color, marca, modelo: Características básicas del producto
- codigo_barras, sku: Identificadores únicos para inventario
- garantia_meses, origen_pais: Información legal y de garantía
- condicion: Estado del producto (nuevo, usado, etc.)

ESPECIFICACIONES:
- tags: Para mejores búsquedas
- especificaciones_tecnicas: JSON para flexibilidad
- instrucciones_uso, ingredientes: Información detallada
- certificaciones, advertencias_seguridad: Información legal

LOGÍSTICA:
- fragil, requiere_refrigeracion: Para manejo especial
- peso_envio, costo_envio_adicional: Cálculos de envío
- tiempo_preparacion_dias: Para tiempos de entrega
- requiere_envio: Para productos digitales

MARKETING:
- descuento_porcentaje, precio_oferta: Sistema de ofertas
- fecha_inicio_oferta, fecha_fin_oferta: Temporalidad de ofertas
- valoracion_promedio, total_valoraciones: Sistema de reviews
- veces_vendido, vistas_producto: Estadísticas

INVENTARIO:
- stock_minimo, stock_maximo: Control de inventario
- precio_costo, margen_ganancia: Análisis financiero

SEO:
- seo_titulo, seo_descripcion, seo_palabras_clave: Optimización para buscadores

Las tablas adicionales permiten:
- Gestión de variantes (tallas, colores, etc.)
- Múltiples imágenes por producto  
- Sistema de valoraciones y reseñas
- Preguntas y respuestas
- Historial de cambios de precio
- Especificaciones técnicas detalladas
*/