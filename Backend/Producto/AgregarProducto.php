<?php
session_start();
header('Content-Type: application/json');
require('../BD.php');

// Función para limpiar el nombre del archivo
function limpiarNombreArchivo($nombreOriginal) {
    $nombreOriginal = iconv('UTF-8', 'ASCII//TRANSLIT', $nombreOriginal); // Quitar acentos
    $nombreOriginal = preg_replace('/[^a-zA-Z0-9.\-_]/', '-', $nombreOriginal); // Reemplazar caracteres especiales por guion
    $nombreOriginal = preg_replace('/-+/', '-', $nombreOriginal); // Evitar múltiples guiones seguidos
    return strtolower($nombreOriginal);
}

// Recibir datos del formulario
$Nombre = mysqli_real_escape_string($Conexion, $_POST['Nombre']);
$Descripcion = mysqli_real_escape_string($Conexion, $_POST['descripcion']);
$Categoria = mysqli_real_escape_string($Conexion, $_POST['Categoria']);
$Proveedor = mysqli_real_escape_string($Conexion, $_POST['Proveedor']);
$Precio = str_replace(',', '', $_POST['Precio']);
$Genero = mysqli_real_escape_string($Conexion, $_POST['Genero']);
$FotoP = isset($_POST['ImagenPrincipal']) ? limpiarNombreArchivo($_POST['ImagenPrincipal']) : null;

$Precio = (int)$Precio;
$Fotos = $_FILES['Foto'];
$Directorio = "../../images";

// Verificar si ya existe un producto con el mismo nombre
$checkProductQuery = "SELECT * FROM producto WHERE PRO_NOMBRE = '$Nombre'";
$result = mysqli_query($Conexion, $checkProductQuery);
if (mysqli_num_rows($result) > 0) {
    echo json_encode(["status" => "error", "message" => "El producto con el nombre '$Nombre' ya existe."]);
    return;
}

if (!empty($Fotos['name'][0])) {
    $Insert = "INSERT INTO `producto`(
        `PRO_ID`, `CAT_ID`, `PROV_ID`, `PRO_NOMBRE`, `PRO_DESCRIP`, `PRO_GENERO`, `PRO_PRECIO`,`PRO_VISTAS`
    ) VALUES (
        NULL, '$Categoria', '$Proveedor', '$Nombre', '$Descripcion', '$Genero', '$Precio',0
    )";

    if (mysqli_query($Conexion, $Insert)) {
        $producto_id = mysqli_insert_id($Conexion);
        $imagenPrincipalRegistrada = false;
        $totalFotos = count($Fotos['name']);

        // Procesar imágenes
        for ($i = 0; $i < $totalFotos; $i++) {
            $tmp_name = $Fotos['tmp_name'][$i];
            $original_name = $Fotos['name'][$i];
            $img_type = $Fotos['type'][$i];
            $img_file = limpiarNombreArchivo($original_name);

            if (strpos($img_type, "jpeg") !== false || strpos($img_type, "jpg") !== false || strpos($img_type, "png") !== false) {
                $Destino = $Directorio . "/" . $img_file;

                if (move_uploaded_file($tmp_name, $Destino)) {
                    $isPrincipal = ($FotoP === $img_file) ? 1 : 0;
                    $InsertFoto = "INSERT INTO producto_fotos (FOTO_ID, PRO_ID, FOTO, FOTO_PRINCIPAL) 
                                   VALUES (NULL, '$producto_id', '$img_file', $isPrincipal)";
                    if (mysqli_query($Conexion, $InsertFoto)) {
                        if ($isPrincipal == 1) $imagenPrincipalRegistrada = true;
                    } else {
                        echo json_encode(["status" => "error", "message" => "Error al guardar las fotos."]);
                        return;
                    }
                } else {
                    echo json_encode(["status" => "error", "message" => "Error al mover la imagen al directorio."]);
                    return;
                }
            } else {
                echo json_encode(["status" => "error", "message" => "El archivo '$original_name' no es una imagen válida."]);
                return;
            }
        }

        if (!$imagenPrincipalRegistrada) {
            echo json_encode(["status" => "error", "message" => "Debe seleccionarse una imagen como principal."]);
            return;
        }

        // Registrar tallas y cantidades
        if (isset($_POST['tallas'])) {
            foreach ($_POST['tallas'] as $talla_id) {
                $cantidad_key = "cantidad[$talla_id]";
                if (isset($_POST['cantidad'][$talla_id])) {
                    $cantidad = (int)$_POST['cantidad'][$talla_id];
                    $talla_id = mysqli_real_escape_string($Conexion, $talla_id);

                    $insertTalla = "INSERT INTO producto_tallas (`PRO-TALLAS_ID`, `TALLAS`, `PRODUCTO`, `STOCK`) 
                    VALUES (NULL, '$talla_id', '$producto_id',  '$cantidad')";

                    if (!mysqli_query($Conexion, $insertTalla)) {
                        echo json_encode(["status" => "error", "message" => "Error al registrar la talla $talla_id."]);
                        return;
                    }
                }
            }
        }


        echo json_encode(["status" => "success", "message" => "Producto, fotos y tallas guardados correctamente"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Error al guardar el producto"]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "No se ha subido ninguna foto"]);
}
?>
