<?php
require_once "../Modelos/cliente.php";

$cliente = new Cliente();

function limpiarCadena($str) {
    return htmlspecialchars(strip_tags(trim($str)));
}

// Verificamos si se recibieron los datos por POST
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $ident_id = 1; // valor fijo si no usas dropdown
    $nombre = limpiarCadena($_POST["nombre"]);
    $apellido = limpiarCadena($_POST["apellido"]);
    $num_ident = limpiarCadena($_POST["identidad"]);
    $correo = limpiarCadena($_POST["correo"]);
    $telefono = limpiarCadena($_POST["telefono"]);
    $direccion = limpiarCadena($_POST["direccion"]);
    $pais = limpiarCadena($_POST["pais"]);
    $ciudad = limpiarCadena($_POST["ciudad"]);
    $contra = $num_ident; // Contraseña provisional

    $resultado = $cliente->insertar(
        $ident_id, $nombre, $apellido, $num_ident,
        $correo, $telefono, $direccion, $pais,
        $ciudad, $contra
    );

    echo $resultado ? "✅ Cliente registrado exitosamente" : "❌ No se pudo registrar el cliente";
} else {
    echo "❌ Método no permitido";
}

