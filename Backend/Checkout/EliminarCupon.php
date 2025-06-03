<?php
// Backend/Checkout/EliminarCupon.php

// Iniciar sesión si no está iniciada
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Verificar si hay un cupón en la sesión
if (isset($_SESSION['cupon'])) {
    // Guardar el código de cupón para informar en el mensaje
    $codigo = $_SESSION['cupon']['codigo'];
    
    // Eliminar el cupón de la sesión
    unset($_SESSION['cupon']);
    
    // Enviar respuesta de éxito
    echo json_encode([
        'status' => 'success',
        'message' => 'Cupón "' . $codigo . '" eliminado correctamente'
    ]);
} else {
    // Si no hay cupón, enviar un mensaje informativo
    echo json_encode([
        'status' => 'error',
        'message' => 'No hay un cupón aplicado para eliminar'
    ]);
}
?>