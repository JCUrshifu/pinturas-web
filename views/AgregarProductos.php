<?php
$mensaje = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once __DIR__ . '/../controllers/ProductoController.php';
    $productoController = new ProductoController($db);

    $data = [
        'codigo_sku' => $_POST['codigo_sku'],
        'nombre' => $_POST['nombre'],
        'descripcion' => $_POST['descripcion'],
        'tipo_producto_id' => $_POST['tipo_producto_id'],
        'medida_id' => $_POST['medida_id'],
        'precio_base' => $_POST['precio_base'],
        'porcentaje_descuento' => $_POST['porcentaje_descuento'],
        'stock_minimo' => $_POST['stock_minimo'],
        'duracion_anios' => $_POST['duracion_anios'],
        'cobertura_m2' => $_POST['cobertura_m2'],
        'color' => $_POST['color'],
        'proveedor_id' => $_POST['proveedor_id'],
        'activo' => isset($_POST['activo']) ? 1 : 0,
        'fecha_creacion' => $_POST['fecha_creacion']
    ];

    if ($productoController->agregarProducto($data)) {
        $mensaje = "<div class='alert alert-success text-center'><i class='bi bi-check-circle-fill'></i> Producto agregado correctamente.</div>";
    } else {
        $mensaje = "<div class='alert alert-danger text-center'><i class='bi bi-x-circle-fill'></i> Error al agregar producto.</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Agregar Producto</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background-color: #eef2f7;
            font-family: 'Segoe UI', sans-serif;
        }
        .form-container {
            max-width: 850px;
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
    <h2><i class="bi bi-paint-bucket"></i> Agregar Producto</h2>

    <?= $mensaje ?>

    <form method="POST" action="">
        <div class="row g-3">
            <div class="col-md-4">
                <label class="form-label"><i class="bi bi-upc-scan"></i> Código SKU</label>
                <input type="text" class="form-control" name="codigo_sku" placeholder="Ej. PNT-001" required>
            </div>

            <div class="col-md-8">
                <label class="form-label"><i class="bi bi-box-seam"></i> Nombre del Producto</label>
                <input type="text" class="form-control" name="nombre" placeholder="Ej. Pintura acrílica blanca" required>
            </div>

            <div class="col-12">
                <label class="form-label"><i class="bi bi-card-text"></i> Descripción</label>
                <textarea class="form-control" name="descripcion" rows="2" placeholder="Breve descripción del producto..."></textarea>
            </div>

            <div class="col-md-4">
                <label class="form-label"><i class="bi bi-diagram-3"></i> Tipo de Producto ID</label>
                <input type="number" class="form-control" name="tipo_producto_id" placeholder="Ej. 1">
            </div>

            <div class="col-md-4">
                <label class="form-label"><i class="bi bi-rulers"></i> Medida ID</label>
                <input type="number" class="form-control" name="medida_id" placeholder="Ej. 2">
            </div>

            <div class="col-md-4">
                <label class="form-label"><i class="bi bi-cash-stack"></i> Precio Base</label>
                <input type="number" step="0.01" class="form-control" name="precio_base" placeholder="Ej. 45.50">
            </div>

            <div class="col-md-4">
                <label class="form-label"><i class="bi bi-percent"></i> Descuento (%)</label>
                <input type="number" step="0.01" class="form-control" name="porcentaje_descuento" placeholder="Ej. 10">
            </div>

            <div class="col-md-4">
                <label class="form-label"><i class="bi bi-box"></i> Stock Mínimo</label>
                <input type="number" class="form-control" name="stock_minimo" placeholder="Ej. 5">
            </div>

            <div class="col-md-4">
                <label class="form-label"><i class="bi bi-hourglass-split"></i> Duración (años)</label>
                <input type="number" class="form-control" name="duracion_anios" placeholder="Ej. 2">
            </div>

            <div class="col-md-4">
                <label class="form-label"><i class="bi bi-brush"></i> Cobertura (m²)</label>
                <input type="number" class="form-control" name="cobertura_m2" placeholder="Ej. 12">
            </div>

            <div class="col-md-4">
                <label class="form-label"><i class="bi bi-palette"></i> Color</label>
                <input type="text" class="form-control" name="color" placeholder="Ej. Blanco nieve">
            </div>

            <div class="col-md-4">
                <label class="form-label"><i class="bi bi-truck"></i> Proveedor ID</label>
                <input type="number" class="form-control" name="proveedor_id" placeholder="Ej. 7">
            </div>

            <div class="col-md-4">
                <label class="form-label"><i class="bi bi-calendar3"></i> Fecha de Creación</label>
                <input type="date" class="form-control" name="fecha_creacion">
            </div>

            <div class="col-md-4 d-flex align-items-center mt-4">
                <div class="form-check">
                    <input type="checkbox" class="form-check-input" name="activo" id="activo">
                    <label class="form-check-label" for="activo"><i class="bi bi-toggle-on"></i> Activo</label>
                </div>
            </div>
        </div>

        <button type="submit" class="btn btn-primary mt-4 w-100">
            <i class="bi bi-save-fill"></i> Guardar Producto
        </button>
        <a href="index.php?action=listarProductos" class="btn btn-secondary mt-2 w-100">
            <i class="bi bi-arrow-left-circle"></i> Volver a la Lista
        </a>
    </form>
</div>

</body>
</html>
