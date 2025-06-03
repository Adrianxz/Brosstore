<?php
// Iniciar sesión si no está iniciada
if (session_status() == PHP_SESSION_NONE) {
    session_start();  // Solo se necesita una vez
}

// Cabeceras para JSON
header('Content-Type: application/json');

// Verificar que exista la sesión del carrito
if (!isset($_SESSION['carrito']) || !is_array($_SESSION['carrito'])) {
    $_SESSION['carrito'] = array();
}

// Obtener los parámetros de la solicitud
$accion = isset($_POST['accion']) ? $_POST['accion'] : '';
$id = isset($_POST['id']) ? intval($_POST['id']) : 0;
$talla = isset($_POST['talla']) ? $_POST['talla'] : '';

// Validar los parámetros
if (empty($accion) || $id <= 0) {
    echo json_encode(['error' => 'Parámetros inválidos']);
    exit;
}

// Respuesta por defecto
$respuesta = ['exito' => false];

// Procesar según la acción solicitada
switch ($accion) {
    case 'actualizar':
        // Obtener la cantidad
        $cantidad = isset($_POST['cantidad']) ? intval($_POST['cantidad']) : 0;
        
        // Validar cantidad
        if ($cantidad <= 0) {
            echo json_encode(['error' => 'Cantidad inválida']);
            exit;
        }
        
        // Actualizar la cantidad del producto en el carrito
        $actualizado = false;
        
        foreach ($_SESSION['carrito'] as $key => $producto) {
            // Verificar si es el mismo producto y la misma talla
            if ($producto['id'] == $id && $producto['tallaTexto'] == $talla) {
                $_SESSION['carrito'][$key]['cantidad'] = $cantidad;
                $actualizado = true;
                break;
            }
        }
        
        $respuesta = [
            'exito' => $actualizado,
            'mensaje' => $actualizado ? 'Cantidad actualizada' : 'Producto no encontrado'
        ];
        break;
        
    case 'eliminar':
        // Eliminar el producto del carrito
        $eliminado = false;
        
        foreach ($_SESSION['carrito'] as $key => $producto) {
            // Verificar si es el mismo producto y la misma talla
            if ($producto['id'] == $id && $producto['tallaTexto'] == $talla) {
                // Remover este elemento del array
                unset($_SESSION['carrito'][$key]);
                // Reindexar el array para evitar huecos
                $_SESSION['carrito'] = array_values($_SESSION['carrito']);
                $eliminado = true;
                break;
            }
        }
        
        $respuesta = [
            'exito' => $eliminado,
            'mensaje' => $eliminado ? 'Producto eliminado del carrito' : 'Producto no encontrado',
            'cantidadProductos' => count($_SESSION['carrito'])
        ];
        break;
        
    default:
        $respuesta = ['error' => 'Acción no reconocida'];
        break;
}

// Devolver la respuesta
echo json_encode($respuesta);
exit;
