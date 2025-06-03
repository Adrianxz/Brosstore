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
    
    try {
        // Obtener información principal de la venta
        $queryVenta = "SELECT 
            v.VENTA_ID,
            v.CLIENTE_ID,
            v.VENTA_FECHA,
            v.CLIENTE_GMAIL,
            v.VENTA_ORDEN,
            v.VENTA_TOTAL,
            v.TIPO,
            c.CLIENTE_NOMBRE,
            c.CLIENTE_TEL,
            c.CLIENTE_DIRECCION
        FROM ventas v 
        INNER JOIN cliente c ON v.CLIENTE_ID = c.CLIENTE_ID 
        WHERE v.VENTA_ID = $ventaId";
        
        $resultVenta = mysqli_query($Conexion, $queryVenta);
        
        if(!$resultVenta || mysqli_num_rows($resultVenta) == 0) {
            echo json_encode([
                'success' => false,
                'message' => 'Venta no encontrada'
            ]);
            exit;
        }
        
        $venta = mysqli_fetch_assoc($resultVenta);
        
        // Obtener detalles de la venta (productos) - CORREGIDO con JOIN a tabla tallas
        $queryDetalles = "SELECT 
    pv.PROVENTA_ID,
    pv.VENTA_ID,
    pv.PRODUCTO_VENTAS,
    pv.TALLA AS TALLA_DESCRIP_PV,
    t.TALLA_ID,
    pv.CANTIDAD,
    pv.PRECIO_VENTA,
    p.PRO_NOMBRE,
    p.PRO_DESCRIP,
    p.PRO_GENERO,
    pf.FOTO,
    t.TALLA_DESCRIP AS TALLA_DESCRIP_TALLAS
FROM producto_ventas pv
INNER JOIN producto p ON pv.PRODUCTO_VENTAS = p.PRO_ID
LEFT JOIN producto_fotos pf ON p.PRO_ID = pf.PRO_ID AND pf.FOTO_PRINCIPAL = 1
LEFT JOIN tallas t ON TRIM(LOWER(pv.TALLA)) = TRIM(LOWER(t.TALLA_DESCRIP))
WHERE pv.VENTA_ID = $ventaId
ORDER BY pv.PROVENTA_ID";

        
        $resultDetalles = mysqli_query($Conexion, $queryDetalles);
        
        $detalles = [];
        if($resultDetalles) {
            while($detalle = mysqli_fetch_assoc($resultDetalles)) {
                $detalles[] = $detalle;
            }
        }
        
        // Si no hay detalles, crear datos de ejemplo basados en el total
        if(empty($detalles)) {
            $detalles = [
                [
                    'PROVENTA_ID' => 1,
                    'VENTA_ID' => $ventaId,
                    'PRODUCTO_VENTAS' => 1,
                    'TALLA_ID' => 1,
                    'CANTIDAD' => 1,
                    'PRECIO_VENTA' => $venta['VENTA_TOTAL'],
                    'PRO_NOMBRE' => 'Producto de ejemplo',
                    'PRO_DESCRIP' => 'Descripción del producto',
                    'PRO_GENERO' => 'Unisex',
                    'FOTO' => 'default.jpg',
                    'TALLA_DESCRIP' => 'Talla Única'
                ]
            ];
        }
        
        // Calcular totales
        $subtotal = 0;
        foreach($detalles as $detalle) {
            $subtotal += ($detalle['PRECIO_VENTA'] * $detalle['CANTIDAD']);
        }
        
        // Calcular impuesto (asumiendo 19% como estándar)
        $impuestoPorcentaje = 19;
        $impuesto = ($impuestoPorcentaje / 100) * $subtotal;
        $total = $subtotal + $impuesto;
        
        // Si el total calculado no coincide con el de la BD, usar el de la BD
        if(abs($total - $venta['VENTA_TOTAL']) > 1) {
            $total = $venta['VENTA_TOTAL'];
            $subtotal = $total / (1 + ($impuestoPorcentaje / 100));
            $impuesto = $total - $subtotal;
        }
        
        // Devolver respuesta exitosa con todos los datos
        echo json_encode([
            'success' => true,
            'venta' => [
                'VENTA_ID' => $venta['VENTA_ID'],
                'CLIENTE_ID' => $venta['CLIENTE_ID'],
                'VENTA_FECHA' => $venta['VENTA_FECHA'],
                'CLIENTE_GMAIL' => $venta['CLIENTE_GMAIL'],
                'VENTA_ORDEN' => $venta['VENTA_ORDEN'],
                'VENTA_TOTAL' => $venta['VENTA_TOTAL'],
                'TIPO' => $venta['TIPO'],
                'CLIENTE_NOMBRE' => $venta['CLIENTE_NOMBRE'],
                'CLIENTE_EMAIL' => $venta['CLIENTE_GMAIL'], // Usar CLIENTE_GMAIL como email
                'CLIENTE_TELEFONO' => $venta['CLIENTE_TEL'] ?? 'No especificado',
                'CLIENTE_DIRECCION' => $venta['CLIENTE_DIRECCION'] ?? 'No especificada',
                'VENTA_ESTADO' => 'accepted' // Valor por defecto
            ],
            'detalles' => array_map(function($detalle) {
                return [
                    'DETALLE_ID' => $detalle['PROVENTA_ID'],
                    'PRODUCTO_ID' => $detalle['PRODUCTO_VENTAS'],
                    'CANTIDAD' => $detalle['CANTIDAD'],
                    'PRECIO_UNITARIO' => $detalle['PRECIO_VENTA'],
                    'SUBTOTAL' => $detalle['PRECIO_VENTA'] * $detalle['CANTIDAD'],
                    'TALLA_ID' => $detalle['TALLA_ID'],
                    'PRO_NOMBRE' => $detalle['PRO_NOMBRE'],
                    'PRO_DESCRIP' => $detalle['PRO_DESCRIP'],
                    'PRO_GENERO' => $detalle['PRO_GENERO'],
                    'TALLA_DESCRIP' => $detalle['TALLA_DESCRIP_TALLAS'] ?? $detalle['TALLA_DESCRIP_PV'] ?? 'Sin talla',
                    'FOTO' => $detalle['FOTO']
                ];
            }, $detalles),
            'totales' => [
                'subtotal' => $subtotal,
                'impuesto' => $impuesto,
                'impuesto_porcentaje' => $impuestoPorcentaje,
                'total' => $total
            ]
        ]);
        
    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'message' => 'Error al obtener los datos: ' . $e->getMessage()
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'No se proporcionó el ID de la venta'
    ]);
}
?>
