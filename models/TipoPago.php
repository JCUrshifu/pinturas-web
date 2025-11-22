<?php
class TipoPago {
    private $conn;
    private $table_name = "tipo_pago";

    public $tipo_pago_id;
    public $nombre_pago;
    public $descripcion;
    public $activo;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Obtener todos los tipos de pago
    public function getAll() {
        $query = "SELECT * FROM " . $this->table_name . " 
                  ORDER BY nombre_pago ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    // Obtener solo tipos de pago activos
    public function getActivos() {
        $query = "SELECT * FROM " . $this->table_name . " 
                  WHERE activo = 1 
                  ORDER BY nombre_pago ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    // Crear nuevo tipo de pago
    public function create($nombre, $descripcion, $activo) {
        $query = "INSERT INTO " . $this->table_name . " 
                  (nombre_pago, descripcion, activo) 
                  VALUES (:nombre, :descripcion, :activo)";
        
        $stmt = $this->conn->prepare($query);

        // Limpiar datos
        $nombre = htmlspecialchars(strip_tags($nombre));
        $descripcion = htmlspecialchars(strip_tags($descripcion));

        $stmt->bindParam(":nombre", $nombre);
        $stmt->bindParam(":descripcion", $descripcion);
        $stmt->bindParam(":activo", $activo);

        return $stmt->execute();
    }

    // Obtener tipo de pago por ID
    public function getById($tipo_pago_id) {
        $query = "SELECT * FROM " . $this->table_name . " 
                  WHERE tipo_pago_id = :tipo_pago_id LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":tipo_pago_id", $tipo_pago_id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Actualizar tipo de pago
    public function update($tipo_pago_id, $nombre, $descripcion, $activo) {
        $query = "UPDATE " . $this->table_name . " SET 
                    nombre_pago=:nombre,
                    descripcion=:descripcion,
                    activo=:activo
                  WHERE tipo_pago_id=:tipo_pago_id";

        $stmt = $this->conn->prepare($query);

        // Limpiar datos
        $nombre = htmlspecialchars(strip_tags($nombre));
        $descripcion = htmlspecialchars(strip_tags($descripcion));
        $tipo_pago_id = htmlspecialchars(strip_tags($tipo_pago_id));

        $stmt->bindParam(":nombre", $nombre);
        $stmt->bindParam(":descripcion", $descripcion);
        $stmt->bindParam(":activo", $activo);
        $stmt->bindParam(":tipo_pago_id", $tipo_pago_id);

        return $stmt->execute();
    }

    // Eliminar tipo de pago
    public function delete($tipo_pago_id) {
        // Verificar si tiene pagos asociados
        $query_check = "SELECT COUNT(*) as total FROM pago 
                        WHERE tipo_pago_id = :tipo_pago_id";
        $stmt_check = $this->conn->prepare($query_check);
        $stmt_check->bindParam(":tipo_pago_id", $tipo_pago_id);
        $stmt_check->execute();
        $result = $stmt_check->fetch(PDO::FETCH_ASSOC);
        
        if ($result['total'] > 0) {
            return ['success' => false, 'mensaje' => 'No se puede eliminar. Tiene transacciones asociadas.'];
        }

        $query = "DELETE FROM " . $this->table_name . " WHERE tipo_pago_id=:tipo_pago_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":tipo_pago_id", $tipo_pago_id);
        
        if ($stmt->execute()) {
            return ['success' => true, 'mensaje' => 'Tipo de pago eliminado correctamente'];
        }
        return ['success' => false, 'mensaje' => 'Error al eliminar tipo de pago'];
    }

    // Cambiar estado activo/inactivo
    public function cambiarEstado($tipo_pago_id, $activo) {
        $query = "UPDATE " . $this->table_name . " 
                  SET activo = :activo 
                  WHERE tipo_pago_id = :tipo_pago_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":activo", $activo);
        $stmt->bindParam(":tipo_pago_id", $tipo_pago_id);
        return $stmt->execute();
    }
}
?>