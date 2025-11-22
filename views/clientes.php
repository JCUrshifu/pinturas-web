<?php
// Se asume que $clientes viene del controlador
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mantenimiento de Clientes</title>
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
        .btn {
            border-radius: 8px;
        }
        .badge-promocion {
            font-size: 0.9rem;
            padding: 6px 10px;
        }
        .page-header h2 {
            font-weight: 700;
        }
        .empty-state {
            padding: 50px 0;
            text-align: center;
            color: #6c757d;
        }
        .empty-state i {
            font-size: 3rem;
            color: #adb5bd;
        }
    </style>
</head>
<body>

<div class="container mt-4">
    
    <!-- Encabezado -->
    <div class="page-header d-flex justify-content-between align-items-center">
        <div>
            <h2 class="mb-1 text-primary">
                <i class="bi bi-people-fill"></i> Mantenimiento de Clientes
            </h2>
            <small class="text-muted">Administra los datos y registros de tus clientes</small>
        </div>
        <div>
            <a href="index.php?action=formularioCliente" class="btn btn-primary me-2">
                <i class="bi bi-person-plus"></i> Nuevo Cliente
            </a>
            <a href="index.php?action=dashboard" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Dashboard
            </a>
        </div>
    </div>
    
    <!-- Tabla de Clientes -->
    <div class="table-container">
        <div class="table-responsive">
            <table class="table table-hover align-middle text-center">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Usuario</th>
                        <th>Nombre</th>
                        <th>NIT</th>
                        <th>Email</th>
                        <th>Teléfono</th>
                        <th>Dirección</th>
                        <th>Registro</th>
                        <th>Promociones</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                <?php 
                if (is_object($clientes) && $clientes->rowCount() > 0) :
                    while ($row = $clientes->fetch(PDO::FETCH_ASSOC)) : 
                ?>
                    <tr>
                        <td><strong><?= htmlspecialchars($row['cliente_id']) ?></strong></td>
                        <td><?= htmlspecialchars($row['usuario_id']) ?></td>
                        <td>
                            <i class="bi bi-person-circle text-primary"></i>
                            <?= htmlspecialchars($row['nombre_cliente']) ?>
                        </td>
                        <td>
                            <span class="badge bg-info text-dark">
                                <?= htmlspecialchars($row['nit'] ?? 'N/A') ?>
                            </span>
                        </td>
                        <td><small><?= htmlspecialchars($row['email']) ?></small></td>
                        <td><?= htmlspecialchars($row['telefono']) ?></td>
                        <td><small class="text-muted"><?= htmlspecialchars(substr($row['direccion'], 0, 40)) ?><?= strlen($row['direccion']) > 40 ? '...' : '' ?></small></td>
                        <td><small><?= date('d/m/Y', strtotime($row['fecha_registro'])) ?></small></td>
                        <td>
                            <?php if ($row['acepta_promociones']): ?>
                                <span class="badge bg-success badge-promocion">
                                    <i class="bi bi-check-circle"></i> Sí
                                </span>
                            <?php else: ?>
                                <span class="badge bg-secondary badge-promocion">
                                    <i class="bi bi-x-circle"></i> No
                                </span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <a href="index.php?action=editarCliente&cliente_id=<?= $row['cliente_id'] ?>" class="btn btn-warning text-white" title="Editar">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <a href="index.php?action=eliminarCliente&cliente_id=<?= $row['cliente_id'] ?>" class="btn btn-danger" onclick="return confirm('¿Está seguro de eliminar este cliente?')" title="Eliminar">
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
                        <td colspan="10" class="empty-state">
                            <i class="bi bi-inbox"></i>
                            <p class="mt-2">No hay clientes registrados en el sistema.</p>
                            <a href="index.php?action=formularioCliente" class="btn btn-primary mt-2">
                                <i class="bi bi-person-plus"></i> Agregar Primer Cliente
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
