<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once "../../Admin/Modelos/cliente.php";
$cliente = new Cliente();

switch ($_GET["op"]) {
  case 'insertar':
    $nombre = $_POST["CLIENTE_NOMBRE"];
    $apellido = $_POST["CLIENTE_APELLIDO"];
    $numident = $_POST["CLIENTE_NUMIDENT"];
    $correo = $_POST["CLIENTE_CORREO"];
    $tel = $_POST["CLIENTE_TEL"];
    $direccion = $_POST["CLIENTE_DIRECCION"];
    $pais = $_POST["PAIS"];
    $ciudad = $_POST["CIUDAD"];
    $contra = $_POST["CLIENTE_CONTRA"];

    $rspta = $cliente->insertar($nombre, $apellido, $numident, $correo, $tel, $direccion, $pais, $ciudad, $contra);
    echo $rspta ? "ok" : "error";
    break;
}
