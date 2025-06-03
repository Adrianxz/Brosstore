<?php
require('../BD.php');

header('Content-Type: application/json');

$productoId = isset($_POST['productoId']) ? intval($_POST['productoId']) : null;
$tallaTexto = isset($_POST['tallaTexto']) ? $_POST['tallaTexto'] : null;

if ($productoId && $tallaTexto) {
    // Primero obtenemos el ID de la talla basado en el texto
    $tallaQuery = "SELECT TALLA_ID FROM tallas WHERE TALLA_DESCRIP = '" . mysqli_real_escape_string($Conexion, $tallaTexto) . "'";
    $tallaResult = mysqli_query($Conexion, $tallaQuery);
    
    if ($tallaResult && mysqli_num_rows($tallaResult) > 0) {
        $tallaRow = mysqli_fetch_assoc($tallaResult);
        $tallaId = $tallaRow['TALLA_ID'];
        
        // Ahora consultamos el stock con el ID de la talla
        $stockQuery = "SELECT STOCK FROM producto_tallas WHERE PRODUCTO = $productoId AND TALLAS = $tallaId";
        $stockResult = mysqli_query($Conexion, $stockQuery);
        
        if ($stockResult && mysqli_num_rows($stockResult) > 0) {
            $stockRow = mysqli_fetch_assoc($stockResult);
            echo json_encode(['stock' => intval($stockRow['STOCK'])]);
        } else {
            echo json_encode(['stock' => 0]);
        }
    } else {
        echo json_encode(['error' => 'Talla no encontrada']);
    }
} else {
    echo json_encode(['error' => 'Datos incompletos']);
}
?>