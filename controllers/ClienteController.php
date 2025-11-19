<?php
require_once __DIR__ . '/../models/Cliente.php';

class ClienteController {
    private $cliente;

    public function __construct($db) {
        $this->cliente = new Cliente($db);
    }

    public function listarClientes() {
        return $this->cliente->getAll();
    }

    public function agregarCliente($usuario_id, $nombre_cliente, $nit, $email, $telefono, $direccion, $acepta_promociones) {
        return $this->cliente->create($usuario_id, $nombre_cliente, $nit, $email, $telefono, $direccion, $acepta_promociones);
    }

    public function obtenerCliente($cliente_id) {
        return $this->cliente->getById($cliente_id);
    }

    public function actualizarCliente($cliente_id, $usuario_id, $nombre_cliente, $nit, $email, $telefono, $direccion, $acepta_promociones) {
        return $this->cliente->update($cliente_id, $usuario_id, $nombre_cliente, $nit, $email, $telefono, $direccion, $acepta_promociones);
    }

    public function eliminarCliente($cliente_id) {
        return $this->cliente->delete($cliente_id);
    }

    public function buscarCliente($termino) {
        return $this->cliente->buscar($termino);
    }

    public function verificarNit($nit, $cliente_id = null) {
        return $this->cliente->nitExiste($nit, $cliente_id);
    }
}
?>