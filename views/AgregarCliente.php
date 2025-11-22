<?php
require_once __DIR__ . '/../controllers/ClienteController.php';
require_once __DIR__ . '/../config/Database.php';

$database = new Database();
$db = $database->getConnection();
$controller = new ClienteController($db);
$mensaje = "";
$tipo_mensaje = "";

// Si se envía el formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario_id = $_POST['usuario_id'];
    $nombre_cliente = $_POST['nombre_cliente'];
    $nit = $_POST['nit'];
    $email = $_POST['email'];
    $telefono = $_POST['telefono'];
    $direccion = $_POST['direccion'];
    $acepta_promociones = isset($_POST['acepta_promociones']) ? 1 : 0;

    // Validar que el NIT no esté vacío
    if (empty($nit)) {
        $mensaje = "El campo NIT es obligatorio.";
        $tipo_mensaje = "danger";
    } 
    // Verificar si el NIT ya existe
    elseif ($controller->verificarNit($nit)) {
        $mensaje = "El NIT ingresado ya está registrado en el sistema.";
        $tipo_mensaje = "warning";
    } 
    else {
        if ($controller->agregarCliente($usuario_id, $nombre_cliente, $nit, $email, $telefono, $direccion, $acepta_promociones)) {
            $mensaje = "Cliente agregado correctamente.";
            $tipo_mensaje = "success";
            // Limpiar el formulario
            $_POST = array();
        } else {
            $mensaje = "Error al agregar el cliente.";
            $tipo_mensaje = "danger";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agregar Cliente</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .form-container {
            max-width: 800px;
            margin: 30px auto;
            padding: 30px;
            background: white;
            border-radius: 15px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        }
        .form-header {
            border-bottom: 3px solid #0d6efd;
            padding-bottom: 15px;
            margin-bottom: 25px;
        }
        .required-field::after {
            content: " *";
            color: red;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="form-container">
        <div class="form-header">
            <h2 class="mb-0">
                <i class="bi bi-person-plus-fill text-primary"></i>
                Agregar Nuevo Cliente
            </h2>
        </div>

        <?php if ($mensaje): ?>
            <div class="alert alert-<?= $tipo_mensaje ?> alert-dismissible fade show" role="alert">
                <i class="bi bi-<?= $tipo_mensaje == 'success' ? 'check-circle-fill' : 'exclamation-triangle-fill' ?>"></i>
                <?= $mensaje ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <form method="POST" action="" id="formCliente">
            <div class="row g-3">
                
                <!-- Usuario ID -->
                <div class="col-md-6">
                    <label for="usuario_id" class="form-label required-field">Usuario ID</label>
                    <input type="number" 
                           class="form-control" 
                           id="usuario_id" 
                           name="usuario_id" 
                           value="<?= $_POST['usuario_id'] ?? '' ?>"
                           required>
                    <small class="text-muted">ID del usuario asociado</small>
                </div>

                <!-- Nombre Completo -->
                <div class="col-md-6">
                    <label for="nombre_cliente" class="form-label required-field">Nombre Completo</label>
                    <input type="text" 
                           class="form-control" 
                           id="nombre_cliente" 
                           name="nombre_cliente"
                           value="<?= $_POST['nombre_cliente'] ?? '' ?>"
                           placeholder="Juan Pérez García" 
                           required>
                </div>

                <!-- NIT (NUEVO CAMPO) -->
                <div class="col-md-6">
                    <label for="nit" class="form-label required-field">
                        <i class="bi bi-card-text"></i> NIT
                    </label>
                    <input type="text" 
                           class="form-control" 
                           id="nit" 
                           name="nit"
                           value="<?= $_POST['nit'] ?? '' ?>"
                           placeholder="1234567-8 o CF" 
                           maxlength="20"
                           required>
                    <small class="text-muted">Ingrese el NIT o "CF" para consumidor final</small>
                </div>

                <!-- Email -->
                <div class="col-md-6">
                    <label for="email" class="form-label required-field">
                        <i class="bi bi-envelope"></i> Email
                    </label>
                    <input type="email" 
                           class="form-control" 
                           id="email" 
                           name="email"
                           value="<?= $_POST['email'] ?? '' ?>"
                           placeholder="cliente@ejemplo.com" 
                           required>
                </div>

                <!-- Teléfono -->
                <div class="col-md-6">
                    <label for="telefono" class="form-label">
                        <i class="bi bi-telephone"></i> Teléfono
                    </label>
                    <input type="text" 
                           class="form-control" 
                           id="telefono" 
                           name="telefono"
                           value="<?= $_POST['telefono'] ?? '' ?>"
                           placeholder="5555-5555">
                </div>

                <!-- Dirección -->
                <div class="col-12">
                    <label for="direccion" class="form-label">
                        <i class="bi bi-geo-alt"></i> Dirección
                    </label>
                    <textarea class="form-control" 
                              id="direccion" 
                              name="direccion" 
                              rows="3"
                              placeholder="Dirección completa del cliente"><?= $_POST['direccion'] ?? '' ?></textarea>
                </div>

                <!-- Acepta Promociones -->
                <div class="col-12">
                    <div class="form-check form-switch">
                        <input class="form-check-input" 
                               type="checkbox" 
                               id="acepta_promociones" 
                               name="acepta_promociones"
                               <?= isset($_POST['acepta_promociones']) ? 'checked' : '' ?>>
                        <label class="form-check-label" for="acepta_promociones">
                            <i class="bi bi-megaphone"></i> 
                            El cliente acepta recibir promociones y ofertas por correo electrónico
                        </label>
                    </div>
                </div>

            </div>

            <!-- Botones -->
            <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
                <a href="index.php?action=listarClientes" class="btn btn-secondary">
                    <i class="bi bi-arrow-left"></i> Volver
                </a>
                <button type="reset" class="btn btn-outline-warning">
                    <i class="bi bi-x-circle"></i> Limpiar
                </button>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save"></i> Guardar Cliente
                </button>
            </div>

            <div class="mt-3">
                <small class="text-muted">
                    <i class="bi bi-info-circle"></i> 
                    Los campos marcados con <span class="text-danger">*</span> son obligatorios
                </small>
            </div>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Validación adicional del NIT
document.getElementById('formCliente').addEventListener('submit', function(e) {
    const nit = document.getElementById('nit').value.trim();
    
    if (nit === '') {
        e.preventDefault();
        alert('El campo NIT es obligatorio');
        document.getElementById('nit').focus();
        return false;
    }
});

// Auto-completar NIT con "CF" si está vacío al hacer blur
document.getElementById('nit').addEventListener('blur', function() {
    if (this.value.trim() === '') {
        this.value = 'CF';
    }
});
</script>

</body>
</html>