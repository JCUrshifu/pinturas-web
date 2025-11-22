<?php
require_once __DIR__ . '/../models/TipoPago.php';

class TipoPagoController {
    private $tipoPago;

    public function __construct($db) {
        $this->tipoPago = new TipoPago($db);
    }

    public function listarTiposPago() {
        return $this->tipoPago->getAll();
    }

    public function listarTiposPagoActivos() {
        return $this->tipoPago->getActivos();
    }

    public function agregarTipoPago($nombre, $descripcion, $activo) {
        return $this->tipoPago->create($nombre, $descripcion, $activo);
    }

    public function obtenerTipoPago($tipo_pago_id) {
        return $this->tipoPago->getById($tipo_pago_id);
    }

    public function actualizarTipoPago($tipo_pago_id, $nombre, $descripcion, $activo) {
        return $this->tipoPago->update($tipo_pago_id, $nombre, $descripcion, $activo);
    }

    public function eliminarTipoPago($tipo_pago_id) {
        return $this->tipoPago->delete($tipo_pago_id);
    }

    public function cambiarEstadoTipoPago($tipo_pago_id, $activo) {
        return $this->tipoPago->cambiarEstado($tipo_pago_id, $activo);
    }
}
?>