<?php
class Sucursal {
    private $conn;
    private $table_name = "sucursal";

    public $sucursal_id;
    public $nombre_sucursal;
    public $direccion_sucursal;
    public $telefono_sucursal;
    public $latitud;
    public $longitud;
    public $activa;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Obtener todas las sucursales
    public function getAll() {
        $query = "SELECT * FROM " . $this->table_name . " 
                  ORDER BY nombre_sucursal ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    // Obtener solo sucursales activas
    public function getActivas() {
        $query = "SELECT * FROM " . $this->table_name . " 
                  WHERE activa = 1 
                  ORDER BY nombre_sucursal ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    // Crear nueva sucursal
    public function create($nombre, $direccion, $telefono, $latitud, $longitud, $activa) {
        $query = "INSERT INTO " . $this->table_name . " 
                  (nombre_sucursal, direccion_sucursal, telefono_sucursal, 
                   latitud, longitud, activa) 
                  VALUES (:nombre, :direccion, :telefono, :latitud, :longitud, :activa)";
        
        $stmt = $this->conn->prepare($query);

        // Limpiar datos
        $nombre = htmlspecialchars(strip_tags($nombre));
        $direccion = htmlspecialchars(strip_tags($direccion));
        $telefono = htmlspecialchars(strip_tags($telefono));

        $stmt->bindParam(":nombre", $nombre);
        $stmt->bindParam(":direccion", $direccion);
        $stmt->bindParam(":telefono", $telefono);
        $stmt->bindParam(":latitud", $latitud);
        $stmt->bindParam(":longitud", $longitud);
        $stmt->bindParam(":activa", $activa);

        return $stmt->execute();
    }

    // Obtener sucursal por ID
    public function getById($sucursal_id) {
        $query = "SELECT * FROM " . $this->table_name . " 
                  WHERE sucursal_id = :sucursal_id LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":sucursal_id", $sucursal_id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Actualizar sucursal
    public function update($sucursal_id, $nombre, $direccion, $telefono, $latitud, $longitud, $activa) {
        $query = "UPDATE " . $this->table_name . " SET 
                    nombre_sucursal=:nombre,
                    direccion_sucursal=:direccion,
                    telefono_sucursal=:telefono,
                    latitud=:latitud,
                    longitud=:longitud,
                    activa=:activa
                  WHERE sucursal_id=:sucursal_id";

        $stmt = $this->conn->prepare($query);

        // Limpiar datos
        $nombre = htmlspecialchars(strip_tags($nombre));
        $direccion = htmlspecialchars(strip_tags($direccion));
        $telefono = htmlspecialchars(strip_tags($telefono));
        $sucursal_id = htmlspecialchars(strip_tags($sucursal_id));

        $stmt->bindParam(":nombre", $nombre);
        $stmt->bindParam(":direccion", $direccion);
        $stmt->bindParam(":telefono", $telefono);
        $stmt->bindParam(":latitud", $latitud);
        $stmt->bindParam(":longitud", $longitud);
        $stmt->bindParam(":activa", $activa);
        $stmt->bindParam(":sucursal_id", $sucursal_id);

        return $stmt->execute();
    }

    // Eliminar sucursal
    public function delete($sucursal_id) {
        // Verificar si tiene empleados o inventario asociados
        $query_check = "SELECT 
                        (SELECT COUNT(*) FROM empleado WHERE sucursal_id = :id1) as empleados,
                        (SELECT COUNT(*) FROM inventario WHERE sucursal_id = :id2) as inventario";
        $stmt_check = $this->conn->prepare($query_check);
        $stmt_check->bindParam(":id1", $sucursal_id);
        $stmt_check->bindParam(":id2", $sucursal_id);
        $stmt_check->execute();
        $result = $stmt_check->fetch(PDO::FETCH_ASSOC);
        
        if ($result['empleados'] > 0 || $result['inventario'] > 0) {
            return ['success' => false, 'mensaje' => 'No se puede eliminar. Tiene empleados o inventario asociado.'];
        }

        $query = "DELETE FROM " . $this->table_name . " WHERE sucursal_id=:sucursal_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":sucursal_id", $sucursal_id);
        
        if ($stmt->execute()) {
            return ['success' => true, 'mensaje' => 'Sucursal eliminada correctamente'];
        }
        return ['success' => false, 'mensaje' => 'Error al eliminar sucursal'];
    }

    // Cambiar estado activo/inactivo
    public function cambiarEstado($sucursal_id, $activa) {
        $query = "UPDATE " . $this->table_name . " 
                  SET activa = :activa 
                  WHERE sucursal_id = :sucursal_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":activa", $activa);
        $stmt->bindParam(":sucursal_id", $sucursal_id);
        return $stmt->execute();
    }
}
?>