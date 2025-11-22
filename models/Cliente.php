<?php
class Cliente {
    private $conn;
    private $table_name = "cliente";

    public $cliente_id;
    public $usuario_id;
    public $nombre_cliente;
    public $nit; // ✅ CAMPO NUEVO
    public $email;
    public $telefono;
    public $direccion;
    public $fecha_registro;
    public $acepta_promociones;

    public function __construct($db) {
        $this->conn = $db;
    }
    
    // Obtener todos los clientes
    public function getAll() {
        $query = "SELECT cliente_id, usuario_id, nombre_cliente, nit, email, telefono, 
                         direccion, fecha_registro, acepta_promociones 
                  FROM " . $this->table_name . "
                  ORDER BY cliente_id DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    // Crear nuevo cliente (CON NIT)
    public function create($usuario_id, $nombre_cliente, $nit, $email, $telefono, $direccion, $acepta_promociones) {
        $query = "INSERT INTO " . $this->table_name . " 
                  (usuario_id, nombre_cliente, nit, email, telefono, direccion, fecha_registro, acepta_promociones) 
                  VALUES (:usuario_id, :nombre_cliente, :nit, :email, :telefono, :direccion, NOW(), :acepta_promociones)";
        
        $stmt = $this->conn->prepare($query);

        // Limpiar datos
        $nombre_cliente = htmlspecialchars(strip_tags($nombre_cliente));
        $nit = htmlspecialchars(strip_tags($nit));
        $email = htmlspecialchars(strip_tags($email));
        $telefono = htmlspecialchars(strip_tags($telefono));
        $direccion = htmlspecialchars(strip_tags($direccion));

        $stmt->bindParam(":usuario_id", $usuario_id);
        $stmt->bindParam(":nombre_cliente", $nombre_cliente);
        $stmt->bindParam(":nit", $nit);
        $stmt->bindParam(":email", $email);
        $stmt->bindParam(":telefono", $telefono);
        $stmt->bindParam(":direccion", $direccion);
        $stmt->bindParam(":acepta_promociones", $acepta_promociones);

        return $stmt->execute();
    }

    // Obtener cliente por ID
    public function getById($cliente_id) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE cliente_id = :cliente_id LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":cliente_id", $cliente_id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Obtener cliente por NIT (NUEVO)
    public function getByNit($nit) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE nit = :nit LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":nit", $nit);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Verificar si NIT ya existe (NUEVO)
    public function nitExiste($nit, $cliente_id = null) {
        if ($cliente_id) {
            // Para actualización, excluir el cliente actual
            $query = "SELECT cliente_id FROM " . $this->table_name . " 
                     WHERE nit = :nit AND cliente_id != :cliente_id LIMIT 1";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":nit", $nit);
            $stmt->bindParam(":cliente_id", $cliente_id);
        } else {
            $query = "SELECT cliente_id FROM " . $this->table_name . " 
                     WHERE nit = :nit LIMIT 1";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":nit", $nit);
        }
        
        $stmt->execute();
        return $stmt->rowCount() > 0;
    }

    // Actualizar cliente (CON NIT)
    public function update($cliente_id, $usuario_id, $nombre_cliente, $nit, $email, $telefono, $direccion, $acepta_promociones) {
        $query = "UPDATE " . $this->table_name . " SET 
                    usuario_id=:usuario_id, 
                    nombre_cliente=:nombre_cliente,
                    nit=:nit,
                    email=:email, 
                    telefono=:telefono, 
                    direccion=:direccion, 
                    acepta_promociones=:acepta_promociones
                  WHERE cliente_id=:cliente_id";

        $stmt = $this->conn->prepare($query);

        // Limpiar datos
        $usuario_id = htmlspecialchars(strip_tags($usuario_id));
        $nombre_cliente = htmlspecialchars(strip_tags($nombre_cliente));
        $nit = htmlspecialchars(strip_tags($nit));
        $email = htmlspecialchars(strip_tags($email));
        $telefono = htmlspecialchars(strip_tags($telefono));
        $direccion = htmlspecialchars(strip_tags($direccion));
        $acepta_promociones = htmlspecialchars(strip_tags($acepta_promociones));
        $cliente_id = htmlspecialchars(strip_tags($cliente_id));

        // Bind
        $stmt->bindParam(":usuario_id", $usuario_id);
        $stmt->bindParam(":nombre_cliente", $nombre_cliente);
        $stmt->bindParam(":nit", $nit);
        $stmt->bindParam(":email", $email);
        $stmt->bindParam(":telefono", $telefono);
        $stmt->bindParam(":direccion", $direccion);
        $stmt->bindParam(":acepta_promociones", $acepta_promociones);
        $stmt->bindParam(":cliente_id", $cliente_id);

        return $stmt->execute();
    }

    // Eliminar cliente
    public function delete($cliente_id) {
        $query = "DELETE FROM " . $this->table_name . " WHERE cliente_id=:cliente_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":cliente_id", $cliente_id);
        return $stmt->execute();
    }

    // Buscar clientes por nombre o NIT (NUEVO - útil para búsquedas)
    public function buscar($termino) {
        $query = "SELECT * FROM " . $this->table_name . " 
                  WHERE nombre_cliente LIKE :termino 
                     OR nit LIKE :termino 
                     OR email LIKE :termino
                  ORDER BY nombre_cliente ASC";
        
        $stmt = $this->conn->prepare($query);
        $termino = "%{$termino}%";
        $stmt->bindParam(":termino", $termino);
        $stmt->execute();
        
        return $stmt;
    }
}
?>