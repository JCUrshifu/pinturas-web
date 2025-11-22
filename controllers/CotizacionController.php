<?php
require_once "models/Cotizacion.php";

class CotizacionController {
    private $cotizacion;
    private $db;

    public function __construct($db) {
        $this->db = $db;
        $this->cotizacion = new Cotizacion($db);
    }

    public function listarCotizaciones() {
        return $this->cotizacion->obtenerCotizaciones();
    }

    public function obtenerCotizacion($cotizacion_id) {
        return $this->cotizacion->obtenerCotizacion($cotizacion_id);
    }

    /**
     * Crear cotización con detalles (sin afectar inventario)
     */
    public function crearCotizacionConDetalles($datosCotizacion, $detalles) {
        try {
            // Iniciar transacción
            $this->db->beginTransaction();
            
            // 1. Generar número de cotización automático
            $stmt = $this->db->query("SELECT MAX(CAST(SUBSTRING(numero_cotizacion, 5) AS UNSIGNED)) as ultimo 
                                       FROM cotizacion 
                                       WHERE numero_cotizacion LIKE 'COT-%'");
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            $siguiente = ($resultado['ultimo'] ?? 0) + 1;
            $numero_cotizacion = 'COT-' . str_pad($siguiente, 6, '0', STR_PAD_LEFT);
            
            // 2. Calcular fecha de validez (15 días desde hoy)
            $valida_hasta = date('Y-m-d', strtotime('+15 days'));
            
            // 3. Crear cotización
            $cotizacion_id = $this->cotizacion->crearCotizacion(
                $numero_cotizacion,
                $datosCotizacion['cliente_id'],
                $datosCotizacion['empleado_id'] ?? null,
                $datosCotizacion['sucursal_id'] ?? null,
                $datosCotizacion['subtotal'],
                $datosCotizacion['impuestos'],
                $datosCotizacion['total'],
                $valida_hasta
            );

            if(!$cotizacion_id) {
                throw new Exception("Error al crear la cotización");
            }

            // 4. Agregar detalles (NO se descuenta inventario)
            foreach($detalles as $detalle){
                $this->cotizacion->agregarDetalle(
                    $cotizacion_id,
                    $detalle['producto_id'],
                    $detalle['cantidad'],
                    $detalle['precio_unitario'],
                    $detalle['porcentaje_descuento']
                );
            }
            
            // Confirmar transacción
            $this->db->commit();
            
            $_SESSION['mensaje'] = "Cotización creada exitosamente. Número: $numero_cotizacion";
            return $cotizacion_id;
            
        } catch (Exception $e) {
            // Revertir cambios si hay error
            $this->db->rollBack();
            $_SESSION['error'] = "Error al crear cotización: " . $e->getMessage();
            return false;
        }
    }

    /**
     * Convertir cotización a factura
     */
    public function convertirAFactura($cotizacion_id) {
        try {
            $this->db->beginTransaction();
            
            // 1. Obtener cotización
            $cotizacion = $this->cotizacion->obtenerCotizacion($cotizacion_id);
            
            if (!$cotizacion || $cotizacion['estado'] !== 'PENDIENTE') {
                throw new Exception("La cotización no está disponible para convertir");
            }
            
            // 2. Generar número de factura
            $stmt = $this->db->query("SELECT MAX(CAST(SUBSTRING(numero_factura, 5) AS UNSIGNED)) as ultimo 
                                       FROM factura 
                                       WHERE numero_factura LIKE 'FAC-%'");
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            $siguiente = ($resultado['ultimo'] ?? 0) + 1;
            $numero_factura = 'FAC-' . str_pad($siguiente, 6, '0', STR_PAD_LEFT);
            
            // 3. Crear factura
            $stmt = $this->db->prepare("INSERT INTO factura 
                                         (numero_factura, serie_factura, cliente_id, empleado_id, sucursal_id, 
                                          fecha, subtotal, impuestos, total, estado, created_at) 
                                         VALUES (?, 'A', ?, ?, ?, CURDATE(), ?, ?, ?, 'ACTIVA', NOW())");
            $stmt->execute([
                $numero_factura,
                $cotizacion['cliente_id'],
                $cotizacion['empleado_id'],
                $cotizacion['sucursal_id'],
                $cotizacion['subtotal'],
                $cotizacion['impuestos'],
                $cotizacion['total']
            ]);
            
            $factura_id = $this->db->lastInsertId();
            
            // 4. Copiar detalles y descontar inventario
            foreach($cotizacion['detalles'] as $detalle) {
                // Insertar detalle de factura
                $stmt = $this->db->prepare("INSERT INTO detalle_factura 
                                             (factura_id, producto_id, cantidad, precio_unitario, porcentaje_descuento, subtotal) 
                                             VALUES (?, ?, ?, ?, ?, ?)");
                $stmt->execute([
                    $factura_id,
                    $detalle['producto_id'],
                    $detalle['cantidad'],
                    $detalle['precio_unitario'],
                    $detalle['porcentaje_descuento'],
                    $detalle['subtotal']
                ]);
                
                // Descontar inventario
                $stmt = $this->db->prepare("UPDATE inventario 
                                             SET cantidad = cantidad - ? 
                                             WHERE producto_id = ? AND cantidad >= ?");
                $resultado = $stmt->execute([
                    $detalle['cantidad'], 
                    $detalle['producto_id'],
                    $detalle['cantidad']
                ]);
                
                if (!$resultado || $stmt->rowCount() === 0) {
                    throw new Exception("Stock insuficiente para el producto: " . $detalle['nombre_producto']);
                }
            }
            
            // 5. Actualizar estado de cotización
            $this->cotizacion->actualizarEstado($cotizacion_id, 'CONVERTIDA');
            
            $this->db->commit();
            
            $_SESSION['mensaje'] = "Cotización convertida a factura exitosamente. Factura: $numero_factura";
            return $factura_id;
            
        } catch (Exception $e) {
            $this->db->rollBack();
            $_SESSION['error'] = "Error al convertir cotización: " . $e->getMessage();
            return false;
        }
    }

    /**
     * Anular cotización
     */
    public function anularCotizacion($cotizacion_id) {
        try {
            if ($this->cotizacion->anularCotizacion($cotizacion_id)) {
                $_SESSION['mensaje'] = "Cotización anulada correctamente";
                return true;
            }
            return false;
        } catch (Exception $e) {
            $_SESSION['error'] = "Error al anular cotización: " . $e->getMessage();
            return false;
        }
    }
}
?>