<?php
require_once __DIR__ . "/../models/Empleado.php";

class EmpleadoController {
    private $empleado;

    public function __construct($db) {
        $this->empleado = new Empleado($db);
    }

    public function listarEmpleados() {
        return $this->empleado->getAll();
    }

    public function agregarEmpleado($usuario_id, $nombre_empleado, $apellido_empleado, $sucursal_id, $activo, $fecha_contratacion) {
        return $this->empleado->create($usuario_id, $nombre_empleado, $apellido_empleado, $sucursal_id, $activo, $fecha_contratacion);
    }

    public function obtenerEmpleado($empleado_id) {
        return $this->empleado->getById($empleado_id);
    }

    public function actualizarEmpleado($empleado_id, $usuario_id, $nombre_empleado, $apellido_empleado, $sucursal_id, $activo, $fecha_contratacion) {
        return $this->empleado->update($empleado_id, $usuario_id, $nombre_empleado, $apellido_empleado, $sucursal_id, $activo, $fecha_contratacion);
    }

    public function eliminarEmpleado($empleado_id) {
        return $this->empleado->delete($empleado_id);
    }
}
?>
