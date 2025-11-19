<?php
class Usuario {
    private $conn;
    private $table_name = "usuario";

    public $usuario_id;
    public $username;
    public $email;
    public $password;
    public $contrasena;
    public $rol_id;
    public $activo;
    public $fecha_creacion;
    public $ultimo_login;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Obtener usuario por username (para login)
    public function obtenerPorUsuario($username) {
        $query = "SELECT u.*, r.nombre_rol, r.permisos 
                  FROM " . $this->table_name . " u
                  INNER JOIN rol r ON u.rol_id = r.rol_id
                  WHERE u.username = :username AND u.activo = 1
                  LIMIT 1";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':username', $username);
        $stmt->execute();
        
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Normalizar el campo de contraseña
        if ($result && !isset($result['password']) && isset($result['contrasena'])) {
            $result['password'] = $result['contrasena'];
        }
        
        return $result;
    }

    // Obtener usuario por email (para login con email)
    public function obtenerPorEmail($email) {
        $query = "SELECT u.*, r.nombre_rol, r.permisos 
                  FROM " . $this->table_name . " u
                  INNER JOIN rol r ON u.rol_id = r.rol_id
                  WHERE u.email = :email AND u.activo = 1
                  LIMIT 1";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Normalizar el campo de contraseña
        if ($result && !isset($result['password']) && isset($result['contrasena'])) {
            $result['password'] = $result['contrasena'];
        }
        
        return $result;
    }

    // Obtener usuario por ID
    public function obtenerPorId($usuario_id) {
        $query = "SELECT u.*, r.nombre_rol, r.permisos 
                  FROM " . $this->table_name . " u
                  INNER JOIN rol r ON u.rol_id = r.rol_id
                  WHERE u.usuario_id = :usuario_id
                  LIMIT 1";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':usuario_id', $usuario_id);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Crear nuevo usuario (SIN HASHEO)
    public function crear($username, $email, $password, $rol_id) {
        $query = "INSERT INTO " . $this->table_name . " 
                  (username, email, contrasena, rol_id, activo, fecha_creacion) 
                  VALUES (:username, :email, :password, :rol_id, 1, NOW())";
        
        $stmt = $this->conn->prepare($query);
        
        // SIN HASHEAR - Contraseña en texto plano
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':password', $password);
        $stmt->bindParam(':rol_id', $rol_id);
        
        return $stmt->execute();
    }

    // Actualizar último login
    public function actualizarUltimoLogin($usuario_id) {
        $query = "UPDATE " . $this->table_name . " 
                  SET ultimo_login = NOW() 
                  WHERE usuario_id = :usuario_id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':usuario_id', $usuario_id);
        
        return $stmt->execute();
    }

    // Cambiar contraseña (SIN HASHEO)
    public function cambiarPassword($usuario_id, $nuevo_password) {
        $query = "UPDATE " . $this->table_name . " 
                  SET contrasena = :password 
                  WHERE usuario_id = :usuario_id";
        
        $stmt = $this->conn->prepare($query);
        
        // SIN HASHEAR
        $stmt->bindParam(':password', $nuevo_password);
        $stmt->bindParam(':usuario_id', $usuario_id);
        
        return $stmt->execute();
    }

    // Verificar si username existe
    public function usernameExiste($username) {
        $query = "SELECT usuario_id FROM " . $this->table_name . " 
                  WHERE username = :username LIMIT 1";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':username', $username);
        $stmt->execute();
        
        return $stmt->rowCount() > 0;
    }

    // Verificar si email existe
    public function emailExiste($email) {
        $query = "SELECT usuario_id FROM " . $this->table_name . " 
                  WHERE email = :email LIMIT 1";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        
        return $stmt->rowCount() > 0;
    }

    // Obtener todos los usuarios
    public function obtenerTodos() {
        $query = "SELECT u.*, r.nombre_rol 
                  FROM " . $this->table_name . " u
                  INNER JOIN rol r ON u.rol_id = r.rol_id
                  ORDER BY u.usuario_id DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        return $stmt;
    }

    // Activar/Desactivar usuario
    public function cambiarEstado($usuario_id, $activo) {
        $query = "UPDATE " . $this->table_name . " 
                  SET activo = :activo 
                  WHERE usuario_id = :usuario_id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':activo', $activo);
        $stmt->bindParam(':usuario_id', $usuario_id);
        
        return $stmt->execute();
    }
}
?>