<?php
require_once __DIR__ . '/../controllers/EmpleadoController.php';

$empleadoController = new EmpleadoController($db);
$mensaje = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario_id = $_POST['usuario_id'];
    $nombre_empleado = $_POST['nombre_empleado'];
    $apellido_empleado = $_POST['apellido_empleado'];
    $sucursal_id = $_POST['sucursal_id'];
    $activo = isset($_POST['activo']) ? 1 : 0;
    $fecha_contratacion = $_POST['fecha_contratacion'];

    if ($empleadoController->agregarEmpleado($usuario_id, $nombre_empleado, $apellido_empleado, $sucursal_id, $activo, $fecha_contratacion)) {
        $mensaje = "<div class='alert alert-success text-center'><i class='bi bi-check-circle-fill'></i> Empleado agregado correctamente.</div>";
    } else {
        $mensaje = "<div class='alert alert-danger text-center'><i class='bi bi-x-circle-fill'></i> Error al agregar empleado.</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Agregar Empleado</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background-color: #eef2f7;
            font-family: 'Segoe UI', sans-serif;
        }
        .form-container {
            max-width: 550px;
            margin: 60px auto;
            background-color: #fff;
            border-radius: 16px;
            box-shadow: 0 6px 14px rgba(0,0,0,0.1);
            padding: 40px 35px;
        }
        h2 {
            text-align: center;
            margin-bottom: 25px;
            font-weight: 700;
            color: #0d6efd;
        }
        label {
            font-weight: 500;
            color: #343a40;
        }
        .btn {
            border-radius: 10px;
            font-weight: 500;
        }
        .btn i {
            margin-right: 6px;
        }
        .form-check-label {
            margin-left: 6px;
        }
    </style>
</head>
<body>

<div class="container form-container">
    <h2><i class="bi bi-person-plus-fill"></i> Agregar Empleado</h2>

    <?= $mensaje ?>

    <form method="POST" action="">
        <div class="mb-3">
            <label for="usuario_id" class="form-label"><i class="bi bi-person-badge"></i> Usuario ID</label>
            <input type="number" class="form-control" name="usuario_id" id="usuario_id" placeholder="Ej. 101" required>
        </div>

        <div class="mb-3">
            <label for="nombre_empleado" class="form-label"><i class="bi bi-person-fill"></i> Nombre</label>
            <input type="text" class="form-control" name="nombre_empleado" id="nombre_empleado" placeholder="Ej. Julio" required>
        </div>

        <div class="mb-3">
            <label for="apellido_empleado" class="form-label"><i class="bi bi-person-lines-fill"></i> Apellido</label>
            <input type="text" class="form-control" name="apellido_empleado" id="apellido_empleado" placeholder="Ej. Cossío" required>
        </div>

        <div class="mb-3">
            <label for="sucursal_id" class="form-label"><i class="bi bi-building"></i> Sucursal ID</label>
            <input type="number" class="form-control" name="sucursal_id" id="sucursal_id" placeholder="Ej. 3">
        </div>

        <div class="form-check mb-3">
            <input type="checkbox" class="form-check-input" name="activo" id="activo">
            <label for="activo" class="form-check-label"><i class="bi bi-toggle-on"></i> Activo</label>
        </div>

        <div class="mb-4">
            <label for="fecha_contratacion" class="form-label"><i class="bi bi-calendar-check"></i> Fecha de Contratación</label>
            <input type="date" class="form-control" name="fecha_contratacion" id="fecha_contratacion">
        </div>

        <button type="submit" class="btn btn-primary w-100 mb-2">
            <i class="bi bi-save-fill"></i> Guardar Empleado
        </button>
        <a href="index.php?action=listarEmpleados" class="btn btn-secondary w-100">
            <i class="bi bi-arrow-left-circle"></i> Volver a la Lista
        </a>
    </form>
</div>

</body>
</html>
