<?php
class Empleado {
    private $conn;
    private $table_name = "empleado";

    public $empleado_id;
    public $usuario_id;
    public $nombre_empleado;
    public $apellido_empleado;
    public $sucursal_id;
    public $activo;
    public $fecha_contratacion;
    public $created_at;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Obtener todos los empleados
    public function getAll() {
        $query = "SELECT empleado_id, usuario_id, nombre_empleado, apellido_empleado, sucursal_id, activo, fecha_contratacion, created_at 
                  FROM " . $this->table_name;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    // Crear nuevo empleado
    public function create($usuario_id, $nombre_empleado, $apellido_empleado, $sucursal_id, $activo, $fecha_contratacion) {
        $query = "INSERT INTO " . $this->table_name . 
                 " (usuario_id, nombre_empleado, apellido_empleado, sucursal_id, activo, fecha_contratacion, created_at) 
                  VALUES (:usuario_id, :nombre_empleado, :apellido_empleado, :sucursal_id, :activo, :fecha_contratacion, NOW())";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":usuario_id", $usuario_id);
        $stmt->bindParam(":nombre_empleado", $nombre_empleado);
        $stmt->bindParam(":apellido_empleado", $apellido_empleado);
        $stmt->bindParam(":sucursal_id", $sucursal_id);
        $stmt->bindParam(":activo", $activo);
        $stmt->bindParam(":fecha_contratacion", $fecha_contratacion);

        return $stmt->execute();
    }

    // Obtener empleado por ID
    public function getById($empleado_id) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE empleado_id = :empleado_id LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":empleado_id", $empleado_id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Actualizar empleado
    public function update($empleado_id, $usuario_id, $nombre_empleado, $apellido_empleado, $sucursal_id, $activo, $fecha_contratacion) {
        $query = "UPDATE " . $this->table_name . " SET 
                  usuario_id=:usuario_id, nombre_empleado=:nombre_empleado, apellido_empleado=:apellido_empleado,
                  sucursal_id=:sucursal_id, activo=:activo, fecha_contratacion=:fecha_contratacion
                  WHERE empleado_id=:empleado_id";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":usuario_id", $usuario_id);
        $stmt->bindParam(":nombre_empleado", $nombre_empleado);
        $stmt->bindParam(":apellido_empleado", $apellido_empleado);
        $stmt->bindParam(":sucursal_id", $sucursal_id);
        $stmt->bindParam(":activo", $activo);
        $stmt->bindParam(":fecha_contratacion", $fecha_contratacion);
        $stmt->bindParam(":empleado_id", $empleado_id);

        return $stmt->execute();
    }

    // Eliminar empleado
    public function delete($empleado_id) {
        $query = "DELETE FROM " . $this->table_name . " WHERE empleado_id=:empleado_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":empleado_id", $empleado_id);
        return $stmt->execute();
    }
}
?>
