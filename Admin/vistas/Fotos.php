<?php
header('Content-Type: application/json');
require('../../Backend/BD.php');


$pro_id = isset($_GET['pro_id']) ? (int)$_GET['pro_id'] : 0;


$sql = "SELECT pf.FOTO
        FROM producto_fotos AS pf
        WHERE pf.PRO_ID = $pro_id
        LIMIT 3";  // opcional, puedes quitar el LIMIT
$res = mysqli_query($Conexion, $sql);

$urls = [];
while($row = mysqli_fetch_assoc($res)) {
    $urls[] = "../images/".$row['FOTO'];
}

echo json_encode($urls);
