<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tipos de Pago</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #e3f2fd, #ffffff);
            font-family: 'Segoe UI', sans-serif;
        }
        .page-header {
            background: #ffffff;
            padding: 25px 30px;
            border-radius: 15px;
            box-shadow: 0 3px 12px rgba(0,0,0,0.1);
            margin-bottom: 25px;
        }
        .table-container {
            background: #ffffff;
            padding: 20px 25px;
            border-radius: 15px;
            box-shadow: 0 3px 12px rgba(0,0,0,0.08);
        }
        .table thead {
            background-color: #0d6efd;
            color: white;
        }
        .table-hover tbody tr:hover {
            background-color: #f1f7ff;
        }
        .badge-activo {
            background-color: #28a745;
            padding: 6px 12px;
            border-radius: 20px;
        }
        .badge-inactivo {
            background-color: #dc3545;
            padding: 6px 12px;
            border-radius: 20px;
        }
    </style>
</head>
<body>

<div class="container mt-4">
    
    <!-- Encabezado -->
    <div class="page-header d-flex justify-content-between align-items-center">
        <div>
            <h2 class="mb-1 text-primary">
                <i class="bi bi-credit-card"></i> Tipos de Pago
            </h2>
            <small class="text-muted">Métodos de pago disponibles en el sistema</small>
        </div>
        <div>
            <a href="index.php?action=formularioTipoPago" class="btn btn-primary me-2">
                <i class="bi bi-plus-lg"></i> Nuevo Tipo de Pago
            </a>
            <a href="index.php?action=dashboard" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Dashboard
            </a>
        </div>
    </div>

    <!-- Mensajes -->
    <?php if(isset($_SESSION['mensaje'])): ?>
        <div class="alert alert-success alert-dismissible fade show">
            <i class="bi bi-check-circle-fill"></i>
            <?= htmlspecialchars($_SESSION['mensaje']); unset($_SESSION['mensaje']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if(isset($_SESSION['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show">
            <i class="bi bi-exclamation-triangle-fill"></i>
            <?= htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>
    
    <!-- Tabla -->
    <div class="table-container">
        <div class="table-responsive">
            <table class="table table-hover align-middle text-center">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Descripción</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                <?php 
                if (is_object($tiposPago) && $tiposPago->rowCount() > 0) :
                    while ($row = $tiposPago->fetch(PDO::FETCH_ASSOC)) : 
                ?>
                    <tr>
                        <td><strong><?= htmlspecialchars($row['tipo_pago_id']) ?></strong></td>
                        <td>
                            <?php
                            $iconos = [
                                'Efectivo' => 'cash-stack',
                                'Tarjeta' => 'credit-card',
                                'Cheque' => 'file-earmark-text',
                                'Transferencia' => 'bank',
                                'Depósito' => 'piggy-bank'
                            ];
                            $icono = $iconos[$row['nombre_pago']] ?? 'wallet2';
                            ?>
                            <i class="bi bi-<?= $icono ?> text-primary"></i>
                            <strong><?= htmlspecialchars($row['nombre_pago']) ?></strong>
                        </td>
                        <td>
                            <small class="text-muted">
                                <?= htmlspecialchars($row['descripcion'] ?? 'Sin descripción') ?>
                            </small>
                        </td>
                        <td>
                            <?php if($row['activo']): ?>
                                <span class="badge badge-activo">
                                    <i class="bi bi-check-circle"></i> Activo
                                </span>
                            <?php else: ?>
                                <span class="badge badge-inactivo">
                                    <i class="bi bi-x-circle"></i> Inactivo
                                </span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <a href="index.php?action=editarTipoPago&tipo_pago_id=<?= $row['tipo_pago_id'] ?>" 
                                   class="btn btn-warning text-white" 
                                   title="Editar">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <a href="index.php?action=cambiarEstadoTipoPago&tipo_pago_id=<?= $row['tipo_pago_id'] ?>&estado=<?= $row['activo'] ? 0 : 1 ?>" 
                                   class="btn btn-info" 
                                   title="<?= $row['activo'] ? 'Desactivar' : 'Activar' ?>">
                                    <i class="bi bi-toggle-<?= $row['activo'] ? 'on' : 'off' ?>"></i>
                                </a>
                                <a href="index.php?action=eliminarTipoPago&tipo_pago_id=<?= $row['tipo_pago_id'] ?>" 
                                   class="btn btn-danger" 
                                   onclick="return confirm('¿Está seguro de eliminar este tipo de pago?')" 
                                   title="Eliminar">
                                    <i class="bi bi-trash"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                <?php 
                    endwhile; 
                else: 
                ?>
                    <tr>
                        <td colspan="5" class="text-center py-5">
                            <i class="bi bi-inbox fs-1 text-muted"></i>
                            <p class="mt-2 text-muted">No hay tipos de pago registrados.</p>
                            <a href="index.php?action=formularioTipoPago" class="btn btn-primary mt-2">
                                <i class="bi bi-plus-lg"></i> Agregar Primer Tipo de Pago
                            </a>
                        </td>
                    </tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>