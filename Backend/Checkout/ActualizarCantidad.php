<?php
// Backend/Checkout/ActualizarCantidad.php
// Iniciar sesión si no está iniciada
if (session_status() == PHP_SESSION_NONE) {
    session_start();  // Solo una llamada a session_start() es necesaria
}

// Verificar si se recibieron los datos necesarios
if (isset($_POST['producto_id']) && isset($_POST['cantidad']) && isset($_POST['talla'])) {
    $producto_id = $_POST['producto_id'];
    $cantidad = intval($_POST['cantidad']);
    $talla = $_POST['talla'];
    
    // Validar que la cantidad sea al menos 1
    if ($cantidad < 1) {
        $cantidad = 1;
    }
    
    // Verificar si el carrito existe y tiene productos
    if (!isset($_SESSION['carrito']) || !is_array($_SESSION['carrito']) || empty($_SESSION['carrito'])) {
        echo json_encode([
            'status' => 'error',
            'message' => 'No hay productos en el carrito'
        ]);
        exit;
    }
    
    // Buscar el producto en el carrito con la misma talla y actualizar su cantidad
    $producto_encontrado = false;
    foreach ($_SESSION['carrito'] as $key => $producto) {
        if ($producto['id'] == $producto_id && $producto['tallaTexto'] == $talla) {
            $_SESSION['carrito'][$key]['cantidad'] = $cantidad;
            $producto_encontrado = true;
            break;
        }
    }
    
    if (!$producto_encontrado) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Producto con esa talla no encontrado en el carrito'
        ]);
        exit;
    }
    
    // Recalcular el total del carrito
    $total_general = 0;
    foreach ($_SESSION['carrito'] as $producto) {
        $total_general += $producto['cantidad'] * $producto['precio'];
    }
    
    // Preparar la respuesta
    $response = [
        'status' => 'success',
        'message' => 'Cantidad actualizada correctamente',
        'total_general' => $total_general,
        'cupon_aplicado' => false
    ];
    
    // Si hay un cupón aplicado, recalcular el descuento
    if (isset($_SESSION['cupon'])) {
        $porcentaje_descuento = floatval($_SESSION['cupon']['descuento']);
        $monto_descuento = round($total_general * ($porcentaje_descuento / 100), 2);
        $nuevo_total = $total_general - $monto_descuento;
        
        // Actualizar los valores en la sesión
        $_SESSION['cupon']['monto_descuento'] = $monto_descuento;
        $_SESSION['cupon']['total_original'] = $total_general;
        $_SESSION['cupon']['nuevo_total'] = $nuevo_total;
        
        // Añadir información del cupón a la respuesta
        $response['cupon_aplicado'] = true;
        $response['cupon_descuento'] = $porcentaje_descuento;
        $response['cupon_monto_descuento'] = $monto_descuento;
        $response['cupon_nuevo_total'] = $nuevo_total;
    }
    
    echo json_encode($response);
} else {
    echo json_encode([
        'status' => 'error',
        'message' => 'Datos incompletos'
    ]);
}
?>
