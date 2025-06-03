<?php
// registrar_venta.php - Script para registrar la venta después de pago exitoso

// Verificar que hay sesión activa
if (!isset($_SESSION)) {
    session_start();
}

// Configurar zona horaria para Bogotá, Colombia
date_default_timezone_set('America/Bogota');

// Conectar a la base de datos si no está ya conectada
if (!isset($Conexion)) {
    require_once __DIR__ . '/../BD.php';
}

// Incluir PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/../../vendor/autoload.php'; // Ajusta la ruta según tu estructura

// Log para debugging
$log_file = __DIR__ . '/../../ventas_log.txt';
file_put_contents($log_file, date('[Y-m-d H:i:s] ') . "Iniciando registro de venta\n", FILE_APPEND);

// Verifica si el usuario está logueado y hay carrito
if (!isset($_SESSION['usuario']) || empty($_SESSION['carrito'])) {
    file_put_contents($log_file, date('[Y-m-d H:i:s] ') . "Error: Sesión expirada o carrito vacío\n", FILE_APPEND);
    echo "Error: Sesión expirada o carrito vacío.";
    exit;
}


function enviarCorreos($correo_cliente, $nombre_cliente, $orden, $total, $carrito, $cupon = null) {
    global $log_file;
    
    try {
       
        $mail = new PHPMailer(true);
        
        
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com'; 
        $mail->SMTPAuth   = true;
        $mail->Username   = 'brosstorexz@gmail.com';
        $mail->Password   = 'uqta fymj eozn bsur'; 
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;
        
        
        $mail->setFrom('brosstorexz@gmail.com', 'Tu Tienda Brosstore');
        $mail->CharSet = 'UTF-8';
        
        
        $mail->addAddress($correo_cliente, $nombre_cliente);
        
        $mail->isHTML(true);
        $mail->Subject = 'Confirmación de tu pedido #' . $orden;
        
        
        $tabla_productos = '';
        $subtotal = 0;
        foreach ($carrito as $item) {
            $precio_total = $item['cantidad'] * $item['precio'];
            $subtotal += $precio_total;
            
            $tabla_productos .= "
                <tr>
                    <td style='padding: 10px; border: 1px solid #ddd;'>{$item['nombre']}</td>
                    <td style='padding: 10px; border: 1px solid #ddd; text-align: center;'>{$item['tallaTexto']}</td>
                    <td style='padding: 10px; border: 1px solid #ddd; text-align: center;'>{$item['cantidad']}</td>
                    <td style='padding: 10px; border: 1px solid #ddd; text-align: right;'>$" . number_format($item['precio'], 0, ',', '.') . "</td>
                    <td style='padding: 10px; border: 1px solid #ddd; text-align: right;'>$" . number_format($precio_total, 0, ',', '.') . "</td>
                </tr>";
        }
        
        
        $info_cupon = '';
        if ($cupon) {
            $descuento = $subtotal - $cupon['nuevo_total'];
            $info_cupon = "
                <tr>
                    <td colspan='4' style='padding: 10px; text-align: right; font-weight: bold;'>Subtotal:</td>
                    <td style='padding: 10px; text-align: right;'>$" . number_format($subtotal, 0, ',', '.') . "</td>
                </tr>
                <tr>
                    <td colspan='4' style='padding: 10px; text-align: right; color: green;'>Descuento ({$cupon['codigo']}):</td>
                    <td style='padding: 10px; text-align: right; color: green;'>-$" . number_format($descuento, 0, ',', '.') . "</td>
                </tr>";
        }
        
        $mail->Body = "
        <html>
        <head>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background-color: #000; color: white; padding: 20px; text-align: center; }
                .content { padding: 20px; background-color: #f9f9f9; }
                table { width: 100%; border-collapse: collapse; margin: 20px 0; }
                th { background-color: #000; color: white; padding: 12px; }
                .total { background-color: #000; color: white; font-weight: bold; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1>¡Gracias por tu compra!</h1>
                </div>
                <div class='content'>
                    <center> <img src='https://blue-parrot-771704.hostingersite.com/404/images/Imagen1l.png'> </center>
                    <h2>Hola {$nombre_cliente},</h2>
                    <p>Tu pedido ha sido confirmado y está siendo procesado.</p>
                    
                    <h3>Detalles del pedido:</h3>
                    <p><strong>Número de pedido:</strong> {$orden}</p>
                    <p><strong>Fecha:</strong> " . date('d/m/Y H:i:s') . "</p>
                    
                    <table>
                        <thead>
                            <tr>
                                <th>Producto</th>
                                <th>Talla</th>
                                <th>Cantidad</th>
                                <th>Precio Unit.</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            {$tabla_productos}
                            {$info_cupon}
                            <tr class='total'>
                                <td colspan='4' style='padding: 15px; text-align: right;'>TOTAL:</td>
                                <td style='padding: 15px; text-align: right;'>$" . number_format($total, 0, ',', '.') . "</td>
                            </tr>
                        </tbody>
                    </table>
                    
                    <p><strong>Estado Atento.</strong> Tu equipo llegará pronto.</p>
                    <p>Recibirás un correo con información de seguimiento cuando tu pedido sea enviado.</p>
                    
                    <p>¡Gracias por elegir nuestra tienda!</p>
                </div>
            </div>
        </body>
        </html>";
        
        
        $mail->send();
        file_put_contents($log_file, date('[Y-m-d H:i:s] ') . "Correo enviado al cliente: $correo_cliente\n", FILE_APPEND);
        
        // === CORREO PARA EL ADMINISTRADOR ===
        $mail->clearAddresses();
        $mail->addAddress('brosstorexz@gmail.com', 'Administrador'); // Cambia por el email del admin
        
        $mail->Subject = 'Nueva venta registrada #' . $orden;
        
        $mail->Body = "
        <html>
        <head>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background-color: #000; color: white; padding: 20px; text-align: center; }
                .content { padding: 20px; background-color: #f9f9f9; }
                table { width: 100%; border-collapse: collapse; margin: 20px 0; }
                th { background-color: #000; color: white; padding: 12px; }
                .total { background-color: #000; color: white; font-weight: bold; }
                .alert { background-color: #fff3cd; border: 1px solid #ffeaa7; padding: 15px; margin: 10px 0; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1>Nueva Venta Registrada</h1>
                </div>
                <div class='content'>
                    <div class='alert'>
                        <h3>Información del cliente:</h3>
                        <p><strong>Nombre:</strong> {$nombre_cliente}</p>
                        <p><strong>Email:</strong> {$correo_cliente}</p>
                        <p><strong>Número de pedido:</strong> {$orden}</p>
                        <p><strong>Fecha:</strong> " . date('d/m/Y H:i:s') . "</p>
                        <p><strong>Total:</strong> $" . number_format($total, 0, ',', '.') . "</p>
                    </div>
                    
                    <h3>Productos vendidos:</h3>
                    <table>
                        <thead>
                            <tr>
                                <th>Producto</th>
                                <th>Talla</th>
                                <th>Cantidad</th>
                                <th>Precio Unit.</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            {$tabla_productos}
                            {$info_cupon}
                            <tr class='total'>
                                <td colspan='4' style='padding: 15px; text-align: right;'>TOTAL:</td>
                                <td style='padding: 15px; text-align: right;'>$" . number_format($total, 0, ',', '.') . "</td>
                            </tr>
                        </tbody>
                    </table>
                    
                    <p><strong>Acción requerida:</strong> Procesar y enviar el pedido.</p>
                </div>
            </div>
        </body>
        </html>";
        
        // Enviar correo al administrador
        $mail->send();
        file_put_contents($log_file, date('[Y-m-d H:i:s] ') . "Correo enviado al administrador\n", FILE_APPEND);
        
        return true;
        
    } catch (Exception $e) {
        file_put_contents($log_file, date('[Y-m-d H:i:s] ') . "Error enviando correos: {$mail->ErrorInfo}\n", FILE_APPEND);
        return false;
    }
}

try {
    // Obtener datos necesarios
    $cliente_id = $_SESSION['usuario']['Id'];
    $correo = $_SESSION['usuario']['Correo'];
    $nombre_cliente = $_SESSION['usuario']['Nombre'] ?? 'Cliente'; // Ajusta según tu estructura
    $carrito = $_SESSION['carrito'];
    $cupon = $_SESSION['cupon'] ?? null;
    
    // CORRECCIÓN: Calcular el total desde el carrito si no está disponible en la sesión
    if (isset($cupon['nuevo_total'])) {
        $total = (float)$cupon['nuevo_total']; // Convertir a float para cálculos
    } elseif (isset($_SESSION['total_general'])) {
        $total = (float)$_SESSION['total_general']; // Convertir a float para cálculos
    } elseif (isset($_SESSION['wompi_amount'])) {
        // Usar el monto guardado durante la creación de la transacción Wompi
        $total = (float)$_SESSION['wompi_amount'];
    } else {
        // Recalcular total desde el carrito
        $total = 0;
        foreach ($carrito as $item) {
            $total += $item['cantidad'] * $item['precio'];
        }
    }
    
    // CORRECCIÓN: Registrar el total calculado
    file_put_contents($log_file, date('[Y-m-d H:i:s] ') . "Total calculado para la venta: $total\n", FILE_APPEND);
    
    // Verificar que tenemos un total válido
    if ($total <= 0) {
        throw new Exception("Error: Total inválido ($total)");
    }
    
    $fecha = date('Y-m-d H:i:s'); // Ya está en zona horaria de Bogotá
    
    // Usar el ID de transacción de Wompi como orden en lugar de generar uno nuevo
    $orden = isset($_SESSION['wompi_transaction_id']) ? 
        $_SESSION['wompi_transaction_id'] : 
        uniqid("ORD-"); // Fallback si no hay ID de transacción
    
    // Log de información
    file_put_contents($log_file, date('[Y-m-d H:i:s] ') . "Cliente ID: $cliente_id, Total: $total\n", FILE_APPEND);
    file_put_contents($log_file, date('[Y-m-d H:i:s] ') . "Carrito: " . print_r($carrito, true) . "\n", FILE_APPEND);
    file_put_contents($log_file, date('[Y-m-d H:i:s] ') . "Orden/ID Transacción Wompi: $orden\n", FILE_APPEND);
    
    // Verificamos si es cliente por Gmail
    $esGmail = isset($_SESSION['usuario']['GMAIL']) && $_SESSION['usuario']['GMAIL'];
    $cliente_gmail_id = $esGmail ? $cliente_id : null;
    $cliente_normal_id = !$esGmail ? $cliente_id : null;
    
    // 1. Insertar en ventas
    $Conexion->begin_transaction(); // Iniciar transacción para garantizar integridad
    
    $sql = "INSERT INTO ventas (CLIENTE_ID, CLIENTE_GMAIL, VENTA_FECHA, VENTA_ORDEN, VENTA_TOTAL)
            VALUES (?, ?, ?, ?, ?)";
    $stmt = $Conexion->prepare($sql);
    
    // Verificar si la preparación fue exitosa
    if (!$stmt) {
        throw new Exception("Error preparando consulta: " . $Conexion->error);
    }
    
    // CORRECCIÓN: Convertir el total a valor entero para MySQL (ya que el campo es INT)
    // Si el total tiene decimales, redondear al entero más cercano
    $total_db = (int)round($total);
    
    file_put_contents($log_file, date('[Y-m-d H:i:s] ') . "Preparando para insertar total como entero: $total_db\n", FILE_APPEND);
    
    $stmt->bind_param("iissi", $cliente_normal_id, $cliente_gmail_id, $fecha, $orden, $total_db);
    $stmt->execute();
    
    if ($stmt->error) {
        throw new Exception("Error insertando venta: " . $stmt->error);
    }
    
    $venta_id = $stmt->insert_id;
    file_put_contents($log_file, date('[Y-m-d H:i:s] ') . "Venta insertada con ID: $venta_id\n", FILE_APPEND);
    
    // 2. Insertar productos y actualizar stock
    foreach ($carrito as $item) {
        $producto_id = $item['id'];
        $talla_texto = $item['tallaTexto'];
        $cantidad = $item['cantidad'];
        $precio = $item['precio'];
        
        file_put_contents($log_file, date('[Y-m-d H:i:s] ') . "Procesando producto ID: $producto_id, Talla: $talla_texto, Cantidad: $cantidad\n", FILE_APPEND);
        
        // CAMBIO: Obtener el ID de la talla a partir del texto
        $sql_talla = "SELECT TALLA_ID FROM tallas WHERE TALLA_DESCRIP = ?";
        $stmt_talla = $Conexion->prepare($sql_talla);
        
        if (!$stmt_talla) {
            throw new Exception("Error preparando consulta de talla: " . $Conexion->error);
        }
        
        $stmt_talla->bind_param("s", $talla_texto);
        $stmt_talla->execute();
        
        if ($stmt_talla->error) {
            throw new Exception("Error consultando ID de talla: " . $stmt_talla->error);
        }
        
        $result_talla = $stmt_talla->get_result();
        
        if ($result_talla->num_rows === 0) {
            throw new Exception("No se encontró ID para la talla: '$talla_texto'");
        }
        
        $talla_row = $result_talla->fetch_assoc();
        $talla_id = $talla_row['TALLA_ID'];
        
        file_put_contents($log_file, date('[Y-m-d H:i:s] ') . "Talla ID obtenido: $talla_id para texto: $talla_texto\n", FILE_APPEND);
        
        // Insertar en producto_ventas (guardamos el texto de la talla para referencia)
        $sql = "INSERT INTO producto_ventas (VENTA_ID, PRODUCTO_VENTAS, TALLA, CANTIDAD,PRECIO_VENTA)
                VALUES (?, ?, ?, ?,?)";
        $stmt = $Conexion->prepare($sql);
        
        if (!$stmt) {
            throw new Exception("Error preparando consulta de producto_ventas: " . $Conexion->error);
        }
        
        $stmt->bind_param("iisid", $venta_id, $producto_id, $talla_texto, $cantidad,$precio);
        $stmt->execute();
        
        if ($stmt->error) {
            throw new Exception("Error insertando producto_ventas: " . $stmt->error);
        }
        
        // CAMBIO: Actualizar stock usando el ID de la talla, no el texto
        $sql = "UPDATE producto_tallas 
                SET STOCK = STOCK - ? 
                WHERE PRODUCTO = ? AND TALLAS = ?";
        $stmt = $Conexion->prepare($sql);
        
        if (!$stmt) {
            throw new Exception("Error preparando consulta de actualización de stock: " . $Conexion->error);
        }
        
        // Asegurar que cantidad es un entero
        $cantidad_int = (int)$cantidad;
        $producto_id_int = (int)$producto_id;
        
        // Ahora pasamos el ID de la talla, no el texto
        $stmt->bind_param("iii", $cantidad_int, $producto_id_int, $talla_id);
        $stmt->execute();
        
        if ($stmt->error) {
            throw new Exception("Error actualizando stock: " . $stmt->error . " para Producto: $producto_id, Talla ID: $talla_id, Cantidad: $cantidad");
        }
        
        // Verificar si se actualizó correctamente
        if ($stmt->affected_rows == 0) {
            file_put_contents($log_file, date('[Y-m-d H:i:s] ') . "ADVERTENCIA: No se actualizó el stock para Producto: $producto_id, Talla ID: $talla_id\n", FILE_APPEND);
        } else {
            file_put_contents($log_file, date('[Y-m-d H:i:s] ') . "Stock actualizado correctamente\n", FILE_APPEND);
        }
    }
    
    // 3. Si hay cupon, actualizar uso
    if ($cupon) {
        $codigo_cupon = $cupon['codigo'];
        $sql = "UPDATE cupones SET CUPON_USO = CUPON_USO - 1 WHERE CUPON_CODIGO = ?";
        $stmt = $Conexion->prepare($sql);
        
        if (!$stmt) {
            throw new Exception("Error preparando consulta de actualización de cupón: " . $Conexion->error);
        }
        
        $stmt->bind_param("s", $codigo_cupon);
        $stmt->execute();
        
        if ($stmt->error) {
            throw new Exception("Error actualizando uso de cupón: " . $stmt->error);
        }
        
        file_put_contents($log_file, date('[Y-m-d H:i:s] ') . "Cupón actualizado: $codigo_cupon\n", FILE_APPEND);
    }
    
    // Confirmar cambios (commit)
    $Conexion->commit();
    file_put_contents($log_file, date('[Y-m-d H:i:s] ') . "Transacción completada con éxito\n", FILE_APPEND);
    
    // 4. ENVIAR CORREOS ELECTRÓNICOS
    file_put_contents($log_file, date('[Y-m-d H:i:s] ') . "Iniciando envío de correos\n", FILE_APPEND);
    $correos_enviados = enviarCorreos($correo, $nombre_cliente, $orden, $total, $carrito, $cupon);
    
    if ($correos_enviados) {
        file_put_contents($log_file, date('[Y-m-d H:i:s] ') . "Correos enviados exitosamente\n", FILE_APPEND);
    } else {
        file_put_contents($log_file, date('[Y-m-d H:i:s] ') . "Error al enviar correos (pero venta registrada)\n", FILE_APPEND);
    }
    
    // 5. Limpiar carrito, cupon y datos de transacción
    unset($_SESSION['carrito']);
    unset($_SESSION['cupon']);
    unset($_SESSION['total_general']);
    unset($_SESSION['wompi_reference']);
    unset($_SESSION['wompi_amount']);
    unset($_SESSION['wompi_transaction_id']);
    
    // 6. Indicar éxito
    $success = true;
    file_put_contents($log_file, date('[Y-m-d H:i:s] ') . "Venta registrada correctamente: $orden\n", FILE_APPEND);
    
    // Si se llama directamente, mostrar mensaje de éxito
    if (basename($_SERVER['PHP_SELF']) == basename(__FILE__)) {
        echo "<h2>Pago exitoso</h2>";
        echo "<p>Tu orden <strong>$orden</strong> ha sido registrada correctamente.</p>";
        if ($correos_enviados) {
            echo "<p>Se ha enviado un correo de confirmación a tu email: <strong>$correo</strong></p>";
        }
        echo "<a href='../../mis_pedidos.php'>Ver mis pedidos</a>";
    }
    
} catch (Exception $e) {
    // Si hay error, hacer rollback
    if (isset($Conexion)) {
        $Conexion->rollback();
    }
    
    file_put_contents($log_file, date('[Y-m-d H:i:s] ') . "ERROR: " . $e->getMessage() . "\n", FILE_APPEND);
    
    if (basename($_SERVER['PHP_SELF']) == basename(__FILE__)) {
        echo "<h2>Error al procesar tu pago</h2>";
        echo "<p>Lo sentimos, ha ocurrido un error al registrar tu compra. Por favor, contacta con soporte.</p>";
        // En producción, nunca mostrar el error específico al usuario
        // echo "<p>Error: " . $e->getMessage() . "</p>";
    }
}
?>