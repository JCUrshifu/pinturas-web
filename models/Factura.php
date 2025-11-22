<?php
class Factura {
    private $conn;
    private $table = "factura";

    public function __construct($db) {
        $this->conn = $db;
    }

    public function obtenerFacturas() {
        $query = "SELECT f.*, 
                         c.nombre_cliente, 
                         c.nit,
                         CONCAT(e.nombre_empleado, ' ', e.apellido_empleado) as nombre_empleado,
                         s.nombre_sucursal
                  FROM factura f 
                  INNER JOIN cliente c ON f.cliente_id = c.cliente_id 
                  INNER JOIN empleado e ON f.empleado_id = e.empleado_id
                  INNER JOIN sucursal s ON f.sucursal_id = s.sucursal_id
                  ORDER BY f.factura_id DESC";
        
        $stmt = $this->conn->query($query);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerFactura($factura_id) {
        $query = "SELECT f.*, 
                         c.nombre_cliente,
                         c.nit,
                         c.email as email_cliente,
                         c.telefono as telefono_cliente,
                         c.direccion as direccion_cliente,
                         CONCAT(e.nombre_empleado, ' ', e.apellido_empleado) as nombre_empleado,
                         s.nombre_sucursal
                  FROM factura f 
                  INNER JOIN cliente c ON f.cliente_id = c.cliente_id 
                  INNER JOIN empleado e ON f.empleado_id = e.empleado_id
                  INNER JOIN sucursal s ON f.sucursal_id = s.sucursal_id
                  WHERE f.factura_id = ?";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$factura_id]);
        $factura = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($factura) {
            // Obtener detalles
            $query_detalles = "SELECT df.*, 
                                      p.nombre as nombre_producto,
                                      p.codigo_sku
                               FROM detalle_factura df 
                               INNER JOIN producto p ON df.producto_id = p.producto_id 
                               WHERE df.factura_id = ?";
            
            $stmt2 = $this->conn->prepare($query_detalles);
            $stmt2->execute([$factura_id]);
            $factura['detalles'] = $stmt2->fetchAll(PDO::FETCH_ASSOC);
            
            // Obtener pagos
            $query_pagos = "SELECT p.*, tp.nombre_pago
                            FROM pago p
                            INNER JOIN tipo_pago tp ON p.tipo_pago_id = tp.tipo_pago_id
                            WHERE p.factura_id = ?";
            
            $stmt3 = $this->conn->prepare($query_pagos);
            $stmt3->execute([$factura_id]);
            $factura['pagos'] = $stmt3->fetchAll(PDO::FETCH_ASSOC);
        }

        return $factura;
    }

    public function crearFactura($numero, $serie, $cliente_id, $empleado_id, $sucursal_id, $subtotal, $impuestos, $total) {
        $query = "INSERT INTO factura 
                  (numero_factura, serie_factura, cliente_id, empleado_id, sucursal_id, 
                   fecha, subtotal, impuestos, total, estado, created_at) 
                  VALUES (?, ?, ?, ?, ?, CURDATE(), ?, ?, ?, 'ACTIVA', NOW())";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$numero, $serie, $cliente_id, $empleado_id, $sucursal_id, $subtotal, $impuestos, $total]);
        
        return $this->conn->lastInsertId();
    }

    public function agregarDetalle($factura_id, $producto_id, $cantidad, $precio_unitario, $porcentaje_descuento) {
        $subtotal = $cantidad * $precio_unitario * (1 - $porcentaje_descuento / 100);
        
        $query = "INSERT INTO detalle_factura 
                  (factura_id, producto_id, cantidad, precio_unitario, porcentaje_descuento, subtotal) 
                  VALUES (?, ?, ?, ?, ?, ?)";
        
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$factura_id, $producto_id, $cantidad, $precio_unitario, $porcentaje_descuento, $subtotal]);
    }
    
    public function anularFactura($factura_id) {
        $query = "UPDATE factura 
                  SET estado = 'ANULADA', 
                      total = 0, 
                      subtotal = 0, 
                      impuestos = 0,
                      fecha_anulacion = NOW()
                  WHERE factura_id = ?";
        
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$factura_id]);
    }
}
?>