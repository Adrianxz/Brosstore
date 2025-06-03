<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once "../../Admin/Modelos/rol.php";

header('Content-Type: application/json');

if (!isset($_POST['id'])) {
    echo json_encode(['status' => 'error', 'message' => 'ID no proporcionado']);
    exit;
}

$id = intval($_POST['id']);
$rol = new Rol();

$rspta = $rol->eliminar($id);

if ($rspta) {
    echo json_encode(['status' => 'success', 'message' => 'Registro eliminado']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'No se pudo eliminar el registro']);
}
