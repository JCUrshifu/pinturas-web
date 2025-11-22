<?php
class Cotizacion {
    private $conn;
    private $table = "cotizacion";

    public function __construct($db) {
        $this->conn = $db;
    }

    /**
     * Obtener todas las cotizaciones con información relacionada
     */
    public function obtenerCotizaciones() {
        $query = "SELECT c.*, 
                         cl.nombre_cliente, 
                         cl.nit,
                         CONCAT(e.nombre_empleado, ' ', e.apellido_empleado) as nombre_empleado,
                         s.nombre_sucursal
                  FROM cotizacion c 
                  INNER JOIN cliente cl ON c.cliente_id = cl.cliente_id 
                  LEFT JOIN empleado e ON c.empleado_id = e.empleado_id
                  LEFT JOIN sucursal s ON c.sucursal_id = s.sucursal_id
                  ORDER BY c.cotizacion_id DESC";
        
        $stmt = $this->conn->query($query);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtener una cotización específica con sus detalles
     */
    public function obtenerCotizacion($cotizacion_id) {
        $query = "SELECT c.*, 
                         cl.nombre_cliente,
                         cl.nit,
                         cl.email as email_cliente,
                         cl.telefono as telefono_cliente,
                         cl.direccion as direccion_cliente,
                         CONCAT(e.nombre_empleado, ' ', e.apellido_empleado) as nombre_empleado,
                         s.nombre_sucursal
                  FROM cotizacion c 
                  INNER JOIN cliente cl ON c.cliente_id = cl.cliente_id 
                  LEFT JOIN empleado e ON c.empleado_id = e.empleado_id
                  LEFT JOIN sucursal s ON c.sucursal_id = s.sucursal_id
                  WHERE c.cotizacion_id = ?";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$cotizacion_id]);
        $cotizacion = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($cotizacion) {
            // Obtener detalles
            $query_detalles = "SELECT dc.*, 
                                      p.nombre as nombre_producto,
                                      p.codigo_sku
                               FROM detalle_cotizacion dc 
                               INNER JOIN producto p ON dc.producto_id = p.producto_id 
                               WHERE dc.cotizacion_id = ?";
            
            $stmt2 = $this->conn->prepare($query_detalles);
            $stmt2->execute([$cotizacion_id]);
            $cotizacion['detalles'] = $stmt2->fetchAll(PDO::FETCH_ASSOC);
        }

        return $cotizacion;
    }

    /**
     * Crear nueva cotización
     */
    public function crearCotizacion($numero, $cliente_id, $empleado_id, $sucursal_id, $subtotal, $impuestos, $total, $valida_hasta) {
        $query = "INSERT INTO cotizacion 
                  (numero_cotizacion, cliente_id, empleado_id, sucursal_id, 
                   fecha, subtotal, impuestos, total, valida_hasta, estado, created_at) 
                  VALUES (?, ?, ?, ?, CURDATE(), ?, ?, ?, ?, 'PENDIENTE', NOW())";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$numero, $cliente_id, $empleado_id, $sucursal_id, $subtotal, $impuestos, $total, $valida_hasta]);
        
        return $this->conn->lastInsertId();
    }

    /**
     * Agregar detalle a cotización
     */
    public function agregarDetalle($cotizacion_id, $producto_id, $cantidad, $precio_unitario, $porcentaje_descuento) {
        $subtotal = $cantidad * $precio_unitario * (1 - $porcentaje_descuento / 100);
        
        $query = "INSERT INTO detalle_cotizacion 
                  (cotizacion_id, producto_id, cantidad, precio_unitario, porcentaje_descuento, subtotal) 
                  VALUES (?, ?, ?, ?, ?, ?)";
        
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$cotizacion_id, $producto_id, $cantidad, $precio_unitario, $porcentaje_descuento, $subtotal]);
    }

    /**
     * Actualizar estado de cotización
     */
    public function actualizarEstado($cotizacion_id, $estado) {
        $query = "UPDATE cotizacion 
                  SET estado = ? 
                  WHERE cotizacion_id = ?";
        
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$estado, $cotizacion_id]);
    }

    /**
     * Convertir cotización a factura
     * Retorna el ID de la factura creada
     */
    public function convertirAFactura($cotizacion_id) {
        // Esta función se implementará en el controller
        // para usar transacciones y crear la factura
        return false;
    }

    /**
     * Anular cotización
     */
    public function anularCotizacion($cotizacion_id) {
        $query = "UPDATE cotizacion 
                  SET estado = 'ANULADA', 
                      total = 0, 
                      subtotal = 0, 
                      impuestos = 0
                  WHERE cotizacion_id = ?";
        
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$cotizacion_id]);
    }
}
?>