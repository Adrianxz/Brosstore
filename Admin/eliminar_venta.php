<?php
// Incluir archivo de conexión a la base de datos
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
header('Content-Type: application/json');
require('../Backend/BD.php');

// Verificar si se recibió el ID de la venta
if(isset($_POST['venta_id'])) {
    $ventaId = intval($_POST['venta_id']);
    
    // Iniciar transacción para asegurar la integridad de los datos
    mysqli_begin_transaction($Conexion);
    
    try {
        // Desactivar comprobación de llaves foráneas
        mysqli_query($Conexion, "SET FOREIGN_KEY_CHECKS = 0");

        // Eliminar detalles de venta (si aplica)
        // $deleteDetalles = "DELETE FROM detalle_venta WHERE VENTA_ID = $ventaId";
        // $resultDetalles = mysqli_query($Conexion, $deleteDetalles);
        
        // Eliminar la venta principal
        $deleteVenta = "DELETE FROM ventas WHERE VENTA_ID = $ventaId";
        $resultVenta = mysqli_query($Conexion, $deleteVenta);

        // Activar comprobación de llaves foráneas
        mysqli_query($Conexion, "SET FOREIGN_KEY_CHECKS = 1");
        
        if($resultVenta) {
            // Confirmar la transacción
            mysqli_commit($Conexion);
            
            echo json_encode([
                'success' => true,
                'message' => 'Venta eliminada correctamente'
            ]);
        } else {
            mysqli_rollback($Conexion);
            echo json_encode([
                'success' => false,
                'message' => 'Error al eliminar la venta: ' . mysqli_error($Conexion)
            ]);
        }
    } catch (Exception $e) {
        mysqli_rollback($Conexion);
        echo json_encode([
            'success' => false,
            'message' => 'Error en la transacción: ' . $e->getMessage()
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'No se proporcionó el ID de la venta'
    ]);
}
?>
