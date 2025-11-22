<?php
require_once __DIR__ . '/../models/Productos.php';

class ProductoController {
    private $producto;

    public function __construct($db) {
        $this->producto = new Producto($db);
    }

    public function listarProductos() {
        return $this->producto->getAll();
    }

    public function agregarProducto($data) {
        return $this->producto->create($data);
    }

    public function obtenerProducto($producto_id) {
        return $this->producto->getById($producto_id);
    }

    public function actualizarProducto($producto_id, $data) {
        return $this->producto->update($producto_id, $data);
    }

    public function eliminarProducto($producto_id) {
        return $this->producto->delete($producto_id);
    }
}
