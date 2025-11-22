<?php
if (!isset($sucursal)) {
    header("Location: index.php?action=listarSucursales");
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Sucursal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .form-container {
            max-width: 900px;
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
        #map {
            height: 300px;
            border-radius: 10px;
            margin-top: 10px;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="form-container">
        <div class="form-header">
            <h2 class="mb-0">
                <i class="bi bi-pencil-square text-warning"></i>
                Editar Sucursal
            </h2>
            <small class="text-muted">ID: <?= $sucursal['sucursal_id'] ?></small>
        </div>

        <form method="POST" action="index.php?action=actualizarSucursal">
            <input type="hidden" name="sucursal_id" value="<?= $sucursal['sucursal_id'] ?>">

            <div class="row g-3">
                
                <!-- Nombre de la Sucursal -->
                <div class="col-md-6">
                    <label for="nombre_sucursal" class="form-label required-field">
                        <i class="bi bi-shop-window"></i> Nombre de la Sucursal
                    </label>
                    <input type="text" 
                           class="form-control" 
                           id="nombre_sucursal" 
                           name="nombre_sucursal"
                           value="<?= htmlspecialchars($sucursal['nombre_sucursal']) ?>" 
                           required>
                </div>

                <!-- Teléfono -->
                <div class="col-md-6">
                    <label for="telefono_sucursal" class="form-label">
                        <i class="bi bi-telephone"></i> Teléfono
                    </label>
                    <input type="tel" 
                           class="form-control" 
                           id="telefono_sucursal" 
                           name="telefono_sucursal"
                           value="<?= htmlspecialchars($sucursal['telefono_sucursal'] ?? '') ?>">
                </div>

                <!-- Dirección -->
                <div class="col-12">
                    <label for="direccion_sucursal" class="form-label required-field">
                        <i class="bi bi-geo-alt"></i> Dirección
                    </label>
                    <textarea class="form-control" 
                              id="direccion_sucursal" 
                              name="direccion_sucursal" 
                              rows="2"
                              required><?= htmlspecialchars($sucursal['direccion_sucursal']) ?></textarea>
                </div>

                <!-- Coordenadas -->
                <div class="col-12">
                    <h6 class="text-muted">
                        <i class="bi bi-pin-map"></i> Coordenadas GPS (Opcional)
                    </h6>
                </div>

                <div class="col-md-6">
                    <label for="latitud" class="form-label">
                        <i class="bi bi-geo"></i> Latitud
                    </label>
                    <input type="number" 
                           class="form-control" 
                           id="latitud" 
                           name="latitud"
                           step="0.000001"
                           value="<?= htmlspecialchars($sucursal['latitud'] ?? '') ?>">
                </div>

                <div class="col-md-6">
                    <label for="longitud" class="form-label">
                        <i class="bi bi-geo-alt-fill"></i> Longitud
                    </label>
                    <input type="number" 
                           class="form-control" 
                           id="longitud" 
                           name="longitud"
                           step="0.000001"
                           value="<?= htmlspecialchars($sucursal['longitud'] ?? '') ?>">
                </div>

                <!-- Mapa -->
                <div class="col-12">
                    <div id="map"></div>
                </div>

                <!-- Estado Activa -->
                <div class="col-12">
                    <div class="form-check form-switch">
                        <input class="form-check-input" 
                               type="checkbox" 
                               id="activa" 
                               name="activa"
                               <?= $sucursal['activa'] ? 'checked' : '' ?>>
                        <label class="form-check-label" for="activa">
                            <i class="bi bi-toggle-on"></i> 
                            Sucursal Activa
                        </label>
                    </div>
                </div>

            </div>

            <!-- Botones -->
            <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
                <a href="index.php?action=listarSucursales" class="btn btn-secondary">
                    <i class="bi bi-arrow-left"></i> Cancelar
                </a>
                <button type="submit" class="btn btn-warning">
                    <i class="bi bi-save"></i> Actualizar Sucursal
                </button>
            </div>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<!-- Leaflet para el mapa -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script>
// Coordenadas iniciales
const lat = <?= $sucursal['latitud'] ? $sucursal['latitud'] : '14.634915' ?>;
const lng = <?= $sucursal['longitud'] ? $sucursal['longitud'] : '-90.506882' ?>;

// Inicializar mapa
const map = L.map('map').setView([lat, lng], 15);

L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '© OpenStreetMap contributors'
}).addTo(map);

let marker = L.marker([lat, lng]).addTo(map);

// Click en el mapa
map.on('click', function(e) {
    const newLat = e.latlng.lat;
    const newLng = e.latlng.lng;
    
    document.getElementById('latitud').value = newLat.toFixed(6);
    document.getElementById('longitud').value = newLng.toFixed(6);
    
    if (marker) {
        map.removeLayer(marker);
    }
    
    marker = L.marker([newLat, newLng]).addTo(map);
});

// Actualizar marcador al cambiar coordenadas
const latInput = document.getElementById('latitud');
const lngInput = document.getElementById('longitud');

latInput.addEventListener('change', actualizarMarcador);
lngInput.addEventListener('change', actualizarMarcador);

function actualizarMarcador() {
    const newLat = parseFloat(latInput.value);
    const newLng = parseFloat(lngInput.value);
    
    if (newLat && newLng) {
        if (marker) {
            map.removeLayer(marker);
        }
        marker = L.marker([newLat, newLng]).addTo(map);
        map.setView([newLat, newLng], 15);
    }
}
</script>

</body>
</html>