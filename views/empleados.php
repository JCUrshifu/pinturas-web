<?php
// Se asume que $empleados viene de $controller->listarEmpleados()
if (!isset($empleados)) $empleados = [];
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Empleados | Sistema de Parqueo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #e3f2fd, #f8f9fa);
            font-family: 'Segoe UI', sans-serif;
        }
        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        }
        .table {
            border-radius: 10px;
            overflow: hidden;
        }
        .table thead {
            background-color: #0d6efd;
            color: white;
        }
        h2 {
            font-weight: 700;
            color: #0d6efd;
        }
        .btn-primary {
            background-color: #0d6efd;
            border: none;
        }
        .btn-primary:hover {
            background-color: #0b5ed7;
        }
        .badge {
            font-size: 0.9em;
        }
        .top-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
    </style>
</head>
<body>
<div class="container mt-5">
    <div class="card p-4">
        <div class="top-bar mb-3">
            <h2>👥 Lista de Empleados</h2>
            <div>
                <a href="index.php?action=dashboard" class="btn btn-secondary">← Volver</a>
                <a href="index.php?action=formularioEmpleado" class="btn btn-primary">+ Agregar Empleado</a>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-hover text-center align-middle">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Usuario</th>
                        <th>Nombre</th>
                        <th>Apellido</th>
                        <th>Sucursal</th>
                        <th>Estado</th>
                        <th>Fecha Contratación</th>
                        <th>Registrado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                <?php if (is_object($empleados) && method_exists($empleados, 'fetch')): ?>
                    <?php while ($row = $empleados->fetch(PDO::FETCH_ASSOC)): ?>
                        <tr>
                            <td><?= $row['empleado_id'] ?></td>
                            <td><?= $row['usuario_id'] ?></td>
                            <td><?= htmlspecialchars($row['nombre_empleado']) ?></td>
                            <td><?= htmlspecialchars($row['apellido_empleado']) ?></td>
                            <td><?= $row['sucursal_id'] ?></td>
                            <td>
                                <?= $row['activo'] 
                                    ? '<span class="badge bg-success">Activo</span>' 
                                    : '<span class="badge bg-danger">Inactivo</span>' ?>
                            </td>
                            <td><?= $row['fecha_contratacion'] ?></td>
                            <td><?= $row['created_at'] ?></td>
                            <td>
                                <a href="index.php?action=editarEmpleado&empleado_id=<?= $row['empleado_id'] ?>" class="btn btn-warning btn-sm">Editar</a>
                                <a href="index.php?action=eliminarEmpleado&empleado_id=<?= $row['empleado_id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('¿Eliminar este empleado?')">Eliminar</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="9" class="text-muted py-4">No hay empleados registrados.</td>
                    </tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
</body>
</html>
