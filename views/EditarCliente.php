<?php
// Se asume que $cliente ya fue cargado desde el controlador
if (!isset($cliente)) {
    header("Location: index.php?action=listarClientes");
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Cliente</title>
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
            border-bottom: 3px solid #ffc107;
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
                <i class="bi bi-pencil-square text-warning"></i>
                Editar Cliente
            </h2>
            <small class="text-muted">ID: <?= $cliente['cliente_id'] ?></small>
        </div>

        <form method="POST" action="index.php?action=actualizarCliente">
            <input type="hidden" name="cliente_id" value="<?= $cliente['cliente_id'] ?>">

            <div class="row g-3">
                
                <!-- Usuario ID -->
                <div class="col-md-6">
                    <label for="usuario_id" class="form-label required-field">Usuario ID</label>
                    <input type="number" 
                           class="form-control" 
                           id="usuario_id" 
                           name="usuario_id" 
                           value="<?= htmlspecialchars($cliente['usuario_id']) ?>" 
                           required>
                </div>

                <!-- Nombre Completo -->
                <div class="col-md-6">
                    <label for="nombre_cliente" class="form-label required-field">Nombre Completo</label>
                    <input type="text" 
                           class="form-control" 
                           id="nombre_cliente" 
                           name="nombre_cliente" 
                           value="<?= htmlspecialchars($cliente['nombre_cliente']) ?>" 
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
                           value="<?= htmlspecialchars($cliente['nit'] ?? 'CF') ?>"
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
                           value="<?= htmlspecialchars($cliente['email']) ?>" 
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
                           value="<?= htmlspecialchars($cliente['telefono']) ?>">
                </div>

                <!-- Dirección -->
                <div class="col-12">
                    <label for="direccion" class="form-label">
                        <i class="bi bi-geo-alt"></i> Dirección
                    </label>
                    <textarea class="form-control" 
                              id="direccion" 
                              name="direccion" 
                              rows="3"><?= htmlspecialchars($cliente['direccion']) ?></textarea>
                </div>

                <!-- Acepta Promociones -->
                <div class="col-12">
                    <div class="form-check form-switch">
                        <input class="form-check-input" 
                               type="checkbox" 
                               id="acepta_promociones" 
                               name="acepta_promociones" 
                               <?= $cliente['acepta_promociones'] ? 'checked' : '' ?>>
                        <label class="form-check-label" for="acepta_promociones">
                            <i class="bi bi-megaphone"></i> 
                            El cliente acepta recibir promociones
                        </label>
                    </div>
                </div>

                <!-- Información adicional -->
                <div class="col-12">
                    <div class="alert alert-info" role="alert">
                        <i class="bi bi-info-circle"></i>
                        <strong>Fecha de registro:</strong> <?= $cliente['fecha_registro'] ?>
                    </div>
                </div>

            </div>

            <!-- Botones -->
            <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
                <a href="index.php?action=listarClientes" class="btn btn-secondary">
                    <i class="bi bi-arrow-left"></i> Cancelar
                </a>
                <button type="submit" class="btn btn-warning">
                    <i class="bi bi-save"></i> Actualizar Cliente
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

</body>
</html>