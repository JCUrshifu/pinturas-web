<?php
require_once __DIR__ . '/../controllers/EmpleadoController.php';

$empleadoController = new EmpleadoController($db);
$mensaje = "";

// Obtener empleado si se envía por GET
if (!isset($empleado) && isset($_GET['empleado_id'])) {
    $empleado = $empleadoController->obtenerEmpleado($_GET['empleado_id']);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $empleado_id = $_POST['empleado_id'];
    $usuario_id = $_POST['usuario_id'];
    $nombre_empleado = trim($_POST['nombre_empleado']);
    $apellido_empleado = trim($_POST['apellido_empleado']);
    $sucursal_id = $_POST['sucursal_id'];
    $activo = isset($_POST['activo']) ? 1 : 0;
    $fecha_contratacion = $_POST['fecha_contratacion'];

    if ($empleadoController->actualizarEmpleado($empleado_id, $usuario_id, $nombre_empleado, $apellido_empleado, $sucursal_id, $activo, $fecha_contratacion)) {
        $mensaje = "<div class='alert alert-success'><i class='bi bi-check-circle'></i> Empleado actualizado correctamente.</div>";
        $empleado = $empleadoController->obtenerEmpleado($empleado_id);
    } else {
        $mensaje = "<div class='alert alert-danger'><i class='bi bi-x-circle'></i> Error al actualizar empleado.</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Empleado</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background-color: #f1f5f9;
            font-family: "Segoe UI", sans-serif;
        }
        .form-container {
            max-width: 700px;
            background: #fff;
            padding: 35px;
            border-radius: 12px;
            margin: 60px auto;
            box-shadow: 0 4px 18px rgba(0,0,0,0.1);
        }
        h2 {
            color: #1e293b;
        }
        .btn-primary {
            background-color: #2563eb;
            border: none;
        }
        .btn-primary:hover { background-color: #1d4ed8; }
        .btn-secondary {
            background-color: #475569;
            border: none;
        }
        .btn-secondary:hover { background-color: #334155; }
        label { font-weight: 500; }
    </style>
</head>
<body>

<div class="container form-container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2><i class="bi bi-person-gear text-primary"></i> Editar Empleado</h2>
        <a href="index.php?action=listarEmpleados" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Volver
        </a>
    </div>

    <?= $mensaje ?>

    <?php if ($empleado): ?>
    <form method="POST" action="">
        <input type="hidden" name="empleado_id" value="<?= $empleado['empleado_id'] ?>">

        <div class="row g-3">
            <div class="col-md-4">
                <label for="usuario_id" class="form-label">Usuario ID</label>
                <input type="number" class="form-control" name="usuario_id" id="usuario_id"
                       value="<?= htmlspecialchars($empleado['usuario_id']) ?>" required>
            </div>

            <div class="col-md-4">
                <label for="sucursal_id" class="form-label">Sucursal ID</label>
                <input type="number" class="form-control" name="sucursal_id" id="sucursal_id"
                       value="<?= htmlspecialchars($empleado['sucursal_id']) ?>">
            </div>

            <div class="col-md-4">
                <label for="fecha_contratacion" class="form-label">Fecha Contratación</label>
                <input type="date" class="form-control" name="fecha_contratacion" id="fecha_contratacion"
                       value="<?= htmlspecialchars($empleado['fecha_contratacion']) ?>">
            </div>

            <div class="col-md-6">
                <label for="nombre_empleado" class="form-label">Nombre</label>
                <input type="text" class="form-control" name="nombre_empleado" id="nombre_empleado"
                       value="<?= htmlspecialchars($empleado['nombre_empleado']) ?>" required>
            </div>

            <div class="col-md-6">
                <label for="apellido_empleado" class="form-label">Apellido</label>
                <input type="text" class="form-control" name="apellido_empleado" id="apellido_empleado"
                       value="<?= htmlspecialchars($empleado['apellido_empleado']) ?>" required>
            </div>

            <div class="col-md-6 form-check mt-3">
                <input type="checkbox" class="form-check-input" name="activo" id="activo"
                       <?= $empleado['activo'] ? 'checked' : '' ?>>
                <label class="form-check-label" for="activo">Empleado activo</label>
            </div>
        </div>

        <div class="mt-4">
            <button type="submit" class="btn btn-primary w-100">
                <i class="bi bi-save"></i> Guardar Cambios
            </button>
        </div>
    </form>
    <?php else: ?>
        <div class="alert alert-warning text-center">
            <i class="bi bi-exclamation-triangle"></i> No se encontró información del empleado.
        </div>
    <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
