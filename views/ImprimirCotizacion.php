<?php
if (!isset($cotizacion)) {
    header("Location: index.php?action=home");
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cotización <?= htmlspecialchars($cotizacion['numero_cotizacion']) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        @media print {
            .no-print { display: none !important; }
            body { margin: 0; padding: 20px; }
        }
        .cotizacion-container {
            max-width: 800px;
            margin: 20px auto;
            background: white;
            padding: 30px;
            border: 2px solid #667eea;
        }
        .header-cotizacion {
            border-bottom: 3px solid #667eea;
            padding-bottom: 20px;
            margin-bottom: 20px;
        }
        .datos-cliente {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            border-left: 4px solid #667eea;
        }
        .validez-box {
            background: #fff3cd;
            border: 1px solid #ffc107;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 10px; text-align: left; border-bottom: 1px solid #dee2e6; }
        th { 
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            font-weight: bold; 
        }
        .totales { margin-top: 20px; text-align: right; }
        .totales table { width: 300px; margin-left: auto; }
        .nota-importante {
            background: #e7f3ff;
            border-left: 4px solid #0d6efd;
            padding: 15px;
            margin-top: 20px;
        }
        .estado-badge {
            display: inline-block;
            padding: 8px 16px;
            border-radius: 20px;
            font-weight: bold;
            font-size: 0.9rem;
        }
        .estado-pendiente {
            background: #ffc107;
            color: #000;
        }
        .estado-convertida {
            background: #28a745;
            color: white;
        }
        .estado-anulada {
            background: #dc3545;
            color: white;
        }
    </style>
</head>
<body>

<!-- Botones de acción (no se imprimen) -->
<div class="no-print mb-3 text-center">
    <button onclick="window.print()" class="btn btn-primary btn-lg">
        <i class="bi bi-printer"></i> Imprimir Cotización
    </button>
    <a href="index.php?action=home" class="btn btn-secondary btn-lg">
        <i class="bi bi-arrow-left"></i> Volver al Inicio
    </a>
</div>

<!-- Notificación de éxito (no se imprime) -->
<div class="no-print container">
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <h5 class="alert-heading">
            <i class="bi bi-check-circle-fill"></i> ¡Cotización Enviada Exitosamente!
        </h5>
        <hr>
        <p class="mb-0">
            <strong>Tu solicitud de cotización ha sido registrada.</strong><br>
            Número de cotización: <strong><?= htmlspecialchars($cotizacion['numero_cotizacion']) ?></strong><br>
            <i class="bi bi-envelope"></i> Recibirás una respuesta en tu correo electrónico en menos de 24 horas.<br>
            <i class="bi bi-telephone"></i> También puedes contactarnos directamente para más información.
        </p>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
</div>

<div class="cotizacion-container">
    <!-- Header -->
    <div class="header-cotizacion">
        <div class="row">
            <div class="col-6">
                <h1 style="color: #667eea; margin: 0;">
                    <i class="bi bi-paint-bucket"></i> PAINTS
                </h1>
                <p class="mb-0">Sistema de Gestión de Pinturas</p>
                <?php if (!empty($cotizacion['nombre_sucursal'])): ?>
                    <p class="mb-0"><strong>Sucursal:</strong> <?= htmlspecialchars($cotizacion['nombre_sucursal']) ?></p>
                <?php endif; ?>
            </div>
            <div class="col-6 text-end">
                <h3 style="margin: 0; color: #667eea;">COTIZACIÓN</h3>
                <p class="mb-0"><strong>No:</strong> <?= htmlspecialchars($cotizacion['numero_cotizacion']) ?></p>
                <p class="mb-0"><strong>Fecha:</strong> <?= date('d/m/Y', strtotime($cotizacion['fecha'])) ?></p>
                <p class="mb-0">
                    <span class="estado-badge estado-<?= strtolower($cotizacion['estado']) ?>">
                        <?= htmlspecialchars($cotizacion['estado']) ?>
                    </span>
                </p>
            </div>
        </div>
    </div>

    <!-- Validez de la cotización -->
    <div class="validez-box">
        <div class="row">
            <div class="col-md-6">
                <strong><i class="bi bi-calendar-check"></i> Válida hasta:</strong>
                <?= date('d/m/Y', strtotime($cotizacion['valida_hasta'])) ?>
            </div>
            <div class="col-md-6 text-end">
                <strong><i class="bi bi-clock"></i> Días restantes:</strong>
                <?php
                $hoy = new DateTime();
                $validaHasta = new DateTime($cotizacion['valida_hasta']);
                $diasRestantes = $hoy->diff($validaHasta)->days;
                $vencida = $hoy > $validaHasta;
                ?>
                <span class="<?= $vencida ? 'text-danger' : 'text-success' ?>">
                    <?= $vencida ? 'VENCIDA' : $diasRestantes . ' días' ?>
                </span>
            </div>
        </div>
    </div>

    <!-- Datos del Cliente -->
    <div class="datos-cliente">
        <h5 class="mb-3" style="color: #667eea;">
            <i class="bi bi-person-fill"></i> Datos del Cliente
        </h5>
        <div class="row">
            <div class="col-md-8">
                <p class="mb-1"><strong>Cliente:</strong> <?= htmlspecialchars($cotizacion['nombre_cliente']) ?></p>
                <p class="mb-1"><strong>NIT:</strong> <?= htmlspecialchars($cotizacion['nit'] ?? 'CF') ?></p>
                <?php if (!empty($cotizacion['email_cliente'])): ?>
                    <p class="mb-1">
                        <strong><i class="bi bi-envelope"></i> Email:</strong> 
                        <?= htmlspecialchars($cotizacion['email_cliente']) ?>
                    </p>
                <?php endif; ?>
                <?php if (!empty($cotizacion['telefono_cliente'])): ?>
                    <p class="mb-1">
                        <strong><i class="bi bi-telephone"></i> Teléfono:</strong> 
                        <?= htmlspecialchars($cotizacion['telefono_cliente']) ?>
                    </p>
                <?php endif; ?>
                <?php if (!empty($cotizacion['direccion_cliente'])): ?>
                    <p class="mb-1">
                        <strong><i class="bi bi-geo-alt"></i> Dirección:</strong> 
                        <?= htmlspecialchars($cotizacion['direccion_cliente']) ?>
                    </p>
                <?php endif; ?>
            </div>
            <div class="col-md-4 text-end">
                <?php if (!empty($cotizacion['nombre_empleado'])): ?>
                    <p class="mb-1"><strong>Atendió:</strong></p>
                    <p class="mb-0"><?= htmlspecialchars($cotizacion['nombre_empleado']) ?></p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Productos -->
    <h5 class="mb-3" style="color: #667eea;">
        <i class="bi bi-cart-plus"></i> Detalle de Productos
    </h5>
    <table>
        <thead>
            <tr>
                <th>Cant.</th>
                <th>Descripción</th>
                <th>SKU</th>
                <th class="text-end">P. Unit.</th>
                <th class="text-center">Desc%</th>
                <th class="text-end">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($cotizacion['detalles'])): ?>
                <?php foreach ($cotizacion['detalles'] as $detalle): ?>
                    <tr>
                        <td><?= $detalle['cantidad'] ?></td>
                        <td><?= htmlspecialchars($detalle['nombre_producto']) ?></td>
                        <td><?= htmlspecialchars($detalle['codigo_sku']) ?></td>
                        <td class="text-end">Q<?= number_format($detalle['precio_unitario'], 2) ?></td>
                        <td class="text-center"><?= $detalle['porcentaje_descuento'] ?>%</td>
                        <td class="text-end">Q<?= number_format($detalle['subtotal'], 2) ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6" class="text-center text-muted">No hay productos en esta cotización</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <!-- Totales -->
    <div class="totales">
        <table>
            <tr>
                <td><strong>Subtotal:</strong></td>
                <td class="text-end">Q<?= number_format($cotizacion['subtotal'], 2) ?></td>
            </tr>
            <tr>
                <td><strong>IVA (12%):</strong></td>
                <td class="text-end">Q<?= number_format($cotizacion['impuestos'], 2) ?></td>
            </tr>
            <tr style="border-top: 2px solid #667eea;">
                <td><strong style="font-size: 1.2em; color: #667eea;">TOTAL ESTIMADO:</strong></td>
                <td class="text-end">
                    <strong style="font-size: 1.2em; color: #667eea;">
                        Q<?= number_format($cotizacion['total'], 2) ?>
                    </strong>
                </td>
            </tr>
        </table>
    </div>

    <!-- Nota Importante -->
    <div class="nota-importante">
        <h6><i class="bi bi-info-circle-fill"></i> Información Importante</h6>
        <ul class="mb-0 small">
            <li>Esta cotización es válida hasta el <strong><?= date('d/m/Y', strtotime($cotizacion['valida_hasta'])) ?></strong></li>
            <li>Los precios están sujetos a cambios sin previo aviso después de la fecha de validez.</li>
            <li>Los precios incluyen IVA (12%).</li>
            <li>La disponibilidad de productos está sujeta a existencias.</li>
            <li>Para realizar el pedido, contacte con nosotros citando el número de cotización.</li>
        </ul>
    </div>

    <!-- Estado de la cotización -->
    <?php if ($cotizacion['estado'] === 'ANULADA'): ?>
        <div class="alert alert-danger mt-4 text-center">
            <h5><i class="bi bi-x-circle-fill"></i> COTIZACIÓN ANULADA</h5>
            <p class="mb-0">Esta cotización ha sido cancelada y no es válida.</p>
        </div>
    <?php elseif ($cotizacion['estado'] === 'CONVERTIDA'): ?>
        <div class="alert alert-success mt-4 text-center">
            <h5><i class="bi bi-check-circle-fill"></i> COTIZACIÓN CONVERTIDA A FACTURA</h5>
            <p class="mb-0">Esta cotización ya fue procesada y convertida en factura.</p>
        </div>
    <?php endif; ?>

    <!-- Información de contacto -->
    <div class="mt-4 text-center">
        <hr>
        <h6 style="color: #667eea;">Contáctanos</h6>
        <p class="mb-0">
            <i class="bi bi-telephone-fill"></i> Tel: (502) 2345-6789 | 
            <i class="bi bi-envelope-fill"></i> Email: ventas@paints.com<br>
            <i class="bi bi-globe"></i> www.paints.com | 
            <i class="bi bi-clock-fill"></i> Lun-Vie: 8:00 - 18:00, Sáb: 8:00 - 13:00
        </p>
    </div>

    <!-- Footer -->
    <div class="mt-4 text-center">
        <small class="text-muted">
            Gracias por su preferencia | Sistema Paints © <?= date('Y') ?><br>
            <strong>Esta es una cotización, NO es una factura fiscal</strong>
        </small>
    </div>
</div>

<!-- Instrucciones adicionales (no se imprimen) -->
<div class="no-print container mt-4 mb-5">
    <div class="card">
        <div class="card-body">
            <h5 class="card-title">
                <i class="bi bi-question-circle"></i> ¿Qué sigue después?
            </h5>
            <div class="row">
                <div class="col-md-4">
                    <h6><i class="bi bi-1-circle-fill text-primary"></i> Revisión</h6>
                    <p class="small">Nuestro equipo revisará tu cotización y te contactará en menos de 24 horas.</p>
                </div>
                <div class="col-md-4">
                    <h6><i class="bi bi-2-circle-fill text-primary"></i> Confirmación</h6>
                    <p class="small">Recibirás un email con la cotización oficial y opciones de pago.</p>
                </div>
                <div class="col-md-4">
                    <h6><i class="bi bi-3-circle-fill text-primary"></i> Compra</h6>
                    <p class="small">Una vez aceptada, procederemos con tu pedido y entrega.</p>
                </div>
            </div>
            <hr>
            <div class="d-flex gap-2 justify-content-center">
                <a href="index.php?action=catalogoProductos" class="btn btn-primary">
                    <i class="bi bi-palette"></i> Ver más productos
                </a>
                <a href="index.php?action=solicitarCotizacion" class="btn btn-outline-primary">
                    <i class="bi bi-plus-lg"></i> Nueva cotización
                </a>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>