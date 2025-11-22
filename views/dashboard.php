<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Dashboard - PinturasWeb</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px 0;
        }
        .dashboard-header {
            background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%); /* ← CAMBIO AQUÍ */
            color: white; /* ← CAMBIO AQUÍ */
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            margin-bottom: 30px;
        }
        .dashboard-header h1 {
            color: white; /* ← CAMBIO AQUÍ */
        }
        .dashboard-header .lead {
            color: rgba(255,255,255,0.9) !important; /* ← CAMBIO AQUÍ */
        }
        .dashboard-header small {
            color: rgba(255,255,255,0.8) !important; /* ← CAMBIO AQUÍ */
        }
        .card {
            border-radius: 15px;
            box-shadow: 0 8px 20px rgba(0,0,0,0.15);
            transition: all 0.3s ease;
            border: none;
            height: 100%;
            background: white;
        }
        .card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 35px rgba(0,0,0,0.25);
        }
        .card-title {
            font-weight: 700;
            font-size: 1.4rem;
            margin-bottom: 15px;
        }
        .card-icon {
            font-size: 3rem;
            margin-bottom: 15px;
            display: block;
        }
        .card-body {
            padding: 30px;
        }
        .btn-card {
            margin-top: 15px;
            width: 100%;
            padding: 12px;
            font-weight: 600;
            border-radius: 10px;
            transition: all 0.3s;
        }
        .btn-card:hover {
            transform: scale(1.05);
        }
        .section-title {
            color: white;
            font-weight: 700;
            font-size: 1.8rem;
            margin: 40px 0 25px 0;
            text-align: center;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
        }
        .logout-section {
            text-align: center;
            margin-top: 40px;
            margin-bottom: 20px;
        }
        .btn-logout {
            padding: 12px 40px;
            font-size: 1.1rem;
            font-weight: 600;
            border-radius: 50px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }
        
        /* Colores específicos por módulo */
        .card-clientes { border-left: 5px solid #007bff; }
        .card-empleados { border-left: 5px solid #28a745; }
        .card-productos { border-left: 5px solid #ffc107; }
        .card-facturas { border-left: 5px solid #17a2b8; }
        .card-reportes { border-left: 5px solid #343a40; }
        .card-proveedores { border-left: 5px solid #6f42c1; }
        .card-sucursales { border-left: 5px solid #fd7e14; }
        .card-pagos { border-left: 5px solid #e83e8c; }
    </style>
</head>
<body>
<div class="container">
    
    <!-- Encabezado del Dashboard -->
    <div class="dashboard-header text-center">
        <h1 class="display-4 mb-2">
            <i class="bi bi-speedometer2"></i> 
            Panel de Administración
        </h1>
        <p class="lead mb-0">Sistema de Gestión - PinturasWeb</p>
        <?php if(isset($usuarioActual)): ?>
            <small>
                Bienvenido, <strong><?= htmlspecialchars($usuarioActual['email']) ?></strong> 
                | Rol: <span class="badge bg-light text-dark"><?= htmlspecialchars($usuarioActual['nombre_rol']) ?></span>
            </small>
        <?php endif; ?>
    </div>

    <!-- Sección Principal -->
    <h2 class="section-title">
        <i class="bi bi-grid-3x3-gap"></i> Módulos Principales
    </h2>
    
    <div class="row g-4 mb-4">
        <!-- Clientes -->
        <div class="col-md-6 col-lg-3">
            <a href="index.php?action=listarClientes" class="text-decoration-none">
                <div class="card card-clientes text-center">
                    <div class="card-body">
                        <i class="bi bi-people-fill card-icon text-primary"></i>
                        <h3 class="card-title text-dark">Clientes</h3>
                        <p class="card-text text-muted">Gestión completa de clientes</p>
                        <span class="btn btn-primary btn-card">
                            <i class="bi bi-arrow-right-circle"></i> Acceder
                        </span>
                    </div>
                </div>
            </a>
        </div>

        <!-- Empleados -->
        <div class="col-md-6 col-lg-3">
            <a href="index.php?action=listarEmpleados" class="text-decoration-none">
                <div class="card card-empleados text-center">
                    <div class="card-body">
                        <i class="bi bi-person-badge-fill card-icon text-success"></i>
                        <h3 class="card-title text-dark">Empleados</h3>
                        <p class="card-text text-muted">Administrar personal</p>
                        <span class="btn btn-success btn-card">
                            <i class="bi bi-arrow-right-circle"></i> Acceder
                        </span>
                    </div>
                </div>
            </a>
        </div>

        <!-- Productos -->
        <div class="col-md-6 col-lg-3">
            <a href="index.php?action=listarProductos" class="text-decoration-none">
                <div class="card card-productos text-center">
                    <div class="card-body">
                        <i class="bi bi-box-seam-fill card-icon text-warning"></i>
                        <h3 class="card-title text-dark">Productos</h3>
                        <p class="card-text text-muted">Catálogo e inventario</p>
                        <span class="btn btn-warning btn-card text-white">
                            <i class="bi bi-arrow-right-circle"></i> Acceder
                        </span>
                    </div>
                </div>
            </a>
        </div>

        <!-- Facturas -->
        <div class="col-md-6 col-lg-3">
            <a href="index.php?action=listarFacturas" class="text-decoration-none">
                <div class="card card-facturas text-center">
                    <div class="card-body">
                        <i class="bi bi-receipt-cutoff card-icon text-info"></i>
                        <h3 class="card-title text-dark">Facturas</h3>
                        <p class="card-text text-muted">Ventas y facturación</p>
                        <span class="btn btn-info btn-card text-white">
                            <i class="bi bi-arrow-right-circle"></i> Acceder
                        </span>
                    </div>
                </div>
            </a>
        </div>
    </div>

    <!-- Sección Configuración -->
    <h2 class="section-title">
        <i class="bi bi-gear-fill"></i> Configuración del Sistema
    </h2>
    
    <div class="row g-4 mb-4">
        <!-- Proveedores -->
        <div class="col-md-6 col-lg-3">
            <a href="index.php?action=listarProveedores" class="text-decoration-none">
                <div class="card card-proveedores text-center">
                    <div class="card-body">
                        <i class="bi bi-truck card-icon" style="color: #6f42c1;"></i>
                        <h3 class="card-title text-dark">Proveedores</h3>
                        <p class="card-text text-muted">Gestión de proveedores</p>
                        <span class="btn btn-card text-white" style="background-color: #6f42c1;">
                            <i class="bi bi-arrow-right-circle"></i> Acceder
                        </span>
                    </div>
                </div>
            </a>
        </div>

        <!-- Sucursales -->
        <div class="col-md-6 col-lg-3">
            <a href="index.php?action=listarSucursales" class="text-decoration-none">
                <div class="card card-sucursales text-center">
                    <div class="card-body">
                        <i class="bi bi-shop card-icon" style="color: #fd7e14;"></i>
                        <h3 class="card-title text-dark">Sucursales</h3>
                        <p class="card-text text-muted">Administrar sucursales</p>
                        <span class="btn btn-card text-white" style="background-color: #fd7e14;">
                            <i class="bi bi-arrow-right-circle"></i> Acceder
                        </span>
                    </div>
                </div>
            </a>
        </div>

        <!-- Tipos de Pago -->
        <div class="col-md-6 col-lg-3">
            <a href="index.php?action=listarTiposPago" class="text-decoration-none">
                <div class="card card-pagos text-center">
                    <div class="card-body">
                        <i class="bi bi-credit-card-fill card-icon" style="color: #e83e8c;"></i>
                        <h3 class="card-title text-dark">Tipos de Pago</h3>
                        <p class="card-text text-muted">Métodos de pago</p>
                        <span class="btn btn-card text-white" style="background-color: #e83e8c;">
                            <i class="bi bi-arrow-right-circle"></i> Acceder
                        </span>
                    </div>
                </div>
            </a>
        </div>

        <!-- Reportes -->
        <div class="col-md-6 col-lg-3">
            <a href="index.php?action=reportes" class="text-decoration-none">
                <div class="card card-reportes text-center">
                    <div class="card-body">
                        <i class="bi bi-bar-chart-fill card-icon text-dark"></i>
                        <h3 class="card-title text-dark">Reportes</h3>
                        <p class="card-text text-muted">Estadísticas y análisis</p>
                        <span class="btn btn-dark btn-card">
                            <i class="bi bi-arrow-right-circle"></i> Acceder
                        </span>
                    </div>
                </div>
            </a>
        </div>
    </div>
    
    
    <!-- Botón de Cerrar Sesión -->
    <div class="logout-section">
        <a href="index.php?action=logout" class="btn btn-danger btn-logout">
            <i class="bi bi-box-arrow-right"></i> Cerrar Sesión
        </a>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>