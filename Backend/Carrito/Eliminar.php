<?php
// Backend/Carrito/Eliminar.php
session_start();
header('Content-Type: application/json');

try {
    // Obtener los datos enviados
    $id = $_POST['id'] ?? '';
    $idCompuesto = $_POST['idCompuesto'] ?? '';
    $productoId = $_POST['productoId'] ?? '';
    $talla = $_POST['talla'] ?? '';
    
    // Log para debug
    error_log("Datos recibidos para eliminar: " . json_encode($_POST));
    
    // Verificar que tengamos al menos el ID compuesto
    if (empty($idCompuesto) && empty($id)) {
        throw new Exception('ID del producto requerido');
    }
    
    // Usar idCompuesto si está disponible, sino usar id
    $idAEliminar = !empty($idCompuesto) ? $idCompuesto : $id;
    
    // Verificar si existe el carrito en la sesión
    if (!isset($_SESSION['carrito']) || !is_array($_SESSION['carrito'])) {
        $_SESSION['carrito'] = [];
    }
    
    $carritoAntes = $_SESSION['carrito'];
    error_log("Carrito antes de eliminar: " . json_encode($carritoAntes));
    
    $productoEncontrado = false;
    $nuevoCarrito = [];
    
    // Filtrar el carrito para eliminar el producto específico
    foreach ($_SESSION['carrito'] as $item) {
        // Construir el ID compuesto del item actual
        $itemIdCompuesto = isset($item['idCompuesto']) ? 
            $item['idCompuesto'] : 
            $item['id'] . '-' . (isset($item['talla']) ? $item['talla'] : '');
        
        // Verificar si este es el producto a eliminar
        if ($itemIdCompuesto === $idAEliminar) {
            $productoEncontrado = true;
            error_log("Producto encontrado y eliminado: " . json_encode($item));
            // No agregar este item al nuevo carrito (lo eliminamos)
            continue;
        }
        
        // Agregar el item al nuevo carrito
        $nuevoCarrito[] = $item;
    }
    
    if (!$productoEncontrado) {
        // Intentar búsqueda alternativa por ID original y talla
        if (!empty($productoId) && !empty($talla)) {
            foreach ($_SESSION['carrito'] as $key => $item) {
                if ($item['id'] == $productoId && $item['talla'] == $talla) {
                    $productoEncontrado = true;
                    unset($_SESSION['carrito'][$key]);
                    $_SESSION['carrito'] = array_values($_SESSION['carrito']); // Reindexar
                    error_log("Producto encontrado por ID y talla: " . json_encode($item));
                    break;
                }
            }
        }
        
        if (!$productoEncontrado) {
            error_log("Producto no encontrado. Buscando: " . $idAEliminar);
            error_log("Carrito actual: " . json_encode($_SESSION['carrito']));
            
            // Mostrar todos los IDs disponibles para debug
            $idsDisponibles = [];
            foreach ($_SESSION['carrito'] as $item) {
                $itemId = isset($item['idCompuesto']) ? 
                    $item['idCompuesto'] : 
                    $item['id'] . '-' . (isset($item['talla']) ? $item['talla'] : '');
                $idsDisponibles[] = $itemId;
            }
            error_log("IDs disponibles en carrito: " . json_encode($idsDisponibles));
            
            throw new Exception('Producto no encontrado en el carrito');
        }
    } else {
        // Actualizar el carrito con el array filtrado
        $_SESSION['carrito'] = $nuevoCarrito;
    }
    
    error_log("Carrito después de eliminar: " . json_encode($_SESSION['carrito']));
    
    // Respuesta exitosa
    echo json_encode([
        'success' => true,
        'message' => 'Producto eliminado correctamente',
        'cart' => $_SESSION['carrito']
    ]);
    
} catch (Exception $e) {
    error_log("Error al eliminar producto: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage(),
        'debug' => [
            'post_data' => $_POST,
            'session_cart' => $_SESSION['carrito'] ?? []
        ]
    ]);
}
?>