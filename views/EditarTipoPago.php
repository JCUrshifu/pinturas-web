<?php
if (!isset($tipoPago)) {
    header("Location: index.php?action=listarTiposPago");
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Tipo de Pago</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .form-container {
            max-width: 700px;
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
                Editar Tipo de Pago
            </h2>
            <small class="text-muted">ID: <?= $tipoPago['tipo_pago_id'] ?></small>
        </div>

        <form method="POST" action="index.php?action=actualizarTipoPago">
            <input type="hidden" name="tipo_pago_id" value="<?= $tipoPago['tipo_pago_id'] ?>">

            <div class="row g-3">
                
                <!-- Nombre del Tipo de Pago -->
                <div class="col-12">
                    <label for="nombre_pago" class="form-label required-field">
                        <i class="bi bi-wallet2"></i> Nombre del Tipo de Pago
                    </label>
                    <input type="text" 
                           class="form-control" 
                           id="nombre_pago" 
                           name="nombre_pago"
                           value="<?= htmlspecialchars($tipoPago['nombre_pago']) ?>" 
                           required>
                </div>

                <!-- Descripción -->
                <div class="col-12">
                    <label for="descripcion" class="form-label">
                        <i class="bi bi-card-text"></i> Descripción
                    </label>
                    <textarea class="form-control" 
                              id="descripcion" 
                              name="descripcion" 
                              rows="3"><?= htmlspecialchars($tipoPago['descripcion'] ?? '') ?></textarea>
                </div>

                <!-- Estado Activo -->
                <div class="col-12">
                    <div class="form-check form-switch">
                        <input class="form-check-input" 
                               type="checkbox" 
                               id="activo" 
                               name="activo"
                               <?= $tipoPago['activo'] ? 'checked' : '' ?>>
                        <label class="form-check-label" for="activo">
                            <i class="bi bi-toggle-on"></i> 
                            Método de Pago Activo
                        </label>
                    </div>
                </div>

            </div>

            <!-- Botones -->
            <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
                <a href="index.php?action=listarTiposPago" class="btn btn-secondary">
                    <i class="bi bi-arrow-left"></i> Cancelar
                </a>
                <button type="submit" class="btn btn-warning">
                    <i class="bi bi-save"></i> Actualizar Tipo de Pago
                </button>
            </div>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>