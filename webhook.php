<?php
// Iniciar sesión si no está iniciada
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Recibir datos de la notificación como JSON
$inputJSON = file_get_contents('php://input');
$input = json_decode($inputJSON, true);

// Verificar que se haya recibido información válida
if (!$input || !isset($input['payment_id'])) {
    http_response_code(400);
    exit('Bad Request');
}

// Configuración de DLocalGo
$apiKey = 'cXhIchZENTdyMKpjaIQDuczeoMasaxJc';
$apiSecret = 'qSW5Td29J3RDcXEmtxp5zP0T0QAS4oGjAtJUyR5U';
$isProduction = false;

// URLs base correctas (sin {payment_id} en la variable)
$apiBaseUrl = $isProduction
    ? 'https://api.dlocalgo.com/v1'
    : 'https://api-sbx.dlocalgo.com/v1';

// Headers para autenticación
function getAuthHeaders($apiKey, $apiSecret) {
    return [
        "Content-Type: application/json",
        "Authorization: Bearer {$apiKey}:{$apiSecret}"
    ];
}

// ID de pago recibido
$paymentId = $input['payment_id'];

// Consultar el estado del pago (corrigiendo URL)
$ch = curl_init("{$apiBaseUrl}/payments/{$paymentId}");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, getAuthHeaders($apiKey, $apiSecret));
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, $isProduction);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

// Crear carpeta de logs si no existe
if (!file_exists('logs')) {
    mkdir('logs', 0755, true);
}

// Procesar respuesta si fue exitosa
if ($httpCode == 200) {
    $paymentData = json_decode($response, true);
    $orderId = $paymentData['order_id'] ?? 'desconocido';

    // Crear carpeta para logs por pedido si no existe
    if (!file_exists("logs/pedidos")) {
        mkdir("logs/pedidos", 0755, true);
    }

    // Log por orden
    file_put_contents(
        "logs/pedidos/{$orderId}.log",
        json_encode($paymentData, JSON_PRETTY_PRINT) . PHP_EOL,
        FILE_APPEND
    );

    // Guardar también un log general
    file_put_contents(
        'logs/dlocal_webhook_' . date('Y-m-d') . '.log',
        date('Y-m-d H:i:s') . " - Payment ID: {$paymentId} - Status: {$paymentData['status']}" . PHP_EOL,
        FILE_APPEND
    );

    // Guardar en sesión para redirección (opcional)
    $_SESSION['ultimo_pago'] = $paymentId;

    // Verificar estado
    if (isset($paymentData['status'])) {
        switch ($paymentData['status']) {
            case 'PAID':
            case 'COMPLETED':
                actualizarOrdenPagada($orderId, $paymentId);
                unset($_SESSION['carrito'], $_SESSION['cupon']);
                break;
            case 'REJECTED':
            case 'FAILED':
                registrarPagoRechazado($orderId, $paymentId, $paymentData['status_detail'] ?? 'Sin detalles');
                break;
            case 'PENDING':
            case 'PROCESSING':
                registrarPagoPendiente($orderId, $paymentId);
                break;
            case 'CANCELLED':
                registrarPagoCancelado($orderId, $paymentId);
                break;
            default:
                registrarEventoDesconocido($orderId, $paymentId, $paymentData['status']);
        }
    }

    http_response_code(200);
    echo json_encode(['status' => 'success']);
} else {
    file_put_contents(
        'logs/dlocal_webhook_error_' . date('Y-m-d') . '.log',
        date('Y-m-d H:i:s') . " - Payment ID: {$paymentId} - HTTP Error: {$httpCode}" . PHP_EOL,
        FILE_APPEND
    );
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Error al verificar el pago']);
}

// FUNCIONES AUXILIARES

function actualizarOrdenPagada($orderId, $paymentId) {
    file_put_contents(
        'logs/ordenes_pagadas.log',
        date('Y-m-d H:i:s') . " - Order ID: {$orderId} - Payment ID: {$paymentId}" . PHP_EOL,
        FILE_APPEND
    );
}

function registrarPagoRechazado($orderId, $paymentId, $detalles) {
    file_put_contents(
        'logs/ordenes_rechazadas.log',
        date('Y-m-d H:i:s') . " - Order ID: {$orderId} - Payment ID: {$paymentId} - Detalles: {$detalles}" . PHP_EOL,
        FILE_APPEND
    );
}

function registrarPagoPendiente($orderId, $paymentId) {
    file_put_contents(
        'logs/ordenes_pendientes.log',
        date('Y-m-d H:i:s') . " - Order ID: {$orderId} - Payment ID: {$paymentId}" . PHP_EOL,
        FILE_APPEND
    );
}

function registrarPagoCancelado($orderId, $paymentId) {
    file_put_contents(
        'logs/ordenes_canceladas.log',
        date('Y-m-d H:i:s') . " - Order ID: {$orderId} - Payment ID: {$paymentId}" . PHP_EOL,
        FILE_APPEND
    );
}

function registrarEventoDesconocido($orderId, $paymentId, $estado) {
    file_put_contents(
        'logs/ordenes_estado_desconocido.log',
        date('Y-m-d H:i:s') . " - Order ID: {$orderId} - Payment ID: {$paymentId} - Estado: {$estado}" . PHP_EOL,
        FILE_APPEND
    );
}
?>
