<?php
require_once "../../Admin/Modelos/cliente.php";
$cliente = new Cliente();

switch ($_GET["op"]) {
  case 'eliminar':
    $id = $_POST["CLIENTE_ID"];
    $rspta = $cliente->eliminar($id);
    echo $rspta ? "ok" : "error";
    break;
}
