<?php
// IMPORTANTE: No debe haber NADA antes de esta línea, ni espacios ni saltos de línea

// Limpiar cualquier output previo
ob_clean();

// Configurar zona horaria de Colombia
date_default_timezone_set('America/Bogota');

// Configurar headers antes de cualquier output
header('Content-Type: application/pdf');
header('Cache-Control: no-cache, must-revalidate');
header('Expires: Sat, 26 Jul 1997 05:00:00 GMT');

require_once('../vendor/autoload.php'); // Ajusta según tu ruta
require('../Backend/BD.php');

// Función para limpiar HTML de las descripciones
function limpiarHTML($texto) {
    $texto = strip_tags($texto);
    $texto = html_entity_decode($texto, ENT_QUOTES, 'UTF-8');
    $texto = trim(preg_replace('/\s+/', ' ', $texto));
    if (strlen($texto) > 60) {
        $texto = substr($texto, 0, 57) . '...';
    }
    return $texto;
}

// Función para formatear fecha en español
function fechaEspanol($fecha) {
    $meses = [
        1 => 'enero', 2 => 'febrero', 3 => 'marzo', 4 => 'abril',
        5 => 'mayo', 6 => 'junio', 7 => 'julio', 8 => 'agosto',
        9 => 'septiembre', 10 => 'octubre', 11 => 'noviembre', 12 => 'diciembre'
    ];
    
    $timestamp = strtotime($fecha);
    $dia = date('d', $timestamp);
    $mes = $meses[date('n', $timestamp)];
    $año = date('Y', $timestamp);
    
    return "$dia de $mes de $año";
}

// Verificar si se recibió el ID de la venta
if (!isset($_POST['venta_id']) && !isset($_GET['venta_id'])) {
    http_response_code(400);
    exit('No se proporcionó el ID de la venta');
}

$ventaId = isset($_POST['venta_id']) ? intval($_POST['venta_id']) : intval($_GET['venta_id']);

if ($ventaId <= 0) {
    http_response_code(400);
    exit('ID de venta inválido');
}

try {
    // Obtener información de la venta
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
    
    if (!$resultVenta || mysqli_num_rows($resultVenta) == 0) {
        http_response_code(404);
        exit('Venta no encontrada');
    }
    
    $venta = mysqli_fetch_assoc($resultVenta);
    
    // Obtener detalles de la venta
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
    if ($resultDetalles) {
        while ($detalle = mysqli_fetch_assoc($resultDetalles)) {
            $detalles[] = $detalle;
        }
    }
    
    // Crear el PDF
    $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
    
    // Configuración del documento
    $pdf->SetCreator('Sistema de Ventas BROSSTORE');
    $pdf->SetAuthor('BROSSTORE');
    $pdf->SetTitle('Factura #' . str_pad($venta['VENTA_ID'], 4, '0', STR_PAD_LEFT));
    $pdf->SetSubject('Factura de Venta');
    
    // Remover header y footer por defecto
    $pdf->setPrintHeader(false);
    $pdf->setPrintFooter(false);
    
    // Configurar márgenes
    $pdf->SetMargins(15, 15, 15);
    $pdf->SetAutoPageBreak(TRUE, 25);
    
    // Configurar fuente
    $pdf->SetFont('helvetica', '', 10);
    
    // Agregar página
    $pdf->AddPage();
    
    // ===== HEADER SIMPLE Y ALINEADO =====
    $html = '
    <table cellpadding="5" cellspacing="0" border="0" style="width: 100%;">
        <tr>
            <td style="width: 70%; vertical-align: top;">
                <table cellpadding="2" cellspacing="0" border="0">
                    <tr>
                        <td style="width: 70px; vertical-align: top;">
                            <img src="https://blue-parrot-771704.hostingersite.com//404/images/Imagen1.png" width="60" height="60">
                        </td>
                        <td style="vertical-align: top; padding-left: 10px;">
                            <h1 style="color: #2d5016; font-size: 22px; margin: 0; font-weight: bold;">BROSSTORE</h1>
                            <p style="color: #4a7c59; font-size: 10px; margin: 3px 0 5px 0;">Moda y Estilo para Todos</p>
                            <p style="font-size: 8px; color: #666; margin: 0; line-height: 1.3;">
                                Calle Principal #123, Bogotá, Colombia<br>
                                Tel: +57 300 123 4567<br>
                                contacto@brosstore.com<br>
                                NIT: 900.123.456-7
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
            <td style="width: 30%; vertical-align: top; text-align: right;">
                <table cellpadding="5" cellspacing="0" border="1" style="border-collapse: collapse; width: 100%;">
                    <tr>
                        <td style="background-color: #2d5016; color: white; text-align: center; font-weight: bold; font-size: 14px;">
                            FACTURA DE VENTA
                        </td>
                    </tr>
                    <tr>
                        <td style="font-size: 9px;">
                            <strong>Número:</strong> #' . str_pad($venta['VENTA_ID'], 4, '0', STR_PAD_LEFT) . '<br>
                            <strong>Fecha:</strong> ' . fechaEspanol($venta['VENTA_FECHA']) . '<br>
                            <strong>Hora:</strong> ' . date('h:i A', strtotime($venta['VENTA_FECHA'])) . '<br>
                            <strong>Orden:</strong> ' . ($venta['VENTA_ORDEN'] ?: 'N/A') . '<br>
                            <strong>Tipo:</strong> ' . ucfirst($venta['TIPO'] ?: 'Contado') . '
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>';
    
    $pdf->writeHTML($html, true, false, true, false, '');
    $pdf->Ln(5);
    
    // ===== INFORMACIÓN DEL CLIENTE SIMPLE =====
    $html = '
    <table cellpadding="5" cellspacing="0" border="1" style="width: 100%; border-collapse: collapse;">
        <tr>
            <td style="background-color: #2d5016; color: white; font-weight: bold; font-size: 11px; width: 100%;" colspan="2">
                INFORMACIÓN DEL CLIENTE
            </td>
        </tr>
        <tr>
            <td style="width: 50%; vertical-align: top; font-size: 10px;">
                <strong>Cliente:</strong> ' . htmlspecialchars($venta['CLIENTE_NOMBRE']) . '<br>
                ' . ($venta['CLIENTE_GMAIL'] ? '<strong>Email:</strong> ' . htmlspecialchars($venta['CLIENTE_GMAIL']) . '<br>' : '') . '
                ' . ($venta['CLIENTE_TEL'] ? '<strong>Teléfono:</strong> ' . htmlspecialchars($venta['CLIENTE_TEL']) : '') . '
            </td>
            <td style="width: 50%; vertical-align: top; font-size: 10px;">
                <strong>Dirección de entrega:</strong><br>
                ' . ($venta['CLIENTE_DIRECCION'] ? htmlspecialchars($venta['CLIENTE_DIRECCION']) : 'No especificada') . '
            </td>
        </tr>
    </table>';
    
    $pdf->writeHTML($html, true, false, true, false, '');
    $pdf->Ln(5);
    
    // ===== TABLA DE PRODUCTOS SIMPLE Y BIEN ALINEADA =====
    $html = '
    <table cellpadding="6" cellspacing="0" border="1" style="width: 100%; border-collapse: collapse;">
        <tr style="background-color: #2d5016; color: white;">
            <th style="text-align: left; font-weight: bold; font-size: 10px; width: 40%;">PRODUCTO</th>
            <th style="text-align: center; font-weight: bold; font-size: 10px; width: 15%;">TALLA</th>
            <th style="text-align: center; font-weight: bold; font-size: 10px; width: 15%;">CANT.</th>
            <th style="text-align: right; font-weight: bold; font-size: 10px; width: 15%;">PRECIO</th>
            <th style="text-align: right; font-weight: bold; font-size: 10px; width: 15%;">TOTAL</th>
        </tr>';
    
    $totalGeneral = 0;
    $contador = 0;
    
    foreach ($detalles as $detalle) {
        $contador++;
        $subtotalItem = $detalle['PRECIO_VENTA'] * $detalle['CANTIDAD'];
        $totalGeneral += $subtotalItem;
        
        $talla = $detalle['TALLA_DESCRIP_TALLAS'] ?: $detalle['TALLA_DESCRIP_PV'] ?: 'N/A';
        $bgColor = ($contador % 2 == 0) ? '#f8f8f8' : 'white';
        $descripcionLimpia = limpiarHTML($detalle['PRO_DESCRIP']);
        
        $html .= '
        <tr style="background-color: ' . $bgColor . ';">
            <td style="font-size: 9px; vertical-align: top;">
                <strong>' . htmlspecialchars($detalle['PRO_NOMBRE']) . '</strong>';
        
        if ($descripcionLimpia) {
            $html .= '<br><span style="color: #666; font-size: 8px;">' . htmlspecialchars($descripcionLimpia) . '</span>';
        }
        
        if ($detalle['PRO_GENERO']) {
            $html .= '<br><span style="color: #4a7c59; font-size: 8px;">Género: ' . htmlspecialchars($detalle['PRO_GENERO']) . '</span>';
        }
        
        $html .= '
            </td>
            <td style="text-align: center; font-weight: bold; color: #2d5016; font-size: 9px;">' . htmlspecialchars($talla) . '</td>
            <td style="text-align: center; font-weight: bold; font-size: 10px;">' . $detalle['CANTIDAD'] . '</td>
            <td style="text-align: right; color: #4a7c59; font-weight: bold; font-size: 9px;">$' . number_format($detalle['PRECIO_VENTA'], 0, '.', ',') . '</td>
            <td style="text-align: right; color: #2d5016; font-weight: bold; font-size: 10px;">$' . number_format($subtotalItem, 0, '.', ',') . '</td>
        </tr>';
    }
    
    $html .= '</table>';
    $pdf->writeHTML($html, true, false, true, false, '');
    $pdf->Ln(10);
    
    // ===== TOTAL SIMPLE Y ALINEADO =====
    $html = '
    <table cellpadding="0" cellspacing="0" border="0" style="width: 100%;">
        <tr>
            <td style="width: 65%;"></td>
            <td style="width: 35%;">
                <table cellpadding="8" cellspacing="0" border="1" style="width: 100%; border-collapse: collapse;">
                    <tr>
                        <td style="background-color: #2d5016; color: white; text-align: center; font-weight: bold; font-size: 12px;">
                            TOTAL A PAGAR
                        </td>
                    </tr>
                    <tr>
                        <td style="text-align: center; background-color: white; padding: 15px;">
                            <span style="font-size: 20px; font-weight: bold; color: #2d5016;">$' . number_format($venta['VENTA_TOTAL'], 0, '.', ',') . '</span><br>
                            <span style="font-size: 8px; color: #666;">Incluye todos los impuestos</span><br>
                            <span style="font-size: 8px; color: #666;">Pesos colombianos (COP)</span>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>';
    
    $pdf->writeHTML($html, true, false, true, false, '');
    $pdf->Ln(10);
    
    // ===== FOOTER SIMPLE =====
    $fechaActual = new DateTime('now', new DateTimeZone('America/Bogota'));
    
    $html = '
    <hr style="border: 0; height: 1px; background-color: #2d5016;">
    
    <table cellpadding="5" cellspacing="0" border="0" style="width: 100%;">
        <tr>
            <td style="text-align: center;">
                <p style="color: #2d5016; margin: 5px 0; font-size: 12px; font-weight: bold;">¡Gracias por tu compra en BROSSTORE!</p>
                <p style="color: #666; font-size: 8px; margin: 3px 0; line-height: 1.3;">
                    Factura generada el ' . $fechaActual->format('d/m/Y') . ' a las ' . $fechaActual->format('h:i A') . ' (Hora de Colombia)<br>
                    Para consultas: +57 300 123 4567 | contacto@brosstore.com<br>
                    Síguenos en redes sociales: @brosstore
                </p>
            </td>
        </tr>
    </table>
    
    <table cellpadding="5" cellspacing="0" border="1" style="width: 100%; border-collapse: collapse; margin-top: 10px;">
        <tr>
            <td style="background-color: #2d5016; color: white; text-align: center; font-weight: bold; font-size: 10px;" colspan="3">
                TÉRMINOS Y CONDICIONES
            </td>
        </tr>
        <tr>
            <td style="text-align: center; width: 33%; font-size: 8px;">
                <strong>CAMBIOS Y DEVOLUCIONES</strong><br>
                30 días calendario con factura<br>
                Producto en perfecto estado
            </td>
            <td style="text-align: center; width: 34%; font-size: 8px;">
                <strong>GARANTÍA</strong><br>
                Calidad garantizada<br>
                Defectos de fabricación
            </td>
            <td style="text-align: center; width: 33%; font-size: 8px;">
                <strong>ATENCIÓN AL CLIENTE</strong><br>
                Lun-Sáb: 9:00 AM - 7:00 PM<br>
                Dom: 10:00 AM - 5:00 PM
            </td>
        </tr>
        <tr>
            <td style="text-align: center; font-size: 7px; color: #666;" colspan="3">
                Envíos a toda Colombia • Pagos seguros • Soporte 24/7 • Productos originales
            </td>
        </tr>
    </table>';
    
    $pdf->writeHTML($html, true, false, true, false, '');
    
    // Generar el PDF
    $nombreArchivo = 'BROSSTORE_Factura_' . str_pad($venta['VENTA_ID'], 4, '0', STR_PAD_LEFT) . '_' . date('Y-m-d') . '.pdf';
    
    // Limpiar buffer antes de enviar PDF
    ob_end_clean();
    
    // Determinar el tipo de output
    $tipoOutput = 'I'; // Por defecto mostrar en navegador
    if (isset($_GET['download']) && $_GET['download'] == '1') {
        $tipoOutput = 'D'; // Forzar descarga
        header('Content-Disposition: attachment; filename="' . $nombreArchivo . '"');
    }
    
    // Enviar el PDF al navegador
    $pdf->Output($nombreArchivo, $tipoOutput);
    
    // Asegurar que no se envíe nada más
    exit();
    
} catch (Exception $e) {
    ob_end_clean();
    http_response_code(500);
    exit('Error generando PDF: ' . $e->getMessage());
}
?>