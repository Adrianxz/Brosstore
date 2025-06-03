<?php
require('../BD.php');
header('Content-Type: application/json');

session_start(); 

$name = mysqli_real_escape_string($Conexion, $_POST['name']);
$contra = mysqli_real_escape_string($Conexion, $_POST['contra']);
$Contra = null;

$Select = "SELECT `CLIENTE_CONTRA` FROM `cliente` WHERE `CLIENTE_CORREO` = '$name'";
$result = mysqli_query($Conexion, $Select);

if (mysqli_num_rows($result) > 0) {
    $R = mysqli_fetch_assoc($result);
    if (password_verify($contra, $R['CLIENTE_CONTRA'])) {
        $Contra = $R['CLIENTE_CONTRA'];
    }
}

$SelectPersona = "SELECT * FROM `cliente` WHERE `CLIENTE_CORREO` = '$name' AND `CLIENTE_CONTRA` = '$Contra'";
$resultPersona = mysqli_query($Conexion, $SelectPersona);

if (mysqli_num_rows($resultPersona) > 0) {
    $Query = mysqli_fetch_assoc($resultPersona);

    $_SESSION['usuario'] = [
        'Id' => $Query['CLIENTE_ID'],
        'Correo' => $Query['CLIENTE_CORREO'],
        'Nombre' => $Query['CLIENTE_NOMBRE'],
        'Pais' => $Query['PAIS'],
        'Ciudad' => $Query['CIUDAD'],
        'Direcc' =>$Query['CLIENTE_DIRECCION']
    ];

    http_response_code(200);
    echo json_encode([]);
} else {
    http_response_code(400);
    echo json_encode(["Message" => "Inicio de sesión incorrecto"]);
    exit();
}

mysqli_close($Conexion);
?>
