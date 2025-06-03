<?php
require_once "../../Admin/Modelos/rol.php";

header('Content-Type: application/json');

if (!isset($_POST['id'])) {
    echo json_encode(['status' => 'error', 'message' => 'ID no proporcionado']);
    exit;
}

$id = intval($_POST['id']);
$rol = new Rol();

$data = $rol->obtenerPorId($id);

if ($data) {
    echo json_encode(['status' => 'success', 'data' => $data]);
} else {
    echo json_encode(['status' => 'error', 'message' => 'No se encontró el rol']);
}
