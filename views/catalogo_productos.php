<?php
/**
 * CATÁLOGO DE PRODUCTOS - VERSIÓN COMPLETA Y CORREGIDA
 */

// ⚠️ DEBUG: Activar errores (quitar en producción)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Verificar que $db existe (viene del index.php)
if (!isset($db)) {
    die("Error: No hay conexión a la base de datos");
}

// Obtener filtros de búsqueda
$busqueda = $_GET['buscar'] ?? '';
$orden = $_GET['orden'] ?? 'nombre_asc';

// ✅ Consulta optimizada y corregida
$query = "SELECT 
            p.producto_id,
            p.nombre,
            p.codigo_sku,
            p.descripcion,
            p.precio_base,
            p.porcentaje_descuento,
            p.color,
            p.cobertura_m2,
            p.duracion_anios,
            p.stock_minimo,
            p.activo,
            p.fecha_creacion,
            COALESCE(SUM(i.cantidad), 0) as stock_disponible,
            tp.nombre_tipo_producto AS tipo_nombre,
            m.nombre_medida
          FROM producto p
          LEFT JOIN inventario i ON p.producto_id = i.producto_id
          LEFT JOIN tipo_producto tp ON p.tipo_producto_id = tp.tipo_producto_id
          LEFT JOIN medida m ON p.medida_id = m.medida_id
          WHERE p.activo = 1";

// Agregar búsqueda si existe
if (!empty($busqueda)) {
    $query .= " AND (p.nombre LIKE :busqueda 
                OR p.codigo_sku LIKE :busqueda 
                OR p.color LIKE :busqueda
                OR p.descripcion LIKE :busqueda
                OR tp.nombre_tipo_producto LIKE :busqueda)";
}

// ✅ SOLUCIÓN: Solo agrupar por la clave primaria
$query .= " GROUP BY p.producto_id";

// Aplicar orden
switch ($orden) {
    case 'precio_asc':
        $query .= " ORDER BY p.precio_base ASC";
        break;
    case 'precio_desc':
        $query .= " ORDER BY p.precio_base DESC";
        break;
    case 'nombre_desc':
        $query .= " ORDER BY p.nombre DESC";
        break;
    case 'stock_asc':
        $query .= " ORDER BY stock_disponible ASC";
        break;
    case 'stock_desc':
        $query .= " ORDER BY stock_disponible DESC";
        break;
    default: // nombre_asc
        $query .= " ORDER BY p.nombre ASC";
        break;
}

// Preparar y ejecutar consulta
try {
    $stmt = $db->prepare($query);
    
    if (!empty($busqueda)) {
        $busqueda_param = "%{$busqueda}%";
        $stmt->bindParam(':busqueda', $busqueda_param, PDO::PARAM_STR);
    }
    
    $stmt->execute();
    $productos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Contar total de productos
    $total_productos = count($productos);
    
} catch (PDOException $e) {
    die("Error en la consulta: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Catálogo de Productos - PinturasWeb</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #667eea;
            --secondary-color: #764ba2;
        }

        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .catalog-header {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            color: white;
            padding: 40px 0;
            margin-bottom: 30px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
        }

        .catalog-header h1 {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 10px;
        }

        .navbar {
            background: white !important;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .cart-count {
            position: absolute;
            top: -5px;
            right: -5px;
            background: #dc3545;
            color: white;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.75rem;
            font-weight: bold;
        }

        .filter-section {
            background: white;
            padding: 20px;
            border-radius: 15px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }

        .product-card {
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 2px 15px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
            height: 100%;
            display: flex;
            flex-direction: column;
        }

        .product-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.3);
        }

        .product-image {
            width: 100%;
            height: 250px;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
        }

        .product-image i {
            font-size: 5rem;
            color: var(--primary-color);
            opacity: 0.3;
        }

        .product-badge {
            position: absolute;
            top: 10px;
            right: 10px;
            background: #dc3545;
            color: white;
            padding: 5px 15px;
            border-radius: 20px;
            font-weight: bold;
            font-size: 0.9rem;
        }

        .stock-badge {
            position: absolute;
            top: 10px;
            left: 10px;
            padding: 5px 15px;
            border-radius: 20px;
            font-weight: bold;
            font-size: 0.85rem;
            color: white;
        }

        .stock-badge.disponible { background: #28a745; }
        .stock-badge.bajo { background: #ffc107; }
        .stock-badge.agotado { background: #dc3545; }

        .product-body {
            padding: 20px;
            flex-grow: 1;
            display: flex;
            flex-direction: column;
        }

        .product-title {
            font-size: 1.2rem;
            font-weight: 600;
            color: #333;
            margin-bottom: 10px;
            min-height: 50px;
        }

        .product-sku {
            color: #999;
            font-size: 0.85rem;
            margin-bottom: 10px;
        }

        .product-description {
            color: #666;
            font-size: 0.9rem;
            margin-bottom: 15px;
            flex-grow: 1;
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .product-specs {
            background: #f8f9fa;
            padding: 10px;
            border-radius: 8px;
            margin-bottom: 15px;
            font-size: 0.85rem;
        }

        .product-specs div {
            display: flex;
            justify-content: space-between;
            margin-bottom: 5px;
        }

        .product-price-section {
            margin-top: auto;
        }

        .product-price {
            font-size: 2rem;
            font-weight: 700;
            color: var(--primary-color);
            margin-bottom: 10px;
        }

        .product-price-original {
            text-decoration: line-through;
            color: #999;
            font-size: 1.2rem;
            margin-left: 10px;
        }

        .btn-add-cart {
            width: 100%;
            padding: 12px;
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            border: none;
            color: white;
            font-weight: 600;
            border-radius: 10px;
            transition: all 0.3s;
        }

        .btn-add-cart:hover {
            transform: scale(1.05);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }

        .btn-add-cart:disabled {
            background: #6c757d;
            cursor: not-allowed;
        }

        .empty-catalog {
            text-align: center;
            padding: 80px 20px;
        }

        .empty-catalog i {
            font-size: 6rem;
            color: #ddd;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-light bg-light sticky-top">
    <div class="container">
        <a class="navbar-brand fw-bold text-primary" href="index.php?action=home">
            <i class="bi bi-paint-bucket"></i> PinturasWeb
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link" href="index.php?action=home">
                        <i class="bi bi-house-door"></i> Inicio
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link active" href="index.php?action=catalogoProductos">
                        <i class="bi bi-palette"></i> Catálogo
                    </a>
                </li>
            </ul>
            <div class="d-flex gap-2">
                <a href="index.php?action=verCarrito" class="btn btn-outline-primary position-relative">
                    <i class="bi bi-cart3"></i> Carrito
                    <span class="cart-count">
                        <?php echo isset($_SESSION['carrito']) ? count($_SESSION['carrito']) : 0; ?>
                    </span>
                </a>
                
                <?php if(isset($_SESSION['logueado']) && $_SESSION['logueado']): ?>
                    <a href="index.php?action=dashboard" class="btn btn-success">
                        <i class="bi bi-speedometer2"></i> Dashboard
                    </a>
                <?php else: ?>
                    <a href="index.php?action=loginForm" class="btn btn-primary">
                        <i class="bi bi-box-arrow-in-right"></i> Iniciar Sesión
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</nav>

<!-- Header -->
<div class="catalog-header">
    <div class="container text-center">
        <h1><i class="bi bi-palette-fill"></i> Catálogo de Productos</h1>
        <p class="lead mb-0">Encuentra las mejores pinturas para tu proyecto</p>
        <p class="mt-2 mb-0">
            <span class="badge bg-light text-dark fs-6">
                <?php echo $total_productos; ?> producto<?php echo $total_productos != 1 ? 's' : ''; ?> disponible<?php echo $total_productos != 1 ? 's' : ''; ?>
            </span>
        </p>
    </div>
</div>

<div class="container mb-5">
    
    <!-- Filtros y Búsqueda -->
    <div class="filter-section">
        <form method="GET" action="" class="row g-3">
            <input type="hidden" name="action" value="catalogoProductos">
            
            <div class="col-md-6">
                <div class="input-group">
                    <span class="input-group-text">
                        <i class="bi bi-search"></i>
                    </span>
                    <input type="text" 
                           class="form-control" 
                           name="buscar" 
                           placeholder="Buscar por nombre, SKU, color o tipo..."
                           value="<?php echo htmlspecialchars($busqueda); ?>">
                </div>
            </div>
            
            <div class="col-md-4">
                <select name="orden" class="form-select">
                    <option value="nombre_asc" <?php echo $orden === 'nombre_asc' ? 'selected' : ''; ?>>
                        Nombre (A-Z)
                    </option>
                    <option value="nombre_desc" <?php echo $orden === 'nombre_desc' ? 'selected' : ''; ?>>
                        Nombre (Z-A)
                    </option>
                    <option value="precio_asc" <?php echo $orden === 'precio_asc' ? 'selected' : ''; ?>>
                        Precio (Menor a Mayor)
                    </option>
                    <option value="precio_desc" <?php echo $orden === 'precio_desc' ? 'selected' : ''; ?>>
                        Precio (Mayor a Menor)
                    </option>
                    <option value="stock_desc" <?php echo $orden === 'stock_desc' ? 'selected' : ''; ?>>
                        Stock (Mayor a Menor)
                    </option>
                </select>
            </div>
            
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-funnel"></i> Filtrar
                </button>
            </div>
        </form>

        <?php if (!empty($busqueda)): ?>
            <div class="mt-3">
                <span class="badge bg-secondary">
                    Buscando: "<?php echo htmlspecialchars($busqueda); ?>"
                </span>
                <a href="index.php?action=catalogoProductos" class="btn btn-sm btn-outline-secondary ms-2">
                    <i class="bi bi-x"></i> Limpiar
                </a>
            </div>
        <?php endif; ?>
    </div>

    <!-- Grid de Productos -->
    <?php if ($total_productos > 0): ?>
        <div class="row g-4">
            <?php foreach ($productos as $producto): ?>
                <?php
                $precio_base = $producto['precio_base'];
                $descuento = $producto['porcentaje_descuento'];
                $precio_final = $precio_base * (1 - $descuento / 100);
                $stock = $producto['stock_disponible'];
                
                // Determinar estado del stock
                if ($stock <= 0) {
                    $stock_class = 'agotado';
                    $stock_text = 'Agotado';
                } elseif ($stock <= $producto['stock_minimo']) {
                    $stock_class = 'bajo';
                    $stock_text = 'Stock Bajo';
                } else {
                    $stock_class = 'disponible';
                    $stock_text = 'Disponible';
                }
                ?>
                
                <div class="col-lg-3 col-md-4 col-sm-6">
                    <div class="product-card">
                        <div class="product-image">
                            <i class="bi bi-droplet-fill"></i>
                            
                            <?php if ($descuento > 0): ?>
                                <span class="product-badge">
                                    -<?php echo $descuento; ?>%
                                </span>
                            <?php endif; ?>
                            
                            <span class="stock-badge <?php echo $stock_class; ?>">
                                <?php echo $stock_text; ?>
                            </span>
                        </div>
                        
                        <div class="product-body">
                            <h5 class="product-title">
                                <?php echo htmlspecialchars($producto['nombre']); ?>
                            </h5>
                            
                            <div class="product-sku">
                                <i class="bi bi-upc-scan"></i> 
                                SKU: <?php echo htmlspecialchars($producto['codigo_sku']); ?>
                            </div>
                            
                            <?php if (!empty($producto['descripcion'])): ?>
                                <p class="product-description">
                                    <?php echo htmlspecialchars($producto['descripcion']); ?>
                                </p>
                            <?php endif; ?>
                            
                            <div class="product-specs">
                                <?php if (!empty($producto['color'])): ?>
                                    <div>
                                        <span><i class="bi bi-palette"></i> Color:</span>
                                        <strong><?php echo htmlspecialchars($producto['color']); ?></strong>
                                    </div>
                                <?php endif; ?>
                                
                                <?php if (!empty($producto['nombre_medida'])): ?>
                                    <div>
                                        <span><i class="bi bi-rulers"></i> Medida:</span>
                                        <strong><?php echo htmlspecialchars($producto['nombre_medida']); ?></strong>
                                    </div>
                                <?php endif; ?>
                                
                                <?php if ($producto['cobertura_m2'] > 0): ?>
                                    <div>
                                        <span><i class="bi bi-bounding-box"></i> Cobertura:</span>
                                        <strong><?php echo $producto['cobertura_m2']; ?> m²</strong>
                                    </div>
                                <?php endif; ?>
                                
                                <div>
                                    <span><i class="bi bi-box-seam"></i> Stock:</span>
                                    <strong><?php echo $stock; ?> unidades</strong>
                                </div>
                            </div>
                            
                            <div class="product-price-section">
                                <div class="product-price">
                                    Q<?php echo number_format($precio_final, 2); ?>
                                    <?php if ($descuento > 0): ?>
                                        <span class="product-price-original">
                                            Q<?php echo number_format($precio_base, 2); ?>
                                        </span>
                                    <?php endif; ?>
                                </div>
                                
                                <?php if ($stock > 0): ?>
                                    <form method="POST" action="index.php?action=agregarCarrito">
                                        <input type="hidden" name="producto_id" value="<?php echo $producto['producto_id']; ?>">
                                        <input type="hidden" name="cantidad" value="1">
                                        <button type="submit" class="btn-add-cart">
                                            <i class="bi bi-cart-plus"></i> Agregar al Carrito
                                        </button>
                                    </form>
                                <?php else: ?>
                                    <button class="btn-add-cart" disabled>
                                        <i class="bi bi-x-circle"></i> Sin Stock
                                    </button>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="empty-catalog">
            <i class="bi bi-inbox"></i>
            <h3>No se encontraron productos</h3>
            <p class="text-muted">
                <?php if (!empty($busqueda)): ?>
                    No hay productos que coincidan con tu búsqueda.
                <?php else: ?>
                    No hay productos disponibles en este momento.
                <?php endif; ?>
            </p>
            <?php if (!empty($busqueda)): ?>
                <a href="index.php?action=catalogoProductos" class="btn btn-primary mt-3">
                    <i class="bi bi-arrow-left"></i> Ver Todos los Productos
                </a>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>