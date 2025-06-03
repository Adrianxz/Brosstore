<?php
// confirmarWompi.php - Página de confirmación después del pago con Wompi
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Configurar zona horaria para Bogotá, Colombia
date_default_timezone_set('America/Bogota');

// Iniciar sesión para acceder a los datos del carrito
session_start();

// Configuración de Wompi
$wompi_public_key = 'pub_test_EtD8UHC27HpmawCaf2afQSzkX6TlSTIg';
$wompi_private_key = 'prv_test_DUNPFPaZHfqmTSPS06GpUifu44n2q8kd';
$wompi_environment = 'sandbox'; // Cambia a 'production' cuando estés listo para producción

// URLs de Wompi según ambiente
$wompi_base_url = ($wompi_environment === 'sandbox') 
    ? 'https://sandbox.wompi.co/v1' 
    : 'https://production.wompi.co/v1';

// Recuperar la información de la transacción desde la URL
$reference = isset($_GET['reference']) ? $_GET['reference'] : '';
$transactionId = isset($_GET['id']) ? $_GET['id'] : '';
$status = isset($_GET['status']) ? $_GET['status'] : '';

// Log para debugging
$log_file = __DIR__ . '/wompi_log.txt';
file_put_contents($log_file, date('[Y-m-d H:i:s] ') . "Confirmación de pago Wompi iniciada\n", FILE_APPEND);
file_put_contents($log_file, date('[Y-m-d H:i:s] ') . "Parámetros recibidos: Reference: $reference, ID: $transactionId, Status: $status\n", FILE_APPEND);

// Verificar el estado de la transacción con Wompi
$transaction_details = null;
if ($transactionId) {
    // Configurar la solicitud cURL para obtener detalles de la transacción
    $transaction_url = $wompi_base_url . '/transactions/' . $transactionId;
    $ch = curl_init($transaction_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Bearer ' . $wompi_private_key // Usar la clave privada para autenticación
    ]);
    
    // Ejecutar la solicitud
    $response = curl_exec($ch);
    $status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    file_put_contents($log_file, date('[Y-m-d H:i:s] ') . "Respuesta de Wompi (HTTP $status_code): " . $response . "\n", FILE_APPEND);
    
    if ($status_code == 200) {
        $transaction_details = json_decode($response, true);
        // Verificar el estado real de la transacción
        $actual_status = $transaction_details['data']['status'];
        
        file_put_contents($log_file, date('[Y-m-d H:i:s] ') . "Transacción {$transactionId} verificada. Estado: {$actual_status}\n", FILE_APPEND);
        
        // Si la transacción está aprobada, registrar la venta
        if ($actual_status === 'APPROVED') {
            // Guardar el ID de transacción de Wompi en la sesión para usarlo como VENTA_ORDEN
            $_SESSION['wompi_transaction_id'] = $transactionId;
            
            // Intentar restaurar la sesión si es necesario
            if (empty($_SESSION['usuario']) || empty($_SESSION['carrito'])) {
                file_put_contents($log_file, date('[Y-m-d H:i:s] ') . "Sesión perdida, intentando recuperar basado en referencia: $reference\n", FILE_APPEND);
                
                // La referencia podría contener información para recuperar la sesión
                // Por ejemplo, si usaste session_id() como parte de la referencia
                $session_parts = explode('-', $reference);
                if (count($session_parts) > 1) {
                    $possible_session_id = $session_parts[1];
                    session_write_close(); // Cerrar la sesión actual
                    session_id($possible_session_id); // Intentar usar el ID de sesión recuperado
                    session_start();
                }
            }
            
            // Verificar si la sesión se recuperó correctamente
            if (isset($_SESSION['usuario']) && isset($_SESSION['carrito'])) {
                try {
                    // Incluir el archivo para registrar la venta
                    require_once 'Backend/BD.php';
                    
                    // Conectar a la base de datos si no está ya conectada
                    if (!isset($Conexion)) {
                        // Si no se ha incluido BD.php, intenta hacerlo en otra ruta
                        if (!class_exists('mysqli') || !isset($Conexion)) {
                            require_once __DIR__ . '/Backend/BD.php';
                        }
                    }
                    
                    // Incluir archivo de registro de venta
                    include_once 'Backend/Checkout/registraVenta.php';
                    
                    file_put_contents($log_file, date('[Y-m-d H:i:s] ') . "Venta registrada correctamente para la transacción {$transactionId}\n", FILE_APPEND);
                } catch (Exception $e) {
                    file_put_contents($log_file, date('[Y-m-d H:i:s] ') . "Error al registrar venta: " . $e->getMessage() . "\n", FILE_APPEND);
                }
            } else {
                file_put_contents($log_file, date('[Y-m-d H:i:s] ') . "No se pudo recuperar la sesión, imposible registrar venta\n", FILE_APPEND);
                
                // Guardar la información de la transacción para procesarla más tarde
                try {
                    if (isset($Conexion)) {
                        $sql = "INSERT INTO wompi_transactions (TRANSACTION_ID, REFERENCE, STATUS, AMOUNT, DATA, CREATED_AT) 
                                VALUES (?, ?, ?, ?, ?, NOW())";
                        $stmt = $Conexion->prepare($sql);
                        
                        if ($stmt) {
                            $amount = $transaction_details['data']['amount_in_cents'] / 100;
                            $data_json = json_encode($transaction_details);
                            $stmt->bind_param("sssds", $transactionId, $reference, $actual_status, $amount, $data_json);
                            $stmt->execute();
                            
                            file_put_contents($log_file, date('[Y-m-d H:i:s] ') . "Información de transacción guardada para procesamiento posterior\n", FILE_APPEND);
                        }
                    }
                } catch (Exception $e) {
                    file_put_contents($log_file, date('[Y-m-d H:i:s] ') . "Error guardando información de transacción: " . $e->getMessage() . "\n", FILE_APPEND);
                }
            }
        }
    } else {
        file_put_contents($log_file, date('[Y-m-d H:i:s] ') . "Error verificando transacción con Wompi: " . $response . "\n", FILE_APPEND);
    }
}

// Generar el mensaje adecuado según el estado
$message = '';
$status_class = '';

if (isset($transaction_details) && $transaction_details['data']['status'] === 'APPROVED') {
    $message = "¡Pago exitoso! Tu compra ha sido procesada correctamente.";
    $status_class = "success";
} elseif (isset($transaction_details) && $transaction_details['data']['status'] === 'PENDING') {
    $message = "Tu pago está siendo procesado. Te notificaremos cuando se complete.";
    $status_class = "pending";
} else {
    $message = "Lo sentimos, hubo un problema con tu pago. Por favor intenta nuevamente.";
    $status_class = "error";
}

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirmación de Pago</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            margin: 0;
            padding: 20px;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            background-color: #f7f7f7;
        }
        .container {
            max-width: 600px;
            padding: 30px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            text-align: center;
        }
        h1 {
            color: #333;
            margin-bottom: 20px;
        }
        .message {
            font-size: 18px;
            margin-bottom: 20px;
            padding: 15px;
            border-radius: 5px;
        }
        .success {
            background-color: #d4edda;
            color: #155724;
        }
        .pending {
            background-color: #fff3cd;
            color: #856404;
        }
        .error {
            background-color: #f8d7da;
            color: #721c24;
        }
        .details {
            text-align: left;
            margin-top: 25px;
            padding: 15px;
            background-color: #f8f9fa;
            border-radius: 5px;
        }
        .details p {
            margin: 8px 0;
        }
        .button {
            display: inline-block;
            background-color: #007bff;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 20px;
            font-weight: bold;
        }
        .button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Confirmación de Pago</h1>
        
        <div class="message <?php echo $status_class; ?>">
            <?php echo $message; ?>
        </div>
        
        <?php if (isset($transaction_details) && is_array($transaction_details['data'])): ?>
        <div class="details">
            <p><strong>Referencia:</strong> <?php echo htmlspecialchars($transaction_details['data']['reference']); ?></p>
            <p><strong>ID de Transacción:</strong> <?php echo htmlspecialchars($transaction_details['data']['id']); ?></p>
            <p><strong>Monto:</strong> <?php echo number_format($transaction_details['data']['amount_in_cents']/100, 2); ?> <?php echo htmlspecialchars($transaction_details['data']['currency']); ?></p>
            <p><strong>Estado:</strong> <?php echo htmlspecialchars($transaction_details['data']['status']); ?></p>
            <p><strong>Fecha:</strong> <?php echo date('d/m/Y H:i:s', strtotime($transaction_details['data']['created_at'])); ?></p>
        </div>
        <?php endif; ?>
        
        <a href="index" class="button">Volver a la tienda</a>
    </div>
</body>
</html>