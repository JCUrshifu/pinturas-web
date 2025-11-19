<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>¡Pedido Exitoso! - Paints</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .success-card {
            background: white;
            border-radius: 20px;
            padding: 50px;
            max-width: 600px;
            text-align: center;
            box-shadow: 0 10px 40px rgba(0,0,0,0.3);
        }
        .success-icon {
            font-size: 5rem;
            color: #28a745;
            animation: scaleIn 0.5s ease-out;
        }
        @keyframes scaleIn {
            0% { transform: scale(0); }
            50% { transform: scale(1.1); }
            100% { transform: scale(1); }
        }
    </style>
</head>
<body>

<div class="success-card">
    <i class="bi bi-check-circle-fill success-icon"></i>
    
    <h1 class="mt-4 mb-3">¡Pedido Confirmado!</h1>
    
    <p class="lead">Tu pedido ha sido procesado exitosamente</p>
    
    <div class="alert alert-success mt-4">
        <strong>Número de Pedido:</strong> <?= htmlspecialchars($pedido['numero_factura']) ?><br>
        <strong>Total:</strong> Q<?= number_format($pedido['total'], 2) ?>
    </div>
    
    <p class="text-muted">
        Hemos enviado una confirmación a:<br>
        <strong><?= htmlspecialchars($pedido['email']) ?></strong>
    </p>
    
    <hr class="my-4">
    
    <h5>¿Qué sigue?</h5>
    <ul class="list-unstyled text-start mt-3">
        <li class="mb-2">
            <i class="bi bi-check-circle text-success"></i>
            Recibirás un email con los detalles de tu pedido
        </li>
        <li class="mb-2">
            <i class="bi bi-truck text-primary"></i>
            Tu pedido será entregado en 24-48 horas
        </li>
        <li class="mb-2">
            <i class="bi bi-telephone text-info"></i>
            Te contactaremos para coordinar la entrega
        </li>
    </ul>
    
    <div class="mt-4">
        <a href="index.php?action=catalogoProductos" class="btn btn-primary btn-lg me-2">
            <i class="bi bi-shop"></i> Seguir Comprando
        </a>
        <a href="index.php?action=home" class="btn btn-outline-secondary btn-lg">
            <i class="bi bi-house"></i> Ir al Inicio
        </a>
    </div>
    
    <p class="text-muted small mt-4 mb-0">
        Gracias por tu compra en <strong>Paints</strong>
    </p>
</div>

</body>
</html>