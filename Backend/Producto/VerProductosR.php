<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require('../BD.php');

// Función para sanitizar entrada
function sanitizeInput($input) {
    return mysqli_real_escape_string($GLOBALS['Conexion'], trim($input));
}

// Función para enviar respuesta JSON
function sendJsonResponse($data, $httpCode = 200) {
    http_response_code($httpCode);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}

$productoId = isset($_POST['Id']) ? sanitizeInput($_POST['Id']) : null;

if (!$productoId || !is_numeric($productoId)) {
    sendJsonResponse(['error' => 'ID de producto no válido'], 400);
}

try {
    // Iniciar transacción para consistencia
    mysqli_autocommit($Conexion, FALSE);
    
    // Actualizar vistas usando prepared statement
    $updateStmt = mysqli_prepare($Conexion, "UPDATE producto SET PRO_VISTAS = PRO_VISTAS + 1 WHERE PRO_ID = ?");
    mysqli_stmt_bind_param($updateStmt, "i", $productoId);
    mysqli_stmt_execute($updateStmt);
    
    // Traer producto + foto principal + categoria
    $selectPStmt = mysqli_prepare($Conexion, "
        SELECT p.PRO_ID, p.PRO_NOMBRE, p.PRO_DESCRIP, p.PRO_PRECIO, p.CAT_ID, pf.FOTO
        FROM producto p 
        INNER JOIN producto_fotos pf ON p.PRO_ID = pf.PRO_ID 
        WHERE pf.FOTO_PRINCIPAL = 1 AND p.PRO_ID = ?
    ");
    mysqli_stmt_bind_param($selectPStmt, "i", $productoId);
    mysqli_stmt_execute($selectPStmt);
    $resultP = mysqli_stmt_get_result($selectPStmt);
    
    if (!$resultP || mysqli_num_rows($resultP) === 0) {
        mysqli_rollback($Conexion);
        sendJsonResponse(['error' => 'Producto no encontrado'], 404);
    }
    
    $producto = mysqli_fetch_assoc($resultP);
    
    // Traer fotos adicionales
    $selectFotStmt = mysqli_prepare($Conexion, "
        SELECT pf.FOTO 
        FROM producto_fotos pf 
        WHERE pf.FOTO_PRINCIPAL = 0 AND pf.PRO_ID = ?
        ORDER BY pf.FOTO_ID
    ");
    mysqli_stmt_bind_param($selectFotStmt, "i", $productoId);
    mysqli_stmt_execute($selectFotStmt);
    $resultFot = mysqli_stmt_get_result($selectFotStmt);
    
    $fotosAdicionales = [];
    if ($resultFot) {
        $fotosAdicionales = mysqli_fetch_all($resultFot, MYSQLI_ASSOC);
    }
    
    // Obtener tallas según la categoría
    $tallas = [];
    if ($producto['CAT_ID'] == 1) {
        // Producto calzado: traer tallas con categoria 1
        $tallasStmt = mysqli_prepare($Conexion, "SELECT TALLA_ID, TALLA_DESCRIP FROM tallas WHERE CATEGORIA = 1 ORDER BY TALLA_DESCRIP");
    } else {
        // Otro producto: traer tallas que NO sean de categoria 1
        $tallasStmt = mysqli_prepare($Conexion, "SELECT TALLA_ID, TALLA_DESCRIP FROM tallas WHERE CATEGORIA != 1 ORDER BY TALLA_DESCRIP");
    }
    
    mysqli_stmt_execute($tallasStmt);
    $resultTallas = mysqli_stmt_get_result($tallasStmt);
    
    if ($resultTallas) {
        $tallas = mysqli_fetch_all($resultTallas, MYSQLI_ASSOC);
    }
    
    // Confirmar transacción
    mysqli_commit($Conexion);
    
    // Preparar respuesta
    $response = [
        'success' => true,
        'nombre' => $producto['PRO_NOMBRE'],
        'descripcion' => $producto['PRO_DESCRIP'],
        'precio' => floatval($producto['PRO_PRECIO']),
        'fotoPrincipal' => 'images/' . $producto['FOTO'],
        'fotosAdicionales' => $fotosAdicionales,
        'tallas' => $tallas,
        'categoria' => intval($producto['CAT_ID'])
    ];
    
    sendJsonResponse($response);
    
} catch (Exception $e) {
    // Rollback en caso de error
    mysqli_rollback($Conexion);
    error_log("Error en VerProductosR.php: " . $e->getMessage());
    sendJsonResponse(['error' => 'Error interno del servidor'], 500);
} finally {
    // Restaurar autocommit
    mysqli_autocommit($Conexion, TRUE);
    
    // Cerrar statements si existen
    if (isset($updateStmt)) mysqli_stmt_close($updateStmt);
    if (isset($selectPStmt)) mysqli_stmt_close($selectPStmt);
    if (isset($selectFotStmt)) mysqli_stmt_close($selectFotStmt);
    if (isset($tallasStmt)) mysqli_stmt_close($tallasStmt);
}
?>
