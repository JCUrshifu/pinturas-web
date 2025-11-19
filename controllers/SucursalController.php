<?php
require_once __DIR__ . '/../models/Sucursal.php';

class SucursalController {
    private $sucursal;

    public function __construct($db) {
        $this->sucursal = new Sucursal($db);
    }

    public function listarSucursales() {
        return $this->sucursal->getAll();
    }

    public function listarSucursalesActivas() {
        return $this->sucursal->getActivas();
    }

    public function agregarSucursal($nombre, $direccion, $telefono, $latitud, $longitud, $activa) {
        return $this->sucursal->create($nombre, $direccion, $telefono, $latitud, $longitud, $activa);
    }

    public function obtenerSucursal($sucursal_id) {
        return $this->sucursal->getById($sucursal_id);
    }

    public function actualizarSucursal($sucursal_id, $nombre, $direccion, $telefono, $latitud, $longitud, $activa) {
        return $this->sucursal->update($sucursal_id, $nombre, $direccion, $telefono, $latitud, $longitud, $activa);
    }

    public function eliminarSucursal($sucursal_id) {
        return $this->sucursal->delete($sucursal_id);
    }

    public function cambiarEstadoSucursal($sucursal_id, $activa) {
        return $this->sucursal->cambiarEstado($sucursal_id, $activa);
    }
}
?>