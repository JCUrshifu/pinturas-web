<?php
class ReporteController {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    /**
     * REPORTE 1: Total facturado entre dos fechas por tipo de pago
     */
    public function reporteVentasPorTipoPago($fecha_inicio, $fecha_fin) {
        $query = "SELECT 
                    SUM(f.total) as total_general,
                    COALESCE(SUM(CASE WHEN tp.nombre_pago = 'Efectivo' THEN p.monto ELSE 0 END), 0) as total_efectivo,
                    COALESCE(SUM(CASE WHEN tp.nombre_pago = 'Tarjeta' THEN p.monto ELSE 0 END), 0) as total_tarjeta,
                    COALESCE(SUM(CASE WHEN tp.nombre_pago = 'Cheque' THEN p.monto ELSE 0 END), 0) as total_cheque
                  FROM factura f
                  LEFT JOIN pago p ON f.factura_id = p.factura_id
                  LEFT JOIN tipo_pago tp ON p.tipo_pago_id = tp.tipo_pago_id
                  WHERE f.fecha BETWEEN :fecha_inicio AND :fecha_fin
                    AND f.estado = 'ACTIVA'";
        
        $stmt = $this->db->prepare($query);
        $stmt->execute([
            ':fecha_inicio' => $fecha_inicio,
            ':fecha_fin' => $fecha_fin
        ]);
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * REPORTE 2: Productos que más dinero generan entre dos fechas
     */
    public function reporteProductosMayorIngreso($fecha_inicio, $fecha_fin, $limite = 10) {
        $query = "SELECT 
                    p.producto_id,
                    p.nombre,
                    p.codigo_sku,
                    SUM(df.subtotal) as total_generado,
                    SUM(df.cantidad) as cantidad_vendida
                  FROM detalle_factura df
                  INNER JOIN producto p ON df.producto_id = p.producto_id
                  INNER JOIN factura f ON df.factura_id = f.factura_id
                  WHERE f.fecha BETWEEN :fecha_inicio AND :fecha_fin
                    AND f.estado = 'ACTIVA'
                  GROUP BY p.producto_id, p.nombre, p.codigo_sku
                  ORDER BY total_generado DESC
                  LIMIT :limite";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':fecha_inicio', $fecha_inicio);
        $stmt->bindParam(':fecha_fin', $fecha_fin);
        $stmt->bindValue(':limite', $limite, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * REPORTE 3: Productos más vendidos en cantidad entre dos fechas
     */
    public function reporteProductosMasVendidos($fecha_inicio, $fecha_fin, $limite = 10) {
        $query = "SELECT 
                    p.producto_id,
                    p.nombre,
                    p.codigo_sku,
                    SUM(df.cantidad) as cantidad_vendida,
                    SUM(df.subtotal) as total_generado,
                    m.nombre_medida
                  FROM detalle_factura df
                  INNER JOIN producto p ON df.producto_id = p.producto_id
                  INNER JOIN factura f ON df.factura_id = f.factura_id
                  LEFT JOIN medida m ON p.medida_id = m.medida_id
                  WHERE f.fecha BETWEEN :fecha_inicio AND :fecha_fin
                    AND f.estado = 'ACTIVA'
                  GROUP BY p.producto_id, p.nombre, p.codigo_sku, m.nombre_medida
                  ORDER BY cantidad_vendida DESC
                  LIMIT :limite";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':fecha_inicio', $fecha_inicio);
        $stmt->bindParam(':fecha_fin', $fecha_fin);
        $stmt->bindValue(':limite', $limite, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * REPORTE 4: Inventario actual de todos los productos
     */
    public function reporteInventarioActual() {
        $query = "SELECT 
                    p.producto_id,
                    p.codigo_sku,
                    p.nombre,
                    p.precio_base,
                    s.nombre_sucursal,
                    COALESCE(i.cantidad, 0) as cantidad_disponible,
                    p.stock_minimo,
                    CASE 
                        WHEN COALESCE(i.cantidad, 0) <= p.stock_minimo THEN 'CRÍTICO'
                        WHEN COALESCE(i.cantidad, 0) <= p.stock_minimo * 2 THEN 'BAJO'
                        ELSE 'NORMAL'
                    END as estado_stock
                  FROM producto p
                  LEFT JOIN inventario i ON p.producto_id = i.producto_id
                  LEFT JOIN sucursal s ON i.sucursal_id = s.sucursal_id
                  WHERE p.activo = 1
                  ORDER BY p.nombre, s.nombre_sucursal";
        
        $stmt = $this->db->query($query);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * REPORTE 5: Productos que menos ventas registran
     */
    public function reporteProductosMenosVendidos($fecha_inicio, $fecha_fin, $limite = 10) {
        $query = "SELECT 
                    p.producto_id,
                    p.nombre,
                    p.codigo_sku,
                    COALESCE(SUM(df.cantidad), 0) as cantidad_vendida,
                    COALESCE(SUM(df.subtotal), 0) as total_generado
                  FROM producto p
                  LEFT JOIN detalle_factura df ON p.producto_id = df.producto_id
                  LEFT JOIN factura f ON df.factura_id = f.factura_id 
                    AND f.fecha BETWEEN :fecha_inicio AND :fecha_fin
                    AND f.estado = 'ACTIVA'
                  WHERE p.activo = 1
                  GROUP BY p.producto_id, p.nombre, p.codigo_sku
                  ORDER BY cantidad_vendida ASC, total_generado ASC
                  LIMIT :limite";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':fecha_inicio', $fecha_inicio);
        $stmt->bindParam(':fecha_fin', $fecha_fin);
        $stmt->bindValue(':limite', $limite, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * REPORTE 6: Productos sin stock que necesitan reabastecimiento
     */
    public function reporteProductosSinStock() {
        $query = "SELECT 
                    p.producto_id,
                    p.codigo_sku,
                    p.nombre,
                    p.stock_minimo,
                    s.nombre_sucursal,
                    COALESCE(i.cantidad, 0) as cantidad_actual,
                    pr.nombre_proveedor,
                    pr.telefono_proveedor,
                    pr.email_proveedor
                  FROM producto p
                  LEFT JOIN inventario i ON p.producto_id = i.producto_id
                  LEFT JOIN sucursal s ON i.sucursal_id = s.sucursal_id
                  LEFT JOIN proveedor pr ON p.proveedor_id = pr.proveedor_id
                  WHERE p.activo = 1 
                    AND (i.cantidad IS NULL OR i.cantidad <= 0 OR i.cantidad <= p.stock_minimo)
                  ORDER BY s.nombre_sucursal, p.nombre";
        
        $stmt = $this->db->query($query);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * REPORTE 7: Buscar factura por número y mostrar detalle completo
     */
    public function reporteDetalleFactura($numero_factura) {
        // Obtener información de la factura
        $query = "SELECT 
                    f.*,
                    c.nombre_cliente,
                    c.nit,
                    c.email as email_cliente,
                    c.telefono as telefono_cliente,
                    c.direccion as direccion_cliente,
                    CONCAT(e.nombre_empleado, ' ', e.apellido_empleado) as nombre_empleado,
                    s.nombre_sucursal,
                    s.direccion_sucursal
                  FROM factura f
                  INNER JOIN cliente c ON f.cliente_id = c.cliente_id
                  INNER JOIN empleado e ON f.empleado_id = e.empleado_id
                  INNER JOIN sucursal s ON f.sucursal_id = s.sucursal_id
                  WHERE f.numero_factura = :numero_factura
                  LIMIT 1";
        
        $stmt = $this->db->prepare($query);
        $stmt->execute([':numero_factura' => $numero_factura]);
        $factura = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$factura) {
            return null;
        }
        
        // Obtener detalles de productos
        $query_detalles = "SELECT 
                            df.*,
                            p.nombre as nombre_producto,
                            p.codigo_sku
                          FROM detalle_factura df
                          INNER JOIN producto p ON df.producto_id = p.producto_id
                          WHERE df.factura_id = :factura_id";
        
        $stmt_detalles = $this->db->prepare($query_detalles);
        $stmt_detalles->execute([':factura_id' => $factura['factura_id']]);
        $factura['detalles'] = $stmt_detalles->fetchAll(PDO::FETCH_ASSOC);
        
        // Obtener medios de pago
        $query_pagos = "SELECT 
                          p.monto,
                          tp.nombre_pago
                        FROM pago p
                        INNER JOIN tipo_pago tp ON p.tipo_pago_id = tp.tipo_pago_id
                        WHERE p.factura_id = :factura_id";
        
        $stmt_pagos = $this->db->prepare($query_pagos);
        $stmt_pagos->execute([':factura_id' => $factura['factura_id']]);
        $factura['pagos'] = $stmt_pagos->fetchAll(PDO::FETCH_ASSOC);
        
        return $factura;
    }

    /**
     * Reporte adicional: Resumen de ventas por sucursal
     */
    public function reporteVentasPorSucursal($fecha_inicio, $fecha_fin) {
        $query = "SELECT 
                    s.nombre_sucursal,
                    COUNT(f.factura_id) as total_facturas,
                    SUM(f.total) as total_ventas,
                    AVG(f.total) as promedio_venta
                  FROM factura f
                  INNER JOIN sucursal s ON f.sucursal_id = s.sucursal_id
                  WHERE f.fecha BETWEEN :fecha_inicio AND :fecha_fin
                    AND f.estado = 'ACTIVA'
                  GROUP BY s.sucursal_id, s.nombre_sucursal
                  ORDER BY total_ventas DESC";
        
        $stmt = $this->db->prepare($query);
        $stmt->execute([
            ':fecha_inicio' => $fecha_inicio,
            ':fecha_fin' => $fecha_fin
        ]);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Reporte adicional: Top clientes por compras
     */
    public function reporteTopClientes($fecha_inicio, $fecha_fin, $limite = 10) {
        $query = "SELECT 
                    c.cliente_id,
                    c.nombre_cliente,
                    c.nit,
                    c.email,
                    COUNT(f.factura_id) as total_compras,
                    SUM(f.total) as total_gastado,
                    AVG(f.total) as promedio_compra
                  FROM cliente c
                  INNER JOIN factura f ON c.cliente_id = f.cliente_id
                  WHERE f.fecha BETWEEN :fecha_inicio AND :fecha_fin
                    AND f.estado = 'ACTIVA'
                  GROUP BY c.cliente_id, c.nombre_cliente, c.nit, c.email
                  ORDER BY total_gastado DESC
                  LIMIT :limite";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':fecha_inicio', $fecha_inicio);
        $stmt->bindParam(':fecha_fin', $fecha_fin);
        $stmt->bindValue(':limite', $limite, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>