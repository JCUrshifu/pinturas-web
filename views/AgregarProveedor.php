<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agregar Proveedor</title>
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
                <i class="bi bi-truck text-primary"></i>
                Agregar Nuevo Proveedor
            </h2>
        </div>

        <form method="POST" action="index.php?action=guardarProveedor" id="formProveedor">
            <div class="row g-3">
                
                <!-- Nombre del Proveedor -->
                <div class="col-md-6">
                    <label for="nombre_proveedor" class="form-label required-field">
                        <i class="bi bi-building"></i> Nombre del Proveedor
                    </label>
                    <input type="text" 
                           class="form-control" 
                           id="nombre_proveedor" 
                           name="nombre_proveedor"
                           placeholder="Ej: Distribuidora Pinturas S.A." 
                           required>
                </div>

                <!-- Contacto -->
                <div class="col-md-6">
                    <label for="contacto_proveedor" class="form-label">
                        <i class="bi bi-person"></i> Persona de Contacto
                    </label>
                    <input type="text" 
                           class="form-control" 
                           id="contacto_proveedor" 
                           name="contacto_proveedor"
                           placeholder="Ej: Juan Pérez">
                </div>

                <!-- Teléfono -->
                <div class="col-md-6">
                    <label for="telefono_proveedor" class="form-label">
                        <i class="bi bi-telephone"></i> Teléfono
                    </label>
                    <input type="tel" 
                           class="form-control" 
                           id="telefono_proveedor" 
                           name="telefono_proveedor"
                           placeholder="5555-5555">
                </div>

                <!-- Email -->
                <div class="col-md-6">
                    <label for="email_proveedor" class="form-label">
                        <i class="bi bi-envelope"></i> Email
                    </label>
                    <input type="email" 
                           class="form-control" 
                           id="email_proveedor" 
                           name="email_proveedor"
                           placeholder="proveedor@ejemplo.com">
                </div>

                <!-- Dirección -->
                <div class="col-12">
                    <label for="direccion_proveedor" class="form-label">
                        <i class="bi bi-geo-alt"></i> Dirección
                    </label>
                    <textarea class="form-control" 
                              id="direccion_proveedor" 
                              name="direccion_proveedor" 
                              rows="3"
                              placeholder="Dirección completa del proveedor"></textarea>
                </div>

            </div>

            <!-- Botones -->
            <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
                <a href="index.php?action=listarProveedores" class="btn btn-secondary">
                    <i class="bi bi-arrow-left"></i> Cancelar
                </a>
                <button type="reset" class="btn btn-outline-warning">
                    <i class="bi bi-x-circle"></i> Limpiar
                </button>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save"></i> Guardar Proveedor
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