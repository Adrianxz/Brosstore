<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
header('Content-Type: application/json');
require('../Backend/BD.php');

// Verificar que sea una petición POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
    exit;
}

// Obtener datos JSON
$input = file_get_contents('php://input');
$data = json_decode($input, true);

if (!$data) {
    echo json_encode(['success' => false, 'message' => 'Datos inválidos']);
    exit;
}

// Log para debugging
error_log("Datos recibidos: " . print_r($data, true));

// Validar datos requeridos
if (empty($data['cliente_id']) || empty($data['fecha']) || empty($data['items'])) {
    echo json_encode(['success' => false, 'message' => 'Faltan datos requeridos']);
    exit;
}

// Iniciar transacción
mysqli_begin_transaction($Conexion);

try {
    // 1. Insertar venta principal
    $cliente_id = intval($data['cliente_id']);
    $fecha = mysqli_real_escape_string($Conexion, $data['fecha']);
    $orden = mysqli_real_escape_string($Conexion, $data['numero'] ?? '');
    $total = floatval($data['total']);
    
    $insertVenta = "INSERT INTO `ventas`(`CLIENTE_ID`, `VENTA_FECHA`, `CLIENTE_GMAIL`, `VENTA_ORDEN`, `VENTA_TOTAL`, `TIPO`) 
                    VALUES (?, ?, NULL, ?, ?, 1)";
    
    $stmt = mysqli_prepare($Conexion, $insertVenta);
    mysqli_stmt_bind_param($stmt, "issd", $cliente_id, $fecha, $orden, $total);
    
    if (!mysqli_stmt_execute($stmt)) {
        throw new Exception('Error al insertar venta: ' . mysqli_error($Conexion));
    }
    
    $venta_id = mysqli_insert_id($Conexion);
    mysqli_stmt_close($stmt);
    
    // 2. Procesar cada item de la venta
    foreach ($data['items'] as $item) {
        // Log del item para debugging
        error_log("Procesando item: " . print_r($item, true));
        
        $producto_id = intval($item['id']);
        $talla_id = intval($item['tallaId'] ?? 0);
        $cantidad = intval($item['cantidad']);
        $precio_venta = floatval($item['precio']);
        
        // CORREGIDO: Usar tallaDescrip en lugar de size, con fallback
        $talla_nombre = '';
        if (isset($item['tallaDescrip']) && !empty($item['tallaDescrip'])) {
            $talla_nombre = mysqli_real_escape_string($Conexion, $item['tallaDescrip']);
        } elseif (isset($item['size']) && !empty($item['size'])) {
            $talla_nombre = mysqli_real_escape_string($Conexion, $item['size']);
        } else {
            $talla_nombre = 'Talla no especificada';
        }
        
        // Verificar stock actual solo si hay talla_id válido
        if ($talla_id > 0) {
            $checkStock = "SELECT STOCK FROM producto_tallas WHERE PRODUCTO = ? AND TALLAS = ?";
            $stmt = mysqli_prepare($Conexion, $checkStock);
            mysqli_stmt_bind_param($stmt, "ii", $producto_id, $talla_id);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            $stockRow = mysqli_fetch_assoc($result);
            mysqli_stmt_close($stmt);
            
            if ($stockRow) {
                $stock_actual = intval($stockRow['STOCK']);
                
                if ($cantidad > $stock_actual) {
                    throw new Exception("Stock insuficiente para producto ID: $producto_id. Disponible: $stock_actual, Solicitado: $cantidad");
                }
                
                // Actualizar stock
                $nuevo_stock = $stock_actual - $cantidad;
                $updateStock = "UPDATE `producto_tallas` SET `STOCK` = ? WHERE `PRODUCTO` = ? AND `TALLAS` = ?";
                $stmt = mysqli_prepare($Conexion, $updateStock);
                mysqli_stmt_bind_param($stmt, "iii", $nuevo_stock, $producto_id, $talla_id);
                
                if (!mysqli_stmt_execute($stmt)) {
                    throw new Exception('Error al actualizar stock: ' . mysqli_error($Conexion));
                }
                mysqli_stmt_close($stmt);
                
                error_log("Stock actualizado para producto $producto_id, talla $talla_id: $stock_actual -> $nuevo_stock");
            } else {
                // Si no hay registro de stock, solo registrar la venta sin actualizar stock
                error_log("No se encontró registro de stock para producto $producto_id, talla $talla_id. Continuando sin actualizar stock.");
            }
        } else {
            // Si no hay talla_id válido, solo registrar la venta
            error_log("Talla ID no válido para producto $producto_id. Continuando sin actualizar stock.");
        }
        
        // Insertar detalle de venta
        $insertDetalle = "INSERT INTO `producto_ventas`(`VENTA_ID`, `PRODUCTO_VENTAS`, `TALLA`, `CANTIDAD`, `PRECIO_VENTA`) 
                         VALUES (?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($Conexion, $insertDetalle);
        mysqli_stmt_bind_param($stmt, "iisid", $venta_id, $producto_id, $talla_nombre, $cantidad, $precio_venta);
        
        if (!mysqli_stmt_execute($stmt)) {
            throw new Exception('Error al insertar detalle de venta: ' . mysqli_error($Conexion));
        }
        mysqli_stmt_close($stmt);
        
        error_log("Detalle de venta insertado: Producto $producto_id, Talla: $talla_nombre, Cantidad: $cantidad");
    }
    
    // Confirmar transacción
    mysqli_commit($Conexion);
    
    error_log("Venta guardada exitosamente con ID: $venta_id");
    
    echo json_encode([
        'success' => true, 
        'message' => 'Venta guardada exitosamente',
        'venta_id' => $venta_id
    ]);
    
} catch (Exception $e) {
    // Revertir transacción en caso de error
    mysqli_rollback($Conexion);
    
    error_log("Error al guardar venta: " . $e->getMessage());
    
    echo json_encode([
        'success' => false, 
        'message' => $e->getMessage()
    ]);
}

// Cerrar conexión
mysqli_close($Conexion);
?>
