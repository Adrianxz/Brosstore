<?php
// Backend/Carrito/Agregar.php

error_reporting(E_ALL);
ini_set('display_errors', 0); // No mostrar errores al usuario
ini_set('log_errors', 1);
ini_set('error_log', 'debug-carrito.log'); // Log de errores

require('../BD.php');

// Iniciar sesión sin cambiar el nombre
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Encabezado de respuesta JSON
header('Content-Type: application/json');

// Registrar datos recibidos
error_log("Request received: " . json_encode($_POST));

// Inicializar carrito si no existe
if (!isset($_SESSION['carrito'])) {
    $_SESSION['carrito'] = [];
    error_log("Inicializando carrito vacío");
}

// Obtener y validar datos del POST
$id = isset($_POST['id']) ? trim($_POST['id']) : null;
$nombre = isset($_POST['nombre']) ? trim($_POST['nombre']) : null;
$precio = isset($_POST['precio']) ? $_POST['precio'] : null;
$cantidad = isset($_POST['cantidad']) ? (int)$_POST['cantidad'] : 1;
$foto = isset($_POST['foto']) ? trim($_POST['foto']) : null;
$talla = isset($_POST['talla']) ? trim($_POST['talla']) : null;
$tallaTexto = isset($_POST['tallaTexto']) ? trim($_POST['tallaTexto']) : null;
$idCompuesto = isset($_POST['idCompuesto']) ? trim($_POST['idCompuesto']) : null;

// Generar idCompuesto si no existe
if (!$idCompuesto && $id && $talla) {
    $idCompuesto = $id . "-" . $talla;
}

error_log("Datos recibidos - ID: $id, Nombre: $nombre, Precio: $precio, Cantidad: $cantidad, Foto: $foto, Talla: $talla, TallaTexto: $tallaTexto, IDCompuesto: $idCompuesto");

// Corrección especial de precio si es necesario
if ($precio == 30000000 && strpos($nombre, 'Gorra') !== false) {
    $precio = 300000;
    error_log("Precio corregido para Gorra a: $precio");
}

// Verificar stock disponible
function verificarStock($productoId, $tallaId, $cantidad) {
    global $Conexion;
    try {
        $query = "SELECT STOCK FROM producto_tallas WHERE PRODUCTO = ? AND TALLAS = ?";
        $stmt = mysqli_prepare($Conexion, $query);
        mysqli_stmt_bind_param($stmt, "ss", $productoId, $tallaId);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if ($row = mysqli_fetch_assoc($result)) {
            $stockDisponible = (int)$row['STOCK'];
            error_log("Stock verificado para producto $productoId, talla $tallaId: $stockDisponible");
            return $stockDisponible >= $cantidad;
        }

        error_log("No se encontró stock para producto $productoId, talla $tallaId");
        return false;
    } catch (Exception $e) {
        error_log("Error al verificar stock: " . $e->getMessage());
        return false;
    }
}

// Obtener cantidad de ese producto en el carrito
function obtenerCantidadEnCarrito($carrito, $idCompuesto) {
    $cantidadTotal = 0;
    foreach ($carrito as $item) {
        $itemIdCompuesto = isset($item['idCompuesto']) ? $item['idCompuesto'] : $item['id'] . "-" . ($item['talla'] ?? '');
        if ($itemIdCompuesto === $idCompuesto) {
            $cantidadTotal += (int)$item['cantidad'];
        }
    }
    return $cantidadTotal;
}

// Validar datos requeridos
if ($id && $nombre && $precio && $cantidad && $talla) {
    $stockActual = obtenerCantidadEnCarrito($_SESSION['carrito'], $idCompuesto);

    if (!verificarStock($id, $talla, $cantidad + $stockActual)) {
        echo json_encode([
            'success' => false,
            'error' => 'No hay suficiente stock disponible para la talla seleccionada'
        ]);
        exit;
    }

    // Convertir tipos
    $precio = (float)$precio;
    $cantidad = (int)$cantidad;

    error_log("Carrito antes de agregar: " . json_encode($_SESSION['carrito']));

    $found = false;
    foreach ($_SESSION['carrito'] as &$item) {
        $itemIdCompuesto = isset($item['idCompuesto']) ? $item['idCompuesto'] : $item['id'] . "-" . ($item['talla'] ?? '');
        if ($itemIdCompuesto === $idCompuesto) {
            $oldCantidad = $item['cantidad'];
            $item['cantidad'] += $cantidad;
            $found = true;
            error_log("Producto existente actualizado - ID: $idCompuesto, Cantidad anterior: $oldCantidad, Nueva cantidad: {$item['cantidad']}");
            break;
        }
    }
    unset($item);

    if (!$found) {
        $newItem = [
            'id' => $id,
            'idCompuesto' => $idCompuesto,
            'nombre' => $nombre,
            'precio' => $precio,
            'cantidad' => $cantidad,
            'foto' => $foto,
            'talla' => $talla,
            'tallaTexto' => $tallaTexto
        ];
        $_SESSION['carrito'][] = $newItem;
        error_log("Nuevo producto agregado: " . json_encode($newItem));
    }

    error_log("Carrito actualizado: " . json_encode($_SESSION['carrito']));

    echo json_encode(['success' => true, 'cart' => $_SESSION['carrito']]);
} else {
    error_log("Datos incompletos: " . json_encode($_POST));
    echo json_encode([
        'success' => false,
        'error' => 'Datos incompletos',
        'received' => [
            'id' => $id,
            'nombre' => $nombre,
            'precio' => $precio,
            'cantidad' => $cantidad,
            'foto' => $foto,
            'talla' => $talla,
            'tallaTexto' => $tallaTexto,
            'idCompuesto' => $idCompuesto
        ]
    ]);
}
