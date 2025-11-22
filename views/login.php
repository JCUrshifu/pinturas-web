<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión - Paints</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background: #ffffff;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .login-container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.1);
            overflow: hidden;
            max-width: 900px;
            width: 100%;
            display: flex;
        }
        .login-left {
            background: #ffffff;
            padding: 60px 40px;
            color: #333;
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            border-right: 1px solid #e0e0e0;
        }
        .login-left h1 {
            font-size: 2.5rem;
            font-weight: bold;
            margin-bottom: 20px;
            color: #667eea;
        }
        .login-right {
            padding: 60px 40px;
            flex: 1;
        }
        .login-title {
            color: #667eea;
            font-size: 1.8rem;
            font-weight: bold;
            margin-bottom: 30px;
            text-align: center;
        }
        .form-control {
            border-radius: 10px;
            padding: 12px 15px;
            border: 2px solid #e0e0e0;
            transition: all 0.3s;
        }
        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }
        .input-group-text {
            background: #f8f9fa;
            border: 2px solid #e0e0e0;
            border-radius: 10px 0 0 10px;
        }
        .btn-login {
            background: #667eea;
            border: none;
            border-radius: 10px;
            padding: 12px;
            font-weight: bold;
            color: white;
            width: 100%;
            transition: transform 0.3s;
        }
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }
        .alert {
            border-radius: 10px;
            border: none;
        }
        @media (max-width: 768px) {
            .login-container {
                flex-direction: column;
            }
            .login-left {
                border-right: none;
                border-bottom: 1px solid #e0e0e0;
            }
        }
    </style>
</head>
<body>

<div class="login-container">
    <!-- Lado Izquierdo -->
    <div class="login-left">
        <div>
            <i class="bi bi-paint-bucket" style="font-size: 4rem; margin-bottom: 20px; color:#667eea;"></i>
            <h1>Paints</h1>
            <p>Sistema de Gestión de Pinturas</p>
            <p class="mt-4">
                <i class="bi bi-check-circle text-primary"></i> Gestión de inventario<br>
                <i class="bi bi-check-circle text-primary"></i> Control de ventas<br>
                <i class="bi bi-check-circle text-primary"></i> Reportes detallados<br>
                <i class="bi bi-check-circle text-primary"></i> Múltiples sucursales
            </p>
        </div>
    </div>

    <!-- Lado Derecho - Formulario -->
    <div class="login-right">
        <h2 class="login-title">
            <i class="bi bi-box-arrow-in-right"></i>
            Iniciar Sesión
        </h2>

        <?php if (!empty($mensaje)): ?>
            <div class="alert alert-danger" role="alert">
                <i class="bi bi-exclamation-triangle-fill"></i>
                <?= htmlspecialchars($mensaje) ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="index.php?action=login">
            <div class="mb-3">
                <label for="usuario" class="form-label">
                    <i class="bi bi-person-circle"></i> Email o Usuario
                </label>
                <div class="input-group">
                    <span class="input-group-text">
                        <i class="bi bi-at"></i>
                    </span>
                    <input type="text" 
                           class="form-control" 
                           id="usuario" 
                           name="usuario" 
                           placeholder="admin@paints.com o admin"
                           required 
                           autofocus>
                </div>
                <small class="text-muted">Puedes usar tu email o nombre de usuario</small>
            </div>

            <div class="mb-4">
                <label for="contrasena" class="form-label">
                    <i class="bi bi-lock-fill"></i> Contraseña
                </label>
                <div class="input-group">
                    <span class="input-group-text">
                        <i class="bi bi-key"></i>
                    </span>
                    <input type="password" 
                           class="form-control" 
                           id="contrasena" 
                           name="contrasena" 
                           placeholder="••••••••"
                           required>
                </div>
            </div>

            <button type="submit" class="btn btn-login">
                <i class="bi bi-box-arrow-in-right"></i>
                Iniciar Sesión
            </button>
        </form>

        <div class="text-center mt-4">
            <a href="index.php?action=home" class="text-decoration-none text-primary">
                <i class="bi bi-house"></i> Volver al inicio
            </a>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
