<?php
// Backend/Carrito/Vaciar.php

// Check if session is already started
if (session_status() == PHP_SESSION_NONE) {
    // Only set session name if session hasn't started
    session_start();
}

header('Content-Type: application/json');

// Vaciar el carrito
$_SESSION['carrito'] = [];

// Responder con éxito
echo json_encode(['success' => true]);
?>