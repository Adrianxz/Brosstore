<?php
require('../BD.php');

$productoId = isset($_POST['productoId']) ? intval($_POST['productoId']) : null;
$tallaId = isset($_POST['tallaId']) ? intval($_POST['tallaId']) : null;

header('Content-Type: application/json');

if ($productoId && $tallaId) {
    $query = "SELECT STOCK FROM producto_tallas WHERE PRODUCTO = $productoId AND TALLAS = $tallaId";
    $result = mysqli_query($Conexion, $query);

    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        echo json_encode(['stock' => intval($row['STOCK'])]);
    } else {
        echo json_encode(['stock' => 0]);
    }
} else {
    echo json_encode(['error' => 'Datos incompletos']);
}
?>
