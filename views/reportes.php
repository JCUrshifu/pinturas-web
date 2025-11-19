<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reportes - PinturasWeb</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background-color: #f5f5f5;
        }
        .page-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            border-radius: 15px;
            margin-bottom: 30px;
            box-shadow: 0 8px 20px rgba(102, 126, 234, 0.3);
        }
        .report-card {
            background: white;
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 20px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            transition: all 0.3s;
        }
        .report-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
        }
        .report-icon {
            font-size: 3rem;
            margin-bottom: 15px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .result-table {
            background: white;
            border-radius: 15px;
            padding: 25px;
            margin-top: 20px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        .table thead {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
    </style>
</head>
<body>

<div class="container mt-4 mb-5">
    <!-- Header -->
    <div class="page-header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="mb-2"><i class="fas fa-chart-bar"></i> Sistema de Reportes</h1>
                <p class="mb-0 opacity-75">Análisis y estadísticas del sistema</p>
            </div>
            <a href="index.php?action=dashboard" class="btn btn-light btn-lg">
                <i class="fas fa-arrow-left"></i> Dashboard
            </a>
        </div>
    </div>

    <!-- Selector de Reportes -->
    <div class="row">
        <!-- Reporte 1 -->
        <div class="col-md-6 col-lg-4">
            <div class="report-card text-center">
                <i class="fas fa-money-bill-wave report-icon"></i>
                <h5>Ventas por Tipo de Pago</h5>
                <p class="text-muted small">Total facturado por método de pago</p>
                <button class="btn btn-primary w-100" data-bs-toggle="collapse" data-bs-target="#reporte1">
                    <i class="fas fa-chart-pie"></i> Ver Reporte
                </button>
            </div>
        </div>

        <!-- Reporte 2 -->
        <div class="col-md-6 col-lg-4">
            <div class="report-card text-center">
                <i class="fas fa-trophy report-icon"></i>
                <h5>Productos que Más Generan</h5>
                <p class="text-muted small">Productos con mayor ingreso</p>
                <button class="btn btn-success w-100" data-bs-toggle="collapse" data-bs-target="#reporte2">
                    <i class="fas fa-chart-line"></i> Ver Reporte
                </button>
            </div>
        </div>

        <!-- Reporte 3 -->
        <div class="col-md-6 col-lg-4">
            <div class="report-card text-center">
                <i class="fas fa-box-open report-icon"></i>
                <h5>Productos Más Vendidos</h5>
                <p class="text-muted small">Por cantidad vendida</p>
                <button class="btn btn-info w-100" data-bs-toggle="collapse" data-bs-target="#reporte3">
                    <i class="fas fa-chart-bar"></i> Ver Reporte
                </button>
            </div>
        </div>

        <!-- Reporte 4 -->
        <div class="col-md-6 col-lg-4">
            <div class="report-card text-center">
                <i class="fas fa-warehouse report-icon"></i>
                <h5>Inventario Actual</h5>
                <p class="text-muted small">Estado del stock</p>
                <button class="btn btn-warning w-100" data-bs-toggle="collapse" data-bs-target="#reporte4">
                    <i class="fas fa-boxes"></i> Ver Reporte
                </button>
            </div>
        </div>

        <!-- Reporte 5 -->
        <div class="col-md-6 col-lg-4">
            <div class="report-card text-center">
                <i class="fas fa-arrow-down report-icon"></i>
                <h5>Productos Menos Vendidos</h5>
                <p class="text-muted small">Productos con bajas ventas</p>
                <button class="btn btn-secondary w-100" data-bs-toggle="collapse" data-bs-target="#reporte5">
                    <i class="fas fa-chart-line"></i> Ver Reporte
                </button>
            </div>
        </div>

        <!-- Reporte 6 -->
        <div class="col-md-6 col-lg-4">
            <div class="report-card text-center">
                <i class="fas fa-exclamation-triangle report-icon"></i>
                <h5>Productos Sin Stock</h5>
                <p class="text-muted small">Productos para reabastecimiento</p>
                <button class="btn btn-danger w-100" data-bs-toggle="collapse" data-bs-target="#reporte6">
                    <i class="fas fa-exclamation-circle"></i> Ver Reporte
                </button>
            </div>
        </div>

        <!-- NUEVO: Reporte 7 - Anulación -->
        <div class="col-md-6 col-lg-4">
            <div class="report-card text-center">
                <i class="fas fa-ban report-icon"></i>
                <h5>Anular Factura</h5>
                <p class="text-muted small">Anular por número de factura</p>
                <button class="btn btn-dark w-100" data-bs-toggle="collapse" data-bs-target="#reporte7">
                    <i class="fas fa-times-circle"></i> Anular
                </button>
            </div>
        </div>
    </div>

    <!-- Formularios y Resultados -->

    <!-- REPORTE 1: Ventas por Tipo de Pago -->
    <div class="collapse" id="reporte1">
        <div class="result-table">
            <h4 class="mb-4"><i class="fas fa-money-bill-wave text-primary"></i> Ventas por Tipo de Pago</h4>
            <form method="GET" action="" class="row g-3 mb-4">
                <input type="hidden" name="action" value="reportes">
                <input type="hidden" name="reporte" value="1">
                <div class="col-md-5">
                    <label class="form-label">Fecha Inicio:</label>
                    <input type="date" name="fecha_inicio" class="form-control" required>
                </div>
                <div class="col-md-5">
                    <label class="form-label">Fecha Fin:</label>
                    <input type="date" name="fecha_fin" class="form-control" required>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-search"></i> Generar
                    </button>
                </div>
            </form>

            <?php if(isset($resultado) && $_GET['reporte'] == '1'): ?>
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Tipo de Pago</th>
                                <th class="text-end">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><i class="fas fa-money-bill text-success"></i> Efectivo</td>
                                <td class="text-end"><strong>Q<?= number_format($resultado['total_efectivo'] ?? 0, 2) ?></strong></td>
                            </tr>
                            <tr>
                                <td><i class="fas fa-credit-card text-warning"></i> Tarjeta</td>
                                <td class="text-end"><strong>Q<?= number_format($resultado['total_tarjeta'] ?? 0, 2) ?></strong></td>
                            </tr>
                            <tr>
                                <td><i class="fas fa-file-invoice text-info"></i> Cheque</td>
                                <td class="text-end"><strong>Q<?= number_format($resultado['total_cheque'] ?? 0, 2) ?></strong></td>
                            </tr>
                            <tr class="table-primary">
                                <td><strong>TOTAL GENERAL</strong></td>
                                <td class="text-end"><strong>Q<?= number_format($resultado['total_general'] ?? 0, 2) ?></strong></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- REPORTE 2: Productos Mayor Ingreso -->
    <div class="collapse" id="reporte2">
        <div class="result-table">
            <h4 class="mb-4"><i class="fas fa-trophy text-success"></i> Productos que Más Generan Dinero</h4>
            <form method="GET" action="" class="row g-3 mb-4">
                <input type="hidden" name="action" value="reportes">
                <input type="hidden" name="reporte" value="2">
                <div class="col-md-4">
                    <label class="form-label">Fecha Inicio:</label>
                    <input type="date" name="fecha_inicio" class="form-control" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Fecha Fin:</label>
                    <input type="date" name="fecha_fin" class="form-control" required>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Límite:</label>
                    <input type="number" name="limite" class="form-control" value="10" min="1" max="50">
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-success w-100">
                        <i class="fas fa-search"></i> Generar
                    </button>
                </div>
            </form>

            <?php if(isset($resultado) && $_GET['reporte'] == '2' && is_array($resultado)): ?>
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Código</th>
                                <th>Producto</th>
                                <th class="text-center">Cantidad Vendida</th>
                                <th class="text-end">Total Generado</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($resultado as $index => $row): ?>
                                <tr>
                                    <td><?= $index + 1 ?></td>
                                    <td><code><?= htmlspecialchars($row['codigo_sku']) ?></code></td>
                                    <td><?= htmlspecialchars($row['nombre']) ?></td>
                                    <td class="text-center"><?= $row['cantidad_vendida'] ?></td>
                                    <td class="text-end"><strong class="text-success">Q<?= number_format($row['total_generado'], 2) ?></strong></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- REPORTE 3: Productos Más Vendidos -->
    <div class="collapse" id="reporte3">
        <div class="result-table">
            <h4 class="mb-4"><i class="fas fa-box-open text-info"></i> Productos Más Vendidos (Por Cantidad)</h4>
            <form method="GET" action="" class="row g-3 mb-4">
                <input type="hidden" name="action" value="reportes">
                <input type="hidden" name="reporte" value="3">
                <div class="col-md-4">
                    <label class="form-label">Fecha Inicio:</label>
                    <input type="date" name="fecha_inicio" class="form-control" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Fecha Fin:</label>
                    <input type="date" name="fecha_fin" class="form-control" required>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Límite:</label>
                    <input type="number" name="limite" class="form-control" value="10" min="1" max="50">
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-info w-100">
                        <i class="fas fa-search"></i> Generar
                    </button>
                </div>
            </form>

            <?php if(isset($resultado) && $_GET['reporte'] == '3' && is_array($resultado)): ?>
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Código</th>
                                <th>Producto</th>
                                <th>Medida</th>
                                <th class="text-center">Cantidad Vendida</th>
                                <th class="text-end">Total Generado</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($resultado as $index => $row): ?>
                                <tr>
                                    <td><?= $index + 1 ?></td>
                                    <td><code><?= htmlspecialchars($row['codigo_sku']) ?></code></td>
                                    <td><?= htmlspecialchars($row['nombre']) ?></td>
                                    <td><?= htmlspecialchars($row['nombre_medida'] ?? 'N/A') ?></td>
                                    <td class="text-center"><strong><?= $row['cantidad_vendida'] ?></strong></td>
                                    <td class="text-end">Q<?= number_format($row['total_generado'], 2) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- REPORTE 4: Inventario Actual -->
    <div class="collapse" id="reporte4">
        <div class="result-table">
            <h4 class="mb-4"><i class="fas fa-warehouse text-warning"></i> Inventario Actual</h4>
            <form method="GET" action="" class="mb-4">
                <input type="hidden" name="action" value="reportes">
                <input type="hidden" name="reporte" value="4">
                <button type="submit" class="btn btn-warning">
                    <i class="fas fa-sync"></i> Generar Reporte
                </button>
            </form>

            <?php if(isset($resultado) && $_GET['reporte'] == '4' && is_array($resultado)): ?>
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Código</th>
                                <th>Producto</th>
                                <th>Sucursal</th>
                                <th class="text-end">Precio</th>
                                <th class="text-center">Stock Actual</th>
                                <th class="text-center">Stock Mínimo</th>
                                <th>Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($resultado as $row): ?>
                                <tr>
                                    <td><code><?= htmlspecialchars($row['codigo_sku']) ?></code></td>
                                    <td><?= htmlspecialchars($row['nombre']) ?></td>
                                    <td><?= htmlspecialchars($row['nombre_sucursal'] ?? 'N/A') ?></td>
                                    <td class="text-end">Q<?= number_format($row['precio_base'], 2) ?></td>
                                    <td class="text-center"><strong><?= $row['cantidad_disponible'] ?></strong></td>
                                    <td class="text-center"><?= $row['stock_minimo'] ?></td>
                                    <td>
                                        <?php
                                        $estado = $row['estado_stock'];
                                        $class = $estado === 'CRÍTICO' ? 'danger' : ($estado === 'BAJO' ? 'warning' : 'success');
                                        ?>
                                        <span class="badge bg-<?= $class ?>"><?= $estado ?></span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- REPORTE 5: Productos Menos Vendidos -->
    <div class="collapse" id="reporte5">
        <div class="result-table">
            <h4 class="mb-4"><i class="fas fa-arrow-down text-secondary"></i> Productos Menos Vendidos</h4>
            <form method="GET" action="" class="row g-3 mb-4">
                <input type="hidden" name="action" value="reportes">
                <input type="hidden" name="reporte" value="5">
                <div class="col-md-4">
                    <label class="form-label">Fecha Inicio:</label>
                    <input type="date" name="fecha_inicio" class="form-control" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Fecha Fin:</label>
                    <input type="date" name="fecha_fin" class="form-control" required>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Límite:</label>
                    <input type="number" name="limite" class="form-control" value="10" min="1" max="50">
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-secondary w-100">
                        <i class="fas fa-search"></i> Generar
                    </button>
                </div>
            </form>

            <?php if(isset($resultado) && $_GET['reporte'] == '5' && is_array($resultado)): ?>
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Código</th>
                                <th>Producto</th>
                                <th class="text-center">Cantidad Vendida</th>
                                <th class="text-end">Total Generado</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($resultado as $row): ?>
                                <tr>
                                    <td><code><?= htmlspecialchars($row['codigo_sku']) ?></code></td>
                                    <td><?= htmlspecialchars($row['nombre']) ?></td>
                                    <td class="text-center"><?= $row['cantidad_vendida'] ?></td>
                                    <td class="text-end">Q<?= number_format($row['total_generado'], 2) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- REPORTE 6: Productos Sin Stock -->
    <div class="collapse" id="reporte6">
        <div class="result-table">
            <h4 class="mb-4"><i class="fas fa-exclamation-triangle text-danger"></i> Productos Sin Stock</h4>
            <form method="GET" action="" class="mb-4">
                <input type="hidden" name="action" value="reportes">
                <input type="hidden" name="reporte" value="6">
                <button type="submit" class="btn btn-danger">
                    <i class="fas fa-exclamation-circle"></i> Generar Reporte
                </button>
            </form>

            <?php if(isset($resultado) && $_GET['reporte'] == '6' && is_array($resultado)): ?>
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Código</th>
                                <th>Producto</th>
                                <th>Sucursal</th>
                                <th class="text-center">Stock Actual</th>
                                <th class="text-center">Stock Mínimo</th>
                                <th>Proveedor</th>
                                <th>Contacto</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($resultado as $row): ?>
                                <tr>
                                    <td><code><?= htmlspecialchars($row['codigo_sku']) ?></code></td>
                                    <td><?= htmlspecialchars($row['nombre']) ?></td>
                                    <td><?= htmlspecialchars($row['nombre_sucursal'] ?? 'N/A') ?></td>
                                    <td class="text-center"><span class="badge bg-danger"><?= $row['cantidad_actual'] ?></span></td>
                                    <td class="text-center"><?= $row['stock_minimo'] ?></td>
                                    <td><?= htmlspecialchars($row['nombre_proveedor'] ?? 'N/A') ?></td>
                                    <td>
                                        <small>
                                            <?= htmlspecialchars($row['telefono_proveedor'] ?? '') ?><br>
                                            <?= htmlspecialchars($row['email_proveedor'] ?? '') ?>
                                        </small>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>