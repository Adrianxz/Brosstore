<?php
// Mostrar errores (modo desarrollo)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Iniciar sesión para acceder a los datos del carrito
session_start();

// Claves y configuración de Wompi
$wompi_public_key = 'pub_test_EtD8UHC27HpmawCaf2afQSzkX6TlSTIg';
$wompi_private_key = 'prv_test_DUNPFPaZHfqmTSPS06GpUifu44n2q8kd';
$integrity_secret = 'test_integrity_9OHIUvyph6K53IHICNzfcwvb2UBvSH6h';
$wompi_environment = 'sandbox';

$wompi_base_url = ($wompi_environment === 'sandbox') 
    ? 'https://sandbox.wompi.co/v1' 
    : 'https://production.wompi.co/v1';

// === Funciones auxiliares ===

function generateUniqueReference() {
    return 'REF-' . time() . '-' . rand(1000, 9999);
}

function generateIntegritySignature($reference, $amount_in_cents, $currency, $integrity_secret, $expiration_time = null) {
    $data = $reference . $amount_in_cents . $currency;
    if ($expiration_time !== null) {
        $data .= $expiration_time;
    }
    $data .= $integrity_secret;
    return hash('sha256', $data);
}

function prepareTransactionData($reference, $amount_in_cents, $currency, $redirect_url, $public_key, $integrity_secret, $expiration_time = null) {
    $signature = generateIntegritySignature($reference, $amount_in_cents, $currency, $integrity_secret, $expiration_time);

    return [
        'public-key' => $public_key,
        'currency' => $currency,
        'amount-in-cents' => $amount_in_cents,
        'reference' => $reference,
        'redirect-url' => $redirect_url,
        'signature' => $signature,
        'expiration-time' => $expiration_time
    ];
}

function createWompiCheckoutUrl($transactionData, $base_url) {
    $checkout_url = $base_url . '/payment_links';

    // Obtener detalles del carrito para descripción
    $carrito_info = "";
    if (!empty($_SESSION['carrito']) && is_array($_SESSION['carrito'])) {
        foreach ($_SESSION['carrito'] as $producto) {
            $carrito_info .= "{$producto['nombre']} (Talla: {$producto['tallaTexto']}) x {$producto['cantidad']}, ";
        }
        $carrito_info = rtrim($carrito_info, ", ");
    } else {
        $carrito_info = "Compra en tienda";
    }

    $data = [
        'name' => 'Pago de productos',
        'description' => $carrito_info,
        'single_use' => true,
        'currency' => $transactionData['currency'],
        'amount_in_cents' => $transactionData['amount-in-cents'],
        'reference' => $transactionData['reference'],
        'redirect_url' => $transactionData['redirect-url'],
        'signature' => $transactionData['signature'],
        'collect_shipping' => false
    ];

    if (!empty($transactionData['expiration-time'])) {
        $data['expiration_time'] = $transactionData['expiration-time'];
    }

    $ch = curl_init($checkout_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Bearer ' . $transactionData['private-key'],
        'Content-Type: application/json'
    ]);

    $response = curl_exec($ch);
    $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($status == 200 || $status == 201) {
        $response_data = json_decode($response, true);
        return $response_data['data']['id'];
    } else {
        echo "<pre>ERROR WOMPI:\n";
        echo "HTTP STATUS: $status\n";
        echo "Respuesta: $response\n</pre>";
        error_log("Error creando link de pago Wompi ($status): $response");
        return false;
    }
}

// === Guardar información de envío del usuario ===
if (isset($_POST['pais']) && isset($_POST['ciudad']) && isset($_POST['direccion'])) {
    $_SESSION['usuario']['Pais'] = $_POST['pais'];
    $_SESSION['usuario']['Ciudad'] = $_POST['ciudad'];
    $_SESSION['usuario']['Direcc'] = $_POST['direccion'];
}

// === Lógica principal ===
if (isset($_POST['process_payment'])) {
    // Usar el monto recibido del formulario o recalcularlo del carrito
    if (isset($_POST['amount']) && is_numeric($_POST['amount'])) {
        $amount = $_POST['amount']; // Monto en pesos
    } else {
        // Recalcular en caso de que el monto no se haya recibido o se intente manipular
        $amount = 0;
        if (!empty($_SESSION['carrito']) && is_array($_SESSION['carrito'])) {
            foreach ($_SESSION['carrito'] as $producto) {
                $amount += $producto['cantidad'] * $producto['precio'];
            }
        }
        
        // Aplicar descuento si hay cupón
        if (isset($_SESSION['cupon']) && isset($_SESSION['cupon']['nuevo_total'])) {
            $amount = $_SESSION['cupon']['nuevo_total'];
        }
    }
    
    // Verificar si hay productos en el carrito
    if ($amount <= 0) {
        echo "<script>
            alert('No hay productos en el carrito para procesar el pago');
            window.location.href = 'checkout.php';
        </script>";
        exit;
    }

    $amount_in_cents = intval($amount) * 100;
    $reference = generateUniqueReference();
    $currency = 'COP';
    $redirect_url = 'https://blue-parrot-771704.hostingersite.com/404/confirmarWompi.php';

    // Guardar la referencia en la sesión para verificarla después
    $_SESSION['wompi_reference'] = $reference;
    $_SESSION['wompi_amount'] = $amount;

    // Expira en 30 minutos
    $expiration_time = gmdate('Y-m-d\TH:i:s.000\Z', time() + 1800);

    $transaction_data = prepareTransactionData(
        $reference,
        $amount_in_cents,
        $currency,
        $redirect_url,
        $wompi_public_key,
        $integrity_secret,
        $expiration_time
    );

    // Agregar la clave privada para autorización en la llamada cURL
    $transaction_data['private-key'] = $wompi_private_key;

    $checkout_id = createWompiCheckoutUrl($transaction_data, $wompi_base_url);

    if ($checkout_id) {
        // Guardar también el ID de la transacción en la sesión para usarlo como VENTA_ORDEN
        $_SESSION['wompi_transaction_id'] = $checkout_id;
        
        $checkout_url = "https://checkout.wompi.co/l/{$checkout_id}";
        header("Location: {$checkout_url}");
        exit;
    } else {
        echo "Error al crear el enlace de pago. Por favor intenta nuevamente.";
    }
} else {
    // Si no hay datos de POST, redirigir al checkout
    header("Location: checkout");
    exit;
}
?>