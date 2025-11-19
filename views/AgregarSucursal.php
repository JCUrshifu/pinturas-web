<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agregar Sucursal</title>
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
            border-bottom: 3px solid #0d6efd;
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
                <i class="bi bi-shop text-primary"></i>
                Agregar Nueva Sucursal
            </h2>
        </div>

        <form method="POST" action="index.php?action=guardarSucursal" id="formSucursal">
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
                           placeholder="Ej: Sucursal Centro" 
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
                           placeholder="5555-5555">
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
                              placeholder="Dirección completa de la sucursal"
                              required></textarea>
                </div>

                <!-- Coordenadas -->
                <div class="col-12">
                    <h6 class="text-muted">
                        <i class="bi bi-pin-map"></i> Coordenadas GPS (Opcional)
                    </h6>
                    <small class="text-muted">Haga clic en el mapa para obtener las coordenadas</small>
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
                           placeholder="14.634915">
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
                           placeholder="-90.506882">
                </div>

                <!-- Mapa -->
                <div class="col-12">
                    <div id="map"></div>
                    <small class="text-muted">
                        <i class="bi bi-info-circle"></i> 
                        Haga clic en el mapa para seleccionar la ubicación
                    </small>
                </div>

                <!-- Estado Activa -->
                <div class="col-12">
                    <div class="form-check form-switch">
                        <input class="form-check-input" 
                               type="checkbox" 
                               id="activa" 
                               name="activa"
                               checked>
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
                <button type="reset" class="btn btn-outline-warning">
                    <i class="bi bi-x-circle"></i> Limpiar
                </button>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save"></i> Guardar Sucursal
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

<!-- Leaflet para el mapa -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script>
// Inicializar mapa centrado en Guatemala
const map = L.map('map').setView([14.634915, -90.506882], 13);

L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '© OpenStreetMap contributors'
}).addTo(map);

let marker;

// Click en el mapa
map.on('click', function(e) {
    const lat = e.latlng.lat;
    const lng = e.latlng.lng;
    
    // Actualizar campos
    document.getElementById('latitud').value = lat.toFixed(6);
    document.getElementById('longitud').value = lng.toFixed(6);
    
    // Remover marcador anterior
    if (marker) {
        map.removeLayer(marker);
    }
    
    // Agregar nuevo marcador
    marker = L.marker([lat, lng]).addTo(map)
        .bindPopup('Ubicación seleccionada')
        .openPopup();
});

// Si ya hay coordenadas, mostrar marcador
const latInput = document.getElementById('latitud');
const lngInput = document.getElementById('longitud');

latInput.addEventListener('change', actualizarMarcador);
lngInput.addEventListener('change', actualizarMarcador);

function actualizarMarcador() {
    const lat = parseFloat(latInput.value);
    const lng = parseFloat(lngInput.value);
    
    if (lat && lng) {
        if (marker) {
            map.removeLayer(marker);
        }
        marker = L.marker([lat, lng]).addTo(map);
        map.setView([lat, lng], 15);
    }
}
</script>

</body>
</html>