<?php
// Backend/Checkout/Cupon.php - Archivo actualizado para verificar y aplicar cupones

// Iniciar sesión si no está iniciada
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Incluir archivo de conexión a la base de datos
require_once '../BD.php';

// Verificar si se recibió un código de cupón
if (isset($_POST['codigo_cupon'])) {
    $codigo = trim($_POST['codigo_cupon']);
    
    // Validar que el código no esté vacío
    if (empty($codigo)) {
        echo json_encode(['status' => 'error', 'message' => 'Por favor ingrese un código de cupón']);
        exit;
    }
    
    // Verificar si ya hay un cupón aplicado en la sesión
    if (isset($_SESSION['cupon']) && $_SESSION['cupon']['codigo'] === $codigo) {
        echo json_encode(['status' => 'error', 'message' => 'Este cupón ya está aplicado']);
        exit;
    }
    
    // Verificar que existan productos en el carrito
    if (!isset($_SESSION['carrito']) || !is_array($_SESSION['carrito']) || count($_SESSION['carrito']) === 0) {
        echo json_encode(['status' => 'error', 'message' => 'No hay productos en el carrito']);
        exit;
    }
    
    // Preparar la consulta SQL para buscar el cupón
    $sql = "SELECT CUPON_ID, CUPON_CODIGO, CUPON_DESCUENTO, CUPON_USO 
            FROM cupones 
            WHERE CUPON_CODIGO = ?";
    
    // Preparar la sentencia
    $stmt = $Conexion->prepare($sql);
    $stmt->bind_param("s", $codigo);
    $stmt->execute();
    $resultado = $stmt->get_result();
    
    // Verificar si se encontró el cupón
    if ($resultado->num_rows > 0) {
        $cupon = $resultado->fetch_assoc();
        
        // Verificar si el cupón ya fue utilizado el máximo de veces
        if ($cupon['CUPON_USO'] > 0) {
            // Obtener el total actual del carrito
            $total_carrito = 0;
            
            foreach ($_SESSION['carrito'] as $producto) {
                // Asegurarse que cantidad y precio sean valores numéricos
                $cantidad = isset($producto['cantidad']) ? floatval($producto['cantidad']) : 0;
                $precio = isset($producto['precio']) ? floatval($producto['precio']) : 0;
                
                // Acumular al total
                $total_carrito += $cantidad * $precio;
            }
            
            // Verificar que el total del carrito sea mayor a cero
            if ($total_carrito <= 0) {
                echo json_encode(['status' => 'error', 'message' => 'El total del carrito debe ser mayor a cero']);
                exit;
            }
            
            // Calcular el descuento (asegurarse que sea un valor numérico válido)
            $porcentaje_descuento = floatval($cupon['CUPON_DESCUENTO']);
            
            // Validar que el porcentaje de descuento sea válido (entre 0 y 100)
            if ($porcentaje_descuento < 0) $porcentaje_descuento = 0;
            if ($porcentaje_descuento > 100) $porcentaje_descuento = 100;
            
            // Calcular el monto del descuento
            $monto_descuento = round($total_carrito * ($porcentaje_descuento / 100), 2);
            
            // Calcular el nuevo total con el descuento aplicado
            $nuevo_total = $total_carrito - $monto_descuento;
            
            // Verificar que el nuevo total no sea negativo
            if ($nuevo_total < 0) $nuevo_total = 0;
            
            // Almacenar información del cupón en la sesión
            $_SESSION['cupon'] = [
                'id' => $cupon['CUPON_ID'],
                'codigo' => $cupon['CUPON_CODIGO'],
                'descuento' => $porcentaje_descuento,
                'monto_descuento' => $monto_descuento,
                'total_original' => $total_carrito,
                'nuevo_total' => $nuevo_total
            ];
            
            // Responder con éxito
            echo json_encode([
                'status' => 'success',
                'message' => 'Cupón aplicado correctamente',
                'descuento' => $porcentaje_descuento,
                'monto_descuento' => $monto_descuento,
                'total_original' => $total_carrito,
                'nuevo_total' => $nuevo_total
            ]);
            
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Este cupón ya ha sido utilizado el máximo de veces permitido']);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Código de cupón inválido']);
    }
    
    // Cerrar la conexión
    $stmt->close();
    $Conexion->close();
    
    exit;
}

// Si no se recibió un POST, devolver un error en formato JSON
echo json_encode(['status' => 'error', 'message' => 'Solicitud inválida']);
?>
