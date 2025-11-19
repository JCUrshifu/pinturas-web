<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agregar Tipo de Pago</title>
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
                <i class="bi bi-credit-card text-primary"></i>
                Agregar Tipo de Pago
            </h2>
        </div>

        <form method="POST" action="index.php?action=guardarTipoPago" id="formTipoPago">
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
                           placeholder="Ej: Tarjeta de Crédito, Efectivo, Transferencia" 
                           required>
                    <small class="text-muted">Nombre corto y descriptivo del método de pago</small>
                </div>

                <!-- Descripción -->
                <div class="col-12">
                    <label for="descripcion" class="form-label">
                        <i class="bi bi-card-text"></i> Descripción
                    </label>
                    <textarea class="form-control" 
                              id="descripcion" 
                              name="descripcion" 
                              rows="3"
                              placeholder="Descripción detallada del método de pago (opcional)"></textarea>
                </div>

                <!-- Estado Activo -->
                <div class="col-12">
                    <div class="form-check form-switch">
                        <input class="form-check-input" 
                               type="checkbox" 
                               id="activo" 
                               name="activo"
                               checked>
                        <label class="form-check-label" for="activo">
                            <i class="bi bi-toggle-on"></i> 
                            Método de Pago Activo
                        </label>
                    </div>
                    <small class="text-muted">
                        Si está activo, aparecerá como opción en las transacciones
                    </small>
                </div>

                <!-- Información Adicional -->
                <div class="col-12">
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle"></i>
                        <strong>Nota:</strong> Los tipos de pago activos aparecerán en el módulo de facturación
                        para que los empleados puedan seleccionarlos al registrar ventas.
                    </div>
                </div>

            </div>

            <!-- Botones -->
            <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
                <a href="index.php?action=listarTiposPago" class="btn btn-secondary">
                    <i class="bi bi-arrow-left"></i> Cancelar
                </a>
                <button type="reset" class="btn btn-outline-warning">
                    <i class="bi bi-x-circle"></i> Limpiar
                </button>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save"></i> Guardar Tipo de Pago
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