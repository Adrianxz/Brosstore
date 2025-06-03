<?php
require_once "../../Admin/Modelos/cliente.php";
$cliente = new Cliente();

switch ($_GET["op"]) {
  case 'actualizar':
    $id = $_POST["CLIENTE_ID"];
    $nombre = $_POST["CLIENTE_NOMBRE"];
    $apellido = $_POST["CLIENTE_APELLIDO"];
    $numident = $_POST["CLIENTE_NUMIDENT"];
    $correo = $_POST["CLIENTE_CORREO"];
    $tel = $_POST["CLIENTE_TEL"];
    $direccion = $_POST["CLIENTE_DIRECCION"];
    $pais = $_POST["PAIS"];
    $ciudad = $_POST["CIUDAD"];
    $contra = $_POST["CLIENTE_CONTRA"];

    // Llama al método actualizar del modelo
    $rspta = $cliente->actualizar($id, $nombre, $apellido, $numident, $correo, $tel, $direccion, $pais, $ciudad, $contra);
    echo $rspta ? "ok" : "error";
    break;

  case 'obtener':
    $id = $_POST["CLIENTE_ID"];
    $data = $cliente->obtenerPorId($id);
    echo json_encode($data);
    break;
}
