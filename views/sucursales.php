<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sucursales</title>
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
        .badge-activa {
            background-color: #28a745;
            padding: 6px 12px;
            border-radius: 20px;
        }
        .badge-inactiva {
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
                <i class="bi bi-shop"></i> Sucursales
            </h2>
            <small class="text-muted">Gestión de sucursales del sistema</small>
        </div>
        <div>
            <a href="index.php?action=formularioSucursal" class="btn btn-primary me-2">
                <i class="bi bi-plus-lg"></i> Nueva Sucursal
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
                        <th>Dirección</th>
                        <th>Teléfono</th>
                        <th>Coordenadas</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                <?php 
                if (is_object($sucursales) && $sucursales->rowCount() > 0) :
                    while ($row = $sucursales->fetch(PDO::FETCH_ASSOC)) : 
                ?>
                    <tr>
                        <td><strong><?= htmlspecialchars($row['sucursal_id']) ?></strong></td>
                        <td>
                            <i class="bi bi-shop-window text-primary"></i>
                            <strong><?= htmlspecialchars($row['nombre_sucursal']) ?></strong>
                        </td>
                        <td>
                            <small class="text-muted">
                                <i class="bi bi-geo-alt"></i>
                                <?= htmlspecialchars(substr($row['direccion_sucursal'], 0, 50)) ?>
                                <?= strlen($row['direccion_sucursal']) > 50 ? '...' : '' ?>
                            </small>
                        </td>
                        <td>
                            <i class="bi bi-telephone"></i>
                            <?= htmlspecialchars($row['telefono_sucursal'] ?? 'N/A') ?>
                        </td>
                        <td>
                            <?php if($row['latitud'] && $row['longitud']): ?>
                                <small>
                                    <i class="bi bi-pin-map"></i>
                                    <?= htmlspecialchars($row['latitud']) ?>, <?= htmlspecialchars($row['longitud']) ?>
                                    <a href="https://www.google.com/maps?q=<?= $row['latitud'] ?>,<?= $row['longitud'] ?>" 
                                       target="_blank" 
                                       class="btn btn-sm btn-outline-primary" 
                                       title="Ver en mapa">
                                        <i class="bi bi-map"></i>
                                    </a>
                                </small>
                            <?php else: ?>
                                <span class="text-muted">N/A</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if($row['activa']): ?>
                                <span class="badge badge-activa">
                                    <i class="bi bi-check-circle"></i> Activa
                                </span>
                            <?php else: ?>
                                <span class="badge badge-inactiva">
                                    <i class="bi bi-x-circle"></i> Inactiva
                                </span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <a href="index.php?action=editarSucursal&sucursal_id=<?= $row['sucursal_id'] ?>" 
                                   class="btn btn-warning text-white" 
                                   title="Editar">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <a href="index.php?action=cambiarEstadoSucursal&sucursal_id=<?= $row['sucursal_id'] ?>&estado=<?= $row['activa'] ? 0 : 1 ?>" 
                                   class="btn btn-info" 
                                   title="<?= $row['activa'] ? 'Desactivar' : 'Activar' ?>">
                                    <i class="bi bi-toggle-<?= $row['activa'] ? 'on' : 'off' ?>"></i>
                                </a>
                                <a href="index.php?action=eliminarSucursal&sucursal_id=<?= $row['sucursal_id'] ?>" 
                                   class="btn btn-danger" 
                                   onclick="return confirm('¿Está seguro de eliminar esta sucursal?')" 
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
                        <td colspan="7" class="text-center py-5">
                            <i class="bi bi-inbox fs-1 text-muted"></i>
                            <p class="mt-2 text-muted">No hay sucursales registradas.</p>
                            <a href="index.php?action=formularioSucursal" class="btn btn-primary mt-2">
                                <i class="bi bi-plus-lg"></i> Agregar Primera Sucursal
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