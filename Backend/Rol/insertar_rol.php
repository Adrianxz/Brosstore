<?php
require_once "../../Admin/Modelos/rol.php";
$rol = new Rol();

switch ($_GET["op"]) {
  case 'insertar':
    $nombre = $_POST["ROLD_DESCRIP"];
    $rspta = $rol->insertar($nombre);
    echo $rspta ? "ok" : "error";
    break;

  case 'existe':
    $nombre = $_POST["ROLD_DESCRIP"];
    $existe = $rol->existe($nombre);
    echo $existe ? "existe" : "no_existe";
    break;
}
