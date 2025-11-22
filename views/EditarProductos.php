<?php
require_once __DIR__ . '/../controllers/ProductoController.php';
$productoController = new ProductoController($db);
$mensaje = "";

// Si se envía el formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['producto_id'];
    $data = [
        'codigo_sku' => trim($_POST['codigo_sku']),
        'nombre' => trim($_POST['nombre']),
        'descripcion' => trim($_POST['descripcion']),
        'tipo_producto_id' => $_POST['tipo_producto_id'],
        'medida_id' => $_POST['medida_id'],
        'precio_base' => $_POST['precio_base'],
        'porcentaje_descuento' => $_POST['porcentaje_descuento'],
        'stock_minimo' => $_POST['stock_minimo'],
        'duracion_anios' => $_POST['duracion_anios'],
        'cobertura_m2' => $_POST['cobertura_m2'],
        'color' => trim($_POST['color']),
        'proveedor_id' => $_POST['proveedor_id'],
        'activo' => isset($_POST['activo']) ? 1 : 0,
        'fecha_creacion' => $_POST['fecha_creacion']
    ];

    if ($productoController->actualizarProducto($id, $data)) {
        $mensaje = "<div class='alert alert-success text-center'>✅ Producto actualizado correctamente.</div>";
        $producto = $productoController->obtenerProducto($id);
    } else {
        $mensaje = "<div class='alert alert-danger text-center'>❌ Error al actualizar producto.</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Producto</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { 
            background: linear-gradient(135deg, #eef2f3, #8e9eab);
            font-family: 'Segoe UI', sans-serif;
        }
        .form-container {
            max-width: 850px;
            margin: 50px auto;
            padding: 30px;
            background-color: #ffffff;
            border-radius: 15px;
            box-shadow: 0 6px 20px rgba(0,0,0,0.15);
        }
        h2 {
            color: #333;
            font-weight: 600;
        }
        label {
            font-weight: 500;
        }
        .btn-primary {
            background-color: #007bff;
            border: none;
            transition: 0.3s;
        }
        .btn-primary:hover {
            background-color: #0056b3;
        }
        .btn-secondary {
            background-color: #6c757d;
            border: none;
        }
        .alert {
            font-size: 0.95rem;
        }
    </style>
</head>
<body>
<div class="container form-container">
    <h2 class="mb-4 text-center">✏️ Editar Producto</h2>

    <?= $mensaje ?>

    <form method="POST" action="">
        <input type="hidden" name="producto_id" value="<?= $producto['producto_id'] ?>">

        <div class="row g-3">
            <div class="col-md-4">
                <label class="form-label">Código SKU</label>
                <input type="text" class="form-control" name="codigo_sku" value="<?= htmlspecialchars($producto['codigo_sku']) ?>" required>
            </div>
            <div class="col-md-8">
                <label class="form-label">Nombre</label>
                <input type="text" class="form-control" name="nombre" value="<?= htmlspecialchars($producto['nombre']) ?>" required>
            </div>

            <div class="col-12">
                <label class="form-label">Descripción</label>
                <textarea class="form-control" name="descripcion" rows="3"><?= htmlspecialchars($producto['descripcion']) ?></textarea>
            </div>

            <div class="col-md-4">
                <label class="form-label">Tipo Producto ID</label>
                <input type="number" class="form-control" name="tipo_producto_id" value="<?= $producto['tipo_producto_id'] ?>">
            </div>
            <div class="col-md-4">
                <label class="form-label">Medida ID</label>
                <input type="number" class="form-control" name="medida_id" value="<?= $producto['medida_id'] ?>">
            </div>
            <div class="col-md-4">
                <label class="form-label">Precio Base (Q)</label>
                <input type="number" class="form-control" step="0.01" name="precio_base" value="<?= $producto['precio_base'] ?>" required>
            </div>

            <div class="col-md-4">
                <label class="form-label">Descuento (%)</label>
                <input type="number" class="form-control" step="0.01" name="porcentaje_descuento" value="<?= $producto['porcentaje_descuento'] ?>">
            </div>
            <div class="col-md-4">
                <label class="form-label">Stock Mínimo</label>
                <input type="number" class="form-control" name="stock_minimo" value="<?= $producto['stock_minimo'] ?>">
            </div>
            <div class="col-md-4">
                <label class="form-label">Duración (años)</label>
                <input type="number" class="form-control" name="duracion_anios" value="<?= $producto['duracion_anios'] ?>">
            </div>

            <div class="col-md-4">
                <label class="form-label">Cobertura (m²)</label>
                <input type="number" class="form-control" name="cobertura_m2" value="<?= $producto['cobertura_m2'] ?>">
            </div>
            <div class="col-md-4">
                <label class="form-label">Color</label>
                <input type="text" class="form-control" name="color" value="<?= htmlspecialchars($producto['color']) ?>">
            </div>
            <div class="col-md-4">
                <label class="form-label">Proveedor ID</label>
                <input type="number" class="form-control" name="proveedor_id" value="<?= $producto['proveedor_id'] ?>">
            </div>

            <div class="col-md-4 form-check mt-4">
                <input type="checkbox" class="form-check-input" name="activo" id="activo" <?= $producto['activo'] ? 'checked' : '' ?>>
                <label class="form-check-label" for="activo">Activo</label>
            </div>

            <div class="col-md-4">
                <label class="form-label">Fecha Creación</label>
                <input type="date" class="form-control" name="fecha_creacion" value="<?= $producto['fecha_creacion'] ?>">
            </div>
        </div>

        <div class="mt-4">
            <button type="submit" class="btn btn-primary w-100">💾 Guardar Cambios</button>
            <a href="index.php?action=listarProductos" class="btn btn-secondary w-100 mt-2">← Volver a la lista</a>
        </div>
    </form>
</div>
</body>
</html>
