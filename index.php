<?php
// Iniciar sesión al principio
session_start();

require_once "config/Database.php";
require_once "controllers/ClienteController.php";
require_once "controllers/EmpleadoController.php";
require_once "controllers/ProductoController.php";
require_once "controllers/FacturaController.php";
require_once "controllers/LoginController.php";
require_once __DIR__ . '/controllers/ProveedorController.php';
require_once __DIR__ . '/controllers/SucursalController.php';
require_once __DIR__ . '/controllers/TipoPagoController.php';

require_once __DIR__ . '/controllers/ProveedorController.php';
require_once __DIR__ . '/controllers/SucursalController.php';
require_once __DIR__ . '/controllers/TipoPagoController.php';
require_once __DIR__ . '/controllers/CotizacionController.php';

// Crear conexión
$database = new Database();
$db = $database->getConnection();

// Crear LoginController para verificaciones
$loginController = new LoginController($db);

// Acción por defecto
$action = isset($_GET['action']) ? $_GET['action'] : 'home';

// =============================================
// RUTAS PÚBLICAS (No requieren login)
// =============================================
// RUTAS PÚBLICAS (No requieren login)
$rutasPublicas = [
    'home',                
    'loginForm',           
    'login',               
    'registro',            
    'procesarRegistro',    
    'catalogoProductos',   
    'detalleProducto',     
    'buscarProductos',     
    
    // CARRITO PÚBLICO
    'verCarrito',          
    'agregarCarrito',      
    'actualizarCarrito',   
    'eliminarDelCarrito',  
    'vaciarCarrito',       
    
    // CHECKOUT PÚBLICO (IMPORTANTE)
    'checkout',            // ✅ Permite ver el formulario de pago
    'procesarPedido',      // ✅ Permite procesar la compra
    'pedidoExitoso',       // ✅ Permite ver confirmación
    
    // COTIZACIONES PÚBLICAS
    'solicitarCotizacion',      
    'crearCotizacionPublica',   
    'imprimirCotizacion'                  
];

// Si no es ruta pública y no está logueado, redirigir a login
if (!in_array($action, $rutasPublicas) && !$loginController->estaLogueado()) {
    header("Location: index.php?action=loginForm&mensaje=" . urlencode("Debe iniciar sesión"));
    exit();
}

// Verificar inactividad en cada petición (solo si está logueado)
if ($loginController->estaLogueado()) {
    $loginController->verificarInactividad();
}

// =============================================
// ENRUTADOR PRINCIPAL
// =============================================
switch ($action) {

    // ===================== HOME/LOGIN =====================
    case 'home':
        require "views/home.php";
        break;

    case 'loginForm':
        $mensaje = $_GET['mensaje'] ?? '';
        require "views/login.php";
        break;

    case 'login':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = $_POST['usuario'] ?? '';
            $password = $_POST['contrasena'] ?? '';

            if ($loginController->login($username, $password)) {
                header("Location: index.php?action=dashboard");
                exit();
            } else {
                $mensaje = urlencode("Usuario o contraseña incorrectos");
                header("Location: index.php?action=loginForm&mensaje=$mensaje");
                exit();
            }
        }
        break;

    case 'logout':
        $loginController->logout();
        header("Location: index.php?action=home");
        exit();
        break;

    case 'registro':
        require "views/registro.php";
        break;

    case 'procesarRegistro':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $resultado = $loginController->registrar(
                $_POST['username'],
                $_POST['email'],
                $_POST['password']
            );
            
            if ($resultado['success']) {
                header("Location: index.php?action=loginForm&mensaje=" . urlencode($resultado['mensaje']));
            } else {
                header("Location: index.php?action=registro&error=" . urlencode($resultado['mensaje']));
            }
            exit();
        }
        break;

    // ===================== CATÁLOGO PÚBLICO =====================
    case 'catalogoProductos':
        $productoController = new ProductoController($db);
        require "views/catalogo_productos.php";  // ← Debe estar aquí
    break;
    // ===================== CARRITO PÚBLICO =====================
    case 'verCarrito':
        // Inicializar carrito en sesión si no existe
        if (!isset($_SESSION['carrito'])) {
            $_SESSION['carrito'] = [];
        }
        require "views/ver_carrito.php";
        break;

    case 'agregarCarrito':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!isset($_SESSION['carrito'])) {
                $_SESSION['carrito'] = [];
            }
            
            $producto_id = filter_input(INPUT_POST, 'producto_id', FILTER_VALIDATE_INT);
            $cantidad = filter_input(INPUT_POST, 'cantidad', FILTER_VALIDATE_INT) ?? 1;
            
            if ($producto_id) {
                // Verificar stock disponible
                $stmt = $db->prepare("
                    SELECT COALESCE(SUM(cantidad), 0) as stock
                    FROM inventario
                    WHERE producto_id = ?
                ");
                $stmt->execute([$producto_id]);
                $stockData = $stmt->fetch(PDO::FETCH_ASSOC);
                $stockDisponible = $stockData['stock'];
                
                // Buscar si ya existe en el carrito
                $encontrado = false;
                foreach ($_SESSION['carrito'] as &$item) {
                    if ($item['id'] == $producto_id) {
                        // Verificar que no exceda el stock
                        if (($item['cantidad'] + $cantidad) <= $stockDisponible) {
                            $item['cantidad'] += $cantidad;
                            $_SESSION['mensaje'] = "Cantidad actualizada en el carrito";
                        } else {
                            $_SESSION['error'] = "No hay suficiente stock disponible";
                        }
                        $encontrado = true;
                        break;
                    }
                }
                
                if (!$encontrado) {
                    if ($cantidad <= $stockDisponible) {
                        // Obtener datos del producto
                        $productoController = new ProductoController($db);
                        $producto = $productoController->obtenerProducto($producto_id);
                        
                        if ($producto) {
                            // Calcular precio con descuento
                            $precio_final = $producto['precio_base'] * (1 - ($producto['porcentaje_descuento'] / 100));
                            
                            $_SESSION['carrito'][] = [
                                'id' => $producto['producto_id'],
                                'nombre' => $producto['nombre'],
                                'sku' => $producto['codigo_sku'],
                                'precio' => $precio_final,
                                'precio_base' => $producto['precio_base'],
                                'descuento' => $producto['porcentaje_descuento'],
                                'cantidad' => $cantidad,
                                'imagen' => $producto['imagen'] ?? 'default.png'
                            ];
                            $_SESSION['mensaje'] = "Producto agregado al carrito";
                        }
                    } else {
                        $_SESSION['error'] = "Stock insuficiente. Solo hay {$stockDisponible} unidades disponibles";
                    }
                }
            }
        }
        header("Location: index.php?action=verCarrito");
        exit();
        break;

    case 'actualizarCarrito':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $index = filter_input(INPUT_POST, 'index', FILTER_VALIDATE_INT);
            $cantidad = filter_input(INPUT_POST, 'cantidad', FILTER_VALIDATE_INT);
            
            if (isset($_SESSION['carrito'][$index]) && $cantidad > 0) {
                // Verificar stock
                $producto_id = $_SESSION['carrito'][$index]['id'];
                $stmt = $db->prepare("
                    SELECT COALESCE(SUM(cantidad), 0) as stock
                    FROM inventario
                    WHERE producto_id = ?
                ");
                $stmt->execute([$producto_id]);
                $stockData = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if ($cantidad <= $stockData['stock']) {
                    $_SESSION['carrito'][$index]['cantidad'] = $cantidad;
                    $_SESSION['mensaje'] = "Cantidad actualizada";
                } else {
                    $_SESSION['error'] = "Stock insuficiente";
                }
            }
        }
        header("Location: index.php?action=verCarrito");
        exit();
        break;

    case 'eliminarDelCarrito':
        if (isset($_GET['index'])) {
            $index = filter_input(INPUT_GET, 'index', FILTER_VALIDATE_INT);
            if (isset($_SESSION['carrito'][$index])) {
                unset($_SESSION['carrito'][$index]);
                $_SESSION['carrito'] = array_values($_SESSION['carrito']); // Reindexar
                $_SESSION['mensaje'] = "Producto eliminado del carrito";
            }
        }
        header("Location: index.php?action=verCarrito");
        exit();
        break;

    case 'vaciarCarrito':
        $_SESSION['carrito'] = [];
        $_SESSION['mensaje'] = "Carrito vaciado";
        header("Location: index.php?action=verCarrito");
        exit();
        break;

    case 'checkout':
        // Verificar que hay productos en el carrito
        if (empty($_SESSION['carrito'])) {
            $_SESSION['error'] = "El carrito está vacío";
            header("Location: index.php?action=catalogoProductos");
            exit();
        }
        
        // Si el usuario está logueado, obtener sus datos
        if ($loginController->estaLogueado()) {
            $usuario = $loginController->obtenerUsuarioLogueado();
            // Obtener datos del cliente si existe
            $stmt = $db->prepare("SELECT * FROM cliente WHERE usuario_id = ?");
            $stmt->execute([$usuario['usuario_id']]);
            $cliente = $stmt->fetch(PDO::FETCH_ASSOC);
        }
        
        // Mostrar página de checkout (funciona para logueados e invitados)
        require "views/checkout.php";
        break;

    case 'procesarPedido':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Verificar que hay productos en el carrito
            if (empty($_SESSION['carrito'])) {
                $_SESSION['error'] = "El carrito está vacío";
                header("Location: index.php?action=catalogoProductos");
                exit();
            }

            try {
                $db->beginTransaction();
                
                // 1. Crear o buscar cliente
                $email = trim($_POST['email']);
                $stmt = $db->prepare("SELECT cliente_id FROM cliente WHERE email = ?");
                $stmt->execute([$email]);
                $clienteExistente = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if ($clienteExistente) {
                    $cliente_id = $clienteExistente['cliente_id'];
                } else {
                    // Crear nuevo cliente
                    $stmt = $db->prepare("INSERT INTO cliente 
                                        (nombre_cliente, nit, email, telefono, direccion, acepta_promociones, usuario_id, fecha_registro) 
                                        VALUES (?, ?, ?, ?, ?, ?, NULL, NOW())");
                    $stmt->execute([
                        $_POST['nombre_cliente'],
                        $_POST['nit'] ?? 'CF',
                        $email,
                        $_POST['telefono'],
                        $_POST['direccion'],
                        isset($_POST['acepta_promociones']) ? 1 : 0
                    ]);
                    $cliente_id = $db->lastInsertId();
                }
                
                // 2. Generar número de factura automático
                $stmt = $db->query("SELECT MAX(CAST(SUBSTRING(numero_factura, 5) AS UNSIGNED)) as ultimo 
                                   FROM factura 
                                   WHERE numero_factura LIKE 'WEB-%'");
                $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
                $siguiente = ($resultado['ultimo'] ?? 0) + 1;
                $numero_factura = 'WEB-' . str_pad($siguiente, 6, '0', STR_PAD_LEFT);
                
                // 3. Calcular totales
                $subtotal = 0;
                foreach($_SESSION['carrito'] as $item) {
                    $subtotal += $item['precio'] * $item['cantidad'];
                }
                $iva = $subtotal * 0.12;
                $envio = $subtotal >= 200 ? 0 : 25;
                $total = $subtotal + $iva + $envio;
                
                // 4. Crear factura
                $empleado_id = 1; // ID de empleado por defecto para ventas web
                $sucursal_id = 1; // Sucursal principal
                
                $stmt = $db->prepare("INSERT INTO factura 
                                     (numero_factura, serie_factura, cliente_id, empleado_id, sucursal_id, 
                                      fecha, subtotal, impuestos, total, estado, created_at) 
                                     VALUES (?, 'WEB', ?, ?, ?, CURDATE(), ?, ?, ?, 'ACTIVA', NOW())");
                $stmt->execute([$numero_factura, $cliente_id, $empleado_id, $sucursal_id, $subtotal, $iva, $total]);
                
                $factura_id = $db->lastInsertId();
                
                // 5. Agregar detalles y descontar inventario
                foreach($_SESSION['carrito'] as $item) {
                    // Insertar detalle
                    $stmt = $db->prepare("INSERT INTO detalle_factura 
                                         (factura_id, producto_id, cantidad, precio_unitario, porcentaje_descuento, subtotal) 
                                         VALUES (?, ?, ?, ?, 0, ?)");
                    $subtotal_item = $item['precio'] * $item['cantidad'];
                    $stmt->execute([$factura_id, $item['id'], $item['cantidad'], $item['precio'], $subtotal_item]);
                    
                    // Descontar inventario
                    $stmt = $db->prepare("UPDATE inventario 
                                         SET cantidad = cantidad - ? 
                                         WHERE producto_id = ? AND cantidad >= ?");
                    $resultado = $stmt->execute([$item['cantidad'], $item['id'], $item['cantidad']]);
                    
                    if (!$resultado || $stmt->rowCount() === 0) {
                        throw new Exception("Stock insuficiente para: " . $item['nombre']);
                    }
                }
                
                // 6. Registrar método de pago
                $tipo_pago_map = [
                    'efectivo' => 1,
                    'tarjeta' => 2,
                    'transferencia' => 4
                ];
                $tipo_pago_id = $tipo_pago_map[$_POST['metodo_pago']] ?? 1;
                
                $stmt = $db->prepare("INSERT INTO pago (factura_id, tipo_pago_id, monto, fecha_pago) 
                                     VALUES (?, ?, ?, NOW())");
                $stmt->execute([$factura_id, $tipo_pago_id, $total]);
                
                // 7. Limpiar carrito
                $_SESSION['carrito'] = [];
                
                $db->commit();
                
                // Redirigir a confirmación
                $_SESSION['pedido_exitoso'] = [
                    'numero_factura' => $numero_factura,
                    'total' => $total,
                    'email' => $email
                ];
                
                header("Location: index.php?action=pedidoExitoso");
                exit();
                
            } catch (Exception $e) {
                $db->rollBack();
                $_SESSION['error'] = "Error al procesar el pedido: " . $e->getMessage();
                header("Location: index.php?action=checkout");
                exit();
            }
        }
        break;

    case 'pedidoExitoso':
        if(!isset($_SESSION['pedido_exitoso'])) {
            header("Location: index.php?action=home");
            exit();
        }
        
        $pedido = $_SESSION['pedido_exitoso'];
        unset($_SESSION['pedido_exitoso']);
        require "views/pedido_exitoso.php";
        break;


    // ===================== DASHBOARD (REQUIERE LOGIN) =====================
    case 'dashboard':
        $loginController->requiereLogin();
        $usuarioActual = $loginController->obtenerUsuarioLogueado();
        require "views/dashboard.php";
        break;

    // ===================== CLIENTES =====================
    case 'listarClientes':
        $loginController->requierePermiso('clientes');
        $clienteController = new ClienteController($db);
        $clientes = $clienteController->listarClientes();
        require "views/clientes.php";
        break;

    case 'formularioCliente':
        $loginController->requierePermiso('clientes');
        require "views/AgregarCliente.php";
        break;

    case 'agregarCliente':
        $loginController->requierePermiso('clientes');
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $clienteController = new ClienteController($db);
            $clienteController->agregarCliente(
                $_POST['usuario_id'],
                $_POST['nombre_cliente'],
                $_POST['nit'],
                $_POST['email'],
                $_POST['telefono'],
                $_POST['direccion'],
                isset($_POST['acepta_promociones']) ? 1 : 0
            );
        }
        header("Location: index.php?action=listarClientes");
        break;

    case 'editarCliente':
        $loginController->requierePermiso('clientes');
        if (isset($_GET['cliente_id'])) {
            $clienteController = new ClienteController($db);
            $cliente_id = $_GET['cliente_id'];
            $cliente = $clienteController->obtenerCliente($cliente_id);
            require "views/EditarCliente.php";
        } else {
            header("Location: index.php?action=listarClientes");
        }
        break;

    case 'actualizarCliente':
        $loginController->requierePermiso('clientes');
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $clienteController = new ClienteController($db);
            $clienteController->actualizarCliente(
                $_POST['cliente_id'],
                $_POST['usuario_id'],
                $_POST['nombre_cliente'],
                $_POST['nit'],
                $_POST['email'],
                $_POST['telefono'],
                $_POST['direccion'],
                isset($_POST['acepta_promociones']) ? 1 : 0
            );
        }
        header("Location: index.php?action=listarClientes");
        break;

    case 'eliminarCliente':
        $loginController->requierePermiso('clientes');
        if (isset($_GET['cliente_id'])) {
            $clienteController = new ClienteController($db);
            $clienteController->eliminarCliente($_GET['cliente_id']);
        }
        header("Location: index.php?action=listarClientes");
        break;

    // ===================== EMPLEADOS =====================
    case 'listarEmpleados':
        $loginController->requierePermiso('productos');
        $empleadoController = new EmpleadoController($db);
        $empleados = $empleadoController->listarEmpleados();
        require "views/empleados.php";
        break;

    case 'formularioEmpleado':
        $loginController->requierePermiso('productos');
        require "views/AgregarEmpleado.php";
        break;

    case 'guardarEmpleado':
        $loginController->requierePermiso('productos');
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $empleadoController = new EmpleadoController($db);
            $empleadoController->agregarEmpleado(
                $_POST['usuario_id'],
                $_POST['nombre_empleado'],
                $_POST['apellido_empleado'],
                $_POST['sucursal_id'],
                isset($_POST['activo']) ? 1 : 0,
                $_POST['fecha_contratacion']
            );
        }
        header("Location: index.php?action=listarEmpleados");
        break;

    case 'editarEmpleado':
        $loginController->requierePermiso('productos');
        if (isset($_GET['empleado_id'])) {
            $empleadoController = new EmpleadoController($db);
            $empleado_id = $_GET['empleado_id'];
            $empleado = $empleadoController->obtenerEmpleado($empleado_id);
            require "views/EditarEmpleado.php";
        } else {
            header("Location: index.php?action=listarEmpleados");
        }
        break;

    case 'eliminarEmpleado':
        $loginController->requierePermiso('productos');
        if (isset($_GET['empleado_id'])) {
            $empleadoController = new EmpleadoController($db);
            $empleadoController->eliminarEmpleado($_GET['empleado_id']);
        }
        header("Location: index.php?action=listarEmpleados");
        break;

    // ===================== PRODUCTOS (ADMIN) =====================
    case 'listarProductos':
        $loginController->requierePermiso('productos');
        $productoController = new ProductoController($db);
        $productos = $productoController->listarProductos();
        require "views/productos.php";
        break;

    case 'formularioProducto':
        $loginController->requierePermiso('productos');
        require "views/AgregarProductos.php";
        break;

    case 'guardarProducto':
        $loginController->requierePermiso('productos');
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $productoController = new ProductoController($db);
            $productoController->agregarProducto($_POST);
        }
        header("Location: index.php?action=listarProductos");
        break;

    case 'editarProducto':
        $loginController->requierePermiso('productos');
        if (isset($_GET['producto_id'])) {
            $productoController = new ProductoController($db);
            $producto_id = $_GET['producto_id'];
            $producto = $productoController->obtenerProducto($producto_id);
            require "views/EditarProductos.php";
        } else {
            header("Location: index.php?action=listarProductos");
        }
        break;

    case 'actualizarProducto':
        $loginController->requierePermiso('productos');
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $productoController = new ProductoController($db);
            $productoController->actualizarProducto($_POST['producto_id'], $_POST);
        }
        header("Location: index.php?action=listarProductos");
        break;
        
    case 'eliminarProducto':
        $loginController->requierePermiso('productos');
        if (isset($_GET['producto_id'])) {
            $productoController = new ProductoController($db);
            $productoController->eliminarProducto($_GET['producto_id']);
        }
        header("Location: index.php?action=listarProductos");
        break;

    // ===================== FACTURAS =====================
    case 'listarFacturas':
        $loginController->requierePermiso('ventas');
        $facturaController = new FacturaController($db);
        $facturas = $facturaController->listarFacturas();
        require "views/factura.php";
        break;

    case 'formularioFactura':
        $loginController->requierePermiso('ventas');
        $clienteController = new ClienteController($db);
        $empleadoController = new EmpleadoController($db);
        $productoController = new ProductoController($db);
        
        $clientes = $clienteController->listarClientes();
        $empleados = $empleadoController->listarEmpleados();
        $productos = $productoController->listarProductos();
        require "views/AgregarFactura.php";
        break;

    case 'crearFactura':
        $loginController->requierePermiso('ventas');
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $facturaController = new FacturaController($db);
            
            $datosFactura = [
                'numero_factura' => $_POST['numero_factura'],
                'serie_factura' => $_POST['serie_factura'],
                'cliente_id' => $_POST['cliente_id'],
                'empleado_id' => $_POST['empleado_id'],
                'sucursal_id' => $_POST['sucursal_id'],
                'subtotal' => 0,
                'impuestos' => 0,
                'total' => 0
            ];

            $detalles = $_POST['detalles'];

            // Calcular totales
            $subtotal = 0;
            foreach($detalles as $d){
                $subtotal += $d['cantidad'] * $d['precio_unitario'] * (1 - $d['porcentaje_descuento']/100);
            }
            $impuestos = $subtotal * 0.12;
            $total = $subtotal + $impuestos;

            $datosFactura['subtotal'] = $subtotal;
            $datosFactura['impuestos'] = $impuestos;
            $datosFactura['total'] = $total;

            $facturaController->crearFacturaConDetalles($datosFactura, $detalles);
        }
        header("Location: index.php?action=listarFacturas");
        break;

    case 'imprimirFactura':
        $loginController->requierePermiso('ventas');
        if(isset($_GET['factura_id'])){
            $facturaController = new FacturaController($db);
            $factura = $facturaController->obtenerFactura($_GET['factura_id']);
            require "views/ImprimirFactura.php";
        } else {
            header("Location: index.php?action=listarFacturas");
        }
        break;

    case 'anularFactura':
        $loginController->requierePermiso('ventas');
        if(isset($_GET['factura_id'])){
            $facturaController = new FacturaController($db);
            if($facturaController->anularFactura($_GET['factura_id'])){
                $_SESSION['mensaje'] = "Factura anulada correctamente";
            } else {
                $_SESSION['error'] = "Error al anular la factura";
            }
        }
        header("Location: index.php?action=listarFacturas");
        break;
    
        // ==================== COTIZACIONES PÚBLICAS ====================
    
    case 'solicitarCotizacion':
        $productoController = new ProductoController($db);
        $productos = $productoController->listarProductos();
        require "views/SolicitarCotizacion.php";
        break;

    case 'crearCotizacionPublica':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            require_once "controllers/CotizacionController.php";
            $cotizacionController = new CotizacionController($db);
            
            try {
                // NO inicies la transacción aquí, el controller ya lo hace
                
                // 1. Crear o buscar cliente
                $email = trim($_POST['email']);
                $stmt = $db->prepare("SELECT cliente_id FROM cliente WHERE email = ?");
                $stmt->execute([$email]);
                $clienteExistente = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if ($clienteExistente) {
                    $cliente_id = $clienteExistente['cliente_id'];
                } else {
                    // Crear nuevo cliente
                    $stmt = $db->prepare("INSERT INTO cliente 
                                        (nombre_cliente, nit, email, telefono, direccion, acepta_promociones, usuario_id, fecha_registro) 
                                        VALUES (?, ?, ?, ?, ?, ?, NULL, NOW())");
                    $stmt->execute([
                        $_POST['nombre_cliente'],
                        $_POST['nit'] ?? 'CF',
                        $email,
                        $_POST['telefono'] ?? '',
                        $_POST['direccion'] ?? '',
                        isset($_POST['acepta_promociones']) ? 1 : 0
                    ]);
                    $cliente_id = $db->lastInsertId();
                }
                
                // 2. Calcular totales
                $detalles = $_POST['detalles'];
                $subtotal = 0;
                
                // Procesar detalles y agregar porcentaje_descuento si no existe
                foreach($detalles as &$d){
                    // Obtener el descuento del producto si no viene en el detalle
                    if (!isset($d['porcentaje_descuento'])) {
                        $stmt = $db->prepare("SELECT porcentaje_descuento FROM producto WHERE producto_id = ?");
                        $stmt->execute([$d['producto_id']]);
                        $producto = $stmt->fetch(PDO::FETCH_ASSOC);
                        $d['porcentaje_descuento'] = $producto['porcentaje_descuento'] ?? 0;
                    }
                    
                    $subtotal += $d['cantidad'] * $d['precio_unitario'] * (1 - ($d['porcentaje_descuento'] ?? 0)/100);
                }
                
                $impuestos = $subtotal * 0.12;
                $total = $subtotal + $impuestos;
                
                // 3. Crear cotización usando el controller (que ya maneja transacciones)
                $datosCotizacion = [
                    'cliente_id' => $cliente_id,
                    'empleado_id' => null,
                    'sucursal_id' => null,
                    'subtotal' => $subtotal,
                    'impuestos' => $impuestos,
                    'total' => $total
                ];
                
                $cotizacion_id = $cotizacionController->crearCotizacionConDetalles($datosCotizacion, $detalles);
                
                if ($cotizacion_id) {
                    $_SESSION['mensaje'] = "¡Cotización creada exitosamente! En breve nos pondremos en contacto contigo.";
                    
                    // Redirigir a la vista de impresión
                    header("Location: index.php?action=imprimirCotizacion&cotizacion_id=" . $cotizacion_id);
                    exit();
                } else {
                    $_SESSION['error'] = "Error al crear la cotización. Por favor, intente nuevamente.";
                    header("Location: index.php?action=solicitarCotizacion");
                    exit();
                }
                
            } catch (Exception $e) {
                // NO hagas rollBack aquí porque no iniciaste transacción
                $_SESSION['error'] = "Error al crear cotización: " . $e->getMessage();
                error_log("Error cotización: " . $e->getMessage());
                header("Location: index.php?action=solicitarCotizacion");
                exit();
            }
        }
        break;

    case 'imprimirCotizacion':
    if(isset($_GET['cotizacion_id'])){
        require_once "controllers/CotizacionController.php";
        $cotizacionController = new CotizacionController($db);
        $cotizacion = $cotizacionController->obtenerCotizacion($_GET['cotizacion_id']);
        
        if($cotizacion) {
            require "views/ImprimirCotizacion.php";
        } else {
            $_SESSION['error'] = "Cotización no encontrada";
            header("Location: index.php?action=home");
            exit();
        }
    } else {
        $_SESSION['error'] = "ID de cotización no especificado";
        header("Location: index.php?action=home");
        exit();
    }
    break;
    
    // ===================== REPORTES =====================
    case 'reportes':
        $loginController->requierePermiso('reportes');
        require_once "controllers/ReporteController.php";
        $reporteController = new ReporteController($db);
        
        // Determinar qué reporte se solicita
        if(isset($_GET['reporte'])){
            $reporte = $_GET['reporte'];
            
            switch($reporte){
                case '1': // Ventas por tipo de pago
                    if(isset($_GET['fecha_inicio']) && isset($_GET['fecha_fin'])){
                        $resultado = $reporteController->reporteVentasPorTipoPago($_GET['fecha_inicio'], $_GET['fecha_fin']);
                    }
                    break;
                    
                case '2': // Productos mayor ingreso
                    if(isset($_GET['fecha_inicio']) && isset($_GET['fecha_fin'])){
                        $limite = $_GET['limite'] ?? 10;
                        $resultado = $reporteController->reporteProductosMayorIngreso($_GET['fecha_inicio'], $_GET['fecha_fin'], $limite);
                    }
                    break;
                    
                case '3': // Productos más vendidos
                    if(isset($_GET['fecha_inicio']) && isset($_GET['fecha_fin'])){
                        $limite = $_GET['limite'] ?? 10;
                        $resultado = $reporteController->reporteProductosMasVendidos($_GET['fecha_inicio'], $_GET['fecha_fin'], $limite);
                    }
                    break;
                    
                case '4': // Inventario actual
                    $resultado = $reporteController->reporteInventarioActual();
                    break;
                    
                case '5': // Productos menos vendidos
                    if(isset($_GET['fecha_inicio']) && isset($_GET['fecha_fin'])){
                        $limite = $_GET['limite'] ?? 10;
                        $resultado = $reporteController->reporteProductosMenosVendidos($_GET['fecha_inicio'], $_GET['fecha_fin'], $limite);
                    }
                    break;
                    
                case '6': // Productos sin stock
                    $resultado = $reporteController->reporteProductosSinStock();
                    break;
                    
                case '7': // Buscar factura
                    if(isset($_GET['numero_factura'])){
                        $resultado = $reporteController->reporteDetalleFactura($_GET['numero_factura']);
                    }
                    break;
                    
                case '8': // Ventas por sucursal
                    if(isset($_GET['fecha_inicio']) && isset($_GET['fecha_fin'])){
                        $resultado = $reporteController->reporteVentasPorSucursal($_GET['fecha_inicio'], $_GET['fecha_fin']);
                    }
                    break;
                    
                case '9': // Top clientes
                    if(isset($_GET['fecha_inicio']) && isset($_GET['fecha_fin'])){
                        $limite = $_GET['limite'] ?? 10;
                        $resultado = $reporteController->reporteTopClientes($_GET['fecha_inicio'], $_GET['fecha_fin'], $limite);
                    }
                    break;
            }
        }
        
        require "views/reportes.php";
        break;
    // ==================== PROVEEDORES ====================
case 'listarProveedores':
    $loginController->requiereLogin();
    $proveedorController = new ProveedorController($db);
    $proveedores = $proveedorController->listarProveedores();
    include __DIR__ . '/views/proveedores.php';
    break;

case 'formularioProveedor':
    $loginController->requiereLogin();
    include __DIR__ . '/views/AgregarProveedor.php';
    break;

case 'guardarProveedor':
    $loginController->requiereLogin();
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $proveedorController = new ProveedorController($db);
        $nombre = $_POST['nombre_proveedor'];
        $contacto = $_POST['contacto_proveedor'] ?? '';
        $telefono = $_POST['telefono_proveedor'] ?? '';
        $email = $_POST['email_proveedor'] ?? '';
        $direccion = $_POST['direccion_proveedor'] ?? '';
        
        if ($proveedorController->agregarProveedor($nombre, $contacto, $telefono, $email, $direccion)) {
            $_SESSION['mensaje'] = "Proveedor agregado correctamente";
        } else {
            $_SESSION['error'] = "Error al agregar proveedor";
        }
    }
    
    header("Location: index.php?action=listarProveedores");
    exit();
    break;

case 'editarProveedor':
    $loginController->requiereLogin();
    $proveedor_id = $_GET['proveedor_id'] ?? null;
    if ($proveedor_id) {
        $proveedorController = new ProveedorController($db);
        $proveedor = $proveedorController->obtenerProveedor($proveedor_id);
        include __DIR__ . '/views/EditarProveedor.php';
    } else {
        header("Location: index.php?action=listarProveedores");
        exit();
    }
    break;

case 'actualizarProveedor':
    $loginController->requiereLogin();
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $proveedorController = new ProveedorController($db);
        $proveedor_id = $_POST['proveedor_id'];
        $nombre = $_POST['nombre_proveedor'];
        $contacto = $_POST['contacto_proveedor'] ?? '';
        $telefono = $_POST['telefono_proveedor'] ?? '';
        $email = $_POST['email_proveedor'] ?? '';
        $direccion = $_POST['direccion_proveedor'] ?? '';
        
        if ($proveedorController->actualizarProveedor($proveedor_id, $nombre, $contacto, $telefono, $email, $direccion)) {
            $_SESSION['mensaje'] = "Proveedor actualizado correctamente";
        } else {
            $_SESSION['error'] = "Error al actualizar proveedor";
        }
    }
    
    header("Location: index.php?action=listarProveedores");
    exit();
    break;

case 'eliminarProveedor':
    $loginController->requiereLogin();
    $proveedor_id = $_GET['proveedor_id'] ?? null;
    if ($proveedor_id) {
        $proveedorController = new ProveedorController($db);
        $resultado = $proveedorController->eliminarProveedor($proveedor_id);
        
        if (is_array($resultado)) {
            if ($resultado['success']) {
                $_SESSION['mensaje'] = $resultado['mensaje'];
            } else {
                $_SESSION['error'] = $resultado['mensaje'];
            }
        }
    }
    
    header("Location: index.php?action=listarProveedores");
    exit();
    break;

    // ==================== SUCURSALES ====================
case 'listarSucursales':
    $loginController->requiereLogin();
    $sucursalController = new SucursalController($db);
    $sucursales = $sucursalController->listarSucursales();
    include __DIR__ . '/views/sucursales.php';
    break;

case 'formularioSucursal':
    $loginController->requiereLogin();
    include __DIR__ . '/views/AgregarSucursal.php';
    break;

case 'guardarSucursal':
    $loginController->requiereLogin();
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $sucursalController = new SucursalController($db);
        $nombre = $_POST['nombre_sucursal'];
        $direccion = $_POST['direccion_sucursal'];
        $telefono = $_POST['telefono_sucursal'] ?? '';
        $latitud = !empty($_POST['latitud']) ? $_POST['latitud'] : null;
        $longitud = !empty($_POST['longitud']) ? $_POST['longitud'] : null;
        $activa = isset($_POST['activa']) ? 1 : 0;
        
        if ($sucursalController->agregarSucursal($nombre, $direccion, $telefono, $latitud, $longitud, $activa)) {
            $_SESSION['mensaje'] = "Sucursal agregada correctamente";
        } else {
            $_SESSION['error'] = "Error al agregar sucursal";
        }
    }
    
    header("Location: index.php?action=listarSucursales");
    exit();
    break;

case 'editarSucursal':
    $loginController->requiereLogin();
    $sucursal_id = $_GET['sucursal_id'] ?? null;
    if ($sucursal_id) {
        $sucursalController = new SucursalController($db);
        $sucursal = $sucursalController->obtenerSucursal($sucursal_id);
        include __DIR__ . '/views/EditarSucursal.php';
    } else {
        header("Location: index.php?action=listarSucursales");
        exit();
    }
    break;

case 'actualizarSucursal':
    $loginController->requiereLogin();
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $sucursalController = new SucursalController($db);
        $sucursal_id = $_POST['sucursal_id'];
        $nombre = $_POST['nombre_sucursal'];
        $direccion = $_POST['direccion_sucursal'];
        $telefono = $_POST['telefono_sucursal'] ?? '';
        $latitud = !empty($_POST['latitud']) ? $_POST['latitud'] : null;
        $longitud = !empty($_POST['longitud']) ? $_POST['longitud'] : null;
        $activa = isset($_POST['activa']) ? 1 : 0;
        
        if ($sucursalController->actualizarSucursal($sucursal_id, $nombre, $direccion, $telefono, $latitud, $longitud, $activa)) {
            $_SESSION['mensaje'] = "Sucursal actualizada correctamente";
        } else {
            $_SESSION['error'] = "Error al actualizar sucursal";
        }
    }
    
    header("Location: index.php?action=listarSucursales");
    exit();
    break;

case 'cambiarEstadoSucursal':
    $loginController->requiereLogin();
    $sucursal_id = $_GET['sucursal_id'] ?? null;
    $estado = $_GET['estado'] ?? 0;
    
    if ($sucursal_id) {
        $sucursalController = new SucursalController($db);
        if ($sucursalController->cambiarEstadoSucursal($sucursal_id, $estado)) {
            $_SESSION['mensaje'] = "Estado de sucursal actualizado";
        } else {
            $_SESSION['error'] = "Error al cambiar estado";
        }
    }
    
    header("Location: index.php?action=listarSucursales");
    exit();
    break;

case 'eliminarSucursal':
    $loginController->requiereLogin();
    $sucursal_id = $_GET['sucursal_id'] ?? null;
    if ($sucursal_id) {
        $sucursalController = new SucursalController($db);
        $resultado = $sucursalController->eliminarSucursal($sucursal_id);
        
        if (is_array($resultado)) {
            if ($resultado['success']) {
                $_SESSION['mensaje'] = $resultado['mensaje'];
            } else {
                $_SESSION['error'] = $resultado['mensaje'];
            }
        }
    }
    
    header("Location: index.php?action=listarSucursales");
    exit();
    break;

    // ==================== TIPOS DE PAGO ====================
case 'listarTiposPago':
    $loginController->requiereLogin();
    $tipoPagoController = new TipoPagoController($db);
    $tiposPago = $tipoPagoController->listarTiposPago();
    include __DIR__ . '/views/tipos_pago.php';
    break;

case 'formularioTipoPago':
    $loginController->requiereLogin();
    include __DIR__ . '/views/AgregarTipoPago.php';
    break;

case 'guardarTipoPago':
    $loginController->requiereLogin();
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $tipoPagoController = new TipoPagoController($db);
        $nombre = $_POST['nombre_pago'];
        $descripcion = $_POST['descripcion'] ?? '';
        $activo = isset($_POST['activo']) ? 1 : 0;
        
        if ($tipoPagoController->agregarTipoPago($nombre, $descripcion, $activo)) {
            $_SESSION['mensaje'] = "Tipo de pago agregado correctamente";
        } else {
            $_SESSION['error'] = "Error al agregar tipo de pago";
        }
    }
    
    header("Location: index.php?action=listarTiposPago");
    exit();
    break;

case 'editarTipoPago':
    $loginController->requiereLogin();
    $tipo_pago_id = $_GET['tipo_pago_id'] ?? null;
    if ($tipo_pago_id) {
        $tipoPagoController = new TipoPagoController($db);
        $tipoPago = $tipoPagoController->obtenerTipoPago($tipo_pago_id);
        include __DIR__ . '/views/EditarTipoPago.php';
    } else {
        header("Location: index.php?action=listarTiposPago");
        exit();
    }
    break;

case 'actualizarTipoPago':
    $loginController->requiereLogin();
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $tipoPagoController = new TipoPagoController($db);
        $tipo_pago_id = $_POST['tipo_pago_id'];
        $nombre = $_POST['nombre_pago'];
        $descripcion = $_POST['descripcion'] ?? '';
        $activo = isset($_POST['activo']) ? 1 : 0;
        
        if ($tipoPagoController->actualizarTipoPago($tipo_pago_id, $nombre, $descripcion, $activo)) {
            $_SESSION['mensaje'] = "Tipo de pago actualizado correctamente";
        } else {
            $_SESSION['error'] = "Error al actualizar tipo de pago";
        }
    }
    
    header("Location: index.php?action=listarTiposPago");
    exit();
    break;

case 'cambiarEstadoTipoPago':
    $loginController->requiereLogin();
    $tipo_pago_id = $_GET['tipo_pago_id'] ?? null;
    $estado = $_GET['estado'] ?? 0;
    
    if ($tipo_pago_id) {
        $tipoPagoController = new TipoPagoController($db);
        if ($tipoPagoController->cambiarEstadoTipoPago($tipo_pago_id, $estado)) {
            $_SESSION['mensaje'] = "Estado de tipo de pago actualizado";
        } else {
            $_SESSION['error'] = "Error al cambiar estado";
        }
    }
    
    header("Location: index.php?action=listarTiposPago");
    exit();
    break;

case 'eliminarTipoPago':
    $loginController->requiereLogin();
    $tipo_pago_id = $_GET['tipo_pago_id'] ?? null;
    if ($tipo_pago_id) {
        $tipoPagoController = new TipoPagoController($db);
        $resultado = $tipoPagoController->eliminarTipoPago($tipo_pago_id);
        
        if (is_array($resultado)) {
            if ($resultado['success']) {
                $_SESSION['mensaje'] = $resultado['mensaje'];
            } else {
                $_SESSION['error'] = $resultado['mensaje'];
            }
        }
    }
    
    header("Location: index.php?action=listarTiposPago");
    exit();
    break;

case 'catalogoProductos':
    include __DIR__ . '/views/catalogo_productos.php';
    break;
    case 'agregarAlCarrito':
    if (!isset($_SESSION['carrito'])) {
        $_SESSION['carrito'] = [];
    }
    
    $producto_id = $_GET['producto_id'] ?? null;
    if ($producto_id) {
        require_once __DIR__ . '/controllers/ProductoController.php';
        $productoController = new ProductoController($db);
        $producto = $productoController->obtenerProducto($producto_id);
        
        if ($producto) {
            // Verificar si ya existe
            $encontrado = false;
            foreach ($_SESSION['carrito'] as &$item) {
                if ($item['producto_id'] == $producto_id) {
                    $item['cantidad']++;
                    $encontrado = true;
                    break;
                }
            }
            
            if (!$encontrado) {
                $_SESSION['carrito'][] = [
                    'producto_id' => $producto['producto_id'],
                    'nombre' => $producto['nombre'],
                    'sku' => $producto['codigo_sku'],
                    'precio_base' => $producto['precio_base'],
                    'descuento' => $producto['porcentaje_descuento'],
                    'precio' => $producto['precio_base'] * (1 - $producto['porcentaje_descuento'] / 100),
                    'cantidad' => 1
                ];
            }
            
            $_SESSION['mensaje'] = "Producto agregado al carrito";
        }
    }
    
    header("Location: index.php?action=catalogoProductos");
    exit();
    break;
    // ===================== DEFAULT =====================
    default:
        header("Location: index.php?action=home");
        break;
}
?>