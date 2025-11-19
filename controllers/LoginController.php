<?php
require_once __DIR__ . "/../models/Usuario.php";

class LoginController {
    private $usuario;

    public function __construct($db) {
        $this->usuario = new Usuario($db);
    }

    // Login del usuario CON DEPURACIÓN
    public function login($identificador, $password) {
        // DEBUG: Mostrar lo que se recibió
        error_log("=== LOGIN DEBUG ===");
        error_log("Identificador recibido: " . $identificador);
        error_log("Password recibido: " . $password);
        
        // Buscar por email o username
        $user = null;
        
        // Intentar buscar por email primero
        if (filter_var($identificador, FILTER_VALIDATE_EMAIL)) {
            error_log("Es un email válido, buscando por email...");
            $user = $this->usuario->obtenerPorEmail($identificador);
        }
        
        // Si no encontró por email, buscar por username
        if (!$user) {
            error_log("No encontró por email, buscando por username...");
            $user = $this->usuario->obtenerPorUsuario($identificador);
        }
        
        // DEBUG: Ver si encontró el usuario
        if ($user) {
            error_log("Usuario encontrado:");
            error_log("- ID: " . $user['usuario_id']);
            error_log("- Email: " . $user['email']);
            error_log("- Username: " . ($user['username'] ?? 'NULL'));
            error_log("- Password en BD: " . $user['password']);
            error_log("- Rol: " . $user['nombre_rol']);
            
            // Comparar contraseñas
            if ($user['password'] === $password) {
                error_log("✅ Contraseñas coinciden! Login exitoso");
                
                // Iniciar sesión
                $this->iniciarSesion($user);
                
                // Actualizar último login
                $this->usuario->actualizarUltimoLogin($user['usuario_id']);
                
                return true;
            } else {
                error_log("❌ Contraseñas NO coinciden!");
                error_log("  Esperaba: " . $user['password']);
                error_log("  Recibió: " . $password);
            }
        } else {
            error_log("❌ Usuario NO encontrado en la base de datos");
        }
        
        return false;
    }

    // Iniciar sesión y guardar datos
    private function iniciarSesion($user) {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        session_regenerate_id(true);

        $_SESSION['usuario_id'] = $user['usuario_id'];
        $_SESSION['username'] = $user['username'] ?? '';
        $_SESSION['email'] = $user['email'];
        $_SESSION['rol_id'] = $user['rol_id'];
        $_SESSION['nombre_rol'] = $user['nombre_rol'];
        $_SESSION['permisos'] = json_decode($user['permisos'], true);
        $_SESSION['logueado'] = true;
        $_SESSION['tiempo_login'] = time();
        
        error_log("✅ Sesión iniciada para usuario: " . $user['email']);
    }

    public function logout() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $_SESSION = array();

        if (isset($_COOKIE[session_name()])) {
            setcookie(session_name(), '', time() - 42000, '/');
        }

        session_destroy();
    }

    public function estaLogueado() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        return isset($_SESSION['logueado']) && $_SESSION['logueado'] === true;
    }

    public function obtenerUsuarioLogueado() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if ($this->estaLogueado()) {
            return [
                'usuario_id' => $_SESSION['usuario_id'],
                'username' => $_SESSION['username'],
                'email' => $_SESSION['email'],
                'rol_id' => $_SESSION['rol_id'],
                'nombre_rol' => $_SESSION['nombre_rol'],
                'permisos' => $_SESSION['permisos']
            ];
        }

        return null;
    }

    public function tienePermiso($modulo) {
        if (!$this->estaLogueado()) {
            return false;
        }

        $permisos = $_SESSION['permisos'] ?? [];
        $modulos = $permisos['modulos'] ?? [];

        if (in_array('todos', $modulos)) {
            return true;
        }

        return in_array($modulo, $modulos);
    }

    public function esRol($nombre_rol) {
        if (!$this->estaLogueado()) {
            return false;
        }

        return $_SESSION['nombre_rol'] === $nombre_rol;
    }

    public function verificarInactividad() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if ($this->estaLogueado()) {
            $timeout = 1800;
            $tiempo_actual = time();
            $tiempo_login = $_SESSION['tiempo_login'] ?? 0;

            if (($tiempo_actual - $tiempo_login) > $timeout) {
                $this->logout();
                return false;
            } else {
                $_SESSION['tiempo_login'] = $tiempo_actual;
            }
        }

        return true;
    }

    public function requiereLogin() {
        if (!$this->estaLogueado()) {
            header("Location: index.php?action=loginForm&mensaje=" . urlencode("Debe iniciar sesión"));
            exit();
        }

        if (!$this->verificarInactividad()) {
            header("Location: index.php?action=loginForm&mensaje=" . urlencode("Sesión expirada por inactividad"));
            exit();
        }
    }

    public function requierePermiso($modulo) {
        $this->requiereLogin();

        if (!$this->tienePermiso($modulo)) {
            header("Location: index.php?action=dashboard&error=" . urlencode("No tiene permisos para acceder a este módulo"));
            exit();
        }
    }

    public function requiereRol($nombre_rol) {
        $this->requiereLogin();

        if (!$this->esRol($nombre_rol)) {
            header("Location: index.php?action=dashboard&error=" . urlencode("Acceso denegado"));
            exit();
        }
    }

    public function registrar($username, $email, $password, $rol_id = 5) {
        if ($this->usuario->usernameExiste($username)) {
            return ['success' => false, 'mensaje' => 'El nombre de usuario ya existe'];
        }

        if ($this->usuario->emailExiste($email)) {
            return ['success' => false, 'mensaje' => 'El email ya está registrado'];
        }

        if ($this->usuario->crear($username, $email, $password, $rol_id)) {
            return ['success' => true, 'mensaje' => 'Usuario registrado exitosamente'];
        }

        return ['success' => false, 'mensaje' => 'Error al registrar usuario'];
    }
}
?>