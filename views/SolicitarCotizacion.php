<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Solicitar Cotización - Paints</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; }
        .header-cotizacion {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 40px 0;
            margin-bottom: 30px;
        }
        .form-section {
            background: white;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 2px 15px rgba(0,0,0,0.1);
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
        .resumen-box {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 25px;
            border-radius: 15px;
            position: sticky;
            top: 20px;
        }
    </style>
</head>
<body>

<!-- Navbar simple -->
<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <div class="container">
        <a class="navbar-brand fw-bold text-primary" href="index.php?action=home">
            <i class="bi bi-paint-bucket"></i> Paints
        </a>
        <div class="d-flex gap-2">
            <a href="index.php?action=catalogoProductos" class="btn btn-outline-primary">
                <i class="bi bi-palette"></i> Ver Productos
            </a>
            <a href="index.php?action=home" class="btn btn-secondary">
                <i class="bi bi-house"></i> Inicio
            </a>
        </div>
    </div>
</nav>

<!-- Header -->
<div class="header-cotizacion">
    <div class="container text-center">
        <h1><i class="bi bi-file-earmark-text"></i> Solicitar Cotización</h1>
        <p class="lead mb-0">Completa el formulario y te responderemos en menos de 24 horas</p>
    </div>
</div>

<div class="container mb-5">
    
    <?php if(isset($_SESSION['error'])): ?>
        <div class="alert alert-danger alert-dismissible">
            <i class="bi bi-exclamation-triangle-fill"></i>
            <?= $_SESSION['error']; unset($_SESSION['error']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <form method="POST" action="index.php?action=crearCotizacionPublica" id="formCotizacion">
        <div class="row">
            
            <!-- Columna Izquierda -->
            <div class="col-lg-8">
                
                <!-- Datos del Cliente -->
                <div class="form-section">
                    <h4 class="text-primary mb-3">
                        <i class="bi bi-person-fill"></i> Tus Datos
                    </h4>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Nombre Completo: <span class="text-danger">*</span></label>
                            <input type="text" name="nombre_cliente" class="form-control" 
                                   placeholder="Juan Pérez" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">NIT:</label>
                            <input type="text" name="nit" class="form-control" 
                                   placeholder="12345678-9 o CF" value="CF">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Email: <span class="text-danger">*</span></label>
                            <input type="email" name="email" class="form-control" 
                                   placeholder="correo@ejemplo.com" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Teléfono:</label>
                            <input type="tel" name="telefono" class="form-control" 
                                   placeholder="5555-5555">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Dirección (opcional):</label>
                            <textarea name="direccion" class="form-control" rows="2"
                                      placeholder="Dirección de entrega"></textarea>
                        </div>
                        <div class="col-12">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" 
                                       name="acepta_promociones" id="promociones">
                                <label class="form-check-label" for="promociones">
                                    Deseo recibir promociones y ofertas por email
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Productos -->
                <div class="form-section">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h4 class="text-success mb-0">
                            <i class="bi bi-cart-plus"></i> Productos
                        </h4>
                        <button type="button" class="btn btn-success btn-sm" onclick="agregarProducto()">
                            <i class="bi bi-plus-lg"></i> Agregar Producto
                        </button>
                    </div>
                    
                    <div id="productos-container">
                        <!-- Primera fila -->
                        <div class="row g-2 producto-item" data-index="0">
                            <div class="col-md-6">
                                <label class="form-label">Producto:</label>
                                <select name="detalles[0][producto_id]" 
                                        class="form-select producto-select" 
                                        required 
                                        onchange="cargarPrecio(this, 0)">
                                    <option value="">Seleccionar...</option>
                                    <?php 
                                    if (is_object($productos)):
                                        while($p = $productos->fetch(PDO::FETCH_ASSOC)): 
                                    ?>
                                        <option value="<?= $p['producto_id'] ?>" 
                                                data-precio="<?= $p['precio_base'] ?>"
                                                data-descuento="<?= $p['porcentaje_descuento'] ?>">
                                            <?= htmlspecialchars($p['nombre']) ?> 
                                            (<?= htmlspecialchars($p['codigo_sku']) ?>)
                                        </option>
                                    <?php 
                                        endwhile;
                                    endif;
                                    ?>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Cantidad:</label>
                                <input type="number" 
                                       name="detalles[0][cantidad]" 
                                       class="form-control cantidad-input" 
                                       min="1" 
                                       value="1"
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

            </div>

            <!-- Columna Derecha - Resumen -->
            <div class="col-lg-4">
                <div class="resumen-box">
                    <h5><i class="bi bi-calculator"></i> Resumen</h5>
                    <hr style="border-color: rgba(255,255,255,0.3);">
                    
                    <div class="d-flex justify-content-between mb-2">
                        <span>Subtotal:</span>
                        <strong>Q <span id="display-subtotal">0.00</span></strong>
                    </div>
                    <div class="d-flex justify-content-between mb-3">
                        <span>IVA (12%):</span>
                        <strong>Q <span id="display-iva">0.00</span></strong>
                    </div>
                    <hr style="border-color: rgba(255,255,255,0.3);">
                    <div class="d-flex justify-content-between mb-4">
                        <h5>Total Estimado:</h5>
                        <h5>Q <span id="display-total">0.00</span></h5>
                    </div>
                    
                    <div class="alert alert-light text-dark">
                        <small>
                            <i class="bi bi-info-circle"></i>
                            Este es un precio estimado. Recibirás una cotización oficial en tu correo.
                        </small>
                    </div>

                    <button type="submit" class="btn btn-light w-100 fw-bold">
                        <i class="bi bi-send"></i> Enviar Solicitud
                    </button>
                </div>
            </div>

        </div>

        <input type="hidden" name="subtotal" id="subtotal">
        <input type="hidden" name="impuestos" id="impuestos">
        <input type="hidden" name="total" id="total">
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
let productoIndex = 1;
const productosData = <?php 
// Guardar productos en JSON para JS
$db_temp = $db;
$stmt_temp = $db_temp->prepare("SELECT producto_id, nombre, codigo_sku, precio_base, porcentaje_descuento FROM producto WHERE activo = 1");
$stmt_temp->execute();
$productos_array = $stmt_temp->fetchAll(PDO::FETCH_ASSOC);
echo json_encode($productos_array);
?>;

function agregarProducto() {
    const container = document.getElementById('productos-container');
    
    // Generar opciones de productos
    let optionsHTML = '<option value="">Seleccionar...</option>';
    productosData.forEach(p => {
        optionsHTML += `<option value="${p.producto_id}" 
                                data-precio="${p.precio_base}"
                                data-descuento="${p.porcentaje_descuento}">
                            ${p.nombre} (${p.codigo_sku})
                        </option>`;
    });
    
    const html = `
    <div class="row g-2 producto-item" data-index="${productoIndex}">
        <div class="col-md-6">
            <label class="form-label">Producto:</label>
            <select name="detalles[${productoIndex}][producto_id]" 
                    class="form-select producto-select" 
                    required 
                    onchange="cargarPrecio(this, ${productoIndex})">
                ${optionsHTML}
            </select>
        </div>
        <div class="col-md-2">
            <label class="form-label">Cantidad:</label>
            <input type="number" name="detalles[${productoIndex}][cantidad]" 
                   class="form-control cantidad-input" 
                   min="1" value="1" required onchange="calcularSubtotal(${productoIndex})">
        </div>
        <div class="col-md-2">
            <label class="form-label">Precio:</label>
            <input type="number" name="detalles[${productoIndex}][precio_unitario]" 
                   class="form-control precio-input" 
                   step="0.01" required readonly>
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
    const precioConDescuento = precio * (1 - descuento / 100);
    row.querySelector('.precio-input').value = precioConDescuento.toFixed(2);
    
    calcularSubtotal(index);
}

function calcularSubtotal(index) {
    const row = document.querySelector(`.producto-item[data-index="${index}"]`);
    if (!row) return;
    
    const cantidad = parseFloat(row.querySelector('.cantidad-input').value) || 0;
    const precio = parseFloat(row.querySelector('.precio-input').value) || 0;
    
    const subtotal = cantidad * precio;
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
    
    document.getElementById('display-subtotal').textContent = subtotal.toFixed(2);
    document.getElementById('display-iva').textContent = iva.toFixed(2);
    document.getElementById('display-total').textContent = total.toFixed(2);
    
    document.getElementById('subtotal').value = subtotal.toFixed(2);
    document.getElementById('impuestos').value = iva.toFixed(2);
    document.getElementById('total').value = total.toFixed(2);
}

// Validar antes de enviar
document.getElementById('formCotizacion').addEventListener('submit', function(e) {
    const total = parseFloat(document.getElementById('total').value) || 0;
    
    if (total === 0) {
        e.preventDefault();
        alert('Debe agregar al menos un producto a la cotización');
        return false;
    }
    
    // Verificar que todos los productos tengan precio
    let productosValidos = true;
    document.querySelectorAll('.producto-select').forEach(select => {
        if (select.value === '') {
            productosValidos = false;
        }
    });
    
    if (!productosValidos) {
        e.preventDefault();
        alert('Debe seleccionar un producto en todas las filas');
        return false;
    }
    
    return true;
});
</script>

</body>
</html>