<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Proveedores</title>
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
    </style>
</head>
<body>

<div class="container mt-4">
    
    <!-- Encabezado -->
    <div class="page-header d-flex justify-content-between align-items-center">
        <div>
            <h2 class="mb-1 text-primary">
                <i class="bi bi-truck"></i> Proveedores
            </h2>
            <small class="text-muted">Gestión de proveedores del sistema</small>
        </div>
        <div>
            <a href="index.php?action=formularioProveedor" class="btn btn-primary me-2">
                <i class="bi bi-plus-lg"></i> Nuevo Proveedor
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
                        <th>Contacto</th>
                        <th>Teléfono</th>
                        <th>Email</th>
                        <th>Dirección</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                <?php 
                if (is_object($proveedores) && $proveedores->rowCount() > 0) :
                    while ($row = $proveedores->fetch(PDO::FETCH_ASSOC)) : 
                ?>
                    <tr>
                        <td><strong><?= htmlspecialchars($row['proveedor_id']) ?></strong></td>
                        <td>
                            <i class="bi bi-building text-primary"></i>
                            <?= htmlspecialchars($row['nombre_proveedor']) ?>
                        </td>
                        <td><?= htmlspecialchars($row['contacto_proveedor'] ?? 'N/A') ?></td>
                        <td>
                            <i class="bi bi-telephone"></i>
                            <?= htmlspecialchars($row['telefono_proveedor'] ?? 'N/A') ?>
                        </td>
                        <td>
                            <i class="bi bi-envelope"></i>
                            <?= htmlspecialchars($row['email_proveedor'] ?? 'N/A') ?>
                        </td>
                        <td>
                            <small class="text-muted">
                                <?= htmlspecialchars(substr($row['direccion_proveedor'] ?? 'N/A', 0, 40)) ?>
                                <?= strlen($row['direccion_proveedor'] ?? '') > 40 ? '...' : '' ?>
                            </small>
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <a href="index.php?action=editarProveedor&proveedor_id=<?= $row['proveedor_id'] ?>" 
                                   class="btn btn-warning text-white" 
                                   title="Editar">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <a href="index.php?action=eliminarProveedor&proveedor_id=<?= $row['proveedor_id'] ?>" 
                                   class="btn btn-danger" 
                                   onclick="return confirm('¿Está seguro de eliminar este proveedor?')" 
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
                            <p class="mt-2 text-muted">No hay proveedores registrados.</p>
                            <a href="index.php?action=formularioProveedor" class="btn btn-primary mt-2">
                                <i class="bi bi-plus-lg"></i> Agregar Primer Proveedor
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