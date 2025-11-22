<?php
class Producto {
    private $conn;
    private $table_name = "producto";

    public $producto_id;
    public $codigo_sku;
    public $nombre;
    public $descripcion;
    public $tipo_producto_id;
    public $medida_id;
    public $precio_base;
    public $porcentaje_descuento;
    public $stock_minimo;
    public $duracion_anios;
    public $cobertura_m2;
    public $color;
    public $proveedor_id;
    public $activo;
    public $fecha_creacion;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getAll() {
        $query = "SELECT * FROM " . $this->table_name . " ORDER BY producto_id DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    public function getById($id) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE producto_id = :id LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create($data) {
        $query = "INSERT INTO " . $this->table_name . " 
            (codigo_sku, nombre, descripcion, tipo_producto_id, medida_id, precio_base, porcentaje_descuento, stock_minimo, duracion_anios, cobertura_m2, color, proveedor_id, activo, fecha_creacion) 
            VALUES 
            (:codigo_sku, :nombre, :descripcion, :tipo_producto_id, :medida_id, :precio_base, :porcentaje_descuento, :stock_minimo, :duracion_anios, :cobertura_m2, :color, :proveedor_id, :activo, :fecha_creacion)";
        
        $stmt = $this->conn->prepare($query);

        foreach ($data as $key => $value) {
            $stmt->bindValue(":$key", $value);
        }

        return $stmt->execute();
    }

    public function update($id, $data) {
        $query = "UPDATE " . $this->table_name . " SET 
            codigo_sku=:codigo_sku, nombre=:nombre, descripcion=:descripcion, tipo_producto_id=:tipo_producto_id, 
            medida_id=:medida_id, precio_base=:precio_base, porcentaje_descuento=:porcentaje_descuento, stock_minimo=:stock_minimo, 
            duracion_anios=:duracion_anios, cobertura_m2=:cobertura_m2, color=:color, proveedor_id=:proveedor_id, 
            activo=:activo, fecha_creacion=:fecha_creacion
            WHERE producto_id=:id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);

        foreach ($data as $key => $value) {
            $stmt->bindValue(":$key", $value);
        }

        return $stmt->execute();
    }

    public function delete($id) {
        $query = "DELETE FROM " . $this->table_name . " WHERE producto_id=:id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        return $stmt->execute();
    }
}
?>
