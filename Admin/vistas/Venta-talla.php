<?php
// Habilitar reporte de errores para debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Headers para debugging
header('Content-Type: text/html; charset=UTF-8');
header('Cache-Control: no-cache, must-revalidate');

// Log de inicio
error_log("Venta-talla.php iniciado");

// Verificar si se recibió el producto_id
if (!isset($_POST['producto_id']) || empty($_POST['producto_id'])) {
    error_log("Error: producto_id no recibido. POST data: " . print_r($_POST, true));
    echo '<div class="alert alert-danger">
            <i class="fas fa-exclamation-triangle"></i>
            Error: ID de producto no válido. Datos recibidos: ' . htmlspecialchars(print_r($_POST, true)) . '
          </div>';
    exit;
}

$producto_id = intval($_POST['producto_id']);
error_log("Producto ID recibido: " . $producto_id);

// Verificar conexión a base de datos
if (!file_exists('../../Backend/BD.php')) {
    error_log("Error: Archivo BD.php no encontrado en ../../Backend/BD.php");
    echo '<div class="alert alert-danger">
            <i class="fas fa-database"></i>
            Error: Archivo de conexión no encontrado
          </div>';
    exit;
}

require('../../Backend/BD.php');

// Verificar conexión
if (!isset($Conexion) || !$Conexion) {
    error_log("Error: Conexión a base de datos no establecida");
    echo '<div class="alert alert-danger">
            <i class="fas fa-database"></i>
            Error: No se pudo conectar a la base de datos
          </div>';
    exit;
}

error_log("Conexión a BD establecida correctamente");

// Verificar si el producto existe
$queryProducto = "SELECT PRO_ID, PRO_NOMBRE, PRO_PRECIO, CAT_ID FROM producto WHERE PRO_ID = ?";
$stmtProducto = mysqli_prepare($Conexion, $queryProducto);

if (!$stmtProducto) {
    error_log("Error preparando consulta producto: " . mysqli_error($Conexion));
    echo '<div class="alert alert-danger">
            <i class="fas fa-database"></i>
            Error en consulta de producto: ' . htmlspecialchars(mysqli_error($Conexion)) . '
          </div>';
    exit;
}

mysqli_stmt_bind_param($stmtProducto, "i", $producto_id);
mysqli_stmt_execute($stmtProducto);
$resultProducto = mysqli_stmt_get_result($stmtProducto);
$producto = mysqli_fetch_assoc($resultProducto);

if (!$producto) {
    error_log("Producto no encontrado con ID: " . $producto_id);
    echo '<div class="alert alert-warning text-center">
            <i class="fas fa-search mb-2"></i>
            <h6>Producto no encontrado</h6>
            <p class="mb-0">No se encontró el producto con ID: ' . $producto_id . '</p>
          </div>';
    exit;
}

error_log("Producto encontrado: " . $producto['PRO_NOMBRE']);

$categoria_id = isset($producto['CAT_ID']) ? intval($producto['CAT_ID']) : null;
error_log("Categoría del producto: " . $categoria_id);

// Consulta de tallas según la categoría
if ($categoria_id == 1) {
    $query = "SELECT 
        t.TALLA_ID as TALLAS,
        p.PRO_ID as PRODUCTO,
        p.PRO_NOMBRE,
        p.PRO_PRECIO,
        t.TALLA_DESCRIP,
        COALESCE(pt.STOCK, 0) as STOCK
    FROM tallas t
    LEFT JOIN producto_tallas pt ON pt.TALLAS = t.TALLA_ID AND pt.PRODUCTO = ?
    INNER JOIN producto p ON p.PRO_ID = ?
    WHERE t.CATEGORIA = 1 OR t.CATEGORIA IS NULL
    ORDER BY LENGTH(t.TALLA_DESCRIP), t.TALLA_DESCRIP";
} else {
    $query = "SELECT 
        t.TALLA_ID as TALLAS,
        p.PRO_ID as PRODUCTO,
        p.PRO_NOMBRE,
        p.PRO_PRECIO,
        t.TALLA_DESCRIP,
        COALESCE(pt.STOCK, 0) as STOCK
    FROM tallas t
    LEFT JOIN producto_tallas pt ON pt.TALLAS = t.TALLA_ID AND pt.PRODUCTO = ?
    INNER JOIN producto p ON p.PRO_ID = ?
    WHERE t.CATEGORIA IS NULL OR t.CATEGORIA != 1
    ORDER BY 
        CASE t.TALLA_DESCRIP 
            WHEN 'XS' THEN 1
            WHEN 'S' THEN 2
            WHEN 'M' THEN 3
            WHEN 'L' THEN 4
            WHEN 'XL' THEN 5
            WHEN 'XXL' THEN 6
            ELSE 7
        END";
}

$stmt = mysqli_prepare($Conexion, $query);
if (!$stmt) {
    error_log("Error preparando consulta tallas: " . mysqli_error($Conexion));
    echo '<div class="alert alert-danger">Error en consulta de tallas</div>';
    exit;
}

mysqli_stmt_bind_param($stmt, "ii", $producto_id, $producto_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

error_log("Consulta ejecutada. Filas encontradas: " . mysqli_num_rows($result));

if (mysqli_num_rows($result) === 0) {
    echo '<div class="alert alert-warning text-center w-100">
            <i class="fas fa-info-circle"></i>
            No hay tallas configuradas para este producto.
          </div>';
}

echo '<div class="row g-3">';
while ($talla = mysqli_fetch_assoc($result)) {
    error_log("Procesando talla: " . print_r($talla, true));

    $hasStock = $talla['STOCK'] > 0;
    $stockClass = $hasStock ? 'btn-outline-primary' : 'btn-outline-secondary';
    $disabled = $hasStock ? '' : 'disabled';
    $stockText = $hasStock ? "Stock: {$talla['STOCK']}" : 'Sin stock';
    $stockIcon = $hasStock ? 'fas fa-check-circle text-success' : 'fas fa-times-circle text-danger';

    echo '<div class="col-6 col-md-4 col-lg-3">
            <button type="button" 
                    class="btn ' . $stockClass . ' w-100 h-100 btnSeleccionarTalla position-relative" 
                    data-talla-id="' . $talla['TALLAS'] . '"
                    data-producto-id="' . $talla['PRODUCTO'] . '"
                    data-producto-nombre="' . htmlspecialchars($talla['PRO_NOMBRE']) . '"
                    data-talla-descrip="' . htmlspecialchars($talla['TALLA_DESCRIP']) . '"
                    data-precio="' . $talla['PRO_PRECIO'] . '"
                    data-stock="' . $talla['STOCK'] . '"
                    ' . $disabled . '
                    style="min-height: 80px;">
                <div class="d-flex flex-column align-items-center justify-content-center h-100">
                    <span class="fs-4 fw-bold mb-1">' . htmlspecialchars($talla['TALLA_DESCRIP']) . '</span>
                    <small class="d-flex align-items-center">
                        <i class="' . $stockIcon . ' me-1"></i>
                        ' . $stockText . '
                    </small>
                </div>';

    if ($hasStock) {
        echo '<div class="position-absolute top-0 end-0 p-1">
                <i class="fas fa-plus-circle text-primary"></i>
              </div>';
    }

    echo '</button>
          </div>';
}
echo '</div>';
echo '<div class="mt-4">
        <div class="alert alert-info d-flex align-items-center" role="alert">
            <i class="fas fa-info-circle me-2"></i>
            <div>
                <strong>Información:</strong> Selecciona una talla con stock disponible para continuar.
            </div>
        </div>
      </div>';

if (isset($stmt)) {
    mysqli_stmt_close($stmt);
}
if (isset($stmtProducto)) {
    mysqli_stmt_close($stmtProducto);
}

error_log("Venta-talla.php completado exitosamente");
?>
