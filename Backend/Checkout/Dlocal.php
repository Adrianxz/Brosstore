<?php
// Datos del pago
$monto = 10000; // en centavos
$moneda = "COP";
$pais = "CO";

// Claves de sandbox
$apiKey = 'cXhIchZENTdyMKpjaIQDuczeoMasaxJc';
$apiSecret = 'qSW5Td29J3RDcXEmtxp5zP0T0QAS4oGjAtJUyR5U';

$returnUrl = "https://blue-parrot-771704.hostingersite.com/404/confirmacion.php";
$notificationUrl = "https://blue-parrot-771704.hostingersite.com/404/webhook.php";

$payload = [
    "amount" => $monto,
    "currency" => $moneda,
    "country" => $pais,
    "payment_method_flow" => "REDIRECT",
    "return_url" => $returnUrl,
    "notification_url" => $notificationUrl
];

$ch = curl_init("https://api-sbx.dlocalgo.com/v1/payments");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Content-Type: application/json",
    "Authorization: Bearer {$apiKey}:{$apiSecret}",
]);

$response = curl_exec($ch);
curl_close($ch);

$data = json_decode($response, true);

// Este archivo ya no hace header("Location") directamente
header('Content-Type: application/json');

if (isset($data["redirect_url"])) {
    echo json_encode(["redirect_url" => $data["redirect_url"]]);
} else {
    echo json_encode(["error" => "No se pudo generar el pago", "detalles" => $data]);
}

