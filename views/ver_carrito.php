<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Carrito de Compras - Paints</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #667eea;
            --danger-color: #dc3545;
        }
        
        body {
            background-color: #f8f9fa;
        }

        .cart-header {
            background: white;
            padding: 20px 0;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }

        .cart-container {
            background: white;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 2px 15px rgba(0,0,0,0.08);
            margin-bottom: 20px;
        }

        .cart-item {
            display: flex;
            gap: 20px;
            padding: 20px;
            border-bottom: 1px solid #e0e0e0;
            align-items: center;
        }

        .cart-item:last-child {
            border-bottom: none;
        }

        .item-image {
            width: 100px;
            height: 100px;
            object-fit: contain;
            background: #f8f9fa;
            padding: 10px;
            border-radius: 10px;
        }

        .item-details {
            flex: 1;
        }

        .item-name {
            font-weight: 600;
            color: #333;
            margin-bottom: 5px;
        }

        .item-sku {
            color: #999;
            font-size: 0.9rem;
        }

        .item-price {
            font-size: 1.2rem;
            color: var(--danger-color);
            font-weight: 700;
        }

        .quantity-controls {
            display: flex;
            align-items: center;
            gap: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 5px;
        }

        .quantity-btn {
            background: white;
            border: none;
            width: 30px;
            height: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            border-radius: 3px;
            transition: all 0.3s;
        }

        .quantity-btn:hover {
            background: var(--primary-color);
            color: white;
        }

        .quantity-display {
            font-weight: 600;
            min-width: 30px;
            text-align: center;
        }

        .btn-remove {
            color: var(--danger-color);
            cursor: pointer;
            font-size: 1.2rem;
            transition: all 0.3s;
        }

        .btn-remove:hover {
            transform: scale(1.2);
        }

        .summary-card {
            background: white;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 2px 15px rgba(0,0,0,0.08);
            position: sticky;
            top: 20px;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #f0f0f0;
        }

        .summary-row:last-child {
            border-bottom: none;
            border-top: 2px solid #333;
            padding-top: 15px;
            font-size: 1.3rem;
            font-weight: 700;
        }

        .empty-cart {
            text-align: center;
            padding: 60px 20px;
        }

        .empty-cart i {
            font-size: 5rem;
            color: #ddd;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>

<!-- Header -->
<div class="cart-header">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center">
            <h2 class="mb-0">
                <i class="bi bi-cart3" style="color: var(--primary-color);"></i> 
                Carrito de Compras
            </h2>
            <a href="index.php?action=catalogoProductos" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Seguir Comprando
            </a>
        </div>
    </div>
</div>

<div class="container mb-5">
    
    <?php if(isset($_SESSION['mensaje'])): ?>
        <div class="alert alert-success alert-dismissible">
            <i class="bi bi-check-circle-fill"></i>
            <?= $_SESSION['mensaje']; unset($_SESSION['mensaje']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if(isset($_SESSION['error'])): ?>
        <div class="alert alert-danger alert-dismissible">
            <i class="bi bi-exclamation-triangle-fill"></i>
            <?= $_SESSION['error']; unset($_SESSION['error']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="row">
        <!-- Productos del Carrito -->
        <div class="col-lg-8">
            <div class="cart-container">
                <h4 class="mb-4">Productos (
                    <?php 
                    $totalItems = 0;
                    if(isset($_SESSION['carrito'])) {
                        foreach($_SESSION['carrito'] as $item) {
                            $totalItems += $item['cantidad'];
                        }
                    }
                    echo $totalItems;
                    ?>)
                </h4>

                <?php if(isset($_SESSION['carrito']) && count($_SESSION['carrito']) > 0): ?>
                    <?php foreach($_SESSION['carrito'] as $index => $item): ?>
                        <div class="cart-item">
                            <img src="https://via.placeholder.com/100x100/667eea/ffffff?text=<?= urlencode($item['nombre']) ?>" 
                                 alt="<?= htmlspecialchars($item['nombre']) ?>" 
                                 class="item-image">
                            
                            <div class="item-details">
                                <div class="item-name"><?= htmlspecialchars($item['nombre']) ?></div>
                                <div class="item-sku">SKU: <?= htmlspecialchars($item['sku'] ?? 'N/A') ?></div>
                                
                                <?php if(isset($item['descuento']) && $item['descuento'] > 0): ?>
                                    <div class="text-muted small">
                                        <del>Q<?= number_format($item['precio_base'], 2) ?></del> 
                                        <span class="badge bg-danger">-<?= $item['descuento'] ?>%</span>
                                    </div>
                                <?php endif; ?>
                                
                                <div class="item-price mt-2">Q<?= number_format($item['precio'], 2) ?></div>
                            </div>

                            <form method="POST" action="index.php?action=actualizarCarrito" class="d-inline">
                                <input type="hidden" name="index" value="<?= $index ?>">
                                <div class="quantity-controls">
                                    <button type="submit" name="cantidad" value="<?= max(1, $item['cantidad'] - 1) ?>" class="quantity-btn">
                                        <i class="bi bi-dash"></i>
                                    </button>
                                    <div class="quantity-display"><?= $item['cantidad'] ?></div>
                                    <button type="submit" name="cantidad" value="<?= $item['cantidad'] + 1 ?>" class="quantity-btn">
                                        <i class="bi bi-plus"></i>
                                    </button>
                                </div>
                            </form>

                            <div style="min-width: 100px; text-align: right;">
                                <strong>Q<?= number_format($item['precio'] * $item['cantidad'], 2) ?></strong>
                            </div>

                            <a href="index.php?action=eliminarDelCarrito&index=<?= $index ?>" 
                               class="btn-remove" 
                               onclick="return confirm('¿Eliminar este producto del carrito?')"
                               title="Eliminar">
                                <i class="bi bi-trash"></i>
                            </a>
                        </div>
                    <?php endforeach; ?>

                <?php else: ?>
                    <div class="empty-cart">
                        <i class="bi bi-cart-x"></i>
                        <h4>Tu carrito está vacío</h4>
                        <p class="text-muted">Agrega productos para comenzar tu compra</p>
                        <a href="index.php?action=catalogoProductos" class="btn btn-primary mt-3">
                            Ver Productos
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Resumen -->
        <div class="col-lg-4">
            <div class="summary-card">
                <h4 class="mb-4">Resumen del Pedido</h4>

                <?php
                $subtotal = 0;
                if(isset($_SESSION['carrito'])) {
                    foreach($_SESSION['carrito'] as $item) {
                        $subtotal += $item['precio'] * $item['cantidad'];
                    }
                }
                $iva = $subtotal * 0.12;
                $total = $subtotal + $iva;
                $envioGratis = $subtotal >= 200;
                ?>

                <div class="summary-row">
                    <span>Subtotal:</span>
                    <span>Q<?= number_format($subtotal, 2) ?></span>
                </div>

                <div class="summary-row">
                    <span>IVA (12%):</span>
                    <span>Q<?= number_format($iva, 2) ?></span>
                </div>

                <div class="summary-row">
                    <span>Envío:</span>
                    <span class="<?= $envioGratis ? 'text-success' : 'text-muted' ?>">
                        <?= $envioGratis ? 'GRATIS' : 'Q25.00' ?>
                    </span>
                </div>

                <div class="summary-row">
                    <span>Total:</span>
                    <span>Q<?= number_format($total + ($envioGratis ? 0 : 25), 2) ?></span>
                </div>

                <?php if(isset($_SESSION['carrito']) && count($_SESSION['carrito']) > 0): ?>
                    <a href="index.php?action=checkout" class="btn btn-primary w-100 mt-4">
                        <i class="bi bi-credit-card"></i> Proceder al Pago
                    </a>
                <?php else: ?>
                    <button class="btn btn-primary w-100 mt-4" disabled>
                        <i class="bi bi-credit-card"></i> Proceder al Pago
                    </button>
                <?php endif; ?>

                <a href="index.php?action=vaciarCarrito" 
                   class="btn btn-outline-danger w-100 mt-2"
                   onclick="return confirm('¿Estás seguro de vaciar todo el carrito?')">
                    <i class="bi bi-trash"></i> Vaciar Carrito
                </a>
            </div>

            <!-- Información Adicional -->
            <div class="summary-card mt-3">
                <h6><i class="bi bi-shield-check"></i> Compra Segura</h6>
                <p class="small text-muted mb-0">
                    Tus datos están protegidos con encriptación SSL
                </p>
            </div>

            <div class="summary-card mt-3">
                <h6><i class="bi bi-truck"></i> Envío Gratis</h6>
                <p class="small text-muted mb-0">
                    En compras mayores a Q200
                </p>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>