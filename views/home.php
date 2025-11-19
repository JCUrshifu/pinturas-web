<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Paints - Inicio</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .carousel-item {
            height: 70vh;
            min-height: 400px;
            background-size: cover;
            background-position: center;
            position: relative;
        }
        .carousel-overlay {
            position: absolute;
            top: 0; left: 0;
            width: 100%; height: 100%;
            background-color: rgba(0,0,0,0.4);
            z-index: 1;
        }
        .carousel-caption {
            z-index: 2;
        }
        .navbar {
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .feature-card {
            transition: all 0.3s ease;
            border: none;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
        }
        .feature-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.15);
        }
        .btn-cart {
            position: relative;
        }
        .cart-badge {
            position: absolute;
            top: -5px;
            right: -5px;
            background: #dc3545;
            color: white;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.75rem;
            font-weight: bold;
        }
        /* NUEVO: Estilos para botón de cotización */
        .btn-cotizacion {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            color: white;
            font-weight: 600;
            padding: 12px 30px;
            border-radius: 50px;
            transition: all 0.3s;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
        }
        .btn-cotizacion:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4);
            color: white;
        }
        .cotizacion-banner {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 60px 0;
            margin: 40px 0;
        }
        .cotizacion-icon {
            font-size: 4rem;
            margin-bottom: 20px;
            animation: pulse 2s infinite;
        }
        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.05); }
        }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <div class="container">
        <a class="navbar-brand" href="index.php?action=home">
            <h1 class="display-5 fw-bold mb-0" style="color: #667eea;">
                <i class="bi bi-paint-bucket"></i> Paints
            </h1>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link active" href="index.php?action=home">
                        <i class="bi bi-house-door"></i> Inicio
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="index.php?action=catalogoProductos">
                        <i class="bi bi-palette"></i> Productos
                    </a>
                </li>
                <!-- NUEVO: Enlace de Cotizaciones -->
                <li class="nav-item">
                    <a class="nav-link" href="index.php?action=solicitarCotizacion">
                        <i class="bi bi-file-earmark-text"></i> Solicitar Cotización
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#ventajas">
                        <i class="bi bi-star"></i> Ventajas
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#contacto">
                        <i class="bi bi-envelope"></i> Contacto
                    </a>
                </li>
            </ul>
            <div class="d-flex gap-2">
                <!-- Carrito con contador -->
                <a href="index.php?action=verCarrito" class="btn btn-outline-primary btn-cart position-relative">
                    <i class="bi bi-cart3"></i> Carrito
                    <span class="cart-badge" id="cart-count">
                        <?php 
                        echo isset($_SESSION['carrito']) ? count($_SESSION['carrito']) : 0;
                        ?>
                    </span>
                </a>
                
                <!-- Botón de login/dashboard -->
                <?php if(isset($_SESSION['logueado']) && $_SESSION['logueado']): ?>
                    <div class="dropdown">
                        <button class="btn btn-success dropdown-toggle" type="button" data-bs-toggle="dropdown">
                            <i class="bi bi-person-circle"></i> 
                            <?= htmlspecialchars($_SESSION['username'] ?? $_SESSION['email']) ?>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li>
                                <a class="dropdown-item" href="index.php?action=dashboard">
                                    <i class="bi bi-speedometer2"></i> Dashboard
                                </a>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <a class="dropdown-item text-danger" href="index.php?action=logout">
                                    <i class="bi bi-box-arrow-right"></i> Cerrar Sesión
                                </a>
                            </li>
                        </ul>
                    </div>
                <?php else: ?>
                    <a href="index.php?action=loginForm" class="btn btn-primary">
                        <i class="bi bi-box-arrow-in-right"></i> Iniciar Sesión
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</nav>

<!-- Carousel -->
<div id="paintsCarousel" class="carousel slide" data-bs-ride="carousel">
    <div class="carousel-indicators">
        <button type="button" data-bs-target="#paintsCarousel" data-bs-slide-to="0" class="active"></button>
        <button type="button" data-bs-target="#paintsCarousel" data-bs-slide-to="1"></button>
        <button type="button" data-bs-target="#paintsCarousel" data-bs-slide-to="2"></button>
    </div>

    <div class="carousel-inner">
        <div class="carousel-item active" style="background-image: url('https://tiendasmonto.es/cdn/shop/files/tienda-3-banner-web.jpg?v=1689498264&width=3840');">
            <div class="carousel-overlay"></div>
            <div class="carousel-caption">
                <h1 class="display-3 fw-bold mb-4">Colores Vivos para tus Proyectos</h1>
                <p class="lead mb-4">Descubre nuestra amplia variedad de pinturas profesionales</p>
                <a href="index.php?action=catalogoProductos" class="btn btn-primary btn-lg me-2">
                    Ver Catálogo <i class="bi bi-arrow-right"></i>
                </a>
                <!-- NUEVO: Botón de Cotización en Carousel -->
                <a href="index.php?action=solicitarCotizacion" class="btn btn-light btn-lg">
                    <i class="bi bi-file-earmark-text"></i> Solicitar Cotización
                </a>
            </div>
        </div>
        
        <div class="carousel-item" style="background-image: url('https://h6a8m2f3.delivery.rocketcdn.me/wp-content/uploads/2024/11/dunn-edwards-paint-store-in-southstreet-1.jpg.webp');">
            <div class="carousel-overlay"></div>
            <div class="carousel-caption">
                <h1 class="display-3 fw-bold mb-4">Visita Nuestras Sucursales</h1>
                <p class="lead mb-4">Atención personalizada y asesoría profesional</p>
                <a href="index.php?action=catalogoProductos" class="btn btn-light btn-lg">
                    Explorar Productos <i class="bi bi-arrow-right"></i>
                </a>
            </div>
        </div>
        
        <div class="carousel-item" style="background-image: url('https://www.datacolor.com/wp-content/uploads/2020/10/Retail-Paint-Dispensing-900x500.png');">
            <div class="carousel-overlay"></div>
            <div class="carousel-caption">
                <h1 class="display-3 fw-bold mb-4">Color a tu Medida</h1>
                <p class="lead mb-4">Personaliza tus pinturas según tus necesidades</p>
                <a href="index.php?action=catalogoProductos" class="btn btn-primary btn-lg">
                    Comprar Ahora <i class="bi bi-cart3"></i>
                </a>
            </div>
        </div>
    </div>

    <button class="carousel-control-prev" type="button" data-bs-target="#paintsCarousel" data-bs-slide="prev">
        <span class="carousel-control-prev-icon"></span>
    </button>
    <button class="carousel-control-next" type="button" data-bs-target="#paintsCarousel" data-bs-slide="next">
        <span class="carousel-control-next-icon"></span>
    </button>
</div>

<!-- Sección de Ventajas -->
<section class="py-5" id="ventajas">
    <div class="container">
        <h2 class="text-center mb-5">¿Por qué elegirnos?</h2>
        <div class="row g-4">
            <div class="col-md-4">
                <div class="card feature-card text-center p-4 h-100">
                    <i class="bi bi-palette-fill text-primary fs-1 mb-3"></i>
                    <h5>Variedad de Colores</h5>
                    <p>Más de 500 tonos disponibles para todo tipo de proyectos.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card feature-card text-center p-4 h-100">
                    <i class="bi bi-brush-fill text-primary fs-1 mb-3"></i>
                    <h5>Calidad Profesional</h5>
                    <p>Productos duraderos y de excelente cobertura garantizada.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card feature-card text-center p-4 h-100">
                    <i class="bi bi-truck text-primary fs-1 mb-3"></i>
                    <h5>Envíos Rápidos</h5>
                    <p>Entrega a todo el país con seguimiento en línea.</p>
                </div>
            </div>
            
        </div>
    </div>
</section>


<!-- Footer -->
<footer class="bg-dark text-white pt-5 pb-4 mt-5" id="contacto">
    <div class="container">
        <div class="row">
            <div class="col-md-3">
                <h5 class="mb-3">
                    <i class="bi bi-paint-bucket"></i> Paints
                </h5>
                <p>Tu fuente confiable para pinturas de calidad profesional.</p>
            </div>
            <div class="col-md-3">
                <h5>Navegación</h5>
                <ul class="list-unstyled">
                    <li><a href="index.php?action=home" class="text-white-50 text-decoration-none">Inicio</a></li>
                    <li><a href="index.php?action=catalogoProductos" class="text-white-50 text-decoration-none">Catálogo</a></li>
                    <li><a href="index.php?action=solicitarCotizacion" class="text-white-50 text-decoration-none">Cotizaciones</a></li>
                    <li><a href="index.php?action=verCarrito" class="text-white-50 text-decoration-none">Carrito</a></li>
                </ul>
            </div>
            <div class="col-md-3">
                <h5>Soporte</h5>
                <ul class="list-unstyled">
                    <li><a href="#" class="text-white-50 text-decoration-none">Centro de Ayuda</a></li>
                    <li><a href="#" class="text-white-50 text-decoration-none">Envío y Devoluciones</a></li>
                    <li><a href="#" class="text-white-50 text-decoration-none">FAQ</a></li>
                </ul>
            </div>
            <div class="col-md-3">
                <h5>Síguenos</h5>
                <div class="d-flex gap-3">
                    <a href="#" class="text-white fs-4"><i class="bi bi-facebook"></i></a>
                    <a href="#" class="text-white fs-4"><i class="bi bi-twitter"></i></a>
                    <a href="#" class="text-white fs-4"><i class="bi bi-instagram"></i></a>
                </div>
            </div>
        </div>
        <hr class="my-4">
        <div class="row">
            <div class="col text-center text-white-50">
                &copy; <?= date('Y') ?> Paints. Todos los Derechos Reservados.
            </div>
        </div>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>