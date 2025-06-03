<?php
// ventas_excel.php - Archivo para generar reportes de ventas (CORREGIDO)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Incluir conexión a base de datos
require_once('../BD.php'); // Ajusta la ruta según tu estructura

// Verificar conexión
if (!$Conexion) {
    die('Error de conexión: ' . mysqli_connect_error());
}

// Función para generar reporte CSV (más liviano y compatible)
function generarReporteCSV($conexion) {
    // Tu consulta SQL completa
    $query = "
        SELECT
            v.VENTA_ID,
            v.CLIENTE_ID,
            v.CLIENTE_GMAIL,
            v.VENTA_FECHA,
            v.VENTA_ORDEN,
            v.VENTA_TOTAL,
            pv.PROVENTA_ID,
            pv.PRODUCTO_VENTAS,
            pv.CANTIDAD,
            pv.PRECIO_VENTA,
            c.CLIENTE_NOMBRE AS CLIENTE_NOMBRE,
            cg.Cliente_Nombre AS CLIENTE_GMAIL_NOMBRE,
            p.PRO_NOMBRE AS PRODUCTO_NOMBRE
        FROM
            ventas v
        INNER JOIN producto_ventas pv ON v.VENTA_ID = pv.VENTA_ID
        INNER JOIN producto p ON p.PRO_ID = pv.PRODUCTO_VENTAS
        LEFT JOIN cliente c ON c.CLIENTE_ID = v.CLIENTE_ID
        LEFT JOIN cliente_gmail cg ON cg.Cliente_Id = v.CLIENTE_GMAIL
        ORDER BY v.VENTA_ID DESC
    ";
    
    $result = mysqli_query($conexion, $query);
    
    if (!$result) {
        die('Error en la consulta: ' . mysqli_error($conexion));
    }
    
    // Configurar headers para descarga
    $filename = 'Reporte_Ventas_Detallado_' . date('Y-m-d_H-i-s') . '.csv';
    
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header('Cache-Control: max-age=0');
    header('Cache-Control: max-age=1');
    header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
    header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
    header('Cache-Control: cache, must-revalidate');
    header('Pragma: public');
    
    // Crear archivo CSV
    $output = fopen('php://output', 'w');
    
    // Agregar BOM para UTF-8 (para que Excel reconozca acentos)
    fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
    
    // Agregar título y fecha
    fputcsv($output, ['REPORTE DETALLADO DE VENTAS'], ';');
    fputcsv($output, ['Fecha de generación: ' . date('d/m/Y H:i:s')], ';');
    fputcsv($output, [''], ';'); // Línea vacía
    
    // Encabezados de columnas
    fputcsv($output, [
        'ID Venta',
        'Cliente ID',
        'Cliente Gmail',
        'Fecha Venta',
        'Orden',
        'Total Venta',
        'ID Producto Venta',
        'ID Producto',
        'Nombre Producto',
        'Cantidad',
        'Precio Unitario',
        'Subtotal Producto',
        'Cliente Nombre',
        'Cliente Gmail Nombre'
    ], ';');
    
    // Variables para evitar duplicación
    $ventasUnicas = [];
    $totalGeneral = 0;
    $totalRegistros = 0;
    
    while ($row = mysqli_fetch_assoc($result)) {
        // Calcular subtotal del producto
        $subtotalProducto = $row['CANTIDAD'] * $row['PRECIO_VENTA'];
        
        fputcsv($output, [
            $row['VENTA_ID'],
            $row['CLIENTE_ID'],
            $row['CLIENTE_GMAIL'],
            $row['VENTA_FECHA'],
            $row['VENTA_ORDEN'],
            number_format($row['VENTA_TOTAL'], 2, '.', ''),
            $row['PROVENTA_ID'],
            $row['PRODUCTO_VENTAS'],
            $row['PRODUCTO_NOMBRE'],
            $row['CANTIDAD'],
            number_format($row['PRECIO_VENTA'], 2, '.', ''),
            number_format($subtotalProducto, 2, '.', ''),
            $row['CLIENTE_NOMBRE'],
            $row['CLIENTE_GMAIL_NOMBRE']
        ], ';');
        
        // Solo sumar el total de venta una vez por venta única
        if (!isset($ventasUnicas[$row['VENTA_ID']])) {
            $ventasUnicas[$row['VENTA_ID']] = $row['VENTA_TOTAL'];
            $totalGeneral += $row['VENTA_TOTAL'];
        }
        
        $totalRegistros++;
    }
    
    // Agregar resumen
    fputcsv($output, [''], ';'); // Línea vacía
    fputcsv($output, ['RESUMEN:'], ';');
    fputcsv($output, ['Total de registros (productos):', $totalRegistros], ';');
    fputcsv($output, ['Total de ventas únicas:', count($ventasUnicas)], ';');
    fputcsv($output, ['Total general (sin duplicar):', number_format($totalGeneral, 2, '.', '')], ';');
    
    fclose($output);
    exit;
}

// Función para generar Excel usando HTML (compatible sin librerías adicionales)
function generarReporteExcel($conexion) {
    // Tu consulta SQL completa
    $query = "
        SELECT
            v.VENTA_ID,
            v.CLIENTE_ID,
            v.CLIENTE_GMAIL,
            v.VENTA_FECHA,
            v.VENTA_ORDEN,
            v.VENTA_TOTAL,
            pv.PROVENTA_ID,
            pv.PRODUCTO_VENTAS,
            pv.CANTIDAD,
            pv.PRECIO_VENTA,
            c.CLIENTE_NOMBRE AS CLIENTE_NOMBRE,
            cg.Cliente_Nombre AS CLIENTE_GMAIL_NOMBRE,
            p.PRO_NOMBRE AS PRODUCTO_NOMBRE
        FROM
            ventas v
        INNER JOIN producto_ventas pv ON v.VENTA_ID = pv.VENTA_ID
        INNER JOIN producto p ON p.PRO_ID = pv.PRODUCTO_VENTAS
        LEFT JOIN cliente c ON c.CLIENTE_ID = v.CLIENTE_ID
        LEFT JOIN cliente_gmail cg ON cg.Cliente_Id = v.CLIENTE_GMAIL
        ORDER BY v.VENTA_ID DESC
    ";
    
    $result = mysqli_query($conexion, $query);
    
    if (!$result) {
        die('Error en la consulta: ' . mysqli_error($conexion));
    }
    
    // Configurar headers para descarga de Excel
    $filename = 'Reporte_Ventas_Detallado_' . date('Y-m-d_H-i-s') . '.xls';
    
    header('Content-Type: application/vnd.ms-excel; charset=utf-8');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header('Cache-Control: max-age=0');
    
    // Comenzar HTML para Excel
    echo '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">';
    echo '<head>';
    echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8">';
    echo '<style>';
    echo 'table { border-collapse: collapse; width: 100%; }';
    echo 'th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }';
    echo 'th { background-color: #4CAF50; color: white; font-weight: bold; }';
    echo '.titulo { background-color: #2196F3; color: white; font-size: 16px; font-weight: bold; text-align: center; }';
    echo '.resumen { background-color: #FF9800; color: white; font-weight: bold; }';
    echo '.numero { text-align: right; }';
    echo '</style>';
    echo '</head>';
    echo '<body>';
    
    // Título
    echo '<table>';
    echo '<tr><td colspan="14" class="titulo">REPORTE DETALLADO DE VENTAS</td></tr>';
    echo '<tr><td colspan="14">Fecha de generación: ' . date('d/m/Y H:i:s') . '</td></tr>';
    echo '<tr><td colspan="14"></td></tr>'; // Línea vacía
    
    // Encabezados
    echo '<tr>';
    echo '<th>ID Venta</th>';
    echo '<th>Cliente ID</th>';
    echo '<th>Cliente Gmail</th>';
    echo '<th>Fecha Venta</th>';
    echo '<th>Orden</th>';
    echo '<th>Total Venta</th>';
    echo '<th>ID Producto Venta</th>';
    echo '<th>ID Producto</th>';
    echo '<th>Nombre Producto</th>';
    echo '<th>Cantidad</th>';
    echo '<th>Precio Unitario</th>';
    echo '<th>Subtotal Producto</th>';
    echo '<th>Cliente Nombre</th>';
    echo '<th>Cliente Gmail Nombre</th>';
    echo '</tr>';
    
    // Variables para evitar duplicación
    $ventasUnicas = [];
    $totalGeneral = 0;
    $totalRegistros = 0;
    
    while ($row = mysqli_fetch_assoc($result)) {
        // Calcular subtotal del producto
        $subtotalProducto = $row['CANTIDAD'] * $row['PRECIO_VENTA'];
        
        echo '<tr>';
        echo '<td>' . htmlspecialchars($row['VENTA_ID']) . '</td>';
        echo '<td>' . htmlspecialchars($row['CLIENTE_ID']) . '</td>';
        echo '<td>' . htmlspecialchars($row['CLIENTE_GMAIL']) . '</td>';
        echo '<td>' . htmlspecialchars($row['VENTA_FECHA']) . '</td>';
        echo '<td>' . htmlspecialchars($row['VENTA_ORDEN']) . '</td>';
        echo '<td class="numero">$' . number_format($row['VENTA_TOTAL'], 2) . '</td>';
        echo '<td>' . htmlspecialchars($row['PROVENTA_ID']) . '</td>';
        echo '<td>' . htmlspecialchars($row['PRODUCTO_VENTAS']) . '</td>';
        echo '<td>' . htmlspecialchars($row['PRODUCTO_NOMBRE']) . '</td>';
        echo '<td class="numero">' . htmlspecialchars($row['CANTIDAD']) . '</td>';
        echo '<td class="numero">$' . number_format($row['PRECIO_VENTA'], 2) . '</td>';
        echo '<td class="numero">$' . number_format($subtotalProducto, 2) . '</td>';
        echo '<td>' . htmlspecialchars($row['CLIENTE_NOMBRE']) . '</td>';
        echo '<td>' . htmlspecialchars($row['CLIENTE_GMAIL_NOMBRE']) . '</td>';
        echo '</tr>';
        
        // Solo sumar el total de venta una vez por venta única
        if (!isset($ventasUnicas[$row['VENTA_ID']])) {
            $ventasUnicas[$row['VENTA_ID']] = $row['VENTA_TOTAL'];
            $totalGeneral += $row['VENTA_TOTAL'];
        }
        
        $totalRegistros++;
    }
    
    // Resumen
    echo '<tr><td colspan="14"></td></tr>'; // Línea vacía
    echo '<tr><td colspan="13" class="resumen">TOTAL DE REGISTROS (PRODUCTOS):</td><td class="resumen">' . $totalRegistros . '</td></tr>';
    echo '<tr><td colspan="13" class="resumen">TOTAL DE VENTAS ÚNICAS:</td><td class="resumen">' . count($ventasUnicas) . '</td></tr>';
    echo '<tr><td colspan="13" class="resumen">TOTAL GENERAL (SIN DUPLICAR):</td><td class="resumen">$' . number_format($totalGeneral, 2) . '</td></tr>';
    
    echo '</table>';
    echo '</body>';
    echo '</html>';
    
    exit;
}

// Función alternativa: Generar reporte de ventas consolidado (una línea por venta)
function generarReporteVentasConsolidado($conexion) {
    // Consulta para obtener ventas consolidadas
    $query = "
        SELECT
            v.VENTA_ID,
            v.CLIENTE_ID,
            v.CLIENTE_GMAIL,
            v.VENTA_FECHA,
            v.VENTA_ORDEN,
            v.VENTA_TOTAL,
            c.CLIENTE_NOMBRE AS CLIENTE_NOMBRE,
            cg.Cliente_Nombre AS CLIENTE_GMAIL_NOMBRE,
            COUNT(pv.PROVENTA_ID) as TOTAL_PRODUCTOS,
            GROUP_CONCAT(CONCAT(p.PRO_NOMBRE, ' (', pv.CANTIDAD, ')') SEPARATOR ', ') as PRODUCTOS_DETALLE
        FROM
            ventas v
        INNER JOIN producto_ventas pv ON v.VENTA_ID = pv.VENTA_ID
        INNER JOIN producto p ON p.PRO_ID = pv.PRODUCTO_VENTAS
        LEFT JOIN cliente c ON c.CLIENTE_ID = v.CLIENTE_ID
        LEFT JOIN cliente_gmail cg ON cg.Cliente_Id = v.CLIENTE_GMAIL
        GROUP BY v.VENTA_ID
        ORDER BY v.VENTA_ID DESC
    ";
    
    $result = mysqli_query($conexion, $query);
    
    if (!$result) {
        die('Error en la consulta: ' . mysqli_error($conexion));
    }
    
    // Configurar headers para descarga de Excel
    $filename = 'Reporte_Ventas_Consolidado_' . date('Y-m-d_H-i-s') . '.xls';
    
    header('Content-Type: application/vnd.ms-excel; charset=utf-8');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header('Cache-Control: max-age=0');
    
    // Comenzar HTML para Excel
    echo '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">';
    echo '<head>';
    echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8">';
    echo '<style>';
    echo 'table { border-collapse: collapse; width: 100%; }';
    echo 'th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }';
    echo 'th { background-color: #4CAF50; color: white; font-weight: bold; }';
    echo '.titulo { background-color: #2196F3; color: white; font-size: 16px; font-weight: bold; text-align: center; }';
    echo '.resumen { background-color: #FF9800; color: white; font-weight: bold; }';
    echo '.numero { text-align: right; }';
    echo '</style>';
    echo '</head>';
    echo '<body>';
    
    // Título
    echo '<table>';
    echo '<tr><td colspan="9" class="titulo">REPORTE CONSOLIDADO DE VENTAS</td></tr>';
    echo '<tr><td colspan="9">Fecha de generación: ' . date('d/m/Y H:i:s') . '</td></tr>';
    echo '<tr><td colspan="9"></td></tr>'; // Línea vacía
    
    // Encabezados
    echo '<tr>';
    echo '<th>ID Venta</th>';
    echo '<th>Cliente ID</th>';
    echo '<th>Cliente Gmail</th>';
    echo '<th>Fecha Venta</th>';
    echo '<th>Orden</th>';
    echo '<th>Total Venta</th>';
    echo '<th>Total Productos</th>';
    echo '<th>Cliente Nombre</th>';
    echo '<th>Productos Vendidos</th>';
    echo '</tr>';
    
    // Datos
    $totalGeneral = 0;
    $totalVentas = 0;
    
    while ($row = mysqli_fetch_assoc($result)) {
        echo '<tr>';
        echo '<td>' . htmlspecialchars($row['VENTA_ID']) . '</td>';
        echo '<td>' . htmlspecialchars($row['CLIENTE_ID']) . '</td>';
        echo '<td>' . htmlspecialchars($row['CLIENTE_GMAIL']) . '</td>';
        echo '<td>' . htmlspecialchars($row['VENTA_FECHA']) . '</td>';
        echo '<td>' . htmlspecialchars($row['VENTA_ORDEN']) . '</td>';
        echo '<td class="numero">$' . number_format($row['VENTA_TOTAL'], 2) . '</td>';
        echo '<td class="numero">' . htmlspecialchars($row['TOTAL_PRODUCTOS']) . '</td>';
        echo '<td>' . htmlspecialchars($row['CLIENTE_NOMBRE'] ?: $row['CLIENTE_GMAIL_NOMBRE']) . '</td>';
        echo '<td>' . htmlspecialchars($row['PRODUCTOS_DETALLE']) . '</td>';
        echo '</tr>';
        
        $totalGeneral += $row['VENTA_TOTAL'];
        $totalVentas++;
    }
    
    // Resumen
    echo '<tr><td colspan="9"></td></tr>'; // Línea vacía
    echo '<tr><td colspan="8" class="resumen">TOTAL DE VENTAS:</td><td class="resumen">' . $totalVentas . '</td></tr>';
    echo '<tr><td colspan="8" class="resumen">TOTAL GENERAL:</td><td class="resumen">$' . number_format($totalGeneral, 2) . '</td></tr>';
    
    echo '</table>';
    echo '</body>';
    echo '</html>';
    
    exit;
}

// Procesar solicitud de descarga
try {
    if (isset($_GET['descargar'])) {
        switch ($_GET['descargar']) {
            case 'excel':
                generarReporteExcel($Conexion);
                break;
            case 'csv':
                generarReporteCSV($Conexion);
                break;
            case 'consolidado':
                generarReporteVentasConsolidado($Conexion);
                break;
            default:
                header('Location: ' . $_SERVER['HTTP_REFERER'] . '?error=tipo_invalido');
                exit;
        }
    } else {
        // Si no hay parámetro de descarga, mostrar mensaje
        echo '<!DOCTYPE html>';
        echo '<html><head><title>Generador de Reportes</title></head><body>';
        echo '<h2>Generador de Reportes de Ventas</h2>';
        echo '<p>Para descargar un reporte, usa los siguientes enlaces:</p>';
        echo '<a href="?descargar=excel">Descargar Excel Detallado</a> | ';
        echo '<a href="?descargar=csv">Descargar CSV Detallado</a> | ';
        echo '<a href="?descargar=consolidado">Descargar Excel Consolidado</a>';
        echo '<p><strong>Nota:</strong></p>';
        echo '<ul>';
        echo '<li><strong>Detallado:</strong> Una fila por cada producto de cada venta</li>';
        echo '<li><strong>Consolidado:</strong> Una fila por venta con resumen de productos</li>';
        echo '</ul>';
        echo '</body></html>';
    }
} catch (Exception $e) {
    error_log('Error en generación de reporte: ' . $e->getMessage());
    header('Location: ' . $_SERVER['HTTP_REFERER'] . '?error=generacion_fallida');
    exit;
}
?>