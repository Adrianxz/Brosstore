<?php
// webhook_wompi.php - Procesa los eventos del webhook de Wompi

// Configuración
$wompi_integrity_secret = 'test_integrity_9OHIUvyph6K53IHICNzfcwvb2UBvSH6h';
$log_file = __DIR__ . '/wompi_webhook.log';

// Iniciar registro de log
file_put_contents($log_file, date('[Y-m-d H:i:s] ') . "Webhook recibido\n", FILE_APPEND);

// Capturar el payload del webhook
$payload = file_get_contents("php://input");
$signature = isset($_SERVER['HTTP_X_WOMPI_SIGNATURE']) ? $_SERVER['HTTP_X_WOMPI_SIGNATURE'] : '';

// Registrar el payload para depuración
file_put_contents($log_file, date('[Y-m-d H:i:s] ') . "Payload: {$payload}\n", FILE_APPEND);
file_put_contents($log_file, date('[Y-m-d H:i:s] ') . "Signature: {$signature}\n", FILE_APPEND);

// Validar la firma (seguridad crítica)
$calculated_signature = hash_hmac('sha256', $payload, $wompi_integrity_secret);
if (!hash_equals($calculated_signature, $signature)) {
    file_put_contents($log_file, date('[Y-m-d H:i:s] ') . "Error: Firma inválida\n", FILE_APPEND);
    http_response_code(401);
    exit("Firma inválida");
}

// Decodificar el payload
$data = json_decode($payload, true);
if (!$data) {
    file_put_contents($log_file, date('[Y-m-d H:i:s] ') . "Error: JSON inválido\n", FILE_APPEND);
    http_response_code(400);
    exit("JSON inválido");
}

// Procesar solo eventos de transacción actualizada
if ($data['event'] !== "transaction.updated") {
    file_put_contents($log_file, date('[Y-m-d H:i:s] ') . "Evento ignorado: {$data['event']}\n", FILE_APPEND);
    http_response_code(200);
    exit("Evento ignorado");
}

// Extraer información de la transacción
$transaction = $data['data']['transaction'];
$transaction_id = $transaction['id'];
$reference = $transaction['reference'];
$status = $transaction['status'];

file_put_contents($log_file, date('[Y-m-d H:i:s] ') . "Transacción {$transaction_id}, Referencia: {$reference}, Estado: {$status}\n", FILE_APPEND);

// Procesar solo transacciones aprobadas
if ($status !== "APPROVED") {
    file_put_contents($log_file, date('[Y-m-d H:i:s] ') . "Transacción no aprobada, estado: {$status}\n", FILE_APPEND);
    http_response_code(200);
    exit("Estado no procesable");
}

try {
    // Conectar a la base de datos
    require_once __DIR__ . '/Backend/BD.php';
    
    // Verificar si la transacción ya fue procesada para evitar duplicados
    $sql = "SELECT VENTA_ID FROM wompi_transactions WHERE TRANSACTION_ID = ?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("s", $transaction_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        file_put_contents($log_file, date('[Y-m-d H:i:s] ') . "Transacción {$transaction_id} ya procesada\n", FILE_APPEND);
        http_response_code(200);
        exit("Transacción ya procesada");
    }
    
    // Buscar los datos de la orden usando la referencia
    $reference_parts = explode('-', $reference);
    if (count($reference_parts) >= 3) {
        $session_id = $reference_parts[1];
        
        // Guardar el ID de transacción y referencia en la base de datos
        $sql = "INSERT INTO wompi_transactions (TRANSACTION_ID, REFERENCE, STATUS, AMOUNT, PAYMENT_METHOD, CREATED_AT) 
                VALUES (?, ?, ?, ?, ?, NOW())";
        $stmt = $conexion->prepare($sql);
        $amount = $transaction['amount_in_cents'] / 100;
        $payment_method = $transaction['payment_method_type'];
        $stmt->bind_param("sssds", $transaction_id, $reference, $status, $amount, $payment_method);
        $stmt->execute();
        
        // Intentar recuperar y procesar la sesión del cliente
        session_write_close(); // Cerrar cualquier sesión activa
        session_id($session_id); // Establecer el ID de sesión
        session_start(); // Iniciar la sesión con ese ID
        
        if (isset($_SESSION['carrito']) && isset($_SESSION['usuario'])) {
            // La sesión se recuperó correctamente, procesar la venta
            file_put_contents($log_file, date('[Y-m-d H:i:s] ') . "Sesión recuperada, procesando venta\n", FILE_APPEND);
            
            // Registrar la venta
            require_once __DIR__ . '/Backend/Checkout/registrar_venta.php';
            
            file_put_contents($log_file, date('[Y-m-d H:i:s] ') . "Venta registrada correctamente\n", FILE_APPEND);
        } else {
            // No se pudo recuperar la sesión
            file_put_contents($log_file, date('[Y-m-d H:i:s] ') . "Error: No se pudo recuperar la sesión\n", FILE_APPEND);
            
            // Aquí puedes implementar un plan alternativo para procesar la venta sin la sesión
            // Por ejemplo, almacenar estos datos en una tabla pendiente para procesamiento manual
        }
    } else {
        file_put_contents($log_file, date('[Y-m-d H:i:s] ') . "Error: Formato de referencia inválido\n", FILE_APPEND);
    }
    
} catch (Exception $e) {
    file_put_contents($log_file, date('[Y-m-d H:i:s] ') . "Error: " . $e->getMessage() . "\n", FILE_APPEND);
    http_response_code(500);
    exit("Error interno: " . $e->getMessage());
}

// Responder con éxito
http_response_code(200);
echo "OK";