<?php
if (!isset($productos)) $productos = [];
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Administrador - Lista de Productos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background-color: #f4f6f8;
            font-family: 'Segoe UI', sans-serif;
        }
        .card {
            border-radius: 12px;
            border: none;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        .table th {
            text-align: center;
            background-color: #1e3a8a;
            color: #fff;
            vertical-align: middle;
        }
        .table td {
            vertical-align: middle;
            text-align: center;
        }
        .btn {
            border-radius: 8px;
        }
        .btn-primary {
            background-color: #2563eb;
            border: none;
        }
        .btn-primary:hover {
            background-color: #1e40af;
        }
        .badge-activo {
            font-weight: 500;
            padding: 6px 12px;
            border-radius: 20px;
        }
        .table-responsive {
            max-height: 70vh;
            overflow-y: auto;
        }
    </style>
</head>
<body>
<div class="container mt-5 mb-5">
    <div class="card p-4">
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
            <div>
                <h2 class="fw-bold mb-1 text-primary"><i class="bi bi-bucket-fill"></i> Lista de Productos</h2>
                <small class="text-muted">Gestión y mantenimiento del catálogo de Paints</small>
            </div>

            <a href="index.php?action=formularioProducto" class="btn btn-primary">
                <i class="bi bi-plus-circle"></i> Agregar Producto
            </a>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>SKU</th>
                        <th>Nombre</th>
                        <th>Descripción</th>
                        <th>Tipo</th>
                        <th>Medida</th>
                        <th>Precio (Q)</th>
                        <th>Descuento</th>
                        <th>Stock Mínimo</th>
                        <th>Duración</th>
                        <th>Cobertura</th>
                        <th>Color</th>
                        <th>Proveedor</th>
                        <th>Activo</th>
                        <th>Creado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (is_object($productos) && $productos->rowCount() > 0): ?>
                        <?php while ($row = $productos->fetch(PDO::FETCH_ASSOC)): ?>
                            <tr>
                                <td><?= $row['producto_id'] ?></td>
                                <td class="fw-semibold"><?= htmlspecialchars($row['codigo_sku']) ?></td>
                                <td><?= htmlspecialchars($row['nombre']) ?></td>
                                <td class="text-truncate" style="max-width:150px;"><?= htmlspecialchars($row['descripcion']) ?></td>
                                <td><?= htmlspecialchars($row['tipo_producto_id']) ?></td>
                                <td><?= htmlspecialchars($row['medida_id']) ?></td>
                                <td><span class="fw-bold text-success">Q<?= number_format($row['precio_base'], 2) ?></span></td>
                                <td><?= $row['porcentaje_descuento'] ?>%</td>
                                <td><?= $row['stock_minimo'] ?></td>
                                <td><?= $row['duracion_anios'] ?></td>
                                <td><?= $row['cobertura_m2'] ?> m²</td>
                                <td><?= htmlspecialchars($row['color']) ?></td>
                                <td><?= htmlspecialchars($row['proveedor_id']) ?></td>
                                <td>
                                    <?= $row['activo']
                                        ? '<span class="badge bg-success badge-activo">Activo</span>'
                                        : '<span class="badge bg-secondary badge-activo">Inactivo</span>'
                                    ?>
                                </td>
                                <td><?= htmlspecialchars($row['fecha_creacion']) ?></td>
                                <td>
                                    <div class="d-flex justify-content-center gap-2">
                                        <a href="index.php?action=editarProducto&producto_id=<?= $row['producto_id'] ?>" class="btn btn-warning btn-sm">
                                            <i class="bi bi-pencil-square"></i>
                                        </a>
                                        <a href="index.php?action=eliminarProducto&producto_id=<?= $row['producto_id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('¿Eliminar este producto?')">
                                            <i class="bi bi-trash"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="16" class="text-center text-info py-4">
                                <i class="bi bi-exclamation-circle fs-4"></i><br>
                                No hay productos registrados.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <div class="mt-4 text-end">
            <a href="index.php?action=dashboard" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left-circle"></i> Volver al Dashboard
            </a>
        </div>
    </div>
</div>
</body>
</html>
