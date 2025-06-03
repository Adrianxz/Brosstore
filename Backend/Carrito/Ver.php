<?php
// Backend/Carrito/Ver.php

// Check if session is already started
if (session_status() == PHP_SESSION_NONE) {
    // Only set session name if session hasn't started
    session_start();
} 

// Set the content type to JSON
header('Content-Type: application/json');

// Get cart data from session
$carrito = isset($_SESSION['carrito']) ? $_SESSION['carrito'] : [];

// Return cart data as JSON
echo json_encode(array_values($carrito));
?>