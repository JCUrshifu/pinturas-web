<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agregar Factura</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; }
        .page-header {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        .card-section {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        .producto-item { 
            border-bottom: 1px solid #dee2e6; 
            padding-bottom: 15px; 
            margin-bottom: 15px; 
        }
        .producto-item:last-child {
            border-bottom: none;
        }
        .totales-box { 
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px; 
            border-radius: 10px;
            position: sticky;
            top: 20px;
        }
        .totales-box h6 {
            border-bottom: 2px solid rgba(255,255,255,0.3);
            padding-bottom: 10px;
            margin-bottom: 15px;
        }
        .form-control:focus, .form-select:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }
    </style>
</head>
<body>

<div class="container mt-4 mb-5">
    <!-- Header -->
    <div class="page-header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2 class="mb-1"><i class="bi bi-receipt text-primary"></i> Nueva Factura</h2>
                <small class="text-muted">Complete todos los campos requeridos</small>
            </div>
            <a href="index.php?action=listarFacturas" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Volver
            </a>
        </div>
    </div>

    <?php if(isset($_SESSION['error'])): ?>
        <div class="alert alert-danger alert-dismissible">
            <i class="bi bi-exclamation-triangle-fill"></i>
            <?= $_SESSION['error']; unset($_SESSION['error']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <form method="POST" action="index.php?action=crearFactura" id="formFactura">
        <div class="row">
            <div class="col-lg-8">
                <!-- Datos de la Factura -->
                <div class="card-section">
                    <h5 class="text-primary mb-3">
                        <i class="bi bi-file-text"></i> Datos de la Factura
                    </h5>
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label">Serie: <span class="text-danger">*</span></label>
                            <input type="text" name="serie_factura" class="form-control" 
                                   placeholder="A" maxlength="5" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Número: <span class="text-danger">*</span></label>
                            <input type="text" name="numero_factura" class="form-control" 
                                   placeholder="001-0000001" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Fecha: <span class="text-danger">*</span></label>
                            <input type="date" name="fecha" class="form-control" 
                                   value="<?= date('Y-m-d') ?>" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Sucursal: <span class="text-danger">*</span></label>
                            <select name="sucursal_id" class="form-select" required>
                                <option value="">Seleccionar...</option>
                                <?php 
                                // Obtener sucursales directamente
                                $stmt_sucursales = $db->query("SELECT sucursal_id, nombre_sucursal FROM sucursal WHERE activa = 1");
                                while($s = $stmt_sucursales->fetch(PDO::FETCH_ASSOC)): 
                                ?>
                                    <option value="<?= $s['sucursal_id'] ?>">
                                        <?= htmlspecialchars($s['nombre_sucursal']) ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Cliente: <span class="text-danger">*</span></label>
                            <select name="cliente_id" class="form-select" required>
                                <option value="">Seleccionar Cliente...</option>
                                <?php 
                                if (is_object($clientes)):
                                    while($c = $clientes->fetch(PDO::FETCH_ASSOC)): 
                                ?>
                                    <option value="<?= $c['cliente_id'] ?>">
                                        <?= htmlspecialchars($c['nombre_cliente']) ?> - 
                                        <?= htmlspecialchars($c['nit'] ?? 'CF') ?>
                                    </option>
                                <?php 
                                    endwhile; 
                                endif;
                                ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Empleado: <span class="text-danger">*</span></label>
                            <select name="empleado_id" class="form-select" required>
                                <option value="">Seleccionar Empleado...</option>
                                <?php 
                                if (is_object($empleados)):
                                    while($e = $empleados->fetch(PDO::FETCH_ASSOC)): 
                                ?>
                                    <option value="<?= $e['empleado_id'] ?>">
                                        <?= htmlspecialchars($e['nombre_empleado']) ?> 
                                        <?= htmlspecialchars($e['apellido_empleado']) ?>
                                    </option>
                                <?php 
                                    endwhile;
                                endif;
                                ?>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Productos -->
                <div class="card-section">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="text-success mb-0">
                            <i class="bi bi-cart-plus"></i> Productos
                        </h5>
                        <button type="button" class="btn btn-success btn-sm" onclick="agregarProducto()">
                            <i class="bi bi-plus-lg"></i> Agregar Producto
                        </button>
                    </div>
                    
                    <div id="productos-container">
                        <!-- Primera fila de producto -->
                        <div class="row g-2 producto-item" data-index="0">
                            <div class="col-md-5">
                                <label class="form-label">Producto:</label>
                                <select name="detalles[0][producto_id]" 
                                        class="form-select producto-select" 
                                        required 
                                        onchange="cargarPrecio(this, 0)">
                                    <option value="">Seleccionar...</option>
                                    <?php 
                                    // CORREGIDO: Usar inventario en lugar de campos inexistentes
                                    $query = "SELECT p.producto_id, p.nombre, p.codigo_sku, p.precio_base, 
                                                     p.porcentaje_descuento, COALESCE(SUM(i.cantidad), 0) as stock_disponible
                                              FROM producto p
                                              LEFT JOIN inventario i ON p.producto_id = i.producto_id
                                              WHERE p.activo = 1
                                              GROUP BY p.producto_id, p.nombre, p.codigo_sku, p.precio_base, p.porcentaje_descuento
                                              ORDER BY p.nombre";
                                    $stmt = $db->query($query);
                                    while($p = $stmt->fetch(PDO::FETCH_ASSOC)): 
                                    ?>
                                        <option value="<?= $p['producto_id'] ?>" 
                                                data-precio="<?= $p['precio_base'] ?>"
                                                data-stock="<?= $p['stock_disponible'] ?>"
                                                data-descuento="<?= $p['porcentaje_descuento'] ?>">
                                            <?= htmlspecialchars($p['nombre']) ?> 
                                            (<?= htmlspecialchars($p['codigo_sku']) ?>) - 
                                            Stock: <?= $p['stock_disponible'] ?>
                                        </option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Cantidad:</label>
                                <input type="number" 
                                       name="detalles[0][cantidad]" 
                                       class="form-control cantidad-input" 
                                       min="1" 
                                       step="1" 
                                       required 
                                       onchange="calcularSubtotal(0)">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Precio:</label>
                                <input type="number" 
                                       name="detalles[0][precio_unitario]" 
                                       class="form-control precio-input" 
                                       step="0.01" 
                                       required 
                                       readonly>
                            </div>
                            <div class="col-md-1">
                                <label class="form-label">Desc%:</label>
                                <input type="number" 
                                       name="detalles[0][porcentaje_descuento]" 
                                       class="form-control descuento-input" 
                                       step="0.01" 
                                       value="0" 
                                       min="0" 
                                       max="100" 
                                       onchange="calcularSubtotal(0)">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Subtotal:</label>
                                <input type="text" 
                                       class="form-control subtotal-display" 
                                       readonly 
                                       value="0.00">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Medios de Pago -->
                <div class="card-section">
                    <h5 class="text-info mb-3">
                        <i class="bi bi-credit-card"></i> Medios de Pago
                    </h5>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">
                                <i class="bi bi-cash-stack text-success"></i> Efectivo:
                            </label>
                            <input type="number" 
                                   name="efectivo" 
                                   id="efectivo" 
                                   class="form-control" 
                                   step="0.01" 
                                   value="0" 
                                   min="0" 
                                   onchange="calcularTotales()">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">
                                <i class="bi bi-credit-card text-warning"></i> Tarjeta:
                            </label>
                            <input type="number" 
                                   name="tarjeta" 
                                   id="tarjeta" 
                                   class="form-control" 
                                   step="0.01" 
                                   value="0" 
                                   min="0" 
                                   onchange="calcularTotales()">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">
                                <i class="bi bi-file-earmark-text text-info"></i> Cheque:
                            </label>
                            <input type="number" 
                                   name="cheque" 
                                   id="cheque" 
                                   class="form-control" 
                                   step="0.01" 
                                   value="0" 
                                   min="0" 
                                   onchange="calcularTotales()">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Columna derecha - Resumen -->
            <div class="col-lg-4">
                <div class="totales-box">
                    <h6><i class="bi bi-calculator"></i> Resumen de Factura</h6>
                    
                    <div class="d-flex justify-content-between mb-2">
                        <span>Subtotal:</span>
                        <strong>Q <span id="display-subtotal">0.00</span></strong>
                    </div>
                    <div class="d-flex justify-content-between mb-3">
                        <span>IVA (12%):</span>
                        <strong>Q <span id="display-iva">0.00</span></strong>
                    </div>
                    <hr style="border-color: rgba(255,255,255,0.3);">
                    <div class="d-flex justify-content-between mb-3">
                        <h5 class="mb-0">Total:</h5>
                        <h5 class="mb-0">Q <span id="display-total">0.00</span></h5>
                    </div>
                    
                    <div class="alert alert-light text-dark mb-0">
                        <small>
                            <strong>Pagado:</strong> Q <span id="display-pagado">0.00</span><br>
                            <strong>Cambio:</strong> Q <span id="display-cambio">0.00</span>
                        </small>
                    </div>

                    <button type="submit" class="btn btn-light w-100 mt-3 fw-bold">
                        <i class="bi bi-save"></i> Crear Factura
                    </button>
                </div>
            </div>
        </div>

        <!-- Campos ocultos -->
        <input type="hidden" name="subtotal" id="subtotal">
        <input type="hidden" name="impuestos" id="impuestos">
        <input type="hidden" name="total" id="total">
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
let productoIndex = 1;

function agregarProducto() {
    const container = document.getElementById('productos-container');
    const html = `
    <div class="row g-2 producto-item" data-index="${productoIndex}">
        <div class="col-md-5">
            <label class="form-label">Producto:</label>
            <select name="detalles[${productoIndex}][producto_id]" 
                    class="form-select producto-select" 
                    required 
                    onchange="cargarPrecio(this, ${productoIndex})">
                <option value="">Seleccionar...</option>
                <?php 
                $query = "SELECT p.producto_id, p.nombre, p.codigo_sku, p.precio_base, 
                                 p.porcentaje_descuento, COALESCE(SUM(i.cantidad), 0) as stock_disponible
                          FROM producto p
                          LEFT JOIN inventario i ON p.producto_id = i.producto_id
                          WHERE p.activo = 1
                          GROUP BY p.producto_id
                          ORDER BY p.nombre";
                $stmt = $db->query($query);
                while($p = $stmt->fetch(PDO::FETCH_ASSOC)): 
                ?>
                    <option value="<?= $p['producto_id'] ?>" 
                            data-precio="<?= $p['precio_base'] ?>"
                            data-stock="<?= $p['stock_disponible'] ?>"
                            data-descuento="<?= $p['porcentaje_descuento'] ?>">
                        <?= htmlspecialchars($p['nombre']) ?> (<?= htmlspecialchars($p['codigo_sku']) ?>) - 
                        Stock: <?= $p['stock_disponible'] ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </div>
        <div class="col-md-2">
            <label class="form-label">Cantidad:</label>
            <input type="number" name="detalles[${productoIndex}][cantidad]" 
                   class="form-control cantidad-input" 
                   min="1" step="1" required onchange="calcularSubtotal(${productoIndex})">
        </div>
        <div class="col-md-2">
            <label class="form-label">Precio:</label>
            <input type="number" name="detalles[${productoIndex}][precio_unitario]" 
                   class="form-control precio-input" 
                   step="0.01" required readonly>
        </div>
        <div class="col-md-1">
            <label class="form-label">Desc%:</label>
            <input type="number" name="detalles[${productoIndex}][porcentaje_descuento]" 
                   class="form-control descuento-input" 
                   step="0.01" value="0" min="0" max="100" 
                   onchange="calcularSubtotal(${productoIndex})">
        </div>
        <div class="col-md-1">
            <label class="form-label">Subtotal:</label>
            <input type="text" class="form-control subtotal-display" readonly value="0.00">
        </div>
        <div class="col-md-1">
            <label class="form-label">&nbsp;</label>
            <button type="button" class="btn btn-danger w-100" onclick="eliminarProducto(this)">
                <i class="bi bi-trash"></i>
            </button>
        </div>
    </div>`;
    container.insertAdjacentHTML('beforeend', html);
    productoIndex++;
}

function eliminarProducto(btn) {
    btn.closest('.producto-item').remove();
    calcularTotales();
}

function cargarPrecio(select, index) {
    const option = select.options[select.selectedIndex];
    const precio = parseFloat(option.getAttribute('data-precio')) || 0;
    const descuento = parseFloat(option.getAttribute('data-descuento')) || 0;
    
    const row = select.closest('.producto-item');
    row.querySelector('.precio-input').value = precio.toFixed(2);
    row.querySelector('.descuento-input').value = descuento;
    
    calcularSubtotal(index);
}

function calcularSubtotal(index) {
    const row = document.querySelector(`.producto-item[data-index="${index}"]`);
    if (!row) return;
    
    const cantidad = parseFloat(row.querySelector('.cantidad-input').value) || 0;
    const precio = parseFloat(row.querySelector('.precio-input').value) || 0;
    const descuento = parseFloat(row.querySelector('.descuento-input').value) || 0;
    
    // Validar stock
    const select = row.querySelector('.producto-select');
    const stock = parseFloat(select.options[select.selectedIndex].getAttribute('data-stock')) || 0;
    
    if (cantidad > stock) {
        alert(`Solo hay ${stock} unidades disponibles`);
        row.querySelector('.cantidad-input').value = stock;
        return;
    }
    
    const subtotal = cantidad * precio * (1 - descuento / 100);
    row.querySelector('.subtotal-display').value = subtotal.toFixed(2);
    
    calcularTotales();
}

function calcularTotales() {
    let subtotal = 0;
    
    document.querySelectorAll('.subtotal-display').forEach(input => {
        subtotal += parseFloat(input.value) || 0;
    });
    
    const iva = subtotal * 0.12;
    const total = subtotal + iva;
    
    const efectivo = parseFloat(document.getElementById('efectivo').value) || 0;
    const tarjeta = parseFloat(document.getElementById('tarjeta').value) || 0;
    const cheque = parseFloat(document.getElementById('cheque').value) || 0;
    const pagado = efectivo + tarjeta + cheque;
    const cambio = pagado - total;
    
    document.getElementById('display-subtotal').textContent = subtotal.toFixed(2);
    document.getElementById('display-iva').textContent = iva.toFixed(2);
    document.getElementById('display-total').textContent = total.toFixed(2);
    document.getElementById('display-pagado').textContent = pagado.toFixed(2);
    document.getElementById('display-cambio').textContent = cambio >= 0 ? cambio.toFixed(2) : '0.00';
    
    document.getElementById('subtotal').value = subtotal.toFixed(2);
    document.getElementById('impuestos').value = iva.toFixed(2);
    document.getElementById('total').value = total.toFixed(2);
}

// Validar antes de enviar
document.getElementById('formFactura').addEventListener('submit', function(e) {
    const total = parseFloat(document.getElementById('total').value) || 0;
    const efectivo = parseFloat(document.getElementById('efectivo').value) || 0;
    const tarjeta = parseFloat(document.getElementById('tarjeta').value) || 0;
    const cheque = parseFloat(document.getElementById('cheque').value) || 0;
    const pagado = efectivo + tarjeta + cheque;
    
    if (total === 0) {
        e.preventDefault();
        alert('Debe agregar al menos un producto a la factura');
        return false;
    }
    
    if (pagado < total) {
        e.preventDefault();
        alert(`Falta pagar: Q${(total - pagado).toFixed(2)}`);
        return false;
    }
    
    if (pagado > total) {
        const cambio = pagado - total;
        if (!confirm(`El cliente pagó Q${pagado.toFixed(2)} y el total es Q${total.toFixed(2)}.\n¿Dar cambio de Q${cambio.toFixed(2)}?`)) {
            e.preventDefault();
            return false;
        }
    }
});
</script>
</body>
</html>