<?php
require_once "models/Factura.php";

class FacturaController {
    private $factura;
    private $db;

    public function __construct($db) {
        $this->db = $db;
        $this->factura = new Factura($db);
    }

    public function listarFacturas() {
        return $this->factura->obtenerFacturas();
    }

    public function obtenerFactura($factura_id) {
        return $this->factura->obtenerFactura($factura_id);
    }

    public function crearFacturaConDetalles($datosFactura, $detalles) {
        try {
            // Iniciar transacción
            $this->db->beginTransaction();
            
            // 1. Crear factura
            $factura_id = $this->factura->crearFactura(
                $datosFactura['numero_factura'],
                $datosFactura['serie_factura'],
                $datosFactura['cliente_id'],
                $datosFactura['empleado_id'],
                $datosFactura['sucursal_id'],
                $datosFactura['subtotal'],
                $datosFactura['impuestos'],
                $datosFactura['total']
            );

            if(!$factura_id) {
                throw new Exception("Error al crear la factura");
            }

            // 2. Agregar detalles y actualizar inventario
            foreach($detalles as $detalle){
                $this->factura->agregarDetalle(
                    $factura_id,
                    $detalle['producto_id'],
                    $detalle['cantidad'],
                    $detalle['precio_unitario'],
                    $detalle['porcentaje_descuento']
                );
                
                // Descontar del inventario
                $stmt = $this->db->prepare("UPDATE inventario 
                                             SET cantidad = cantidad - ? 
                                             WHERE producto_id = ? AND cantidad >= ?");
                $resultado = $stmt->execute([
                    $detalle['cantidad'], 
                    $detalle['producto_id'],
                    $detalle['cantidad']
                ]);
                
                if (!$resultado || $stmt->rowCount() === 0) {
                    throw new Exception("Stock insuficiente para el producto ID: " . $detalle['producto_id']);
                }
                
                // Registrar movimiento en historial
                $stmt = $this->db->prepare("INSERT INTO historial_inventario 
                                             (producto_id, sucursal_id, tipo_movimiento, cantidad_anterior, cantidad_nueva, referencia_id, usuario_id)
                                             SELECT producto_id, sucursal_id, 'VENTA', cantidad + ?, cantidad, ?, ?
                                             FROM inventario 
                                             WHERE producto_id = ? 
                                             LIMIT 1");
                $stmt->execute([
                    $detalle['cantidad'],
                    $factura_id,
                    $_SESSION['usuario_id'] ?? 1,
                    $detalle['producto_id']
                ]);
            }
            
            // 3. Registrar pagos
            if (!empty($_POST['efectivo']) && $_POST['efectivo'] > 0) {
                $this->registrarPago($factura_id, 1, $_POST['efectivo']); // 1 = Efectivo
            }
            if (!empty($_POST['tarjeta']) && $_POST['tarjeta'] > 0) {
                $this->registrarPago($factura_id, 2, $_POST['tarjeta']); // 2 = Tarjeta
            }
            if (!empty($_POST['cheque']) && $_POST['cheque'] > 0) {
                $this->registrarPago($factura_id, 3, $_POST['cheque']); // 3 = Cheque
            }
            
            // Confirmar transacción
            $this->db->commit();
            
            $_SESSION['mensaje'] = "Factura creada exitosamente. ID: $factura_id";
            return $factura_id;
            
        } catch (Exception $e) {
            // Revertir cambios si hay error
            $this->db->rollBack();
            $_SESSION['error'] = "Error al crear factura: " . $e->getMessage();
            return false;
        }
    }
    
    private function registrarPago($factura_id, $tipo_pago_id, $monto) {
        $stmt = $this->db->prepare("INSERT INTO pago (factura_id, tipo_pago_id, monto, fecha_pago) 
                                     VALUES (?, ?, ?, NOW())");
        return $stmt->execute([$factura_id, $tipo_pago_id, $monto]);
    }
    
    public function anularFactura($factura_id) {
        try {
            $this->db->beginTransaction();
            
            // 1. Obtener detalles de la factura para restaurar inventario
            $stmt = $this->db->prepare("SELECT producto_id, cantidad 
                                         FROM detalle_factura 
                                         WHERE factura_id = ?");
            $stmt->execute([$factura_id]);
            $detalles = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // 2. Restaurar inventario
            foreach ($detalles as $detalle) {
                $stmt = $this->db->prepare("UPDATE inventario 
                                             SET cantidad = cantidad + ? 
                                             WHERE producto_id = ?");
                $stmt->execute([$detalle['cantidad'], $detalle['producto_id']]);
                
                // Registrar en historial
                $stmt = $this->db->prepare("INSERT INTO historial_inventario 
                                             (producto_id, sucursal_id, tipo_movimiento, cantidad_anterior, cantidad_nueva, referencia_id, usuario_id)
                                             SELECT producto_id, sucursal_id, 'ANULACION', cantidad - ?, cantidad, ?, ?
                                             FROM inventario 
                                             WHERE producto_id = ? 
                                             LIMIT 1");
                $stmt->execute([
                    $detalle['cantidad'],
                    $factura_id,
                    $_SESSION['usuario_id'] ?? 1,
                    $detalle['producto_id']
                ]);
            }
            
            // 3. Anular factura (poner totales en 0 y cambiar estado)
            $stmt = $this->db->prepare("UPDATE factura SET 
                                         estado = 'ANULADA', 
                                         total = 0, 
                                         subtotal = 0, 
                                         impuestos = 0,
                                         fecha_anulacion = NOW()
                                         WHERE factura_id = ?");
            $stmt->execute([$factura_id]);
            
            $this->db->commit();
            
            $_SESSION['mensaje'] = "Factura anulada correctamente";
            return true;
            
        } catch (Exception $e) {
            $this->db->rollBack();
            $_SESSION['error'] = "Error al anular factura: " . $e->getMessage();
            return false;
        }
    }
}
?>