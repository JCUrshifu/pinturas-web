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
        
        /* 🎯 GRID HORIZONTAL - 3 COLUMNAS */
        .reports-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            margin-bottom: 30px;
        }
        
        /* 📱 RESPONSIVE */
        @media (max-width: 992px) {
            .reports-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }
        
        @media (max-width: 576px) {
            .reports-grid {
                grid-template-columns: 1fr;
            }
        }
        
        .report-card {
            background: white;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            border-left: 5px solid;
            min-height: 240px;
        }
        
        .report-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
        }
        
        /* 🎨 COLORES POR TIPO */
        .report-card.ventas { border-color: #007bff; }
        .report-card.productos { border-color: #28a745; }
        .report-card.inventario { border-color: #ffc107; }
        .report-card.critico { border-color: #dc3545; }
        .report-card.gestion { border-color: #17a2b8; }
        .report-card.anular { border-color: #6c757d; }
        
        .report-icon {
            font-size: 3rem;
            margin-bottom: 15px;
            opacity: 0.8;
        }
        
        .report-card.ventas .report-icon { color: #007bff; }
        .report-card.productos .report-icon { color: #28a745; }
        .report-card.inventario .report-icon { color: #ffc107; }
        .report-card.critico .report-icon { color: #dc3545; }
        .report-card.gestion .report-icon { color: #17a2b8; }
        .report-card.anular .report-icon { color: #6c757d; }
        
        .report-card h5 {
            margin: 10px 0;
            font-weight: 600;
            font-size: 1.1rem;
            min-height: 50px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .report-card p {
            flex-grow: 1;
            margin-bottom: 15px;
        }
        
        .report-card .btn {
            width: 100%;
            padding: 10px;
            font-weight: 600;
            border-radius: 8px;
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

    <!-- 🎯 GRID HORIZONTAL DE REPORTES -->
    <div class="reports-grid">
        
        <!-- 1. Ventas por Tipo de Pago -->
        <div class="report-card ventas">
            <i class="fas fa-money-bill-wave report-icon"></i>
            <h5>Ventas por Tipo de Pago</h5>
            <p class="text-muted small">Total facturado por método de pago</p>
            <button class="btn btn-primary" data-bs-toggle="collapse" data-bs-target="#reporte1">
                <i class="fas fa-chart-pie"></i> Ver Reporte
            </button>
        </div>

        <!-- 2. Productos que Más Generan -->
        <div class="report-card productos">
            <i class="fas fa-trophy report-icon"></i>
            <h5>Productos que Más Generan</h5>
            <p class="text-muted small">Productos con mayor ingreso</p>
            <button class="btn btn-success" data-bs-toggle="collapse" data-bs-target="#reporte2">
                <i class="fas fa-chart-line"></i> Ver Reporte
            </button>
        </div>

        <!-- 3. Productos Más Vendidos -->
        <div class="report-card productos">
            <i class="fas fa-box-open report-icon"></i>
            <h5>Productos Más Vendidos</h5>
            <p class="text-muted small">Por cantidad vendida</p>
            <button class="btn btn-success" data-bs-toggle="collapse" data-bs-target="#reporte3">
                <i class="fas fa-chart-bar"></i> Ver Reporte
            </button>
        </div>

        <!-- 4. Inventario Actual -->
        <div class="report-card inventario">
            <i class="fas fa-warehouse report-icon"></i>
            <h5>Inventario Actual</h5>
            <p class="text-muted small">Estado del stock</p>
            <button class="btn btn-warning" data-bs-toggle="collapse" data-bs-target="#reporte4">
                <i class="fas fa-boxes"></i> Ver Reporte
            </button>
        </div>

        <!-- 5. Productos Menos Vendidos -->
        <div class="report-card inventario">
            <i class="fas fa-arrow-down report-icon"></i>
            <h5>Productos Menos Vendidos</h5>
            <p class="text-muted small">Productos con bajas ventas</p>
            <button class="btn btn-warning" data-bs-toggle="collapse" data-bs-target="#reporte5">
                <i class="fas fa-chart-line"></i> Ver Reporte
            </button>
        </div>

        <!-- 6. Productos Sin Stock -->
        <div class="report-card critico">
            <i class="fas fa-exclamation-triangle report-icon"></i>
            <h5>Productos Sin Stock</h5>
            <p class="text-muted small">Productos para reabastecimiento</p>
            <button class="btn btn-danger" data-bs-toggle="collapse" data-bs-target="#reporte6">
                <i class="fas fa-exclamation-circle"></i> Ver Reporte
            </button>
        </div>

        <!-- ✅ 7. Gestión de Facturas -->
        <div class="report-card gestion">
            <i class="fas fa-file-invoice report-icon"></i>
            <h5>Gestión de Facturas</h5>
            <p class="text-muted small">Ver, imprimir y anular facturas</p>
            <a href="index.php?action=listarFacturas" class="btn btn-info">
                <i class="fas fa-receipt"></i> Ver Facturas
            </a>
        </div>

        <!-- ❌ 8. Anular Factura (Opcional - Solo búsqueda) -->
        <div class="report-card anular">
            <i class="fas fa-search report-icon"></i>
            <h5>Buscar Factura</h5>
            <p class="text-muted small">Buscar por número de factura</p>
            <button class="btn btn-secondary" data-bs-toggle="collapse" data-bs-target="#reporte7">
                <i class="fas fa-search"></i> Buscar
            </button>
        </div>

    </div>

    <!-- ============================================ -->
    <!-- FORMULARIOS Y RESULTADOS (MANTENER IGUAL) -->
    <!-- ============================================ -->

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

    <!-- REPORTE 7: Buscar Factura -->
    <div class="collapse" id="reporte7">
        <div class="result-table">
            <h4 class="mb-4"><i class="fas fa-search text-secondary"></i> Buscar Factura</h4>
            <form method="GET" action="index.php" class="row g-3 mb-4">
                <input type="hidden" name="action" value="reportes">
                <input type="hidden" name="reporte" value="7">
                <div class="col-md-8">
                    <label class="form-label">Número de Factura:</label>
                    <input type="text" name="numero_factura" class="form-control" 
                           placeholder="Ej: FAC-000001 o WEB-000001" required>
                </div>
                <div class="col-md-4 d-flex align-items-end">
                    <button type="submit" class="btn btn-secondary w-100">
                        <i class="fas fa-search"></i> Buscar
                    </button>
                </div>
            </form>
            
            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i> 
                <strong>Sugerencia:</strong> Para gestionar todas las facturas (ver, imprimir, anular), 
                utiliza el botón <strong>"Gestión de Facturas"</strong> arriba.
            </div>
        </div>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>