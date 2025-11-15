<?php
session_start();
require_once __DIR__ . '/../../model/categorias_model.php';
if (!isset($_SESSION['user_id'])) {
    header('Location: ../../views/usuarios/inicio_sesion.php');
    exit;
}
$categoriasModel = new CategoriasModel();
$categorias = $categoriasModel->obtenerTodas();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agregar Producto | Mextium</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;800&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <style>
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background: linear-gradient(120deg, #e0e7ff 0%, #f8fafc 100%);
            min-height: 100vh;
        }
        .bg-white {
            background: linear-gradient(120deg, #f8fafc 60%, #e0e7ff 100%);
        }
        .product-title {
            font-weight: 900;
            color: #2563eb;
            margin-bottom: 1.5rem;
            text-align: center;
            letter-spacing: 1px;
            text-shadow: 0 2px 8px #5faaff33;
        }
        .card {
            border-radius: 18px !important;
            box-shadow: 0 4px 24px rgba(44,68,170,0.10);
            border: none;
        }
        .card-body {
            background: linear-gradient(120deg, #f4f8ff 80%, #e0e7ff 100%);
            border-radius: 16px;
        }
        .form-label {
            font-weight: 700;
            color: #2563eb;
            letter-spacing: 0.5px;
        }
        .form-control, .form-select {
            border-radius: 12px;
            font-size: 1.08rem;
            border: 1.5px solid #e3eafc;
            background: #f8fafc;
            color: #222;
            box-shadow: 0 2px 8px #2563eb11;
            transition: border-color 0.2s;
        }
        .form-control:focus, .form-select:focus {
            border-color: #2563eb;
            box-shadow: 0 0 0 2px #2563eb22;
        }
        .img-preview {
            width: 120px;
            height: 120px;
            object-fit: cover;
            border-radius: 18px;
            box-shadow: 0 4px 24px #2563eb22;
            margin-bottom: 1rem;
            background: #e3eafc;
            border: 2px solid #2563eb33;
        }
        .btn-primary {
            background: linear-gradient(90deg, #2563eb 0%, #5FAAFF 100%);
            color: #fff;
            border: none;
            font-weight: 700;
            border-radius: 14px;
            box-shadow: 0 2px 8px #2563eb22;
            transition: background 0.2s;
        }
        .btn-primary:hover {
            background: linear-gradient(90deg, #5FAAFF 0%, #2563eb 100%);
            color: #fff;
        }
        .btn-outline-secondary {
            border-radius: 14px;
            border: 2px solid #2563eb;
            color: #2563eb;
            background: #fff;
            font-weight: 600;
            transition: background 0.2s, color 0.2s;
        }
        .btn-outline-secondary:hover {
            background: #2563eb;
            color: #fff;
        }
        .card-title, h5 {
            font-weight: 800;
            letter-spacing: 0.5px;
        }
        .text-primary { color: #2563eb !important; }
        .text-success { color: #1976d2 !important; }
        .text-info { color: #5faaff !important; }
        .text-warning { color: #2d44aa !important; }
        hr {
            border: none;
            border-top: 2px solid #e3eafc;
            margin: 2rem 0;
        }
        @media (max-width: 600px) {
            .product-title { font-size: 1.3rem; }
            .card-body { padding: 1rem; }
        }
    </style>
</head>
<body>
<div class="container-fluid px-4 py-4" style="max-width:1700px;">
    <div class="bg-white rounded-4 shadow-lg p-4">
        <h2 class="product-title mb-4" style="font-size:2.2rem;"
            data-bs-toggle="tooltip" data-bs-placement="top" title="Completa todos los campos para agregar un nuevo producto a tu tienda">
            <i class="fas fa-box-open me-2"></i>Agregar Producto
        </h2>
        <form action="../../controller/productos_controller.php" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="vendedor_id" value="<?php echo $_SESSION['user_id']; ?>">
            <div class="row mb-4 g-4">
                <div class="col-xl-3 col-lg-4 col-md-5 col-sm-12">
                    <div class="card shadow-sm border-0 h-100">
                        <div class="card-body text-center">
                            <img id="imgPreview" src="/mextium/public/no-image.png" class="img-preview mb-2" alt="Vista previa de la imagen">
                            <label for="imagen" class="form-label mt-2"><i class="fas fa-image me-1"></i> Imagen del producto</label>
                            <input class="form-control" type="file" id="imagen" name="imagen" accept="image/*" onchange="previewImage(event)" required>
                        </div>
                    </div>
                </div>
                <div class="col-xl-9 col-lg-8 col-md-7 col-sm-12">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <div class="card border-0 shadow-sm h-100">
                                <div class="card-body">
                                    <h5 class="mb-3 text-primary"
                                        data-bs-toggle="tooltip" data-bs-placement="top" title="Datos principales del producto, como nombre, precio y categoría">
                                        <i class="fas fa-info-circle me-1"></i> Información básica
                                    </h5>
                                    <div class="mb-3">
                                        <label for="nombre" class="form-label">Nombre del producto</label>
                                        <input class="form-control" type="text" id="nombre" name="nombre" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="precio" class="form-label">Precio</label>
                                        <input class="form-control" type="number" id="precio" name="precio" min="0" step="0.01" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="stock" class="form-label">Stock</label>
                                        <input class="form-control" type="number" id="stock" name="stock" min="0" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="categoria_id" class="form-label">Categoría</label>
                                        <select class="form-select" id="categoria_id" name="categoria_id" required>
                                            <option value="">Selecciona una categoría</option>
                                            <?php foreach ($categorias as $cat): ?>
                                                <option value="<?php echo $cat['id']; ?>"><?php echo htmlspecialchars($cat['nombre']); ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label for="descripcion" class="form-label">Descripción</label>
                                        <textarea class="form-control" id="descripcion" name="descripcion" rows="2" required></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card border-0 shadow-sm h-100">
                                <div class="card-body">
                                    <h5 class="mb-3 text-success"
                                        data-bs-toggle="tooltip" data-bs-placement="top" title="Especifica el peso y las dimensiones físicas del producto para envíos y logística">
                                        <i class="fas fa-ruler-combined me-1"></i> Medidas y Peso
                                    </h5>
                                    <div class="row">
                                        <div class="col-6 mb-3">
                                            <label for="peso" class="form-label">Peso</label>
                                            <input class="form-control" type="number" step="0.01" id="peso" name="peso" min="0">
                                        </div>
                                        <div class="col-6 mb-3">
                                            <label for="peso_unidad" class="form-label">Unidad de peso</label>
                                            <select class="form-select" id="peso_unidad" name="peso_unidad">
                                                <option value="g">Gramos (g)</option>
                                                <option value="kg">Kilogramos (kg)</option>
                                                <option value="lb">Libras (lb)</option>
                                                <option value="oz">Onzas (oz)</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-4 mb-3">
                                            <label for="largo" class="form-label">Largo</label>
                                            <input class="form-control" type="number" step="0.01" id="largo" name="largo" min="0">
                                        </div>
                                        <div class="col-4 mb-3">
                                            <label for="ancho" class="form-label">Ancho</label>
                                            <input class="form-control" type="number" step="0.01" id="ancho" name="ancho" min="0">
                                        </div>
                                        <div class="col-4 mb-3">
                                            <label for="alto" class="form-label">Alto</label>
                                            <input class="form-control" type="number" step="0.01" id="alto" name="alto" min="0">
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label for="dimensiones_unidad" class="form-label">Unidad de dimensiones</label>
                                        <select class="form-select" id="dimensiones_unidad" name="dimensiones_unidad">
                                            <option value="cm">Centímetros (cm)</option>
                                            <option value="mm">Milímetros (mm)</option>
                                            <option value="m">Metros (m)</option>
                                            <option value="in">Pulgadas (in)</option>
                                        </select>
                                    </div>
                                    <div class="row">
                                        <div class="col-6 mb-3">
                                            <label for="volumen" class="form-label">Volumen</label>
                                            <input class="form-control" type="number" step="0.001" id="volumen" name="volumen" min="0">
                                        </div>
                                        <div class="col-6 mb-3">
                                            <label for="volumen_unidad" class="form-label">Unidad de volumen</label>
                                            <select class="form-select" id="volumen_unidad" name="volumen_unidad">
                                                <option value="ml">Mililitros (ml)</option>
                                                <option value="l">Litros (l)</option>
                                                <option value="cm3">Centímetros cúbicos (cm³)</option>
                                                <option value="in3">Pulgadas cúbicas (in³)</option>
                                                <option value="fl_oz">Onzas líquidas (fl oz)</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row g-4 mt-2">
                        <div class="col-md-6">
                            <div class="card border-0 shadow-sm h-100">
                                <div class="card-body">
                                    <h5 class="mb-3 text-info"
                                        data-bs-toggle="tooltip" data-bs-placement="top" title="Agrega detalles técnicos, materiales, colores y especificaciones del producto">
                                        <i class="fas fa-cogs me-1"></i> Detalles y Especificaciones
                                    </h5>
                                    <div class="row">
                                        <div class="col-6 mb-3">
                                            <label for="material" class="form-label">Material</label>
                                            <input class="form-control" type="text" id="material" name="material">
                                        </div>
                                        <div class="col-6 mb-3">
                                            <label for="color" class="form-label">Color</label>
                                            <input class="form-control" type="text" id="color" name="color">
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-6 mb-3">
                                            <label for="marca" class="form-label">Marca</label>
                                            <input class="form-control" type="text" id="marca" name="marca">
                                        </div>
                                        <div class="col-6 mb-3">
                                            <label for="modelo" class="form-label">Modelo</label>
                                            <input class="form-control" type="text" id="modelo" name="modelo">
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-6 mb-3">
                                            <label for="codigo_barras" class="form-label">Código de barras</label>
                                            <input class="form-control" type="text" id="codigo_barras" name="codigo_barras">
                                        </div>
                                        <div class="col-6 mb-3">
                                            <label for="sku" class="form-label">SKU</label>
                                            <input class="form-control" type="text" id="sku" name="sku">
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-6 mb-3">
                                            <label for="garantia_meses" class="form-label">Garantía (meses)</label>
                                            <input class="form-control" type="number" id="garantia_meses" name="garantia_meses" min="0">
                                        </div>
                                        <div class="col-6 mb-3">
                                            <label for="origen_pais" class="form-label">País de origen</label>
                                            <input class="form-control" type="text" id="origen_pais" name="origen_pais">
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label for="condicion" class="form-label">Condición</label>
                                        <select class="form-select" id="condicion" name="condicion">
                                            <option value="nuevo">Nuevo</option>
                                            <option value="usado">Usado</option>
                                            <option value="reacondicionado">Reacondicionado</option>
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label for="tags" class="form-label">Etiquetas (separadas por comas)</label>
                                        <input class="form-control" type="text" id="tags" name="tags">
                                    </div>
                                    <div class="mb-3">
                                        <label for="especificaciones_tecnicas" class="form-label">Especificaciones técnicas (JSON)</label>
                                        <textarea class="form-control" id="especificaciones_tecnicas" name="especificaciones_tecnicas" rows="2" placeholder='{"potencia":"100W","voltaje":"220V"}'></textarea>
                                    </div>
                                    <div class="mb-3">
                                        <label for="instrucciones_uso" class="form-label">Instrucciones de uso</label>
                                        <textarea class="form-control" id="instrucciones_uso" name="instrucciones_uso" rows="2"></textarea>
                                    </div>
                                    <div class="mb-3">
                                        <label for="ingredientes" class="form-label">Ingredientes</label>
                                        <textarea class="form-control" id="ingredientes" name="ingredientes" rows="2"></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card border-0 shadow-sm h-100">
                                <div class="card-body">
                                    <h5 class="mb-3 text-warning"
                                        data-bs-toggle="tooltip" data-bs-placement="top" title="Información logística, edad recomendada, género y otras características">
                                        <i class="fas fa-truck me-1"></i> Logística y Otros
                                    </h5>
                                    <div class="row">
                                        <div class="col-6 mb-3">
                                            <label for="fecha_vencimiento" class="form-label">Fecha de vencimiento</label>
                                            <input class="form-control" type="date" id="fecha_vencimiento" name="fecha_vencimiento">
                                        </div>
                                        <div class="col-6 mb-3">
                                            <label for="temperatura_almacenamiento" class="form-label">Temperatura de almacenamiento</label>
                                            <input class="form-control" type="text" id="temperatura_almacenamiento" name="temperatura_almacenamiento">
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-6 mb-3">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="fragil" name="fragil" value="1">
                                                <label class="form-check-label" for="fragil">¿Es frágil?</label>
                                            </div>
                                        </div>
                                        <div class="col-6 mb-3">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="requiere_refrigeracion" name="requiere_refrigeracion" value="1">
                                                <label class="form-check-label" for="requiere_refrigeracion">¿Requiere refrigeración?</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-6 mb-3">
                                            <label for="edad_minima" class="form-label">Edad mínima recomendada</label>
                                            <input class="form-control" type="number" id="edad_minima" name="edad_minima" min="0">
                                        </div>
                                        <div class="col-6 mb-3">
                                            <label for="edad_maxima" class="form-label">Edad máxima recomendada</label>
                                            <input class="form-control" type="number" id="edad_maxima" name="edad_maxima" min="0">
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label for="genero" class="form-label">Género</label>
                                        <select class="form-select" id="genero" name="genero">
                                            <option value="unisex">Unisex</option>
                                            <option value="hombre">Hombre</option>
                                            <option value="mujer">Mujer</option>
                                            <option value="niño">Niño</option>
                                            <option value="niña">Niña</option>
                                        </select>
                                    </div>
                                    <div class="row">
                                        <div class="col-6 mb-3">
                                            <label for="talla" class="form-label">Talla</label>
                                            <input class="form-control" type="text" id="talla" name="talla">
                                        </div>
                                        <div class="col-6 mb-3">
                                            <label for="sistema_talla" class="form-label">Sistema de talla</label>
                                            <select class="form-select" id="sistema_talla" name="sistema_talla">
                                                <option value="">Sin sistema</option>
                                                <option value="us">US</option>
                                                <option value="eu">EU</option>
                                                <option value="uk">UK</option>
                                                <option value="xl-xs">XL-XS</option>
                                                <option value="numerico">Numérico</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <hr class="my-4">
            <div class="row mt-2">
                <div class="col-md-6 mb-3">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="destacado" name="destacado" value="1">
                        <label class="form-check-label" for="destacado">Producto destacado</label>
                    </div>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="estado" class="form-label">Estado</label>
                    <select class="form-select" id="estado" name="estado" required>
                        <option value="activo" selected>Activo</option>
                        <option value="inactivo">Inactivo</option>
                    </select>
                </div>
            </div>
            <div class="d-flex justify-content-between mt-4">
                <a href="../vendedores/mi_tienda.php" class="btn btn-outline-secondary btn-lg"><i class="fas fa-arrow-left me-1"></i> Cancelar</a>
                <button type="submit" class="btn btn-primary btn-lg px-4"><i class="fas fa-save me-1"></i> Guardar producto</button>
            </div>
        </form>
    </div>
</div>
<script>
function previewImage(event) {
    const input = event.target;
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('imgPreview').src = e.target.result;
        }
        reader.readAsDataURL(input.files[0]);
    }
}

// Inicializar tooltips de Bootstrap
document.addEventListener('DOMContentLoaded', function () {
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.forEach(function (tooltipTriggerEl) {
        new bootstrap.Tooltip(tooltipTriggerEl);
    });
});
</script>
</body>
</html>
