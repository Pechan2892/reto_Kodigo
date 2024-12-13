<?php 

require_once './clases/Producto.php';
require_once './clases/Inventario.php';
require_once './clases/Usuario.php';
require_once './clases/Ventas.php';

    function displayMenu(){
        echo "---- Menu de la Tiendita ---- \n";
        echo "1. Agregar nuevo producto \n";
        echo "2. Eliminar producto \n";
        echo "3. Actualizar producto \n";
        echo "5. Generar Venta \n";
        echo "6. Generar informe \n";
        echo "7. Salir \n";
        echo "Seleccione una opcion: ";
    }
  // fucnion para obtener valores de la terminal
    function prompt($mensaje){
        echo $mensaje;
        $input = trim(fgets(STDIN));
        return $input;
    }
     
    //funcion para generar informe de productos sin stock
    function generarInformeProductosSinStock($inventario) {
        $productosSinStock = array_filter($inventario->getListaProductos(), function($producto) {
            return $producto->getStock() == 0;
        });
        if (empty($productosSinStock)) {
            echo "No hay productos sin stock \n";
        } else {
            echo "Productos sin stock: \n";
            foreach ($productosSinStock as $producto) {
                echo "_" . $producto->getNombre() . "(ID: " . $producto->getId() . ")\n";
            }
        }
    }
    

    //funcion para generar informe de productos con stock mas bajo 
    function generarInformeProductosConStockMasBajo($inventario, $stockLimite) {
        $productosConStockMasBajo = array_filter($inventario->getListaProductos(), function($producto) use ($stockLimite) {
            return $producto->getStock() < $stockLimite;
        });
        if (empty($productosConStockMasBajo)) {
            echo "No hay productos con stock menor a $stockLimite \n";
        } else {
            echo "Productos con stock bajo: \n";
            foreach ($productosConStockMasBajo as $producto) {
                echo "_" . $producto->getNombre() . "(Stock: " . $producto->getStock() . " ID: " . $producto->getId() . ")\n";
            }
        }
    }
    

  //funcion para generar informe de productos de X precio
  function generarInformeProductosPorPrecio($inventario, $precioLimite) {
    $productosPorPrecio = array_filter($inventario->getListaProductos(), function($producto) use ($precioLimite) {
        return $producto->getPrecio() > $precioLimite;
    });
    if (empty($productosPorPrecio)) {
        echo "No hay productos con precio mayor a $precioLimite \n";
    } else {
        echo "Productos con precio mayor a $precioLimite: \n";
        foreach ($productosPorPrecio as $producto) {
            echo "_" . $producto->getNombre() . "(Precio: $" . $producto->getPrecio() . " ID: " . $producto->getId() . ")\n";
        }
    }
}



   //instanciamos nuestro inventario
    $inventario = new Inventario([]);
    
    $flag = true;
    $idProducto = 0;

    //Bucle para el menu
    while($flag){
        displayMenu();
       $opcion = prompt("");
        switch($opcion){
            case 1: 
                //Obtenemos valores de producto a traves del uso de prompt (funcion para obtener valores de la terminal)
                $idProducto = $idProducto+1;
                $nombre = prompt("Ingrese el nombre del producto:\n");
                $descripcion = prompt("Ingrese la descripcion del producto:\n");
                $precio = prompt("Ingrese el precio del producto:\n");
                $cantidad = prompt("Ingrese la cantidad del producto:\n");
                $categoria = prompt("Ingrese la categoria de su producto: \n");
                $proveedor = prompt("Ingrese quien es el proveedor de su producto: \n");
                //Creamos nuevo producto con los valores recibidos por prompt
                $producto = new Producto($idProducto,$nombre,$descripcion,$precio,$cantidad,$proveedor,$categoria);
                
                //Agregamos el nuevo producto a nuestro inventario
                $inventario->agregarProducto($producto);
                echo "Ingresaste el siguiente producto: \n";
                print_r($inventario);
                break;
            case 2: 
                $idEliminar = prompt("Ingrese el ID del producto a eliminar: \n");
                if($inventario->eliminarProducto($idEliminar)){
                echo "Estas eliminando un producto \n";
                }
                else{
                    echo "El producto no existe \n";
                }
                break;
                case 3: 
                    $idActualizar = prompt("Ingrese el ID del producto a actualizar:\n");
                    $productoActualizar = null;
        
                    foreach ($inventario->getListaProductos() as $producto) {
                        if ($producto->getId() == $idActualizar) {
                            $productoActualizar = $producto;
                            break;
                        }
                    }
        
                    if ($productoActualizar) {
                        $nuevoNombre = prompt("Nuevo nombre (presione Enter para mantener el actual):\n");
                        $nuevaDescripcion = prompt("Nueva descripción (presione Enter para mantener la actual):\n");
                        $nuevoPrecio = prompt("Nuevo precio (presione Enter para mantener el actual):\n");
                        $nuevaCategoria = prompt("Nueva categoría (presione Enter para mantener la actual):\n");
        
                        $productoActualizar->editarProducto([
                            'nombre' => $nuevoNombre ?: null,
                            'descripcion' => $nuevaDescripcion ?: null,
                            'precio' => $nuevoPrecio ?: null,
                            'categoria' => $nuevaCategoria ?: null,
                        ]);
        
                        echo  "Estas actualizando un producto \n";
                    } else {
                        echo "Producto no encontrado.\n";
                    }
                break;
            case 4:
                $idDevolver = prompt("Ingrese el ID del producto a devolver: \n");
                $cantidadDevolver = prompt("Ingrese la cantidad a devolver: \n");
                $productoDevolver = null;

                foreach ($inventario->getListaProductos() as $producto) {
                    if ($producto->getId() == $idDevolver) {
                        $productoDevolver = $producto;
                        break;
                    }
                }

                if ($productoDevolver) {
                    $nuevoStock = $productoDevolver->getStock() + $cantidadDevolver;
                    $productoDevolver->setStock($nuevoStock);
                echo "Estas por devolver un producto \n";
                }else{
                    echo "El producto no existe \n";
                }
                break;
            case 5: 
                $productosVenta=[];
                $continuar = true;
                while ($continuar) {
                    $idVenta = prompt("Ingrese el ID del producto a vender: \n");
                    foreach ($inventario->getListaProductos() as $producto) {
                        if ($producto->getId() == $idVenta) {
                            $productosVenta[] = $producto;
                            break;
                        }
                            
                    }
                    $continuar = prompt("Desea agregar otro producto a la venta?");
                }
                $venta = new Venta(uniqid(), $productosVenta);
                $total = $venta->calcularTotal();
                echo "Estas generando una nueva venta. total: $total \n";
                break;
            case 6:
                echo "Seleccione una opcion para generando un informe \n";
                echo "1. Informe de productos sin stock \n";
                echo "2. Informe de productos con stock mas bajo \n";
                echo "3. Informe de productos por precio mayor de X precio \n";
            
                // Capturamos la opción del usuario
                $opcion = prompt("Ingrese la opcion deseada: \n");
            
                // Verificamos la opción ingresada y ejecutamos la acción correspondiente
                switch ($opcion) { // Cambiado de $opcionInforme a $opcion
                    case 1:
                        generarInformeProductosSinStock($inventario);
                        break;
                    case 2:
                        $stockBajo = prompt("Ingrese el stock minimo: \n");
                        generarInformeProductosConStockMasBajo($inventario, $stockBajo);
                        break;
                    case 3:
                        $precioLimite = prompt("Ingrese el precio limite: \n");
                        generarInformeProductosPorPrecio($inventario, $precioLimite);
                        break;
                    default: 
                        echo "Seleccione una opcion valida \n";
                }
                break;
            case 7:
                echo "Estas saliendo ... \n";
                $flag = false;
                break;

            default: 
            echo "Seleccione una opcion valida \n";

        }


    }
?>
