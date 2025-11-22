<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Facturas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; }
        .page-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            border-radius: 15px;
            margin-bottom: 30px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.2);
        }
        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .badge-anulada {
            background: #dc3545;
            color: white;
            padding: 5px 10px;
            border-radius: 5px;
            font-size: 0.8em;
        }
        .badge-activa {
            background: #28a745;
            color: white;
            padding: 5px 10px;
            border-radius: 5px;
            font-size: 0.8em;
        }
        .table tbody tr:hover {
            background-color: #f8f9ff;
            transition: 0.2s;
        }
        .btn-action {
            border-radius: 8px;
            padding: 5px 10px;
            font-size: 0.85rem;
        }
        .card-body {
            max-height: 70vh;
            overflow-y: auto;
        }
        .table thead th {
            position: sticky;
            top: 0;
            z-index: 100;
            background-color: #212529 !important;
            box-shadow: 0 2px 2px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>

<div class="container mt-4 mb-5">
    <!-- Header -->
    <div class="page-header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2 class="mb-1"><i class="bi bi-receipt"></i> Facturas</h2>
                <p class="mb-0 opacity-75">Gestión de facturación y ventas</p>
            </div>
            <div>
                <a href="index.php?action=formularioFactura" class="btn btn-light btn-lg me-2">
                    <i class="bi bi-plus-lg"></i> Nueva Factura
                </a>
                <a href="index.php?action=dashboard" class="btn btn-outline-light btn-lg">
                    <i class="bi bi-house-door"></i> Dashboard
                </a>
            </div>
        </div>
    </div>

    <!-- Mensajes de éxito/error -->
    <?php if(isset($_SESSION['mensaje'])): ?>
        <div class="alert alert-success alert-dismissible fade show">
            <i class="bi bi-check-circle-fill"></i>
            <?= $_SESSION['mensaje']; unset($_SESSION['mensaje']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if(isset($_SESSION['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show">
            <i class="bi bi-exclamation-triangle-fill"></i>
            <?= $_SESSION['error']; unset($_SESSION['error']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- Tabla de Facturas -->
    <div class="card">
        <div class="card-body p-0">
            <div class="table-container">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th><i class="bi bi-hash"></i> No. Factura</th>
                            <th><i class="bi bi-file-text"></i> Serie</th>
                            <th><i class="bi bi-person"></i> Cliente</th>
                            <th><i class="bi bi-card-text"></i> NIT</th>
                            <th><i class="bi bi-person-badge"></i> Empleado</th>
                            <th><i class="bi bi-building"></i> Sucursal</th>
                            <th><i class="bi bi-calendar"></i> Fecha</th>
                            <th class="text-end"><i class="bi bi-currency-dollar"></i> Total</th>
                            <th class="text-center"><i class="bi bi-info-circle"></i> Estado</th>
                            <th class="text-center"><i class="bi bi-gear"></i> Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(!empty($facturas)): ?>
                            <?php foreach($facturas as $f): ?>
                                <tr>
                                    <td><strong class="text-primary"><?= htmlspecialchars($f['numero_factura']) ?></strong></td>
                                    <td><span class="badge bg-secondary"><?= htmlspecialchars($f['serie_factura']) ?></span></td>
                                    <td><?= htmlspecialchars($f['nombre_cliente']) ?></td>
                                    <td><span class="badge bg-info"><?= htmlspecialchars($f['nit'] ?? 'CF') ?></span></td>
                                    <td><small><i class="bi bi-person-circle"></i> <?= htmlspecialchars($f['nombre_empleado']) ?></small></td>
                                    <td><small class="text-muted"><?= htmlspecialchars($f['nombre_sucursal']) ?></small></td>
                                    <td><small><?= date('d/m/Y', strtotime($f['fecha'])) ?></small></td>
                                    <td class="text-end"><strong class="text-success">Q<?= number_format($f['total'], 2) ?></strong></td>
                                    <td class="text-center">
                                        <?php if($f['estado'] === 'ACTIVA'): ?>
                                            <span class="badge-activa"><i class="bi bi-check-circle"></i> ACTIVA</span>
                                        <?php else: ?>
                                            <span class="badge-anulada"><i class="bi bi-x-circle"></i> ANULADA</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <div class="btn-group btn-group-sm" role="group">
                                            <a href="index.php?action=imprimirFactura&factura_id=<?= $f['factura_id'] ?>" 
                                               class="btn btn-primary btn-action"
                                               title="Imprimir factura"
                                               target="_blank">
                                                <i class="bi bi-printer"></i>
                                            </a>
                                            <button type="button" 
                                                    class="btn btn-info btn-action" 
                                                    title="Ver detalle"
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#modalDetalle<?= $f['factura_id'] ?>">
                                                <i class="bi bi-eye"></i>
                                            </button>
                                            <?php if($f['estado'] === 'ACTIVA'): ?>
                                                <a href="index.php?action=anularFactura&factura_id=<?= $f['factura_id'] ?>" 
                                                   class="btn btn-danger btn-action"
                                                   title="Anular factura"
                                                   onclick="return confirm('⚠️ ¿Está seguro de anular esta factura?\n\n- Se restaurará el inventario\n- Esta acción NO se puede deshacer')">
                                                    <i class="bi bi-x-circle"></i>
                                                </a>
                                            <?php else: ?>
                                                <button class="btn btn-secondary btn-action" disabled title="Ya está anulada">
                                                    <i class="bi bi-x-circle"></i>
                                                </button>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>

                                <!-- Modal sin resumen ni botones -->
                                <div class="modal fade" id="modalDetalle<?= $f['factura_id'] ?>" tabindex="-1">
                                    <div class="modal-dialog modal-lg">
                                        <div class="modal-content">
                                            <div class="modal-header bg-primary text-white">
                                                <h5 class="modal-title">
                                                    <i class="bi bi-receipt"></i>
                                                    Detalle de Factura: <?= htmlspecialchars($f['numero_factura']) ?>
                                                </h5>
                                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="row mb-3">
                                                    <div class="col-md-6">
                                                        <p class="mb-1"><strong>Cliente:</strong> <?= htmlspecialchars($f['nombre_cliente']) ?></p>
                                                        <p class="mb-1"><strong>NIT:</strong> <?= htmlspecialchars($f['nit'] ?? 'CF') ?></p>
                                                    </div>
                                                    <div class="col-md-6 text-end">
                                                        <p class="mb-1"><strong>Empleado:</strong> <?= htmlspecialchars($f['nombre_empleado']) ?></p>
                                                        <p class="mb-1"><strong>Fecha:</strong> <?= date('d/m/Y', strtotime($f['fecha'])) ?></p>
                                                    </div>
                                                </div>

                                                <div class="alert alert-info">
                                                    <strong>Sucursal:</strong> <?= htmlspecialchars($f['nombre_sucursal']) ?>
                                                </div>

                                                <?php if($f['estado'] === 'ANULADA'): ?>
                                                    <div class="alert alert-danger">
                                                        <i class="bi bi-exclamation-triangle"></i>
                                                        <strong>FACTURA ANULADA</strong>
                                                        <?php if(isset($f['fecha_anulacion'])): ?>
                                                            el <?= date('d/m/Y H:i', strtotime($f['fecha_anulacion'])) ?>
                                                        <?php endif; ?>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="10" class="text-center py-5">
                                    <i class="bi bi-inbox fs-1 text-muted"></i>
                                    <p class="text-muted mt-3">No hay facturas registradas</p>
                                    <a href="index.php?action=formularioFactura" class="btn btn-primary">
                                        <i class="bi bi-plus-lg"></i> Crear Primera Factura
                                    </a>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Footer con estadísticas -->
        <?php if(!empty($facturas)): ?>
            <div class="card-footer bg-light">
                <div class="row text-center">
                    <div class="col-md-4">
                        <small class="text-muted">Total Facturas:</small>
                        <h5 class="mb-0"><?= count($facturas) ?></h5>
                    </div>
                    <div class="col-md-4">
                        <small class="text-muted">Activas:</small>
                        <h5 class="mb-0 text-success"><?= count(array_filter($facturas, fn($f) => $f['estado'] === 'ACTIVA')) ?></h5>
                    </div>
                    <div class="col-md-4">
                        <small class="text-muted">Anuladas:</small>
                        <h5 class="mb-0 text-danger"><?= count(array_filter($facturas, fn($f) => $f['estado'] === 'ANULADA')) ?></h5>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
