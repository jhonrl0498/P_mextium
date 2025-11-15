<?php
require_once __DIR__ . '/../../model/productos_model.php';
$categoria = isset($_GET['categoria']) ? htmlspecialchars($_GET['categoria']) : 'Todas';
$filtros = [
    'busqueda' => isset($_GET['busqueda']) ? trim($_GET['busqueda']) : '',
    'precio_min' => isset($_GET['precio_min']) ? floatval($_GET['precio_min']) : '',
    'precio_max' => isset($_GET['precio_max']) ? floatval($_GET['precio_max']) : '',
    'ordenar' => isset($_GET['ordenar']) ? $_GET['ordenar'] : '',
];
$productosModel = new ProductosModel();
$productos = $productosModel->obtenerProductosPorCategoria($categoria, $filtros);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Productos - <?= $categoria ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #e3eaff 0%, #f8fafc 100%);
            min-height: 100vh;
        }
        .category-title {
            font-size: 2.5rem;
            font-weight: 900;
            margin-bottom: 1.5rem;
            color: #fff;
            background: linear-gradient(90deg, #2d44aa 0%, #6a82fb 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .filters-bar {
            background: rgba(255,255,255,0.85);
            border-radius: 22px;
            box-shadow: 0 8px 32px rgba(95,170,255,0.13);
            padding: 1.5rem 2.5rem;
            margin-bottom: 2.5rem;
            display: flex;
            flex-wrap: wrap;
            gap: 1.5rem;
            align-items: center;
            backdrop-filter: blur(6px);
        }
        .filters-bar label {
            font-weight: 700;
            margin-right: 0.7rem;
            color: #2d44aa;
        }
        .filters-bar .form-control, .filters-bar .form-select {
            border-radius: 12px;
            border: 1.5px solid #e3eaff;
            box-shadow: 0 2px 8px rgba(45,68,170,0.04);
        }
        .product-card {
            border-radius: 22px;
            background: rgba(255,255,255,0.92);
            box-shadow: 0 12px 40px rgba(45,68,170,0.13), 0 1.5px 8px rgba(106,130,251,0.08);
            transition: all 0.35s cubic-bezier(.17,.67,.83,.67);
            margin-bottom: 2.2rem;
            border: 1.5px solid #e3eaff;
            overflow: hidden;
            position: relative;
        }
        .product-card::before {
            content: '';
            position: absolute;
            top: 0; left: 0; width: 100%; height: 100%;
            background: linear-gradient(120deg, rgba(106,130,251,0.07) 0%, rgba(255,255,255,0.12) 100%);
            z-index: 0;
        }
        .product-card:hover {
            transform: translateY(-10px) scale(1.03) rotate(-1deg);
            box-shadow: 0 24px 60px rgba(45,68,170,0.18), 0 2px 12px rgba(106,130,251,0.13);
            border-color: #6a82fb;
        }
        .product-image {
            height: 200px;
            background: linear-gradient(135deg, #e3eaff 0%, #fff 100%);
            border-radius: 22px 22px 0 0;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            position: relative;
            z-index: 1;
        }
        .product-image img {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
            filter: drop-shadow(0 4px 16px rgba(45,68,170,0.10));
            transition: transform 0.3s;
        }
        .product-card:hover .product-image img {
            transform: scale(1.07) rotate(-2deg);
        }
        .product-info {
            padding: 1.5rem 1.7rem 1.2rem 1.7rem;
            position: relative;
            z-index: 2;
        }
        .product-title {
            font-size: 1.18rem;
            font-weight: 800;
            margin-bottom: 0.6rem;
            color: #2d44aa;
            letter-spacing: 0.5px;
        }
        .product-price {
            color: #6a82fb;
            font-weight: 900;
            font-size: 1.25rem;
            margin-bottom: 0.3rem;
            letter-spacing: 0.5px;
            text-shadow: 0 2px 8px rgba(106,130,251,0.08);
        }
        .text-muted {
            color: #7b8bb7 !important;
        }
        .btn-primary-gradient {
            background: linear-gradient(90deg, #6a82fb 0%, #2d44aa 100%);
            color: #fff;
            border: none;
            font-weight: 700;
            border-radius: 14px;
            box-shadow: 0 2px 12px rgba(106,130,251,0.13);
            transition: all 0.2s;
        }
        .btn-primary-gradient:hover, .btn-primary-gradient:focus {
            background: linear-gradient(90deg, #2d44aa 0%, #6a82fb 100%);
            color: #fff;
            box-shadow: 0 6px 24px rgba(45,68,170,0.18);
            transform: translateY(-2px) scale(1.04);
        }
        @media (max-width: 992px) {
            .product-image { height: 140px; }
            .product-info { padding: 1.1rem 1rem 1rem 1rem; }
        }
        @media (max-width: 768px) {
            .filters-bar { flex-direction: column; align-items: stretch; padding: 1rem; }
            .product-image { height: 110px; }
        }
        @media (max-width: 576px) {
            .category-title { font-size: 1.5rem; }
            .product-info { padding: 0.8rem 0.5rem 0.7rem 0.5rem; }
        }
    </style>
</head>
<body>
    <div class="container py-4">
        <div class="category-title mb-4">
            <?= $categoria ?>
        </div>
        <form class="filters-bar mb-4" method="get">
            <input type="hidden" name="categoria" value="<?= $categoria ?>">
            <div>
                <label for="busqueda"><i class="fas fa-search"></i> Buscar</label>
                <input type="text" class="form-control d-inline-block w-auto" id="busqueda" name="busqueda" placeholder="Nombre o palabra clave">
            </div>
            <div>
                <label for="precio_min">Precio</label>
                <input type="number" class="form-control d-inline-block w-auto" id="precio_min" name="precio_min" placeholder="Mín" min="0">
                <span>-</span>
                <input type="number" class="form-control d-inline-block w-auto" id="precio_max" name="precio_max" placeholder="Máx" min="0">
            </div>
            <div>
                <label for="ordenar">Ordenar</label>
                <select class="form-select d-inline-block w-auto" id="ordenar" name="ordenar">
                    <option value="">Por defecto</option>
                    <option value="precio_asc">Precio: Menor a mayor</option>
                    <option value="precio_desc">Precio: Mayor a menor</option>
                    <option value="nombre_asc">Nombre A-Z</option>
                    <option value="nombre_desc">Nombre Z-A</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary-gradient"><i class="fas fa-filter me-1"></i> Filtrar</button>
        </form>
        <div class="row" id="productos-lista">
            <?php if (empty($productos)): ?>
                <div class="col-12 text-center text-muted py-5">
                    <i class="fas fa-box-open fa-2x mb-2"></i><br>
                    No se encontraron productos para esta categoría.
                </div>
            <?php else: ?>
                <?php foreach ($productos as $producto): ?>
                <div class="col-lg-3 col-md-4 col-sm-6">
                    <div class="product-card">
                        <div class="product-image">
                            <?php
                            $imgName = preg_replace('#^uploads/#i', '', $producto['imagen']);
                            $imgUrl = '../../uploads/' . rawurlencode($imgName);
                            ?>
                            <img 
                                src="<?= $imgUrl ?>" 
                                alt="<?= htmlspecialchars($producto['nombre']) ?>" 
                                onerror="this.onerror=null;this.src='../../public/no-image.png';"
                            >
                        </div>
                        <div class="product-info">
                            <div class="product-title" style="min-height:2.7em;max-height:2.7em;overflow:hidden;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;line-height:1.35;"><?= htmlspecialchars($producto['nombre']) ?></div>
                            <div class="product-price">$<?= number_format($producto['precio'],2) ?></div>
                            <div class="text-muted" style="font-size:0.95rem;">Stock: <?= (int)$producto['stock'] ?></div>
                            <button class="btn btn-primary-gradient w-100 mt-2"><i class="fas fa-cart-plus"></i> Comprar</button>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
