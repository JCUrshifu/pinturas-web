<?php
class Proveedor {
    private $conn;
    private $table_name = "proveedor";

    public $proveedor_id;
    public $nombre_proveedor;
    public $contacto_proveedor;
    public $telefono_proveedor;
    public $email_proveedor;
    public $direccion_proveedor;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Obtener todos los proveedores
    public function getAll() {
        $query = "SELECT * FROM " . $this->table_name . " 
                  ORDER BY nombre_proveedor ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    // Crear nuevo proveedor
    public function create($nombre, $contacto, $telefono, $email, $direccion) {
        $query = "INSERT INTO " . $this->table_name . " 
                  (nombre_proveedor, contacto_proveedor, telefono_proveedor, 
                   email_proveedor, direccion_proveedor) 
                  VALUES (:nombre, :contacto, :telefono, :email, :direccion)";
        
        $stmt = $this->conn->prepare($query);

        // Limpiar datos
        $nombre = htmlspecialchars(strip_tags($nombre));
        $contacto = htmlspecialchars(strip_tags($contacto));
        $telefono = htmlspecialchars(strip_tags($telefono));
        $email = htmlspecialchars(strip_tags($email));
        $direccion = htmlspecialchars(strip_tags($direccion));

        $stmt->bindParam(":nombre", $nombre);
        $stmt->bindParam(":contacto", $contacto);
        $stmt->bindParam(":telefono", $telefono);
        $stmt->bindParam(":email", $email);
        $stmt->bindParam(":direccion", $direccion);

        return $stmt->execute();
    }

    // Obtener proveedor por ID
    public function getById($proveedor_id) {
        $query = "SELECT * FROM " . $this->table_name . " 
                  WHERE proveedor_id = :proveedor_id LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":proveedor_id", $proveedor_id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Actualizar proveedor
    public function update($proveedor_id, $nombre, $contacto, $telefono, $email, $direccion) {
        $query = "UPDATE " . $this->table_name . " SET 
                    nombre_proveedor=:nombre,
                    contacto_proveedor=:contacto,
                    telefono_proveedor=:telefono,
                    email_proveedor=:email,
                    direccion_proveedor=:direccion
                  WHERE proveedor_id=:proveedor_id";

        $stmt = $this->conn->prepare($query);

        // Limpiar datos
        $nombre = htmlspecialchars(strip_tags($nombre));
        $contacto = htmlspecialchars(strip_tags($contacto));
        $telefono = htmlspecialchars(strip_tags($telefono));
        $email = htmlspecialchars(strip_tags($email));
        $direccion = htmlspecialchars(strip_tags($direccion));
        $proveedor_id = htmlspecialchars(strip_tags($proveedor_id));

        $stmt->bindParam(":nombre", $nombre);
        $stmt->bindParam(":contacto", $contacto);
        $stmt->bindParam(":telefono", $telefono);
        $stmt->bindParam(":email", $email);
        $stmt->bindParam(":direccion", $direccion);
        $stmt->bindParam(":proveedor_id", $proveedor_id);

        return $stmt->execute();
    }

    // Eliminar proveedor
    public function delete($proveedor_id) {
        // Verificar si tiene productos asociados
        $query_check = "SELECT COUNT(*) as total FROM producto 
                        WHERE proveedor_id = :proveedor_id";
        $stmt_check = $this->conn->prepare($query_check);
        $stmt_check->bindParam(":proveedor_id", $proveedor_id);
        $stmt_check->execute();
        $result = $stmt_check->fetch(PDO::FETCH_ASSOC);
        
        if ($result['total'] > 0) {
            return ['success' => false, 'mensaje' => 'No se puede eliminar. Tiene productos asociados.'];
        }

        $query = "DELETE FROM " . $this->table_name . " WHERE proveedor_id=:proveedor_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":proveedor_id", $proveedor_id);
        
        if ($stmt->execute()) {
            return ['success' => true, 'mensaje' => 'Proveedor eliminado correctamente'];
        }
        return ['success' => false, 'mensaje' => 'Error al eliminar proveedor'];
    }

    // Buscar proveedores
    public function buscar($termino) {
        $query = "SELECT * FROM " . $this->table_name . " 
                  WHERE nombre_proveedor LIKE :termino 
                     OR contacto_proveedor LIKE :termino 
                     OR email_proveedor LIKE :termino
                  ORDER BY nombre_proveedor ASC";
        
        $stmt = $this->conn->prepare($query);
        $termino = "%{$termino}%";
        $stmt->bindParam(":termino", $termino);
        $stmt->execute();
        
        return $stmt;
    }
}
?>