<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Finalizar Compra - Paints</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .checkout-header {
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
        .payment-method {
            border: 2px solid #ddd;
            border-radius: 10px;
            padding: 15px;
            cursor: pointer;
            transition: all 0.3s;
            margin-bottom: 15px;
        }
        .payment-method:hover {
            border-color: #667eea;
            background: #f8f9ff;
        }
        .payment-method input[type="radio"] {
            margin-right: 10px;
        }
        .payment-method.selected {
            border-color: #667eea;
            background: #f8f9ff;
        }
    </style>
</head>
<body>

<!-- Header -->
<div class="checkout-header">
    <div class="container text-center">
        <h1><i class="bi bi-credit-card"></i> Finalizar Compra</h1>
        <p class="mb-0">Completa tu información para procesar el pedido</p>
    </div>
</div>

<div class="container mb-5">
    
    <!-- Indicador de pasos -->
    <div class="row mb-4">
        <div class="col text-center">
            <div class="d-inline-flex align-items-center gap-3">
                <div class="badge bg-primary rounded-circle" style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; font-size: 1.2rem;">1</div>
                <span class="fw-bold">Datos Personales</span>
                <i class="bi bi-arrow-right text-muted"></i>
                <div class="badge bg-secondary rounded-circle" style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; font-size: 1.2rem;">2</div>
                <span class="text-muted">Método de Pago</span>
                <i class="bi bi-arrow-right text-muted"></i>
                <div class="badge bg-secondary rounded-circle" style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; font-size: 1.2rem;">3</div>
                <span class="text-muted">Confirmación</span>
            </div>
        </div>
    </div>

    <form method="POST" action="index.php?action=procesarPedido" id="formCheckout">
        <div class="row">
            
            <!-- Columna Izquierda -->
            <div class="col-lg-8">
                
                <!-- Datos Personales -->
                <div class="form-section">
                    <h4 class="text-primary mb-3">
                        <i class="bi bi-person-fill"></i> Datos Personales
                    </h4>
                    
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Nombre Completo: <span class="text-danger">*</span></label>
                            <input type="text" 
                                   name="nombre_cliente" 
                                   class="form-control" 
                                   placeholder="Juan Pérez"
                                   value="<?php 
                                       if(isset($cliente)) {
                                           echo htmlspecialchars($cliente['nombre_cliente']);
                                       } elseif(isset($usuario)) {
                                           echo htmlspecialchars($usuario['username']);
                                       }
                                   ?>"
                                   required>
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label">NIT:</label>
                            <input type="text" 
                                   name="nit" 
                                   class="form-control" 
                                   placeholder="12345678-9 o CF" 
                                   value="<?php echo isset($cliente) ? htmlspecialchars($cliente['nit']) : 'CF'; ?>">
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label">Email: <span class="text-danger">*</span></label>
                            <input type="email" 
                                   name="email" 
                                   class="form-control" 
                                   placeholder="correo@ejemplo.com"
                                   value="<?php 
                                       if(isset($cliente)) {
                                           echo htmlspecialchars($cliente['email']);
                                       } elseif(isset($usuario)) {
                                           echo htmlspecialchars($usuario['email']);
                                       }
                                   ?>"
                                   required>
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label">Teléfono: <span class="text-danger">*</span></label>
                            <input type="tel" 
                                   name="telefono" 
                                   class="form-control" 
                                   placeholder="5555-5555"
                                   value="<?php echo isset($cliente) ? htmlspecialchars($cliente['telefono']) : ''; ?>"
                                   required>
                        </div>
                        
                        <div class="col-12">
                            <label class="form-label">Dirección de Entrega: <span class="text-danger">*</span></label>
                            <textarea name="direccion" 
                                      class="form-control" 
                                      rows="3" 
                                      placeholder="Calle, zona, ciudad..." 
                                      required><?php echo isset($cliente) ? htmlspecialchars($cliente['direccion']) : ''; ?></textarea>
                        </div>
                        
                        <div class="col-12">
                            <div class="form-check">
                                <input class="form-check-input" 
                                       type="checkbox" 
                                       name="acepta_promociones" 
                                       id="promociones"
                                       <?php echo (isset($cliente) && $cliente['acepta_promociones']) ? 'checked' : ''; ?>>
                                <label class="form-check-label" for="promociones">
                                    Deseo recibir promociones y ofertas por email
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Método de Pago -->
                <div class="form-section">
                    <h4 class="text-success mb-3">
                        <i class="bi bi-credit-card"></i> Método de Pago
                    </h4>
                    
                    <div class="payment-method" onclick="selectPayment('efectivo')">
                        <input type="radio" name="metodo_pago" value="efectivo" id="pago-efectivo" required>
                        <label for="pago-efectivo" class="mb-0" style="cursor: pointer;">
                            <i class="bi bi-cash-stack text-success"></i>
                            <strong>Pago Contra Entrega (Efectivo)</strong>
                            <p class="text-muted small mb-0">Paga cuando recibas tu pedido</p>
                        </label>
                    </div>

                    <div class="payment-method" onclick="selectPayment('tarjeta')">
                        <input type="radio" name="metodo_pago" value="tarjeta" id="pago-tarjeta">
                        <label for="pago-tarjeta" class="mb-0" style="cursor: pointer;">
                            <i class="bi bi-credit-card text-primary"></i>
                            <strong>Tarjeta de Crédito/Débito</strong>
                            <p class="text-muted small mb-0">Pago seguro con tarjeta</p>
                        </label>
                    </div>

                    <div class="payment-method" onclick="selectPayment('transferencia')">
                        <input type="radio" name="metodo_pago" value="transferencia" id="pago-transferencia">
                        <label for="pago-transferencia" class="mb-0" style="cursor: pointer;">
                            <i class="bi bi-bank text-info"></i>
                            <strong>Transferencia Bancaria</strong>
                            <p class="text-muted small mb-0">Te enviaremos los datos bancarios</p>
                        </label>
                    </div>
                </div>

                <!-- Notas Adicionales -->
                <div class="form-section">
                    <h4 class="mb-3">
                        <i class="bi bi-chat-left-text text-warning"></i> 
                        Notas Adicionales (Opcional)
                    </h4>
                    <textarea name="notas" class="form-control" rows="3" 
                              placeholder="Instrucciones especiales de entrega, horarios preferidos, etc."></textarea>
                </div>

            </div>

            <!-- Columna Derecha: Resumen -->
            <div class="col-lg-4">
                <div class="resumen-box">
                    <h4 class="mb-4">
                        <i class="bi bi-basket"></i> 
                        Resumen del Pedido
                    </h4>

                    <!-- Productos -->
                    <?php
                    $subtotal = 0;
                    if(isset($_SESSION['carrito']) && count($_SESSION['carrito']) > 0):
                        foreach($_SESSION['carrito'] as $item):
                            $subtotal += $item['precio'] * $item['cantidad'];
                    ?>
                        <div class="producto-item">
                            <div class="d-flex justify-content-between align-items-start">
                                <div class="flex-grow-1">
                                    <strong><?= htmlspecialchars($item['nombre']) ?></strong>
                                    <br>
                                    <small class="opacity-75">
                                        <?= $item['cantidad'] ?> x Q<?= number_format($item['precio'], 2) ?>
                                    </small>
                                </div>
                                <strong>Q<?= number_format($item['precio'] * $item['cantidad'], 2) ?></strong>
                            </div>
                        </div>
                    <?php 
                        endforeach;
                    else:
                    ?>
                        <div class="alert alert-warning">
                            <i class="bi bi-exclamation-triangle"></i>
                            Tu carrito está vacío
                        </div>
                    <?php endif; ?>

                    <!-- Totales -->
                    <?php if(isset($_SESSION['carrito']) && count($_SESSION['carrito']) > 0): ?>
                        <?php
                        $iva = $subtotal * 0.12;
                        $envioGratis = $subtotal >= 200;
                        $costoEnvio = $envioGratis ? 0 : 25;
                        $total = $subtotal + $iva + $costoEnvio;
                        ?>

                        <hr style="border-color: rgba(255,255,255,0.3);">
                        
                        <div class="d-flex justify-content-between mb-2 opacity-75">
                            <span>Subtotal:</span>
                            <strong>Q<?= number_format($subtotal, 2) ?></strong>
                        </div>

                        <div class="d-flex justify-content-between mb-2 opacity-75">
                            <span>IVA (12%):</span>
                            <strong>Q<?= number_format($iva, 2) ?></strong>
                        </div>

                        <div class="d-flex justify-content-between mb-3 opacity-75">
                            <span>Envío:</span>
                            <strong class="<?= $envioGratis ? 'text-warning' : '' ?>">
                                <?= $envioGratis ? 'GRATIS' : 'Q' . number_format($costoEnvio, 2) ?>
                            </strong>
                        </div>

                        <hr style="border-color: rgba(255,255,255,0.5);">

                        <div class="d-flex justify-content-between mb-4">
                            <h4>TOTAL:</h4>
                            <h4>Q<?= number_format($total, 2) ?></h4>
                        </div>

                        <button type="submit" class="btn btn-light w-100 py-3 fw-bold">
                            <i class="bi bi-check-circle"></i> 
                            Confirmar Pedido
                        </button>

                        <a href="index.php?action=verCarrito" class="btn btn-outline-light w-100 mt-2">
                            <i class="bi bi-arrow-left"></i> 
                            Volver al Carrito
                        </a>

                    <?php else: ?>
                        <a href="index.php?action=catalogoProductos" class="btn btn-light w-100 mt-3">
                            Ver Productos
                        </a>
                    <?php endif; ?>

                    <!-- Garantías -->
                    <div class="mt-4 pt-3" style="border-top: 1px solid rgba(255,255,255,0.2);">
                        <small class="opacity-75">
                            <i class="bi bi-shield-check"></i> Compra segura<br>
                            <i class="bi bi-truck"></i> Envío en 24-48 horas<br>
                            <i class="bi bi-arrow-repeat"></i> 30 días de garantía
                        </small>
                    </div>
                </div>
            </div>

        </div>
    </form>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
function selectPayment(method) {
    // Remover clase selected de todos
    document.querySelectorAll('.payment-method').forEach(el => {
        el.classList.remove('selected');
    });
    
    // Agregar selected al clickeado
    const radio = document.getElementById('pago-' + method);
    radio.checked = true;
    radio.closest('.payment-method').classList.add('selected');
}

// Validación del formulario
document.getElementById('formCheckout').addEventListener('submit', function(e) {
    const metodoPago = document.querySelector('input[name="metodo_pago"]:checked');
    
    if (!metodoPago) {
        e.preventDefault();
        alert('Por favor selecciona un método de pago');
        return false;
    }
    
    return confirm('¿Confirmas que deseas realizar este pedido?');
});
</script>

</body>
</html>