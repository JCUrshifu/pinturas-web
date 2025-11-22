<?php
require_once __DIR__ . '/../models/Proveedor.php';

class ProveedorController {
    private $proveedor;

    public function __construct($db) {
        $this->proveedor = new Proveedor($db);
    }

    public function listarProveedores() {
        return $this->proveedor->getAll();
    }

    public function agregarProveedor($nombre, $contacto, $telefono, $email, $direccion) {
        return $this->proveedor->create($nombre, $contacto, $telefono, $email, $direccion);
    }

    public function obtenerProveedor($proveedor_id) {
        return $this->proveedor->getById($proveedor_id);
    }

    public function actualizarProveedor($proveedor_id, $nombre, $contacto, $telefono, $email, $direccion) {
        return $this->proveedor->update($proveedor_id, $nombre, $contacto, $telefono, $email, $direccion);
    }

    public function eliminarProveedor($proveedor_id) {
        return $this->proveedor->delete($proveedor_id);
    }

    public function buscarProveedor($termino) {
        return $this->proveedor->buscar($termino);
    }
}
?>