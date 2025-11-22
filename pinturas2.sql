-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 16, 2025 at 11:37 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `pinturas2`
--

DELIMITER $$
--
-- Procedures
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_buscar_sucursales_cercanas` (IN `p_latitud` DECIMAL(10,8), IN `p_longitud` DECIMAL(11,8), IN `p_radio_km` INT)   BEGIN
    SELECT 
        sucursal_id,
        nombre_sucursal,
        direccion_sucursal,
        telefono_sucursal,
        latitud,
        longitud,
        -- Fórmula simplificada para distancia (Haversine es más preciso y compatible con MySQL)
        ( 6371 * ACOS( 
            COS( RADIANS(p_latitud) ) * COS( RADIANS(latitud) ) * COS( RADIANS(longitud) - RADIANS(p_longitud) ) + 
            SIN( RADIANS(p_latitud) ) * SIN( RADIANS(latitud) ) 
        ) ) AS distancia_km
    FROM sucursal
    WHERE activa = TRUE
    HAVING distancia_km <= p_radio_km -- Se incluye el filtro de radio
    ORDER BY distancia_km ASC;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_registrar_venta` (IN `p_numero_factura` VARCHAR(50), IN `p_serie_factura` VARCHAR(10), IN `p_cliente_id` INT, IN `p_empleado_id` INT, IN `p_sucursal_id` INT, IN `p_detalles` JSON)   BEGIN
    -- Los bloques BEGIN TRANSACTION/COMMIT/ROLLBACK son compatibles
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        RESIGNAL; -- Re-lanza la excepción
    END;

    START TRANSACTION;
    
    -- Insertar factura (los cálculos se harían en la aplicación)
    INSERT INTO factura (numero_factura, serie_factura, cliente_id, empleado_id, sucursal_id, fecha, subtotal, impuestos, total)
    VALUES (p_numero_factura, p_serie_factura, p_cliente_id, p_empleado_id, p_sucursal_id, CURDATE(), 0, 0, 0);
    
    -- La lógica de procesamiento de JSON y actualización de inventario 
    -- sería más compleja en MySQL y dependería de las funciones JSON_TABLE (MySQL 8+) 
    -- o un manejo más manual si usas versiones anteriores. 
    -- Dejo el SP como esqueleto de transacción.
    
    COMMIT;
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `carrito_compras`
--

CREATE TABLE `carrito_compras` (
  `carrito_id` int(11) NOT NULL,
  `cliente_id` int(11) NOT NULL,
  `producto_id` int(11) NOT NULL,
  `cantidad` int(11) NOT NULL,
  `fecha_agregado` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `categoria_producto`
--

CREATE TABLE `categoria_producto` (
  `categoria_id` int(11) NOT NULL,
  `nombre_categoria` varchar(100) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `unidad_venta` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `categoria_producto`
--

INSERT INTO `categoria_producto` (`categoria_id`, `nombre_categoria`, `descripcion`, `unidad_venta`) VALUES
(1, 'Accesorios', 'Brochas, rodillos, bandejas, mantas de limpieza, espátula, etc.', 'unidad'),
(2, 'Solventes', 'Aguarrás, solvente limpiador, gas, etc.', 'galon'),
(3, 'Pinturas', 'A base de agua, a base de aceite, etc.', 'galon'),
(4, 'Barnices', 'Barniz sintético, barniz acrílico, etc.', 'galon');

-- --------------------------------------------------------

--
-- Table structure for table `cliente`
--

CREATE TABLE `cliente` (
  `cliente_id` int(11) NOT NULL,
  `usuario_id` int(11) DEFAULT NULL,
  `nombre_cliente` varchar(100) NOT NULL,
  `nit` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `direccion` text DEFAULT NULL,
  `fecha_registro` datetime DEFAULT current_timestamp(),
  `acepta_promociones` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `cliente`
--

INSERT INTO `cliente` (`cliente_id`, `usuario_id`, `nombre_cliente`, `nit`, `email`, `telefono`, `direccion`, `fecha_registro`, `acepta_promociones`) VALUES
(6, 3, 'Carlos Castillo', '123605', 'Castillo@gmail.com', '12345678', 'Xela', '2025-10-21 01:11:37', 1),
(8, 6, 'rocky', '123', 'rocky@gmail.com', '89562124', 'toto', '2025-11-09 02:16:20', 0);

-- --------------------------------------------------------

--
-- Table structure for table `cotizacion`
--

CREATE TABLE `cotizacion` (
  `cotizacion_id` int(11) NOT NULL,
  `numero_cotizacion` varchar(50) NOT NULL,
  `cliente_id` int(11) NOT NULL,
  `empleado_id` int(11) DEFAULT NULL,
  `sucursal_id` int(11) DEFAULT NULL,
  `fecha` date NOT NULL,
  `valida_hasta` date NOT NULL,
  `subtotal` decimal(10,2) NOT NULL,
  `impuestos` decimal(10,2) NOT NULL,
  `total` decimal(10,2) NOT NULL,
  `estado` varchar(20) DEFAULT 'PENDIENTE',
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `detalle_cotizacion`
--

CREATE TABLE `detalle_cotizacion` (
  `detalle_cotizacion_id` int(11) NOT NULL,
  `cotizacion_id` int(11) NOT NULL,
  `producto_id` int(11) NOT NULL,
  `cantidad` int(11) NOT NULL,
  `precio_unitario` decimal(10,2) NOT NULL,
  `porcentaje_descuento` decimal(5,2) DEFAULT 0.00,
  `subtotal` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `detalle_factura`
--

CREATE TABLE `detalle_factura` (
  `detalle_factura_id` int(11) NOT NULL,
  `factura_id` int(11) NOT NULL,
  `producto_id` int(11) NOT NULL,
  `cantidad` int(11) NOT NULL,
  `precio_unitario` decimal(10,2) NOT NULL,
  `porcentaje_descuento` decimal(5,2) DEFAULT 0.00,
  `subtotal` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `detalle_factura`
--

INSERT INTO `detalle_factura` (`detalle_factura_id`, `factura_id`, `producto_id`, `cantidad`, `precio_unitario`, `porcentaje_descuento`, `subtotal`) VALUES
(1, 1, 3, 2, 150.00, 5.00, 285.00),
(2, 1, 7, 3, 25.50, 10.00, 68.85),
(3, 2, 3, 3, 150.00, 5.00, 427.50),
(4, 3, 3, 1, 150.00, 4.99, 142.52),
(5, 5, 7, 5, 25.50, 10.00, 114.75),
(6, 5, 3, 1, 150.00, 5.00, 142.50);

-- --------------------------------------------------------

--
-- Table structure for table `empleado`
--

CREATE TABLE `empleado` (
  `empleado_id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `nombre_empleado` varchar(100) NOT NULL,
  `apellido_empleado` varchar(100) NOT NULL,
  `sucursal_id` int(11) NOT NULL,
  `activo` tinyint(1) DEFAULT 1,
  `fecha_contratacion` date DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `empleado`
--

INSERT INTO `empleado` (`empleado_id`, `usuario_id`, `nombre_empleado`, `apellido_empleado`, `sucursal_id`, `activo`, `fecha_contratacion`, `created_at`) VALUES
(1, 1, 'Pedro', 'Gómez', 4, 1, '2025-10-20', '2025-10-20 01:33:03'),
(3, 1, 'julio', 'cos', 6, 1, '2025-10-03', '2025-10-20 19:25:03');

-- --------------------------------------------------------

--
-- Table structure for table `factura`
--

CREATE TABLE `factura` (
  `factura_id` int(11) NOT NULL,
  `numero_factura` varchar(50) NOT NULL,
  `serie_factura` varchar(10) NOT NULL,
  `cliente_id` int(11) NOT NULL,
  `empleado_id` int(11) NOT NULL,
  `sucursal_id` int(11) NOT NULL,
  `fecha` date NOT NULL,
  `fecha_anulacion` datetime DEFAULT NULL,
  `subtotal` decimal(10,2) NOT NULL,
  `impuestos` decimal(10,2) NOT NULL,
  `total` decimal(10,2) NOT NULL,
  `estado` varchar(20) DEFAULT 'ACTIVA',
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `factura`
--

INSERT INTO `factura` (`factura_id`, `numero_factura`, `serie_factura`, `cliente_id`, `empleado_id`, `sucursal_id`, `fecha`, `fecha_anulacion`, `subtotal`, `impuestos`, `total`, `estado`, `created_at`) VALUES
(1, '12345', 'A', 6, 1, 3, '2025-11-08', '2025-11-08 23:15:16', 0.00, 0.00, 0.00, 'ANULADA', '2025-11-08 22:58:33'),
(2, '6789', 'B', 6, 3, 6, '2025-11-08', '2025-11-09 03:16:34', 0.00, 0.00, 0.00, 'ANULADA', '2025-11-08 23:14:57'),
(3, '12', 'c', 6, 1, 6, '2025-11-08', NULL, 142.52, 17.10, 159.62, 'ACTIVA', '2025-11-08 23:17:43'),
(5, '1', 'A', 8, 1, 1, '2025-11-09', NULL, 257.25, 30.87, 288.12, 'ACTIVA', '2025-11-09 04:38:57');

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `historial_inventario`
--

CREATE TABLE `historial_inventario` (
  `historial_id` int(11) NOT NULL,
  `sucursal_id` int(11) NOT NULL,
  `producto_id` int(11) NOT NULL,
  `cantidad_anterior` int(11) NOT NULL,
  `cantidad_nueva` int(11) NOT NULL,
  `tipo_movimiento` varchar(50) NOT NULL,
  `referencia_id` int(11) DEFAULT NULL,
  `usuario_id` int(11) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `historial_inventario`
--

INSERT INTO `historial_inventario` (`historial_id`, `sucursal_id`, `producto_id`, `cantidad_anterior`, `cantidad_nueva`, `tipo_movimiento`, `referencia_id`, `usuario_id`, `created_at`) VALUES
(1, 1, 3, 50, 48, 'VENTA', 1, 1, '2025-11-08 22:58:33'),
(2, 1, 7, 75, 72, 'VENTA', 1, 1, '2025-11-08 22:58:33'),
(3, 1, 3, 48, 45, 'VENTA', 2, 1, '2025-11-08 23:14:57'),
(4, 1, 3, 45, 47, 'ANULACION', 1, 1, '2025-11-08 23:15:16'),
(5, 1, 7, 72, 75, 'ANULACION', 1, 1, '2025-11-08 23:15:16'),
(6, 1, 3, 47, 46, 'VENTA', 3, 1, '2025-11-08 23:17:43'),
(7, 1, 3, 46, 49, 'ANULACION', 2, 1, '2025-11-09 03:16:34'),
(8, 1, 7, 75, 70, 'VENTA', 5, 1, '2025-11-09 04:38:57'),
(9, 1, 3, 49, 48, 'VENTA', 5, 1, '2025-11-09 04:38:57');

-- --------------------------------------------------------

--
-- Table structure for table `inventario`
--

CREATE TABLE `inventario` (
  `inventario_id` int(11) NOT NULL,
  `sucursal_id` int(11) NOT NULL,
  `producto_id` int(11) NOT NULL,
  `cantidad` int(11) NOT NULL DEFAULT 0,
  `stock_minimo` int(11) DEFAULT 0,
  `ultima_actualizacion` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `inventario`
--

INSERT INTO `inventario` (`inventario_id`, `sucursal_id`, `producto_id`, `cantidad`, `stock_minimo`, `ultima_actualizacion`) VALUES
(1, 1, 1, 100, 20, '2025-11-08 22:57:13'),
(2, 1, 3, 48, 10, '2025-11-09 04:38:57'),
(3, 1, 7, 70, 15, '2025-11-09 04:38:57'),
(4, 2, 1, 80, 20, '2025-11-08 22:57:13'),
(5, 2, 3, 58, 10, '2025-11-09 04:38:57'),
(6, 2, 7, 85, 15, '2025-11-09 04:38:57');

-- --------------------------------------------------------

--
-- Table structure for table `medida`
--

CREATE TABLE `medida` (
  `medida_id` int(11) NOT NULL,
  `nombre_medida` varchar(50) NOT NULL,
  `equivalente_galones` decimal(8,4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `medida`
--

INSERT INTO `medida` (`medida_id`, `nombre_medida`, `equivalente_galones`) VALUES
(1, '1/32 galón', 0.0313),
(2, '1/16 galón', 0.0625),
(3, '1/8 galón', 0.1250),
(4, '1/4 galón', 0.2500),
(5, '1/2 galón', 0.5000),
(6, '1 galón', 1.0000),
(7, '1 cubeta', 5.0000);

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '2025_10_26_191757_create_carrito_compras_table', 0),
(2, '2025_10_26_191757_create_categoria_producto_table', 0),
(3, '2025_10_26_191757_create_cliente_table', 0),
(4, '2025_10_26_191757_create_cotizacion_table', 0),
(5, '2025_10_26_191757_create_detalle_cotizacion_table', 0),
(6, '2025_10_26_191757_create_detalle_factura_table', 0),
(7, '2025_10_26_191757_create_empleado_table', 0),
(8, '2025_10_26_191757_create_factura_table', 0),
(9, '2025_10_26_191757_create_historial_inventario_table', 0),
(10, '2025_10_26_191757_create_inventario_table', 0),
(11, '2025_10_26_191757_create_medida_table', 0),
(12, '2025_10_26_191757_create_pago_table', 0),
(13, '2025_10_26_191757_create_producto_table', 0),
(14, '2025_10_26_191757_create_proveedor_table', 0),
(15, '2025_10_26_191757_create_rol_table', 0),
(16, '2025_10_26_191757_create_sucursal_table', 0),
(17, '2025_10_26_191757_create_tipo_pago_table', 0),
(18, '2025_10_26_191757_create_tipo_producto_table', 0),
(19, '2025_10_26_191757_create_usuario_table', 0),
(20, '2025_10_26_191758_create_vw_facturas_completas_view', 0),
(21, '2025_10_26_191758_create_vw_inventario_completo_view', 0),
(22, '2025_10_26_191758_create_vw_productos_completos_view', 0),
(23, '2025_10_26_191759_create_sp_buscar_sucursales_cercanas_proc', 0),
(24, '2025_10_26_191759_create_sp_registrar_venta_proc', 0),
(25, '2025_10_26_191800_add_foreign_keys_to_carrito_compras_table', 0),
(26, '2025_10_26_191800_add_foreign_keys_to_cliente_table', 0),
(27, '2025_10_26_191800_add_foreign_keys_to_cotizacion_table', 0),
(28, '2025_10_26_191800_add_foreign_keys_to_detalle_cotizacion_table', 0),
(29, '2025_10_26_191800_add_foreign_keys_to_detalle_factura_table', 0),
(30, '2025_10_26_191800_add_foreign_keys_to_empleado_table', 0),
(31, '2025_10_26_191800_add_foreign_keys_to_factura_table', 0),
(32, '2025_10_26_191800_add_foreign_keys_to_historial_inventario_table', 0),
(33, '2025_10_26_191800_add_foreign_keys_to_inventario_table', 0),
(34, '2025_10_26_191800_add_foreign_keys_to_pago_table', 0),
(35, '2025_10_26_191800_add_foreign_keys_to_producto_table', 0),
(36, '2025_10_26_191800_add_foreign_keys_to_tipo_producto_table', 0),
(37, '2025_10_26_191800_add_foreign_keys_to_usuario_table', 0),
(38, '2014_10_12_000000_create_users_table', 1),
(39, '2014_10_12_100000_create_password_resets_table', 1),
(40, '2019_08_19_000000_create_failed_jobs_table', 1),
(41, '2019_12_14_000001_create_personal_access_tokens_table', 1);

-- --------------------------------------------------------

--
-- Table structure for table `pago`
--

CREATE TABLE `pago` (
  `pago_id` int(11) NOT NULL,
  `factura_id` int(11) NOT NULL,
  `tipo_pago_id` int(11) NOT NULL,
  `monto` decimal(10,2) NOT NULL,
  `numero_referencia` varchar(100) DEFAULT NULL,
  `autorizacion_tarjeta` varchar(100) DEFAULT NULL,
  `fecha_pago` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `pago`
--

INSERT INTO `pago` (`pago_id`, `factura_id`, `tipo_pago_id`, `monto`, `numero_referencia`, `autorizacion_tarjeta`, `fecha_pago`) VALUES
(1, 1, 2, 396.31, NULL, NULL, '2025-11-08 22:58:33'),
(2, 2, 1, 0.80, NULL, NULL, '2025-11-08 23:14:57'),
(3, 2, 2, 478.00, NULL, NULL, '2025-11-08 23:14:57'),
(4, 3, 1, 159.61, NULL, NULL, '2025-11-08 23:17:43'),
(5, 5, 1, 100.00, NULL, NULL, '2025-11-09 04:38:57'),
(6, 5, 2, 200.00, NULL, NULL, '2025-11-09 04:38:57');

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

CREATE TABLE `password_resets` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `personal_access_tokens`
--

CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tokenable_type` varchar(255) NOT NULL,
  `tokenable_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `token` varchar(64) NOT NULL,
  `abilities` text DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `producto`
--

CREATE TABLE `producto` (
  `producto_id` int(11) NOT NULL,
  `codigo_sku` varchar(50) NOT NULL,
  `nombre` varchar(200) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `tipo_producto_id` int(11) NOT NULL,
  `medida_id` int(11) DEFAULT NULL,
  `precio_base` decimal(10,2) NOT NULL,
  `porcentaje_descuento` decimal(5,2) DEFAULT 0.00,
  `stock_minimo` int(11) DEFAULT 0,
  `duracion_anios` int(11) DEFAULT NULL,
  `cobertura_m2` decimal(8,2) DEFAULT NULL,
  `color` varchar(50) DEFAULT NULL,
  `proveedor_id` int(11) NOT NULL,
  `activo` tinyint(1) DEFAULT 1,
  `fecha_creacion` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `producto`
--

INSERT INTO `producto` (`producto_id`, `codigo_sku`, `nombre`, `descripcion`, `tipo_producto_id`, `medida_id`, `precio_base`, `porcentaje_descuento`, `stock_minimo`, `duracion_anios`, `cobertura_m2`, `color`, `proveedor_id`, `activo`, `fecha_creacion`) VALUES
(1, 'SKU001', 'Pintura Látex Interior Blanco', 'Pintura látex para interiores, acabado mate', 1, 1, 120.00, 10.00, 20, 5, 25.00, 'Blanco', 1, 1, '0000-00-00 00:00:00'),
(3, 'SKU002', 'Pintura Látex Exterior Gris', 'Pintura látex para exteriores, resistente a la intemperie', 1, 1, 150.00, 5.00, 5, 5, 25.00, 'Gris', 5, 1, '0000-00-00 00:00:00'),
(7, 'SKU005', 'rojo pol', 'Pintura roja con efecto perlado, cambia ligeramente de tono con la luz.', 2, 2, 25.50, 10.00, 23, 2, 12.00, 'rojo perlado', 1, 1, '2025-11-07 00:00:00'),
(8, 'SKU012', 'rodillo', 'rodillo para pintar', 4, 5, 23.00, 5.00, 25, 1, 5.00, 'blanco', 6, 1, '2025-11-05 00:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `proveedor`
--

CREATE TABLE `proveedor` (
  `proveedor_id` int(11) NOT NULL,
  `nombre_proveedor` varchar(100) NOT NULL,
  `contacto_proveedor` varchar(100) DEFAULT NULL,
  `telefono_proveedor` varchar(20) DEFAULT NULL,
  `direccion_proveedor` text DEFAULT NULL,
  `email_proveedor` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `proveedor`
--

INSERT INTO `proveedor` (`proveedor_id`, `nombre_proveedor`, `contacto_proveedor`, `telefono_proveedor`, `direccion_proveedor`, `email_proveedor`) VALUES
(1, 'Proveedor General Paints', 'Juan Pérez', '0000-0000', 'Dirección del proveedor principal', 'proveedor@paints.com'),
(4, 'Pinturas Casa Blanca', 'Juan Pérez', '555-1111', 'Avenida Central 123, Ciudad', 'contacto@casablanca.com'),
(5, 'ColorExpress', 'María López', '555-2222', 'Calle Principal 45, Ciudad', 'ventas@colorexpress.com'),
(6, 'Pinturas y Más', 'Carlos Gómez', '555-3333', 'Boulevard Sur 78, Ciudad', 'info@pinturaymas.com');

-- --------------------------------------------------------

--
-- Table structure for table `rol`
--

CREATE TABLE `rol` (
  `rol_id` int(11) NOT NULL,
  `nombre_rol` varchar(50) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `permisos` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`permisos`))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `rol`
--

INSERT INTO `rol` (`rol_id`, `nombre_rol`, `descripcion`, `permisos`) VALUES
(1, 'Administrador', 'Acceso completo al sistema', '{\"modulos\": [\"todos\"]}'),
(2, 'Gerente', 'Persona que podrá observar los reportes', '{\"modulos\": [\"reportes\", \"ventas\", \"inventario\"]}'),
(3, 'Digitador', 'Persona encargada de alimentar el sistema', '{\"modulos\": [\"productos\", \"clientes\", \"inventario\"]}'),
(4, 'Cajero', 'Persona que solo podrá cobrar', '{\"modulos\": [\"ventas\", \"pagos\"]}'),
(5, 'Cliente', 'Acceso al portal de clientes', '{\"modulos\": [\"compras_online\", \"cotizaciones\"]}');

-- --------------------------------------------------------

--
-- Table structure for table `sucursal`
--

CREATE TABLE `sucursal` (
  `sucursal_id` int(11) NOT NULL,
  `nombre_sucursal` varchar(100) NOT NULL,
  `direccion_sucursal` text NOT NULL,
  `telefono_sucursal` varchar(20) DEFAULT NULL,
  `latitud` decimal(10,8) DEFAULT NULL,
  `longitud` decimal(11,8) DEFAULT NULL,
  `activa` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sucursal`
--

INSERT INTO `sucursal` (`sucursal_id`, `nombre_sucursal`, `direccion_sucursal`, `telefono_sucursal`, `latitud`, `longitud`, `activa`) VALUES
(1, 'Pradera Chimaltenango', 'Centro Comercial Pradera, Chimaltenango', '7831-0101', 14.66100000, -90.81900000, 1),
(2, 'Pradera Escuintla', 'Centro Comercial Pradera, Escuintla', '7888-0202', 14.30000000, -90.78500000, 1),
(3, 'Las Américas Mazatenango', 'Centro Comercial Las Américas, Mazatenango', '7870-0303', 14.53330000, -91.50000000, 1),
(4, 'La Trinidad Coatepeque', 'Centro Comercial La Trinidad, Coatepeque', '7777-0404', 14.70000000, -91.86670000, 1),
(5, 'Pradera Xela', 'Centro Comercial Pradera, Quetzaltenango', '7761-0505', 14.83470000, -91.51810000, 1),
(6, 'Miraflores Ciudad Guatemala', 'Centro Comercial Miraflores, Ciudad de Guatemala', '2362-0606', 14.63490000, -90.50690000, 1);

-- --------------------------------------------------------

--
-- Table structure for table `tipo_pago`
--

CREATE TABLE `tipo_pago` (
  `tipo_pago_id` int(11) NOT NULL,
  `nombre_pago` varchar(50) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `activo` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tipo_pago`
--

INSERT INTO `tipo_pago` (`tipo_pago_id`, `nombre_pago`, `descripcion`, `activo`) VALUES
(1, 'Efectivo', 'Pago en efectivo', 1),
(2, 'Tarjeta Crédito', 'Pago con tarjeta de crédito', 1),
(3, 'Tarjeta Débito', 'Pago con tarjeta de débito', 1),
(4, 'Transferencia', 'Transferencia bancaria', 1),
(5, 'Cheque', 'Pago con cheque', 0);

-- --------------------------------------------------------

--
-- Table structure for table `tipo_producto`
--

CREATE TABLE `tipo_producto` (
  `tipo_producto_id` int(11) NOT NULL,
  `categoria_id` int(11) NOT NULL,
  `nombre_tipo_producto` varchar(100) NOT NULL,
  `descripcion` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tipo_producto`
--

INSERT INTO `tipo_producto` (`tipo_producto_id`, `categoria_id`, `nombre_tipo_producto`, `descripcion`) VALUES
(1, 1, 'Brochas', 'Brochas para pintura de diferentes tamaños'),
(2, 1, 'Rodillos', 'Rodillos y mangos para pintura'),
(3, 1, 'Bandejas', 'Bandejas para pintura'),
(4, 1, 'Herramientas', 'Espátulas, mantas, etc.'),
(5, 2, 'Aguarrás', 'Solvente aguarrás'),
(6, 2, 'Thinner', 'Solvente thinner'),
(7, 3, 'Pintura Latex', 'Pintura a base de agua'),
(8, 3, 'Pintura Esmalte', 'Pintura a base de aceite'),
(9, 4, 'Barniz Sintético', 'Barniz de base sintética'),
(10, 4, 'Barniz Acrílico', 'Barniz de base acrílica');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `usuario`
--

CREATE TABLE `usuario` (
  `usuario_id` int(11) NOT NULL,
  `username` varchar(50) DEFAULT NULL,
  `email` varchar(100) NOT NULL,
  `contrasena` varchar(255) NOT NULL,
  `rol_id` int(11) NOT NULL,
  `activo` tinyint(1) DEFAULT 1,
  `fecha_creacion` datetime DEFAULT current_timestamp(),
  `ultimo_login` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `usuario`
--

INSERT INTO `usuario` (`usuario_id`, `username`, `email`, `contrasena`, `rol_id`, `activo`, `fecha_creacion`, `ultimo_login`) VALUES
(1, 'admin', 'admin@paints.com', '12345', 1, 1, '2025-10-20 01:27:26', '2025-11-15 01:30:22'),
(3, 'castillo', 'castillo@gmail.com', '123', 5, 1, '2025-10-20 17:10:45', NULL),
(4, 'paints', 'paints@paints.com', '56', 2, 1, '2025-10-01 17:44:53', '2025-10-19 17:44:53'),
(5, 'juanperez', 'juanperez@gmail.com', '123', 1, 1, '2025-10-20 14:59:06', NULL),
(6, 'rocky', 'rocky@gmail.com', '2003', 5, 1, '2025-11-07 22:52:38', '2025-11-09 04:39:58');

-- --------------------------------------------------------

--
-- Stand-in structure for view `vw_facturas_completas`
-- (See below for the actual view)
--
CREATE TABLE `vw_facturas_completas` (
`factura_id` int(11)
,`numero_factura` varchar(50)
,`serie_factura` varchar(10)
,`nombre_cliente` varchar(100)
,`empleado_completo` varchar(201)
,`nombre_sucursal` varchar(100)
,`fecha` date
,`subtotal` decimal(10,2)
,`impuestos` decimal(10,2)
,`total` decimal(10,2)
,`estado` varchar(20)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `vw_inventario_completo`
-- (See below for the actual view)
--
CREATE TABLE `vw_inventario_completo` (
`inventario_id` int(11)
,`nombre_sucursal` varchar(100)
,`codigo_sku` varchar(50)
,`producto_nombre` varchar(200)
,`nombre_categoria` varchar(100)
,`cantidad` int(11)
,`stock_minimo` int(11)
,`ultima_actualizacion` datetime
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `vw_productos_completos`
-- (See below for the actual view)
--
CREATE TABLE `vw_productos_completos` (
`producto_id` int(11)
,`codigo_sku` varchar(50)
,`nombre` varchar(200)
,`descripcion` text
,`nombre_categoria` varchar(100)
,`nombre_tipo_producto` varchar(100)
,`nombre_medida` varchar(50)
,`precio_base` decimal(10,2)
,`porcentaje_descuento` decimal(5,2)
,`duracion_anios` int(11)
,`cobertura_m2` decimal(8,2)
,`color` varchar(50)
,`nombre_proveedor` varchar(100)
,`activo` tinyint(1)
);

-- --------------------------------------------------------

--
-- Structure for view `vw_facturas_completas`
--
DROP TABLE IF EXISTS `vw_facturas_completas`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `vw_facturas_completas`  AS SELECT `f`.`factura_id` AS `factura_id`, `f`.`numero_factura` AS `numero_factura`, `f`.`serie_factura` AS `serie_factura`, `c`.`nombre_cliente` AS `nombre_cliente`, concat(`e`.`nombre_empleado`,' ',`e`.`apellido_empleado`) AS `empleado_completo`, `s`.`nombre_sucursal` AS `nombre_sucursal`, `f`.`fecha` AS `fecha`, `f`.`subtotal` AS `subtotal`, `f`.`impuestos` AS `impuestos`, `f`.`total` AS `total`, `f`.`estado` AS `estado` FROM (((`factura` `f` join `cliente` `c` on(`f`.`cliente_id` = `c`.`cliente_id`)) join `empleado` `e` on(`f`.`empleado_id` = `e`.`empleado_id`)) join `sucursal` `s` on(`f`.`sucursal_id` = `s`.`sucursal_id`)) ;

-- --------------------------------------------------------

--
-- Structure for view `vw_inventario_completo`
--
DROP TABLE IF EXISTS `vw_inventario_completo`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `vw_inventario_completo`  AS SELECT `i`.`inventario_id` AS `inventario_id`, `s`.`nombre_sucursal` AS `nombre_sucursal`, `p`.`codigo_sku` AS `codigo_sku`, `p`.`nombre` AS `producto_nombre`, `cp`.`nombre_categoria` AS `nombre_categoria`, `i`.`cantidad` AS `cantidad`, `i`.`stock_minimo` AS `stock_minimo`, `i`.`ultima_actualizacion` AS `ultima_actualizacion` FROM ((((`inventario` `i` join `sucursal` `s` on(`i`.`sucursal_id` = `s`.`sucursal_id`)) join `producto` `p` on(`i`.`producto_id` = `p`.`producto_id`)) join `tipo_producto` `tp` on(`p`.`tipo_producto_id` = `tp`.`tipo_producto_id`)) join `categoria_producto` `cp` on(`tp`.`categoria_id` = `cp`.`categoria_id`)) ;

-- --------------------------------------------------------

--
-- Structure for view `vw_productos_completos`
--
DROP TABLE IF EXISTS `vw_productos_completos`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `vw_productos_completos`  AS SELECT `p`.`producto_id` AS `producto_id`, `p`.`codigo_sku` AS `codigo_sku`, `p`.`nombre` AS `nombre`, `p`.`descripcion` AS `descripcion`, `cp`.`nombre_categoria` AS `nombre_categoria`, `tp`.`nombre_tipo_producto` AS `nombre_tipo_producto`, `m`.`nombre_medida` AS `nombre_medida`, `p`.`precio_base` AS `precio_base`, `p`.`porcentaje_descuento` AS `porcentaje_descuento`, `p`.`duracion_anios` AS `duracion_anios`, `p`.`cobertura_m2` AS `cobertura_m2`, `p`.`color` AS `color`, `prov`.`nombre_proveedor` AS `nombre_proveedor`, `p`.`activo` AS `activo` FROM ((((`producto` `p` join `tipo_producto` `tp` on(`p`.`tipo_producto_id` = `tp`.`tipo_producto_id`)) join `categoria_producto` `cp` on(`tp`.`categoria_id` = `cp`.`categoria_id`)) left join `medida` `m` on(`p`.`medida_id` = `m`.`medida_id`)) join `proveedor` `prov` on(`p`.`proveedor_id` = `prov`.`proveedor_id`)) ;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `carrito_compras`
--
ALTER TABLE `carrito_compras`
  ADD PRIMARY KEY (`carrito_id`),
  ADD UNIQUE KEY `UK_carrito_cliente_producto` (`cliente_id`,`producto_id`),
  ADD KEY `producto_id` (`producto_id`);

--
-- Indexes for table `categoria_producto`
--
ALTER TABLE `categoria_producto`
  ADD PRIMARY KEY (`categoria_id`),
  ADD UNIQUE KEY `nombre_categoria` (`nombre_categoria`);

--
-- Indexes for table `cliente`
--
ALTER TABLE `cliente`
  ADD PRIMARY KEY (`cliente_id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `IX_cliente_usuario_id` (`usuario_id`),
  ADD KEY `IX_cliente_email` (`email`),
  ADD KEY `idx_cliente_nit` (`nit`);

--
-- Indexes for table `cotizacion`
--
ALTER TABLE `cotizacion`
  ADD PRIMARY KEY (`cotizacion_id`),
  ADD UNIQUE KEY `numero_cotizacion` (`numero_cotizacion`),
  ADD KEY `empleado_id` (`empleado_id`),
  ADD KEY `sucursal_id` (`sucursal_id`),
  ADD KEY `IX_cotizacion_numero_cotizacion` (`numero_cotizacion`),
  ADD KEY `IX_cotizacion_cliente_id` (`cliente_id`),
  ADD KEY `IX_cotizacion_fecha` (`fecha`);

--
-- Indexes for table `detalle_cotizacion`
--
ALTER TABLE `detalle_cotizacion`
  ADD PRIMARY KEY (`detalle_cotizacion_id`),
  ADD KEY `cotizacion_id` (`cotizacion_id`),
  ADD KEY `producto_id` (`producto_id`);

--
-- Indexes for table `detalle_factura`
--
ALTER TABLE `detalle_factura`
  ADD PRIMARY KEY (`detalle_factura_id`),
  ADD KEY `IX_detalle_factura_factura_id` (`factura_id`),
  ADD KEY `IX_detalle_factura_producto_id` (`producto_id`);

--
-- Indexes for table `empleado`
--
ALTER TABLE `empleado`
  ADD PRIMARY KEY (`empleado_id`),
  ADD KEY `IX_empleado_usuario_id` (`usuario_id`),
  ADD KEY `IX_empleado_sucursal_id` (`sucursal_id`);

--
-- Indexes for table `factura`
--
ALTER TABLE `factura`
  ADD PRIMARY KEY (`factura_id`),
  ADD UNIQUE KEY `numero_factura` (`numero_factura`),
  ADD KEY `sucursal_id` (`sucursal_id`),
  ADD KEY `IX_factura_numero_factura` (`numero_factura`),
  ADD KEY `IX_factura_cliente_id` (`cliente_id`),
  ADD KEY `IX_factura_empleado_id` (`empleado_id`),
  ADD KEY `IX_factura_fecha` (`fecha`),
  ADD KEY `IX_factura_estado` (`estado`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `historial_inventario`
--
ALTER TABLE `historial_inventario`
  ADD PRIMARY KEY (`historial_id`),
  ADD KEY `sucursal_id` (`sucursal_id`),
  ADD KEY `producto_id` (`producto_id`),
  ADD KEY `usuario_id` (`usuario_id`);

--
-- Indexes for table `inventario`
--
ALTER TABLE `inventario`
  ADD PRIMARY KEY (`inventario_id`),
  ADD UNIQUE KEY `UK_inventario_sucursal_producto` (`sucursal_id`,`producto_id`),
  ADD KEY `IX_inventario_sucursal_id` (`sucursal_id`),
  ADD KEY `IX_inventario_producto_id` (`producto_id`);

--
-- Indexes for table `medida`
--
ALTER TABLE `medida`
  ADD PRIMARY KEY (`medida_id`),
  ADD UNIQUE KEY `nombre_medida` (`nombre_medida`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `pago`
--
ALTER TABLE `pago`
  ADD PRIMARY KEY (`pago_id`),
  ADD KEY `IX_pago_factura_id` (`factura_id`),
  ADD KEY `IX_pago_tipo_pago_id` (`tipo_pago_id`);

--
-- Indexes for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  ADD KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`);

--
-- Indexes for table `producto`
--
ALTER TABLE `producto`
  ADD PRIMARY KEY (`producto_id`),
  ADD UNIQUE KEY `codigo_sku` (`codigo_sku`),
  ADD KEY `medida_id` (`medida_id`),
  ADD KEY `IX_producto_codigo_sku` (`codigo_sku`),
  ADD KEY `IX_producto_tipo_producto_id` (`tipo_producto_id`),
  ADD KEY `IX_producto_proveedor_id` (`proveedor_id`),
  ADD KEY `IX_producto_activo` (`activo`);

--
-- Indexes for table `proveedor`
--
ALTER TABLE `proveedor`
  ADD PRIMARY KEY (`proveedor_id`);

--
-- Indexes for table `rol`
--
ALTER TABLE `rol`
  ADD PRIMARY KEY (`rol_id`),
  ADD UNIQUE KEY `nombre_rol` (`nombre_rol`);

--
-- Indexes for table `sucursal`
--
ALTER TABLE `sucursal`
  ADD PRIMARY KEY (`sucursal_id`);

--
-- Indexes for table `tipo_pago`
--
ALTER TABLE `tipo_pago`
  ADD PRIMARY KEY (`tipo_pago_id`);

--
-- Indexes for table `tipo_producto`
--
ALTER TABLE `tipo_producto`
  ADD PRIMARY KEY (`tipo_producto_id`),
  ADD KEY `categoria_id` (`categoria_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- Indexes for table `usuario`
--
ALTER TABLE `usuario`
  ADD PRIMARY KEY (`usuario_id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `idx_usuario_username` (`username`),
  ADD KEY `IX_usuario_email` (`email`),
  ADD KEY `IX_usuario_rol_id` (`rol_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `carrito_compras`
--
ALTER TABLE `carrito_compras`
  MODIFY `carrito_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `categoria_producto`
--
ALTER TABLE `categoria_producto`
  MODIFY `categoria_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `cliente`
--
ALTER TABLE `cliente`
  MODIFY `cliente_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `cotizacion`
--
ALTER TABLE `cotizacion`
  MODIFY `cotizacion_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `detalle_cotizacion`
--
ALTER TABLE `detalle_cotizacion`
  MODIFY `detalle_cotizacion_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `detalle_factura`
--
ALTER TABLE `detalle_factura`
  MODIFY `detalle_factura_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `empleado`
--
ALTER TABLE `empleado`
  MODIFY `empleado_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `factura`
--
ALTER TABLE `factura`
  MODIFY `factura_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `historial_inventario`
--
ALTER TABLE `historial_inventario`
  MODIFY `historial_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `inventario`
--
ALTER TABLE `inventario`
  MODIFY `inventario_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `medida`
--
ALTER TABLE `medida`
  MODIFY `medida_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;

--
-- AUTO_INCREMENT for table `pago`
--
ALTER TABLE `pago`
  MODIFY `pago_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `producto`
--
ALTER TABLE `producto`
  MODIFY `producto_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `proveedor`
--
ALTER TABLE `proveedor`
  MODIFY `proveedor_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `rol`
--
ALTER TABLE `rol`
  MODIFY `rol_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `sucursal`
--
ALTER TABLE `sucursal`
  MODIFY `sucursal_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `tipo_pago`
--
ALTER TABLE `tipo_pago`
  MODIFY `tipo_pago_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `tipo_producto`
--
ALTER TABLE `tipo_producto`
  MODIFY `tipo_producto_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `usuario`
--
ALTER TABLE `usuario`
  MODIFY `usuario_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `carrito_compras`
--
ALTER TABLE `carrito_compras`
  ADD CONSTRAINT `carrito_compras_ibfk_1` FOREIGN KEY (`cliente_id`) REFERENCES `cliente` (`cliente_id`),
  ADD CONSTRAINT `carrito_compras_ibfk_2` FOREIGN KEY (`producto_id`) REFERENCES `producto` (`producto_id`);

--
-- Constraints for table `cliente`
--
ALTER TABLE `cliente`
  ADD CONSTRAINT `cliente_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuario` (`usuario_id`);

--
-- Constraints for table `cotizacion`
--
ALTER TABLE `cotizacion`
  ADD CONSTRAINT `cotizacion_ibfk_1` FOREIGN KEY (`cliente_id`) REFERENCES `cliente` (`cliente_id`),
  ADD CONSTRAINT `cotizacion_ibfk_2` FOREIGN KEY (`empleado_id`) REFERENCES `empleado` (`empleado_id`),
  ADD CONSTRAINT `cotizacion_ibfk_3` FOREIGN KEY (`sucursal_id`) REFERENCES `sucursal` (`sucursal_id`);

--
-- Constraints for table `detalle_cotizacion`
--
ALTER TABLE `detalle_cotizacion`
  ADD CONSTRAINT `detalle_cotizacion_ibfk_1` FOREIGN KEY (`cotizacion_id`) REFERENCES `cotizacion` (`cotizacion_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `detalle_cotizacion_ibfk_2` FOREIGN KEY (`producto_id`) REFERENCES `producto` (`producto_id`);

--
-- Constraints for table `detalle_factura`
--
ALTER TABLE `detalle_factura`
  ADD CONSTRAINT `detalle_factura_ibfk_1` FOREIGN KEY (`factura_id`) REFERENCES `factura` (`factura_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `detalle_factura_ibfk_2` FOREIGN KEY (`producto_id`) REFERENCES `producto` (`producto_id`);

--
-- Constraints for table `empleado`
--
ALTER TABLE `empleado`
  ADD CONSTRAINT `empleado_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuario` (`usuario_id`),
  ADD CONSTRAINT `empleado_ibfk_2` FOREIGN KEY (`sucursal_id`) REFERENCES `sucursal` (`sucursal_id`);

--
-- Constraints for table `factura`
--
ALTER TABLE `factura`
  ADD CONSTRAINT `factura_ibfk_1` FOREIGN KEY (`cliente_id`) REFERENCES `cliente` (`cliente_id`),
  ADD CONSTRAINT `factura_ibfk_2` FOREIGN KEY (`empleado_id`) REFERENCES `empleado` (`empleado_id`),
  ADD CONSTRAINT `factura_ibfk_3` FOREIGN KEY (`sucursal_id`) REFERENCES `sucursal` (`sucursal_id`);

--
-- Constraints for table `historial_inventario`
--
ALTER TABLE `historial_inventario`
  ADD CONSTRAINT `historial_inventario_ibfk_1` FOREIGN KEY (`sucursal_id`) REFERENCES `sucursal` (`sucursal_id`),
  ADD CONSTRAINT `historial_inventario_ibfk_2` FOREIGN KEY (`producto_id`) REFERENCES `producto` (`producto_id`),
  ADD CONSTRAINT `historial_inventario_ibfk_3` FOREIGN KEY (`usuario_id`) REFERENCES `usuario` (`usuario_id`);

--
-- Constraints for table `inventario`
--
ALTER TABLE `inventario`
  ADD CONSTRAINT `inventario_ibfk_1` FOREIGN KEY (`sucursal_id`) REFERENCES `sucursal` (`sucursal_id`),
  ADD CONSTRAINT `inventario_ibfk_2` FOREIGN KEY (`producto_id`) REFERENCES `producto` (`producto_id`);

--
-- Constraints for table `pago`
--
ALTER TABLE `pago`
  ADD CONSTRAINT `pago_ibfk_1` FOREIGN KEY (`factura_id`) REFERENCES `factura` (`factura_id`),
  ADD CONSTRAINT `pago_ibfk_2` FOREIGN KEY (`tipo_pago_id`) REFERENCES `tipo_pago` (`tipo_pago_id`);

--
-- Constraints for table `producto`
--
ALTER TABLE `producto`
  ADD CONSTRAINT `producto_ibfk_1` FOREIGN KEY (`tipo_producto_id`) REFERENCES `tipo_producto` (`tipo_producto_id`),
  ADD CONSTRAINT `producto_ibfk_2` FOREIGN KEY (`medida_id`) REFERENCES `medida` (`medida_id`),
  ADD CONSTRAINT `producto_ibfk_3` FOREIGN KEY (`proveedor_id`) REFERENCES `proveedor` (`proveedor_id`);

--
-- Constraints for table `tipo_producto`
--
ALTER TABLE `tipo_producto`
  ADD CONSTRAINT `tipo_producto_ibfk_1` FOREIGN KEY (`categoria_id`) REFERENCES `categoria_producto` (`categoria_id`);

--
-- Constraints for table `usuario`
--
ALTER TABLE `usuario`
  ADD CONSTRAINT `usuario_ibfk_1` FOREIGN KEY (`rol_id`) REFERENCES `rol` (`rol_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
