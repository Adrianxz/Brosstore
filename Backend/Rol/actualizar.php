<?php
require_once "../../Admin/Modelos/rol.php";

header('Content-Type: application/json');

if (!isset($_POST['id']) || !isset($_POST['descripcion'])) {
    echo json_encode(['status' => 'error', 'message' => 'Datos incompletos']);
    exit;
}

$id = intval($_POST['id']);
$descripcion = trim($_POST['descripcion']);

$rol = new Rol();

$rspta = $rol->actualizar($id, $descripcion);

if ($rspta) {
    echo json_encode(['status' => 'success', 'message' => 'Registro actualizado']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'No se pudo actualizar el registro']);
}
