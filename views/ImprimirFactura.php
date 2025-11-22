<?php
if (!isset($factura)) {
    header("Location: index.php?action=listarFacturas");
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Factura <?= htmlspecialchars($factura['numero_factura']) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        @media print {
            .no-print { display: none !important; }
            body { margin: 0; padding: 20px; }
        }
        .factura-container {
            max-width: 800px;
            margin: 20px auto;
            background: white;
            padding: 30px;
            border: 2px solid #000;
        }
        .header-factura {
            border-bottom: 3px solid #000;
            padding-bottom: 20px;
            margin-bottom: 20px;
        }
        .datos-fiscales {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 10px; text-align: left; border-bottom: 1px solid #dee2e6; }
        th { background: #e9ecef; font-weight: bold; }
        .totales { margin-top: 20px; text-align: right; }
        .totales table { width: 300px; margin-left: auto; }
        .firma-section { margin-top: 80px; text-align: center; }
        .firma-line { border-top: 2px solid #000; width: 300px; margin: 0 auto; padding-top: 10px; }
    </style>
</head>
<body>

<div class="no-print mb-3 text-center">
    <button onclick="window.print()" class="btn btn-primary">
        <i class="bi bi-printer"></i> Imprimir
    </button>
    <a href="index.php?action=listarFacturas" class="btn btn-secondary">
        <i class="bi bi-arrow-left"></i> Volver
    </a>
</div>

<div class="factura-container">
    <!-- Header -->
    <div class="header-factura">
        <div class="row">
            <div class="col-6">
                <h1 style="color: #667eea; margin: 0;">PAINTS</h1>
                <p class="mb-0">Sistema de Gestión de Pinturas</p>
                <p class="mb-0"><strong>Sucursal:</strong> <?= htmlspecialchars($factura['nombre_sucursal']) ?></p>
            </div>
            <div class="col-6 text-end">
                <h3 style="margin: 0;">FACTURA</h3>
                <p class="mb-0"><strong>Serie:</strong> <?= htmlspecialchars($factura['serie_factura']) ?></p>
                <p class="mb-0"><strong>No:</strong> <?= htmlspecialchars($factura['numero_factura']) ?></p>
                <p class="mb-0"><strong>Fecha:</strong> <?= date('d/m/Y', strtotime($factura['fecha'])) ?></p>
            </div>
        </div>
    </div>

    <!-- Datos del Cliente -->
    <div class="datos-fiscales">
        <div class="row">
            <div class="col-8">
                <p class="mb-1"><strong>Cliente:</strong> <?= htmlspecialchars($factura['nombre_cliente']) ?></p>
                <p class="mb-1"><strong>NIT:</strong> <?= htmlspecialchars($factura['nit'] ?? 'CF') ?></p>
                <?php if (!empty($factura['direccion_cliente'])): ?>
                    <p class="mb-1"><strong>Dirección:</strong> <?= htmlspecialchars($factura['direccion_cliente']) ?></p>
                <?php endif; ?>
            </div>
            <div class="col-4">
                <p class="mb-1"><strong>Atendió:</strong></p>
                <p class="mb-0"><?= htmlspecialchars($factura['nombre_empleado']) ?></p>
            </div>
        </div>
    </div>

    <!-- Productos -->
    <h5 class="mb-3">Detalle de Productos</h5>
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
            <?php if (!empty($factura['detalles'])): ?>
                <?php foreach ($factura['detalles'] as $detalle): ?>
                    <tr>
                        <td><?= $detalle['cantidad'] ?></td>
                        <td><?= htmlspecialchars($detalle['nombre_producto']) ?></td>
                        <td><?= htmlspecialchars($detalle['codigo_sku']) ?></td>
                        <td class="text-end">Q<?= number_format($detalle['precio_unitario'], 2) ?></td>
                        <td class="text-center"><?= $detalle['porcentaje_descuento'] ?>%</td>
                        <td class="text-end">Q<?= number_format($detalle['subtotal'], 2) ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>

    <!-- Totales -->
    <div class="totales">
        <table>
            <tr>
                <td><strong>Subtotal:</strong></td>
                <td class="text-end">Q<?= number_format($factura['subtotal'], 2) ?></td>
            </tr>
            <tr>
                <td><strong>IVA (12%):</strong></td>
                <td class="text-end">Q<?= number_format($factura['impuestos'], 2) ?></td>
            </tr>
            <tr style="border-top: 2px solid #000;">
                <td><strong style="font-size: 1.2em;">TOTAL:</strong></td>
                <td class="text-end"><strong style="font-size: 1.2em;">Q<?= number_format($factura['total'], 2) ?></strong></td>
            </tr>
        </table>
    </div>

    <!-- Medios de Pago -->
    <?php if (!empty($factura['pagos'])): ?>
        <div class="mt-4">
            <h6>Medios de Pago:</h6>
            <table style="width: 400px;">
                <?php foreach ($factura['pagos'] as $pago): ?>
                    <tr>
                        <td><?= htmlspecialchars($pago['nombre_pago']) ?></td>
                        <td class="text-end">Q<?= number_format($pago['monto'], 2) ?></td>
                    </tr>
                <?php endforeach; ?>
            </table>
        </div>
    <?php endif; ?>

    <!-- Estado -->
    <?php if ($factura['estado'] === 'ANULADA'): ?>
        <div class="alert alert-danger mt-4 text-center">
            <h4>*** FACTURA ANULADA ***</h4>
            <p class="mb-0">Fecha de anulación: <?= date('d/m/Y H:i', strtotime($factura['fecha_anulacion'])) ?></p>
        </div>
    <?php endif; ?>

    <!-- Firma -->
    <div class="firma-section">
        <div class="firma-line">
            Firma y Sello del Cliente
        </div>
    </div>

    <!-- Nota legal -->
    <div class="mt-4 text-center">
        <small class="text-muted">
            Gracias por su compra | Sistema Paints © <?= date('Y') ?><br>
            Original: Cliente | Copia: Contabilidad
        </small>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>