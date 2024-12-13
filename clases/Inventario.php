<?php 

class Inventario {
    private $listaProductos;

    public function __construct($listaProductos = []) {
        $this->listaProductos = $listaProductos;
    }

    public function agregarProducto($producto) {
        // Validar que el dato sea un objeto de la clase Producto
        if ($producto instanceof Producto) {
            $this->listaProductos[] = $producto;
        } else {
            echo "Error: El producto debe ser una instancia de la clase Producto.\n";
        }
    }

    public function eliminarProducto($id) {
        // Buscar y eliminar un producto por su ID
        foreach ($this->listaProductos as $key => $producto) {
            if ($producto->getId() === $id) { // Usamos el método getId()
                unset($this->listaProductos[$key]);
                return true;
            }
        }
        return false;
    }

    public function buscarProductoPorCategoria($categoria) {
        // Filtrar productos por categoría
        return array_filter($this->listaProductos, function($producto) use ($categoria) {
            return $producto->getCategoria() === $categoria; // Usamos el método getCategoria()
        });
    }

    public function generarInforme() {
        // Puedes implementar informes detallados aquí según las necesidades
        echo "Informe generado.\n";
    }

    // Método para obtener la lista de productos
    public function getListaProductos() {
        return $this->listaProductos;
    }
}
?>