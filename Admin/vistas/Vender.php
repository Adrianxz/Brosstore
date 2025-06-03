<!DOCTYPE html>
<?php
require('../../Backend/BD.php');

// Ventas con clientes tipo 1
$SelectRX = "SELECT 
    v.VENTA_ID,
    v.CLIENTE_ID,
    v.VENTA_FECHA,
    v.CLIENTE_GMAIL,
    v.VENTA_ORDEN,
    v.VENTA_TOTAL,
    v.TIPO,
    c.CLIENTE_NOMBRE
FROM ventas v 
INNER JOIN cliente c ON v.CLIENTE_ID = c.CLIENTE_ID 
WHERE v.TIPO = 1";
$QueryRX = mysqli_query($Conexion,$SelectRX);

// Clientes
$SelectClient = "SELECT `CLIENTE_ID`, `CLIENTE_NOMBRE` FROM `cliente`";
$QueryClient = mysqli_query($Conexion,$SelectClient);

// Productos (sin tallas para evitar duplicados)
$SelectProduct = "SELECT
    p.PRO_ID,
    p.CAT_ID,
    p.PROV_ID,
    p.PRO_NOMBRE,
    p.PRO_DESCRIP,
    p.PRO_GENERO,
    p.PRO_PRECIO,
    f.FOTO
FROM producto p
LEFT JOIN producto_fotos f ON p.PRO_ID = f.PRO_ID AND f.FOTO_PRINCIPAL = 1
GROUP BY p.PRO_ID
";
$QueryProduct = mysqli_query($Conexion,$SelectProduct);
?>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Ventas - Completo</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Variables CSS para consistencia */
        :root {
            --primary-color: #667eea;
            --secondary-color: #764ba2;
            --success-color: #28a745;
            --warning-color: #ffc107;
            --danger-color: #dc3545;
            --info-color: #17a2b8;
            --light-color: #f8f9fa;
            --dark-color: #343a40;
            --border-radius: 10px;
            --box-shadow: 0 0 20px rgba(0,0,0,0.1);
            --transition: all 0.3s ease;
        }

        /* Reset y base */
        * {
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f5f6fa;
            line-height: 1.6;
        }

        /* Loading overlay */
        .loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            z-index: 9999;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        
        .spinner {
            width: 50px;
            height: 50px;
            border: 5px solid #f3f3f3;
            border-top: 5px solid var(--primary-color);
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        /* Header responsivo */
        .page-header {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            color: white;
            padding: 1rem;
            border-radius: var(--border-radius);
            margin-bottom: 1.5rem;
            box-shadow: var(--box-shadow);
        }

        .page-header .row {
            align-items: center;
        }

        .page-title {
            margin: 0;
            font-size: 1.5rem;
            font-weight: 600;
        }

        /* Botones responsivos */
        .btn-responsive {
            padding: 0.5rem 1rem;
            font-size: 0.9rem;
            border-radius: 6px;
            transition: var(--transition);
            white-space: nowrap;
        }

        .btn-responsive:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.15);
        }

        /* Filtros */
        .filter-section {
            background: white;
            padding: 1.5rem;
            border-radius: var(--border-radius);
            margin-bottom: 1.5rem;
            border: 1px solid #dee2e6;
            box-shadow: var(--box-shadow);
        }
        
        .filter-title {
            color: var(--dark-color);
            font-weight: 600;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 1.1rem;
        }

        /* Contenedor principal de tabla */
        .table-responsive-custom {
            background: white;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            overflow: hidden;
            margin-bottom: 2rem;
        }
        
        .nav-tabs-custom {
            background: var(--light-color);
            border-radius: var(--border-radius) var(--border-radius) 0 0;
            padding: 0.75rem 1.25rem 0;
            margin: 0;
            border-bottom: 1px solid #dee2e6;
        }
        
        .nav-tabs-custom .nav-link {
            border: none;
            color: #6c757d;
            font-weight: 500;
            padding: 0.75rem 1.25rem;
            margin-right: 0.25rem;
            border-radius: 8px 8px 0 0;
            transition: var(--transition);
        }
        
        .nav-tabs-custom .nav-link.active {
            background: white;
            color: var(--dark-color);
            border-bottom: 3px solid var(--primary-color);
        }
        
        .tab-content {
            background: white;
            padding: 1.5rem;
        }

        /* Tabla responsiva */
        .table-responsive {
            border-radius: var(--border-radius);
            overflow: hidden;
        }

        .table {
            margin-bottom: 0;
        }

        .table th {
            background-color: var(--light-color);
            border-top: none;
            font-weight: 600;
            color: var(--dark-color);
            padding: 1rem 0.75rem;
            vertical-align: middle;
        }

        .table td {
            padding: 0.75rem;
            vertical-align: middle;
            border-color: #f1f3f4;
        }

        /* Botones de acción */
        .action-buttons {
            display: flex;
            gap: 0.25rem;
            flex-wrap: wrap;
            justify-content: center;
        }
        
        .btn-sm-responsive {
            padding: 0.25rem 0.5rem;
            font-size: 0.75rem;
            border-radius: 4px;
            min-width: 32px;
            height: 32px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* Secciones de formulario */
        .form-section {
            background: var(--light-color);
            padding: 1.25rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
            border-left: 4px solid var(--primary-color);
        }
        
        .form-section-title {
            font-weight: 600;
            color: var(--dark-color);
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 1rem;
        }

        /* Sección de total */
        .total-section {
            background: #e9ecef;
            font-weight: bold;
            font-size: 1.1rem;
            text-align: center;
            color: var(--dark-color);
            padding: 1rem;
        }

        /* Animaciones */
        .fade-in {
            animation: fadeIn 0.5s ease-in;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Modales responsivos */
        .modal {
            position: fixed;
            top: 0;
            left: 0;
            z-index: 1050;
            width: 100%;
            height: 100%;
            overflow: hidden;
            outline: 0;
            display: none;
        }

        .modal.show {
            display: flex !important;
            align-items: center;
            justify-content: center;
        }

        .modal-backdrop {
            position: fixed;
            top: 0;
            left: 0;
            z-index: 1040;
            width: 100vw;
            height: 100vh;
            background-color: rgba(0, 0, 0, 0.5);
        }

        .modal-dialog {
            position: relative;
            width: auto;
            margin: 1rem;
            pointer-events: none;
            max-height: calc(100vh - 2rem);
            overflow-y: auto;
            display: flex;
            align-items: center;
            min-height: calc(100vh - 2rem);
        }

        .modal-dialog-centered {
            display: flex;
            align-items: center;
            min-height: calc(100vh - 2rem);
        }

        .modal-content {
            position: relative;
            display: flex;
            flex-direction: column;
            width: 100%;
            pointer-events: auto;
            background-color: #fff;
            background-clip: padding-box;
            border: 1px solid rgba(0, 0, 0, 0.2);
            border-radius: var(--border-radius);
            outline: 0;
            box-shadow: var(--box-shadow);
            margin: auto;
        }

        .modal-header {
            padding: 1rem 1.5rem;
            border-bottom: 1px solid #dee2e6;
            border-radius: var(--border-radius) var(--border-radius) 0 0;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .modal-body {
            padding: 1.5rem;
            flex: 1 1 auto;
            max-height: 60vh;
            overflow-y: auto;
        }

        .modal-footer {
            padding: 1rem 1.5rem;
            border-top: 1px solid #dee2e6;
            border-radius: 0 0 var(--border-radius) var(--border-radius);
            display: flex;
            align-items: center;
            justify-content: flex-end;
            gap: 0.5rem;
        }

        .modal-open {
            overflow: hidden;
        }

        /* Tamaños específicos de modales */
        .modal-sm .modal-dialog {
            max-width: 400px;
        }

        .modal-lg .modal-dialog {
            max-width: 900px;
        }

        .modal-xl .modal-dialog {
            max-width: 1200px;
        }

        /* Responsive breakpoints para modales */
        @media (min-width: 576px) {
            .modal-dialog {
                margin: 0.75rem auto;
                max-width: 540px;
            }

            .modal-lg .modal-dialog {
                max-width: 700px;
            }

            .modal-xl .modal-dialog {
                max-width: 900px;
            }

            .modal-sm .modal-dialog {
                max-width: 400px;
            }
        }

        @media (min-width: 768px) {
            .modal-dialog {
                margin: 1rem auto;
                max-width: 600px;
            }

            .modal-lg .modal-dialog {
                max-width: 800px;
            }

            .modal-xl .modal-dialog {
                max-width: 1000px;
            }

            .modal-sm .modal-dialog {
                max-width: 450px;
            }
        }

        @media (min-width: 992px) {
            .modal-dialog {
                margin: 1.75rem auto;
                max-width: 700px;
            }

            .modal-lg .modal-dialog {
                max-width: 950px;
            }

            .modal-xl .modal-dialog {
                max-width: 1200px;
            }

            .modal-sm .modal-dialog {
                max-width: 500px;
            }

            .modal-body {
                max-height: 70vh;
                padding: 2rem;
            }

            .modal-header {
                padding: 1.5rem 2rem;
            }

            .modal-footer {
                padding: 1.5rem 2rem;
            }
        }

        @media (min-width: 1200px) {
            .modal-dialog {
                margin: 2rem auto;
                max-width: 800px;
            }

            .modal-lg .modal-dialog {
                max-width: 1100px;
            }

            .modal-xl .modal-dialog {
                max-width: 1400px;
            }

            .modal-sm .modal-dialog {
                max-width: 550px;
            }

            .modal-body {
                max-height: 75vh;
                padding: 2.5rem;
            }

            .modal-header {
                padding: 2rem 2.5rem;
            }

            .modal-footer {
                padding: 2rem 2.5rem;
            }
        }

        /* Controles de cantidad en detalle */
        .quantity-input-group {
            display: flex;
            align-items: center;
            gap: 0.25rem;
            max-width: 120px;
        }

        .quantity-input-group .btn {
            padding: 0.25rem 0.5rem;
            font-size: 0.75rem;
            line-height: 1;
            border-radius: 4px;
        }

        .quantity-input-group input {
            width: 50px;
            text-align: center;
            font-size: 0.85rem;
            padding: 0.25rem;
            border: 1px solid #ced4da;
            border-radius: 4px;
        }

        /* Estados de validación para filas */
        .table-row-invalid {
            background-color: #f8d7da !important;
            border-left: 4px solid var(--danger-color);
        }

        .table-row-warning {
            background-color: #fff3cd !important;
            border-left: 4px solid var(--warning-color);
        }

        /* Botones de talla */
        .btnSeleccionarTalla {
            transition: var(--transition);
            border: 2px solid;
            font-weight: 500;
            position: relative;
            overflow: hidden;
        }

        .btnSeleccionarTalla:hover:not(:disabled) {
            transform: translateY(-3px);
            box-shadow: 0 6px 12px rgba(0,0,0,0.15);
        }

        .btnSeleccionarTalla:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none !important;
        }

        .btnSeleccionarTalla.btn-outline-primary:hover:not(:disabled) {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
            color: white;
        }

        /* Controles de cantidad */
        .quantity-controls {
            background: var(--light-color);
            padding: 1rem;
            border-radius: 8px;
            border: 2px solid #e9ecef;
        }

        .quantity-controls input {
            font-size: 1.25rem;
            font-weight: bold;
            text-align: center;
            border: 2px solid #dee2e6;
        }

        .quantity-controls input:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }

        .quantity-controls .btn {
            font-size: 1.1rem;
            font-weight: bold;
            height: 45px;
        }

        /* Alertas de stock */
        .stock-alert {
            border-radius: 8px;
            border: none;
            font-weight: 500;
        }

        .stock-alert.alert-success {
            background-color: #d4edda;
            color: #155724;
            border-left: 4px solid var(--success-color);
        }

        .stock-alert.alert-warning {
            background-color: #fff3cd;
            color: #856404;
            border-left: 4px solid var(--warning-color);
        }

        .stock-alert.alert-danger {
            background-color: #f8d7da;
            color: #721c24;
            border-left: 4px solid var(--danger-color);
        }

        /* Vista de tarjetas */
        .product-card {
            transition: var(--transition);
        }

        .product-card:hover {
            transform: translateY(-5px);
        }

        .product-card .card {
            border: 2px solid #e9ecef;
            border-radius: var(--border-radius);
            overflow: hidden;
            height: 100%;
        }

        .product-card .card:hover {
            border-color: var(--primary-color);
            box-shadow: var(--box-shadow);
        }

        .product-card .card-header {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border-bottom: 1px solid #dee2e6;
            padding: 1rem;
        }

        .product-card .card-body {
            padding: 1rem;
        }

        /* Toasts */
        .toast-container {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 9999;
        }

        .toast {
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            border: none;
            min-width: 300px;
        }

        .toast-header {
            border-radius: var(--border-radius) var(--border-radius) 0 0;
            font-weight: 600;
        }

        .toast-body {
            padding: 1rem;
        }

        /* Asegurar que DataTables se vea correctamente */
        .dataTables_wrapper {
            width: 100%;
        }

        .dataTables_wrapper .dataTables_length,
        .dataTables_wrapper .dataTables_filter,
        .dataTables_wrapper .dataTables_info,
        .dataTables_wrapper .dataTables_paginate {
            margin-bottom: 1rem;
        }

        .dataTables_wrapper .dataTables_length select {
            padding: 0.375rem 0.75rem;
            border-radius: 6px;
            border: 1px solid #ced4da;
            margin: 0 0.5rem;
        }

        .dataTables_wrapper .dataTables_filter input {
            padding: 0.375rem 0.75rem;
            border-radius: 6px;
            border: 1px solid #ced4da;
            margin-left: 0.5rem;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button {
            padding: 0.375rem 0.75rem;
            margin: 0 2px;
            border-radius: 6px;
            border: 1px solid #dee2e6;
            background: white;
            color: var(--primary-color);
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
            background: var(--primary-color);
            color: white;
            border-color: var(--primary-color);
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button.current {
            background: var(--primary-color);
            color: white;
            border-color: var(--primary-color);
        }

        /* Paginación para vista de tarjetas */
        .card-pagination {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 1rem;
            margin-top: 2rem;
            padding: 1rem;
            background: white;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
        }

        .card-pagination .btn {
            min-width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .card-pagination .page-info {
            font-weight: 500;
            color: var(--dark-color);
        }

        /* Filtro de cliente con búsqueda */
        .client-search-container {
            position: relative;
        }

        .client-search-dropdown {
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            background: white;
            border: 1px solid #ced4da;
            border-top: none;
            border-radius: 0 0 6px 6px;
            max-height: 200px;
            overflow-y: auto;
            z-index: 1000;
            display: none;
        }

        .client-search-dropdown.show {
            display: block;
        }

        .client-search-item {
            padding: 0.5rem 0.75rem;
            cursor: pointer;
            border-bottom: 1px solid #f1f3f4;
        }

        .client-search-item:hover {
            background-color: var(--light-color);
        }

        .client-search-item:last-child {
            border-bottom: none;
        }

        /* Responsive breakpoints */
        @media (max-width: 575.98px) {
            .container-fluid {
                padding: 0.5rem;
            }

            .page-header {
                padding: 1rem 0.75rem;
                text-align: center;
            }

            .page-title {
                font-size: 1.25rem;
                margin-bottom: 0.75rem;
            }

            .btn-responsive {
                padding: 0.5rem 0.75rem;
                font-size: 0.85rem;
                width: 100%;
            }

            .filter-section {
                padding: 1rem;
            }

            .form-section {
                padding: 1rem;
            }

            .table-responsive {
                font-size: 0.85rem;
            }

            .btn-sm-responsive {
                padding: 0.2rem 0.4rem;
                font-size: 0.7rem;
                min-width: 28px;
                height: 28px;
            }

            .action-buttons {
                flex-direction: column;
                gap: 0.2rem;
            }

            .quantity-controls {
                padding: 0.75rem;
            }

            .quantity-controls input {
                font-size: 1.1rem;
            }

            .quantity-controls .btn {
                font-size: 1rem;
                height: 40px;
            }

            .btnSeleccionarTalla {
                min-height: 70px !important;
                font-size: 0.9rem;
            }

            .btnSeleccionarTalla .fs-4 {
                font-size: 1.1rem !important;
            }

            .product-card .card-header {
                padding: 0.75rem;
            }

            .product-card .card-body {
                padding: 0.75rem;
            }

            .toast {
                min-width: 250px;
            }
        }

        @media (min-width: 576px) and (max-width: 767.98px) {
            .page-header {
                padding: 1rem;
            }

            .btn-responsive {
                padding: 0.5rem 1rem;
                font-size: 0.9rem;
            }

            .btnSeleccionarTalla {
                min-height: 75px !important;
            }
        }

        @media (min-width: 768px) and (max-width: 991.98px) {
            .btnSeleccionarTalla {
                min-height: 80px !important;
            }
        }

        @media (min-width: 992px) {
            .btnSeleccionarTalla {
                min-height: 85px !important;
            }

            .page-header {
                padding: 1.5rem;
            }
        }

        @media (min-width: 1200px) {
            .container-fluid {
                max-width: 1400px;
                margin: 0 auto;
            }

            .btnSeleccionarTalla {
                min-height: 90px !important;
            }
        }

        /* Utilidades adicionales */
        .min-height-300 {
            min-height: 300px;
        }

        .text-truncate-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .border-start-primary {
            border-left: 4px solid var(--primary-color) !important;
        }

        .bg-gradient-primary {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
        }

        /* Estados de validación */
        .is-invalid {
            border-color: var(--danger-color) !important;
        }

        .is-valid {
            border-color: var(--success-color) !important;
        }

        .invalid-feedback {
            color: var(--danger-color);
            font-size: 0.875rem;
            margin-top: 0.25rem;
        }

        .valid-feedback {
            color: var(--success-color);
            font-size: 0.875rem;
            margin-top: 0.25rem;
        }

        /* Mejoras de accesibilidad */
        .btn:focus,
        .form-control:focus,
        .form-select:focus {
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }

        /* Scrollbar personalizado */
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }

        ::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb {
            background: #c1c1c1;
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #a8a8a8;
        }
    </style>
</head>

<body>
    <!-- Loading Overlay -->
    <div class="loading-overlay" id="loadingOverlay" style="display: none;">
        <div class="spinner"></div>
    </div>

    <!-- Toast Container -->
    <div class="toast-container" id="toastContainer"></div>

    <div class="container-fluid">
        <!-- Header responsivo -->
        <div class="page-header">
            <div class="row">
                <div class="col-12 col-md-8 mb-2 mb-md-0">
                    <h3 class="page-title">
                        <i class="fas fa-shopping-cart me-2"></i>
                        Sistema de Ventas
                    </h3>
                </div>
                <div class="col-12 col-md-4 text-md-end">
                    <button type="button" class="btn btn-light btn-responsive" onclick="ModalSystem.open('modalAgregarVenta')">
                        <i class="fas fa-plus me-1"></i>
                        <span class="d-none d-sm-inline">Agregar Venta</span>
                        <span class="d-sm-none">Nueva</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Pestañas principales -->
        <div class="table-responsive-custom">
            <ul class="nav nav-tabs nav-tabs-custom" id="mainTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button type="button" class="nav-link active" id="ventas-tab" data-bs-toggle="tab" data-bs-target="#ventas" role="tab">
                        <i class="fas fa-shopping-cart me-1"></i> 
                        <span class="d-none d-sm-inline">Ventas</span>
                    </button>
                </li>
            </ul>

            <div class="tab-content" id="mainTabContent">
                <!-- TAB VENTAS -->
                <div class="tab-pane fade show active" id="ventas" role="tabpanel">
                    <!-- Filtros para Ventas -->
                    <div class="filter-section">
                        <div class="filter-title">
                            <i class="fas fa-filter"></i>
                            Filtros de Ventas
                        </div>
                        <div class="row g-3">
                            <div class="col-12 col-sm-6 col-lg-3">
                                <label class="form-label">Fecha Desde</label>
                                <input type="date" class="form-control" id="fechaDesde">
                            </div>
                            <div class="col-12 col-sm-6 col-lg-3">
                                <label class="form-label">Fecha Hasta</label>
                                <input type="date" class="form-control" id="fechaHasta">
                            </div>
                            <div class="col-12 col-sm-6 col-lg-3">
                                <label class="form-label">Cliente</label>
                                <input type="text" class="form-control" id="filtroClienteNombre" placeholder="Buscar cliente...">
                            </div>
                            <div class="col-12 col-sm-6 col-lg-3">
                                <label class="form-label">Estado</label>
                                <select class="form-control" id="filtroEstado">
                                    <option value="">Todos los estados</option>
                                    <option value="Aceptado">Aceptado</option>
                                    <option value="Pendiente">Pendiente</option>
                                    <option value="Rechazado">Rechazado</option>
                                </select>
                            </div>
                        </div>
                        <div class="row g-3 mt-2">
                            <div class="col-12 col-md-6">
                                <label class="form-label">Rango de Total</label>
                                <div class="row g-2">
                                    <div class="col-6">
                                        <input type="number" class="form-control" id="totalMin" placeholder="Mínimo">
                                    </div>
                                    <div class="col-6">
                                        <input type="number" class="form-control" id="totalMax" placeholder="Máximo">
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 col-md-6 d-flex align-items-end">
                                <div class="w-100">
                                    <button type="button" class="btn btn-primary me-2 btn-responsive" onclick="aplicarFiltrosVentas()">
                                        <i class="fas fa-search me-1"></i> 
                                        <span class="d-none d-sm-inline">Aplicar Filtros</span>
                                        <span class="d-sm-none">Aplicar</span>
                                    </button>
                                    <button type="button" class="btn btn-secondary btn-responsive" onclick="limpiarFiltrosVentas()">
                                        <i class="fas fa-times me-1"></i> 
                                        <span class="d-none d-sm-inline">Limpiar</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tabla de ventas -->
                    <div class="table-responsive">
                        <table id="tablaVentas" class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th class="d-none d-md-table-cell">Opciones</th>
                                    <th>Fecha</th>
                                    <th>Cliente</th>
                                    <th class="d-none d-lg-table-cell">Número</th>
                                    <th>Total</th>
                                    <th class="d-none d-sm-table-cell">Estado</th>
                                    <th class="d-md-none">Acciones</th>
                                </tr>
                            </thead>
                            <tbody id="salesTableBody">
                                <?php while($Venta = mysqli_fetch_assoc($QueryRX)): ?>
                                <tr class="fade-in">
                                    <td class="d-none d-md-table-cell">
                                        <div class="action-buttons">
                                            <button type="button" class="btn btn-warning btn-sm-responsive" title="Ver" onclick="viewSale(<?php echo $Venta['VENTA_ID']; ?>)">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <button type="button" class="btn btn-danger btn-sm-responsive" title="Eliminar" onclick="deleteSale(<?php echo $Venta['VENTA_ID']; ?>)">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                            <button type="button" class="btn btn-info btn-sm-responsive" title="PDF" onclick="generatePDF(<?php echo $Venta['VENTA_ID']; ?>)">
                                                <i class="fas fa-file-pdf"></i>
                                            </button>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-md-none small text-muted">Fecha:</div>
                                        <small><?php echo $Venta['VENTA_FECHA']; ?></small>
                                    </td>
                                    <td>
                                        <div class="d-md-none small text-muted">Cliente:</div>
                                        <strong class="text-truncate-2"><?php echo $Venta['CLIENTE_NOMBRE']; ?></strong>
                                    </td>
                                    <td class="d-none d-lg-table-cell"><?php echo str_pad($Venta['VENTA_ID'], 3, '0', STR_PAD_LEFT); ?></td>
                                    <td>
                                        <div class="d-md-none small text-muted">Total:</div>
                                        <span class="text-success fw-bold">$<?php echo number_format($Venta['VENTA_TOTAL'], 0, ',', '.'); ?></span>
                                    </td>
                                    <td class="d-none d-sm-table-cell">
                                        <?php 
                                        $status = isset($Venta['VENTA_ESTADO']) ? $Venta['VENTA_ESTADO'] : 'accepted';
                                        switch($status) {
                                            case 'accepted':
                                                echo '<span class="badge bg-success">Aceptado</span>';
                                                break;
                                            case 'pending':
                                                echo '<span class="badge bg-warning">Pendiente</span>';
                                                break;
                                            case 'rejected':
                                                echo '<span class="badge bg-danger">Rechazado</span>';
                                                break;
                                            default:
                                                echo '<span class="badge bg-success">Aceptado</span>';
                                        }
                                        ?>
                                    </td>
                                    <td class="d-md-none">
                                        <div class="dropdown">
                                            <button type="button" class="btn btn-outline-secondary btn-sm dropdown-toggle" data-bs-toggle="dropdown">
                                                <i class="fas fa-ellipsis-v"></i>
                                            </button>
                                            <ul class="dropdown-menu">
                                                <li><a class="dropdown-item" href="#" onclick="viewSale(<?php echo $Venta['VENTA_ID']; ?>)"><i class="fas fa-eye"></i> Ver</a></li>
                                                <li><a class="dropdown-item" href="#"  onclick="generatePDFSimple(<?php echo $Venta['VENTA_ID']; ?>)"><i class="fas fa-file-pdf"></i> PDF</a></li>
                                                <li><hr class="dropdown-divider"></li>
                                                <li><a class="dropdown-item text-danger" href="#" onclick="deleteSale(<?php echo $Venta['VENTA_ID']; ?>)"><i class="fas fa-trash"></i> Eliminar</a></li>
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Agregar Venta -->
    <div class="modal fade" id="modalAgregarVenta" tabindex="-1" aria-labelledby="modalAgregarVentaLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-plus-circle me-2"></i>
                        Nueva Venta
                    </h5>
                    <button type="button" class="btn-close" onclick="ModalSystem.close('modalAgregarVenta')"></button>
                </div>
                <div class="modal-body">
                    <!-- Información del cliente -->
                    <div class="form-section">
                        <div class="form-section-title">
                            <i class="fas fa-user"></i> Información del Cliente
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Cliente <span class="text-danger">*</span></label>
                            <div class="client-search-container">
                                <input type="text" class="form-control" id="clienteSearch" placeholder="Buscar cliente..." autocomplete="off">
                                <input type="hidden" id="idcliente" required>
                                <div class="client-search-dropdown" id="clienteDropdown">
                                    <?php 
                                    mysqli_data_seek($QueryClient, 0);
                                    while($Client = mysqli_fetch_assoc($QueryClient)):
                                    ?>
                                    <div class="client-search-item" data-id="<?php echo $Client['CLIENTE_ID'];?>" data-name="<?php echo htmlspecialchars($Client['CLIENTE_NOMBRE']);?>">
                                        <?php echo $Client['CLIENTE_NOMBRE'];?>
                                    </div>
                                    <?php endwhile;?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Información de la venta -->
                    <div class="form-section">
                        <div class="form-section-title">
                            <i class="fas fa-receipt"></i> Información de la Venta
                        </div>
                        <div class="row g-3">
                            <div class="col-12 col-sm-6 col-lg-3">
                                <label class="form-label">Serie</label>
                                <input type="text" class="form-control" id="serie">
                            </div>
                            <div class="col-12 col-sm-6 col-lg-3">
                                <label class="form-label">Número</label>
                                <input type="text" class="form-control" id="numero">
                            </div>
                            <div class="col-12 col-sm-6 col-lg-3">
                                <label class="form-label">Fecha <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" id="fecha" value="<?php echo date('Y-m-d'); ?>" required>
                            </div>
                            <div class="col-12 col-sm-6 col-lg-3">
                                <label class="form-label">Impuesto (%)</label>
                                <input type="number" class="form-control" id="impuesto" value="19">
                            </div>
                        </div>
                    </div>

                    <!-- Productos -->
                    <div class="form-section">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div class="form-section-title mb-0">
                                <i class="fas fa-boxes"></i> Productos
                            </div>
                            <button type="button" class="btn btn-primary btn-responsive" onclick="ModalSystem.open('modalArticulos')">
                                <i class="fas fa-plus me-1"></i>
                                <span class="d-none d-sm-inline">Agregar Productos</span>
                                <span class="d-sm-none">Agregar</span>
                            </button>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-bordered table-sm">
                                <thead class="table-light">
                                    <tr>
                                        <th class="d-none d-md-table-cell">Opciones</th>
                                        <th>Producto</th>
                                        <th class="d-none d-sm-table-cell">Talla</th>
                                        <th>Cant.</th>
                                        <th class="d-none d-lg-table-cell">Precio</th>
                                        <th class="d-none d-md-table-cell">Desc.</th>
                                        <th>Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody id="detalleVenta">
                                    <tr id="filaTotal">
                                        <td colspan="7" class="total-section">TOTAL: $0</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-success btn-responsive" onclick="guardarVenta()">
                        <i class="fas fa-save me-1"></i> Guardar Venta
                    </button>
                    <button type="button" class="btn btn-danger btn-responsive" onclick="ModalSystem.close('modalAgregarVenta')">
                        <i class="fas fa-times me-1"></i> Cancelar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Seleccionar Articulos -->
    <div class="modal fade" id="modalArticulos" tabindex="-1">
        <div class="modal-dialog modal-xl modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-search me-2"></i> Seleccionar Artículo
                    </h5>
                    <button type="button" class="btn-close" onclick="ModalSystem.close('modalArticulos')"></button>
                </div>
                <div class="modal-body">
                    <!-- Pestañas para cambiar de vista -->
                    <div class="mb-3">
                        <div class="btn-group w-100" role="group">
                            <button type="button" id="tableViewTab" class="btn btn-primary" onclick="showTableView()">
                                <i class="fas fa-table me-1"></i> 
                                <span class="d-none d-sm-inline">Vista Tabla</span>
                            </button>
                            <button type="button" id="cardViewTab" class="btn btn-outline-primary" onclick="showCardView()">
                                <i class="fas fa-th-large me-1"></i> 
                                <span class="d-none d-sm-inline">Vista Tarjetas</span>
                            </button>
                        </div>
                    </div>
                    
                    <!-- VISTA TABLA -->
                    <div id="tableViewContainer">
                        <div class="table-responsive">
                            <table id="tablaArticulos" class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>Acciones</th>
                                        <th>Nombre</th>
                                        <th>Precio</th>
                                        <th class="d-none d-lg-table-cell">Imagen</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    mysqli_data_seek($QueryProduct, 0);
                                    while($Product = mysqli_fetch_assoc($QueryProduct)):
                                    ?>
                                    <tr>
                                        <td>
                                            <button type="button" class="btn btn-success btn-sm" onclick="selectProduct(<?php echo $Product['PRO_ID']; ?>)" data-nombre="<?php echo htmlspecialchars($Product['PRO_NOMBRE']); ?>" data-precio="<?php echo $Product['PRO_PRECIO']; ?>">
                                                <i class="fas fa-plus"></i>
                                            </button>
                                        </td>
                                        <td class="text-truncate-2"><?php echo $Product['PRO_NOMBRE'];?></td>
                                        <td class="text-success fw-bold">$<?php echo number_format($Product['PRO_PRECIO'], 0, ',', '.'); ?></td>
                                        <td class="d-none d-lg-table-cell">
                                            <img src="https://blue-parrot-771704.hostingersite.com/404/images/<?php echo $Product['FOTO'];?>" alt="img" class="img-thumbnail" width="50" style="object-fit: cover;">
                                        </td>
                                    </tr>
                                    <?php endwhile;?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- VISTA TARJETAS -->
                    <div id="cardViewContainer" style="display: none;">
                        <!-- Filtros para vista de tarjetas -->
                        <div class="row g-3 mb-3">
                            <div class="col-12 col-md-6">
                                <input type="text" class="form-control" id="filtroNombreCard" placeholder="Buscar producto...">
                            </div>
                            <div class="col-6 col-md-3">
                                <select class="form-control" id="filtroGeneroCard">
                                    <option value="">Todos los géneros</option>
                                    <option value="Masculino">Masculino</option>
                                    <option value="Femenino">Femenino</option>
                                    <option value="Unisex">Unisex</option>
                                </select>
                            </div>
                            <div class="col-6 col-md-3">
                                <select class="form-control" id="ordenCard">
                                    <option value="nombre">Ordenar por nombre</option>
                                    <option value="precio_asc">Precio: menor a mayor</option>
                                    <option value="precio_desc">Precio: mayor a menor</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="container-fluid">
                            <div class="row g-3" id="productCardsContainer">
                                <?php 
                                mysqli_data_seek($QueryProduct, 0);
                                while($Product = mysqli_fetch_assoc($QueryProduct)):
                                ?>
                                <div class="col-6 col-sm-4 col-md-3 col-lg-2 product-card" 
                                     data-nombre="<?php echo strtolower($Product['PRO_NOMBRE']); ?>"
                                     data-genero="<?php echo $Product['PRO_GENERO']; ?>"
                                     data-precio="<?php echo $Product['PRO_PRECIO']; ?>">
                                    <div class="card h-100">
                                        <div class="card-header text-center">
                                            <img src="https://blue-parrot-771704.hostingersite.com/404/images/<?php echo $Product['FOTO'];?>" 
                                                 alt="<?php echo htmlspecialchars($Product['PRO_NOMBRE']); ?>" 
                                                 class="img-fluid" 
                                                 style="height: 60px; width: 60px; object-fit: cover;">
                                        </div>
                                        <div class="card-body text-center d-flex flex-column p-2">
                                            <h6 class="card-title mb-2 text-truncate-2" style="font-size: 0.85rem;"><?php echo $Product['PRO_NOMBRE']; ?></h6>
                                            <p class="card-text text-success fw-bold mb-2" style="font-size: 0.9rem;">
                                                $<?php echo number_format($Product['PRO_PRECIO'], 0, ',', '.'); ?>
                                            </p>
                                            <button type="button" class="btn btn-success btn-sm mt-auto" 
                                                    onclick="selectProduct(<?php echo $Product['PRO_ID']; ?>)" 
                                                    data-nombre="<?php echo htmlspecialchars($Product['PRO_NOMBRE']); ?>" 
                                                    data-precio="<?php echo $Product['PRO_PRECIO']; ?>">
                                                <i class="fas fa-plus"></i> 
                                                <span class="d-none d-sm-inline">Agregar</span>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <?php endwhile;?>
                            </div>
                        </div>

                        <!-- Paginación para vista de tarjetas -->
                        <div class="card-pagination" id="cardPagination">
                            <button type="button" class="btn btn-outline-primary" id="btnPrevPage" onclick="changePage(-1)">
                                <i class="fas fa-chevron-left"></i>
                            </button>
                            <span class="page-info" id="pageInfo">Página 1 de 1</span>
                            <button type="button" class="btn btn-outline-primary" id="btnNextPage" onclick="changePage(1)">
                                <i class="fas fa-chevron-right"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Seleccionar Talla -->
    <div class="modal fade" id="modalTallas" tabindex="-1" aria-labelledby="modalTallasLabel">
        <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header border-0 pb-2">
                    <h5 class="modal-title d-flex align-items-center" id="modalTallasLabel">
                        <i class="fas fa-tshirt me-2 text-primary"></i>
                        Seleccionar Talla
                    </h5>
                    <button type="button" class="btn-close" onclick="ModalSystem.close('modalTallas')"></button>
                </div>
                <div class="modal-body">
                    <!-- Información del producto seleccionado -->
                    <div class="product-info mb-4 p-3 bg-light rounded-3 border-start-primary">
                        <div class="row align-items-center">
                            <div class="col-auto">
                                <div class="bg-gradient-primary rounded-circle d-flex align-items-center justify-content-center text-white" style="width: 50px; height: 50px;">
                                    <i class="fas fa-box"></i>
                                </div>
                            </div>
                            <div class="col">
                                <h6 class="mb-1 fw-bold text-primary" id="productoNombreTalla">Cargando producto...</h6>
                                <p class="mb-0 text-muted small">
                                    <i class="fas fa-info-circle me-1"></i>
                                    Selecciona la talla deseada para continuar
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Contenedor de tallas que se carga dinámicamente -->
                    <div id="tallasContainer" class="min-height-300">
                        <!-- Las tallas se cargan aquí dinámicamente -->
                        <div class="text-center py-5">
                            <div class="spinner-border text-primary mb-3" role="status" style="width: 3rem; height: 3rem;">
                                <span class="visually-hidden">Cargando tallas...</span>
                            </div>
                            <h6 class="mb-2">Cargando tallas disponibles...</h6>
                            <p class="text-muted mb-0">Por favor espera un momento</p>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-secondary btn-responsive" onclick="ModalSystem.close('modalTallas')">
                        <i class="fas fa-times me-1"></i>
                        Cancelar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Seleccionar Cantidad -->
    <div class="modal fade" id="modalCantidad" tabindex="-1" aria-labelledby="modalCantidadLabel">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title" id="modalCantidadLabel">
                        <i class="fas fa-shopping-cart me-2 text-success"></i>
                        Seleccionar Cantidad
                    </h5>
                    <button type="button" class="btn-close" onclick="ModalSystem.close('modalCantidad')"></button>
                </div>
                <div class="modal-body text-center">
                    <!-- Información del producto y talla seleccionada -->
                    <div class="product-selection-info mb-4 p-3 bg-light rounded border-start-primary">
                        <h6 class="mb-1 fw-bold" id="productoNombreCantidad">Producto</h6>
                        <p class="mb-0 text-muted">
                            Talla: <span class="badge bg-secondary" id="tallaSeleccionada">-</span>
                        </p>
                    </div>

                    <!-- Controles de cantidad -->
                    <div class="quantity-controls mb-4">
                        <label class="form-label fw-bold mb-3">Cantidad a agregar</label>
                        <div class="row align-items-center justify-content-center g-2">
                            <div class="col-3">
                                <button type="button" class="btn btn-outline-secondary w-100" id="btnMenos">
                                    <i class="fas fa-minus"></i>
                                </button>
                            </div>
                            <div class="col-6">
                                <input type="number" 
                                       class="form-control text-center fw-bold" 
                                       id="cantidad" 
                                       value="1" 
                                       min="1" 
                                       max="999">
                                <div class="invalid-feedback" id="cantidadError"></div>
                            </div>
                            <div class="col-3">
                                <button type="button" class="btn btn-outline-secondary w-100" id="btnMas">
                                    <i class="fas fa-plus"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Información de stock -->
                    <div class="stock-info mb-4">
                        <div class="alert stock-alert mb-0" id="stockAlert">
                            <i class="fas fa-box me-2"></i>
                            <strong>Stock disponible:</strong> <span id="stockDisponible">0</span> unidades
                        </div>
                    </div>

                    <!-- Botón confirmar -->
                    <button type="button" class="btn btn-success w-100 btn-lg" id="btnConfirmarCantidad">
                        <i class="fas fa-check me-2"></i>
                        Confirmar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Ver Detalles de Venta - MODIFICADO SIN SUBTOTAL NI IMPUESTOS -->
    <div class="modal fade" id="modalVerVenta" tabindex="-1" aria-labelledby="modalVerVentaLabel">
        <div class="modal-dialog modal-xl modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header bg-gradient-primary text-white">
                    <h5 class="modal-title d-flex align-items-center" id="modalVerVentaLabel">
                        <i class="fas fa-receipt me-2"></i>
                        Detalles de la Venta
                    </h5>
                    <button type="button" class="btn-close btn-close-white" onclick="ModalSystem.close('modalVerVenta')"></button>
                </div>
                <div class="modal-body">
                    <!-- Loading state -->
                    <div id="ventaLoadingState" class="text-center py-5">
                        <div class="spinner-border text-primary mb-3" role="status" style="width: 3rem; height: 3rem;">
                            <span class="visually-hidden">Cargando detalles...</span>
                        </div>
                        <h6 class="mb-2">Cargando detalles de la venta...</h6>
                        <p class="text-muted mb-0">Por favor espera un momento</p>
                    </div>

                    <!-- Contenido principal (oculto inicialmente) -->
                    <div id="ventaContentState" style="display: none;">
                        <!-- Información de la venta -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="card border-primary">
                                    <div class="card-header bg-light border-primary">
                                        <h6 class="mb-0 text-primary">
                                            <i class="fas fa-info-circle me-2"></i>
                                            Información General
                                        </h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="row g-3">
                                            <div class="col-12 col-md-6 col-lg-3">
                                                <label class="form-label text-muted small">Número de Venta</label>
                                                <div class="fw-bold" id="ventaNumero">#000</div>
                                            </div>
                                            <div class="col-12 col-md-6 col-lg-3">
                                                <label class="form-label text-muted small">Fecha</label>
                                                <div class="fw-bold" id="ventaFecha">-</div>
                                            </div>
                                            <div class="col-12 col-md-6 col-lg-3">
                                                <label class="form-label text-muted small">Serie</label>
                                                <div class="fw-bold" id="ventaSerie">-</div>
                                            </div>
                                            <div class="col-12 col-md-6 col-lg-3">
                                                <label class="form-label text-muted small">Estado</label>
                                                <div id="ventaEstado">
                                                    <span class="badge bg-success">Aceptado</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Información del cliente -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="card border-info">
                                    <div class="card-header bg-light border-info">
                                        <h6 class="mb-0 text-info">
                                            <i class="fas fa-user me-2"></i>
                                            Información del Cliente
                                        </h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="row g-3">
                                            <div class="col-12 col-md-6">
                                                <label class="form-label text-muted small">Nombre</label>
                                                <div class="fw-bold" id="clienteNombre">-</div>
                                            </div>
                                            <div class="col-12 col-md-6">
                                                <label class="form-label text-muted small">Email</label>
                                                <div id="clienteEmail">-</div>
                                            </div>
                                            <div class="col-12 col-md-6">
                                                <label class="form-label text-muted small">Teléfono</label>
                                                <div id="clienteTelefono">-</div>
                                            </div>
                                            <div class="col-12 col-md-6">
                                                <label class="form-label text-muted small">Dirección</label>
                                                <div id="clienteDireccion">-</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Productos de la venta -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="card border-success">
                                    <div class="card-header bg-light border-success">
                                        <h6 class="mb-0 text-success">
                                            <i class="fas fa-shopping-bag me-2"></i>
                                            Productos Comprados
                                        </h6>
                                    </div>
                                    <div class="card-body p-0">
                                        <div class="table-responsive">
                                            <table class="table table-hover mb-0">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th class="d-none d-md-table-cell">Imagen</th>
                                                        <th>Producto</th>
                                                        <th class="d-none d-sm-table-cell">Talla</th>
                                                        <th class="text-center">Cantidad</th>
                                                        <th class="text-end">Precio Unit.</th>
                                                        <th class="text-end">Total</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="ventaDetallesTable">
                                                    <!-- Los detalles se cargan aquí dinámicamente -->
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Total simplificado - SIN SUBTOTAL NI IMPUESTOS -->
                        <div class="row">
                            <div class="col-12 col-md-8 col-lg-9">
                                <!-- Espacio vacío para alineación -->
                            </div>
                            <div class="col-12 col-md-4 col-lg-3">
                                <div class="card border-success">
                                    <div class="card-header bg-light border-success">
                                        <h6 class="mb-0 text-success">
                                            <i class="fas fa-dollar-sign me-2"></i>
                                            Total de la Venta
                                        </h6>
                                    </div>
                                    <div class="card-body text-center">
                                        <div class="d-flex justify-content-center align-items-center">
                                            <span class="fw-bold fs-3 text-success" id="ventaTotal">$0</span>
                                        </div>
                                        <small class="text-muted">Total a pagar</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Estado de error -->
                    <div id="ventaErrorState" style="display: none;">
                        <div class="alert alert-danger d-flex align-items-center" role="alert">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <div>
                                <strong>Error:</strong> <span id="ventaErrorMessage">No se pudieron cargar los detalles de la venta.</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" onclick="imprimirVenta()">
                        <i class="fas fa-print me-1"></i> Imprimir
                    </button>
                    <!--<button type="button" class="btn btn-info" onclick="generatePDFSimple(<?php echo $Venta['VENTA_ID']; ?>)">-->
                    <!--    <i class="fas fa-file-pdf me-1"></i> Descargar PDF-->
                    <!--</button>-->
                    <button type="button" class="btn btn-secondary" onclick="ModalSystem.close('modalVerVenta')">
                        <i class="fas fa-times me-1"></i> Cerrar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>

    <script>
        // Sistema de modales independiente con validación de stock mejorada
        let selectedProduct = null;
        let selectedSize = null;
        let selectedSizeData = null;
        const cartItems = [];

        // ===== SISTEMA DE MODALES INDEPENDIENTE =====
        const ModalSystem = {
            // Abrir modal
            open: function(modalId) {
                console.log(`Abriendo modal: ${modalId}`);
                const modal = document.getElementById(modalId);
                if (!modal) {
                    console.error(`Modal no encontrado: ${modalId}`);
                    return;
                }

                // Mostrar modal con flexbox para centrado
                modal.style.display = "flex";
                modal.classList.add("show");

                // Importante: Quitar aria-hidden para accesibilidad
                modal.removeAttribute("aria-hidden");
                modal.setAttribute("aria-modal", "true");
                modal.setAttribute("role", "dialog");

                // Agregar backdrop
                this.createBackdrop();

                // Bloquear scroll del body
                document.body.classList.add("modal-open");
                document.body.style.overflow = "hidden";
                document.body.style.paddingRight = "15px";

                // Enfocar primer elemento interactivo
                setTimeout(() => {
                    const focusable = modal.querySelector('button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])');
                    if (focusable) {
                        focusable.focus();
                    }
                }, 100);
            },

            // Cerrar modal
            close: function(modalId) {
                console.log(`Cerrando modal: ${modalId}`);
                const modal = document.getElementById(modalId);
                if (!modal) return;

                // Ocultar modal
                modal.style.display = "none";
                modal.classList.remove("show");

                // Restaurar atributos de accesibilidad
                modal.setAttribute("aria-hidden", "true");
                modal.removeAttribute("aria-modal");

                // Remover backdrop
                this.removeBackdrop();

                // Restaurar scroll
                document.body.classList.remove("modal-open");
                document.body.style.overflow = "";
                document.body.style.paddingRight = "";
            },

            // Crear backdrop
            createBackdrop: function() {
                // Remover backdrop existente si hay
                this.removeBackdrop();

                // Crear nuevo backdrop
                const backdrop = document.createElement("div");
                backdrop.className = "modal-backdrop fade show";
                backdrop.id = "modal-backdrop";
                document.body.appendChild(backdrop);

                // Agregar evento de click para cerrar modales
                backdrop.addEventListener("click", () => {
                    const openModals = document.querySelectorAll(".modal.show");
                    if (openModals.length > 0) {
                        const lastModal = openModals[openModals.length - 1];
                        this.close(lastModal.id);
                    }
                });
            },

            // Remover backdrop
            removeBackdrop: function() {
                const backdrop = document.getElementById("modal-backdrop");
                if (backdrop) {
                    backdrop.remove();
                }
            },

            // Inicializar sistema de modales
            init: function() {
                console.log("Inicializando sistema de modales independiente");

                // Cerrar modal con ESC
                document.addEventListener("keydown", (e) => {
                    if (e.key === "Escape") {
                        const openModals = document.querySelectorAll(".modal.show");
                        if (openModals.length > 0) {
                            const lastModal = openModals[openModals.length - 1];
                            this.close(lastModal.id);
                        }
                    }
                });
            }
        };

        // ===== SISTEMA DE TOASTS MEJORADO =====
        const ToastSystem = {
            container: null,

            init: function() {
                this.container = document.getElementById('toastContainer');
                if (!this.container) {
                    this.container = document.createElement('div');
                    this.container.id = 'toastContainer';
                    this.container.className = 'toast-container';
                    document.body.appendChild(this.container);
                }
            },

            show: function(message, type = 'success', duration = 3000) {
                const toast = document.createElement('div');
                toast.className = 'toast show';
                toast.setAttribute('role', 'alert');
                
                const iconMap = {
                    success: 'fas fa-check-circle',
                    error: 'fas fa-exclamation-triangle',
                    warning: 'fas fa-exclamation-circle',
                    info: 'fas fa-info-circle'
                };

                const colorMap = {
                    success: 'bg-success',
                    error: 'bg-danger',
                    warning: 'bg-warning',
                    info: 'bg-info'
                };

                toast.innerHTML = `
                    <div class="toast-header ${colorMap[type]} text-white">
                        <i class="${iconMap[type]} me-2"></i>
                        <strong class="me-auto">${type.charAt(0).toUpperCase() + type.slice(1)}</strong>
                        <button type="button" class="btn-close btn-close-white" onclick="this.closest('.toast').remove()"></button>
                    </div>
                    <div class="toast-body">
                        ${message}
                    </div>
                `;

                this.container.appendChild(toast);

                // Auto-remove después del tiempo especificado
                setTimeout(() => {
                    if (toast.parentNode) {
                        toast.remove();
                    }
                }, duration);

                return toast;
            }
        };

        // ===== VALIDACIÓN DE STOCK MEJORADA =====
        const StockValidator = {
            validateQuantity: function(quantity, maxStock) {
                const cantidadInput = document.getElementById('cantidad');
                const cantidadError = document.getElementById('cantidadError');
                const btnConfirmar = document.getElementById('btnConfirmarCantidad');
                
                // Limpiar estados previos
                cantidadInput.classList.remove('is-invalid', 'is-valid');
                cantidadError.textContent = '';
                
                // Validaciones
                if (!quantity || quantity < 1) {
                    cantidadInput.classList.add('is-invalid');
                    cantidadError.textContent = 'La cantidad debe ser mayor a 0';
                    btnConfirmar.disabled = true;
                    return false;
                }
                
                if (quantity > maxStock) {
                    cantidadInput.classList.add('is-invalid');
                    cantidadError.textContent = `Stock insuficiente. Máximo disponible: ${maxStock}`;
                    btnConfirmar.disabled = true;
                    ToastSystem.show(`Stock insuficiente. Solo hay ${maxStock} unidades disponibles`, 'error');
                    return false;
                }
                
                // Validación exitosa
                cantidadInput.classList.add('is-valid');
                btnConfirmar.disabled = false;
                return true;
            },

            enforceStockLimit: function(input, maxStock) {
                let value = parseInt(input.value) || 0;
                
                if (value > maxStock) {
                    input.value = maxStock;
                    ToastSystem.show(`Cantidad ajustada al stock máximo: ${maxStock}`, 'warning');
                }
                
                if (value < 1) {
                    input.value = 1;
                }
                
                this.validateQuantity(parseInt(input.value), maxStock);
            },

            // Validar stock total antes de guardar
            validateTotalStock: function() {
                let hasErrors = false;
                const errors = [];

                ventaItems.forEach((item, index) => {
                    // Usar el stock que viene de la base de datos (ya está en item.stock)
                    const stockDisponible = item.stock || 0;
                    
                    if (item.cantidad > stockDisponible) {
                        hasErrors = true;
                        errors.push(`${item.nombre} (${item.size}): Stock insuficiente. Disponible: ${stockDisponible}, Solicitado: ${item.cantidad}`);
                        
                        // Marcar fila como inválida
                        const row = document.querySelector(`#detalleVenta tr[data-index="${index}"]`);
                        if (row) {
                            row.classList.add('table-row-invalid');
                        }
                    }
                });

                if (hasErrors) {
                    ToastSystem.show(`Errores de stock encontrados:\n${errors.join('\n')}`, 'error', 5000);
                    return false;
                }

                return true;
            }
        };

        // ===== SISTEMA DE BÚSQUEDA DE CLIENTES =====
        const ClientSearch = {
            clients: [],
            
            init: function() {
                // Cargar clientes desde PHP
                <?php 
                mysqli_data_seek($QueryClient, 0);
                echo "this.clients = [";
                $first = true;
                while($Client = mysqli_fetch_assoc($QueryClient)) {
                    if (!$first) echo ",";
                    echo "{id: " . $Client['CLIENTE_ID'] . ", name: '" . addslashes($Client['CLIENTE_NOMBRE']) . "'}";
                    $first = false;
                }
                echo "];";
                ?>
                
                this.setupEventListeners();
            },
            
            setupEventListeners: function() {
                const searchInput = document.getElementById('clienteSearch');
                const dropdown = document.getElementById('clienteDropdown');
                const hiddenInput = document.getElementById('idcliente');
                
                if (!searchInput || !dropdown || !hiddenInput) return;
                
                // Mostrar dropdown al hacer focus
                searchInput.addEventListener('focus', () => {
                    this.showDropdown();
                });
                
                // Filtrar mientras se escribe
                searchInput.addEventListener('input', (e) => {
                    this.filterClients(e.target.value);
                });
                
                // Ocultar dropdown al hacer click fuera
                document.addEventListener('click', (e) => {
                    if (!e.target.closest('.client-search-container')) {
                        this.hideDropdown();
                    }
                });
                
                // Manejar clicks en items del dropdown
                dropdown.addEventListener('click', (e) => {
                    const item = e.target.closest('.client-search-item');
                    if (item) {
                        this.selectClient(item.dataset.id, item.dataset.name);
                    }
                });
            },
            
            filterClients: function(searchTerm) {
                const dropdown = document.getElementById('clienteDropdown');
                if (!dropdown) return;
                
                const filteredClients = this.clients.filter(client => 
                    client.name.toLowerCase().includes(searchTerm.toLowerCase())
                );
                
                dropdown.innerHTML = '';
                filteredClients.forEach(client => {
                    const item = document.createElement('div');
                    item.className = 'client-search-item';
                    item.dataset.id = client.id;
                    item.dataset.name = client.name;
                    item.textContent = client.name;
                    dropdown.appendChild(item);
                });
                
                this.showDropdown();
            },
            
            selectClient: function(id, name) {
                const searchInput = document.getElementById('clienteSearch');
                const hiddenInput = document.getElementById('idcliente');
                
                if (searchInput) searchInput.value = name;
                if (hiddenInput) hiddenInput.value = id;
                
                this.hideDropdown();
            },
            
            showDropdown: function() {
                const dropdown = document.getElementById('clienteDropdown');
                if (dropdown) {
                    dropdown.classList.add('show');
                }
            },
            
            hideDropdown: function() {
                const dropdown = document.getElementById('clienteDropdown');
                if (dropdown) {
                    dropdown.classList.remove('show');
                }
            }
        };

        // ===== FUNCIONES PARA MANEJO DE PRODUCTOS Y TALLAS =====

        // Función para seleccionar producto
        function selectProduct(productId) {
            console.log("Producto seleccionado:", productId);

            // Obtener el botón desde el evento global o buscar por onclick
            let button = null;
            if (window.event && window.event.target) {
                button = window.event.target.closest("button");
            } else {
                // Fallback: buscar el botón por onclick que contenga el productId
                const buttons = document.querySelectorAll('button[onclick*="selectProduct"]');
                button = Array.from(buttons).find(
                    (btn) => btn.getAttribute("onclick") && btn.getAttribute("onclick").includes(productId.toString())
                );
            }

            if (!button) {
                console.error("No se pudo encontrar el botón del producto");
                ToastSystem.show("Error: No se pudo encontrar la información del producto", 'error');
                return;
            }

            selectedProduct = {
                id: productId,
                name: button.getAttribute("data-nombre") || "Producto",
                price: parseInt(button.getAttribute("data-precio")) || 0
            };

            console.log("Datos del producto:", selectedProduct);

            // Actualizar información del producto en el modal
            const productoNombreTalla = document.getElementById("productoNombreTalla");
            if (productoNombreTalla) {
                productoNombreTalla.textContent = selectedProduct.name;
            }

            // Mostrar modal de tallas usando nuestro sistema independiente
            ModalSystem.open("modalTallas");

            // Cargar tallas dinámicamente desde la base de datos
            loadProductSizes(productId);
        }

        // Función para cargar tallas dinámicamente desde la base de datos
        function loadProductSizes(productId) {
            const tallasContainer = document.getElementById("tallasContainer");
            if (!tallasContainer) {
                console.error("Contenedor de tallas no encontrado");
                return;
            }

            // Mostrar loading
            tallasContainer.innerHTML = `
                <div class="text-center py-5">
                    <div class="spinner-border text-primary mb-3" role="status" style="width: 3rem; height: 3rem;">
                        <span class="visually-hidden">Cargando...</span>
                    </div>
                    <h6 class="mb-2">Cargando tallas disponibles...</h6>
                    <p class="text-muted mb-0">Por favor espera un momento</p>
                </div>
            `;

            // Crear FormData para enviar el producto_id
            const formData = new FormData();
            formData.append('producto_id', productId);

            // Hacer petición AJAX al archivo PHP
            fetch('vistas/Venta-talla.php', {
                method: 'POST',
                body: formData
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.text();
            })
            .then(html => {
                // Insertar el HTML devuelto por PHP
                tallasContainer.innerHTML = html;
                tallasContainer.classList.add("fade-in");
                
                ToastSystem.show("Tallas cargadas correctamente", 'success');
                
                // Log para debugging
                console.log("Tallas cargadas desde BD para producto:", productId);
            })
            .catch(error => {
                console.error('Error cargando tallas:', error);
                
                // Mostrar error y tallas por defecto como fallback
                tallasContainer.innerHTML = `
                    <div class="alert alert-warning text-center mb-3">
                        <i class="fas fa-exclamation-triangle"></i>
                        Error cargando tallas desde la base de datos. Mostrando tallas por defecto.
                    </div>
                    <div class="row g-3">
                        <div class="col-6 col-md-4 col-lg-3">
                            <button type="button" 
                                    class="btn btn-outline-secondary w-100 h-100 btnSeleccionarTalla position-relative" 
                                    data-talla-id="1"
                                    data-producto-id="${productId}"
                                    data-producto-nombre="${selectedProduct.name}"
                                    data-talla-descrip="Talla Única"
                                    data-precio="${selectedProduct.price}"
                                    data-stock="0"
                                    disabled
                                    style="min-height: 80px;">
                                <div class="d-flex flex-column align-items-center justify-content-center h-100">
                                    <span class="fs-4 fw-bold mb-1">Única</span>
                                    <small class="d-flex align-items-center">
                                        <i class="fas fa-times-circle text-danger me-1"></i>
                                        Sin stock
                                    </small>
                                </div>
                            </button>
                        </div>
                    </div>
                    <div class="mt-4">
                        <div class="alert alert-danger d-flex align-items-center" role="alert">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <div>
                                <strong>Error:</strong> No se pudieron cargar las tallas. Verifique la configuración de la base de datos.
                            </div>
                        </div>
                    </div>
                `;
                
                ToastSystem.show('Error cargando tallas desde la base de datos', 'error');
            });
        }

        // Función para seleccionar talla desde botón dinámico
        function selectSizeFromButton(button) {
            console.log("Seleccionando talla desde botón:", button);

            if (button.disabled) {
                console.log("Botón deshabilitado - sin stock");
                ToastSystem.show("Esta talla no tiene stock disponible", 'error');
                return;
            }

            selectedSizeData = {
                tallaId: button.getAttribute("data-talla-id"),
                productoId: button.getAttribute("data-producto-id"),
                productoNombre: button.getAttribute("data-producto-nombre"),
                tallaDescrip: button.getAttribute("data-talla-descrip"),
                precio: parseInt(button.getAttribute("data-precio")) || 0,
                stock: parseInt(button.getAttribute("data-stock")) || 0
            };

            console.log("Talla seleccionada:", selectedSizeData);
            selectedSize = selectedSizeData.tallaDescrip;

            // Actualizar el producto seleccionado con los datos de la talla
            selectedProduct = {
                ...selectedProduct,
                id: selectedSizeData.productoId,
                name: selectedSizeData.productoNombre,
                price: selectedSizeData.precio,
                tallaId: selectedSizeData.tallaId,
                stock: selectedSizeData.stock
            };

            // Cerrar modal de tallas
            ModalSystem.close("modalTallas");

            // Configurar modal de cantidad
            setupQuantityModal();

            // Mostrar modal de cantidad con delay
            setTimeout(() => {
                ModalSystem.open("modalCantidad");
            }, 300);
        }

        // Configurar modal de cantidad con validación de stock
        function setupQuantityModal() {
            const productoNombreCantidad = document.getElementById("productoNombreCantidad");
            const tallaSeleccionada = document.getElementById("tallaSeleccionada");
            const stockDisponible = document.getElementById("stockDisponible");

            if (productoNombreCantidad) {
                productoNombreCantidad.textContent = selectedSizeData.productoNombre;
            }

            if (tallaSeleccionada) {
                tallaSeleccionada.textContent = selectedSizeData.tallaDescrip;
            }

            if (stockDisponible) {
                stockDisponible.textContent = selectedSizeData.stock;
            }

            const cantidadInput = document.getElementById("cantidad");
            if (cantidadInput) {
                cantidadInput.value = 1;
                cantidadInput.max = selectedSizeData.stock;
                
                // Limpiar estados de validación
                cantidadInput.classList.remove('is-invalid', 'is-valid');
                const cantidadError = document.getElementById('cantidadError');
                if (cantidadError) {
                    cantidadError.textContent = '';
                }
            }

            updateStockAlert(selectedSizeData.stock);
            
            // Validar cantidad inicial
            StockValidator.validateQuantity(1, selectedSizeData.stock);
        }

        function updateStockAlert(stock) {
            const stockAlert = document.getElementById("stockAlert");
            if (!stockAlert) return;

            stockAlert.classList.remove('alert-success', 'alert-warning', 'alert-danger');

            if (stock > 5) {
                stockAlert.classList.add('alert-success');
                stockAlert.innerHTML = `
                    <i class="fas fa-check-circle me-2"></i>
                    <strong>Stock disponible:</strong> ${stock} unidades
                `;
            } else if (stock > 0) {
                stockAlert.classList.add('alert-warning');
                stockAlert.innerHTML = `
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <strong>Stock limitado:</strong> ${stock} unidades
                `;
            } else {
                stockAlert.classList.add('alert-danger');
                stockAlert.innerHTML = `
                    <i class="fas fa-times-circle me-2"></i>
                    <strong>Sin stock disponible</strong>
                `;
            }
        }

        // Variables globales para DataTables
        let tablaVentas, tablaArticulos;
        let ventaItems = [];
        let currentPageCard = 1;
        let itemsPerPageCard = 6;
        let filteredProducts = [];
        let totalPages = 1;

        // Inicialización cuando el documento esté listo
        $(document).ready(function() {
            console.log('Inicializando aplicación...');
            
            // Inicializar sistemas
            ModalSystem.init();
            ToastSystem.init();
            ClientSearch.init();
            
            initializeDataTables();
            initializeEventHandlers();
            initializeCardView();
        });

        // Inicializar DataTables con configuración mejorada
        function initializeDataTables() {
            console.log('Inicializando DataTables...');
            
            // Configuración común para todas las tablas
            const commonConfig = {
                language: {
                    url: 'https://cdn.datatables.net/plug-ins/1.13.4/i18n/es-ES.json'
                },
                responsive: true,
                pageLength: 10,
                lengthMenu: [[5, 10, 25, 50, -1], [5, 10, 25, 50, "Todos"]],
                dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>' +
                     '<"row"<"col-sm-12"tr>>' +
                     '<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
                drawCallback: function() {
                    console.log('DataTable redibujada');
                },
                initComplete: function() {
                    console.log('DataTable inicializada completamente');
                }
            };

            try {
                // Tabla de Ventas
                if ($.fn.DataTable.isDataTable('#tablaVentas')) {
                    $('#tablaVentas').DataTable().destroy();
                }
                
                tablaVentas = $('#tablaVentas').DataTable({
                    ...commonConfig,
                    order: [[1, 'desc']], // Ordenar por fecha descendente
                    columnDefs: [
                        { orderable: false, targets: [0, 6] }, // Deshabilitar ordenamiento en columnas de acciones
                        { searchable: false, targets: [0, 6] }
                    ]
                });
                console.log('Tabla de ventas inicializada');

                // Tabla de Artículos en modal - CORREGIDA
                if ($.fn.DataTable.isDataTable('#tablaArticulos')) {
                    $('#tablaArticulos').DataTable().destroy();
                }
                
                tablaArticulos = $('#tablaArticulos').DataTable({
                    language: {
                        url: 'https://cdn.datatables.net/plug-ins/1.13.4/i18n/es-ES.json'
                    },
                    pageLength: 8,
                    lengthMenu: [[5, 8, 10, 15, -1], [5, 8, 10, 15, "Todos"]],
                    columnDefs: [
                        { orderable: false, targets: [0] },
                        { searchable: false, targets: [0] }
                    ],
                    // DOM corregido para mostrar controles
                    dom: '<"row mb-3"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>' +
                         '<"row"<"col-sm-12"tr>>' +
                         '<"row mt-3"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
                    responsive: true,
                    autoWidth: false,
                    // Asegurar que se muestren los controles
                    searching: true,
                    paging: true,
                    info: true,
                    lengthChange: true
                });
                console.log('Tabla de artículos inicializada');

            } catch (error) {
                console.error('Error inicializando DataTables:', error);
                ToastSystem.show('Error inicializando tablas', 'error');
            }
        }

        // Inicializar manejadores de eventos
        function initializeEventHandlers() {
            console.log('Inicializando event handlers...');
            
            // Event listener para tallas con delegation
            document.addEventListener("click", (e) => {
                const tallaButton = e.target.closest(".btnSeleccionarTalla");
                if (tallaButton) {
                    console.log("Click en botón de talla detectado");
                    e.preventDefault();
                    e.stopPropagation();
                    selectSizeFromButton(tallaButton);
                }
            });

            // Event listeners para cantidad con validación de stock
            const btnMenos = document.getElementById("btnMenos");
            const btnMas = document.getElementById("btnMas");
            const cantidadInput = document.getElementById("cantidad");
            const btnConfirmarCantidad = document.getElementById("btnConfirmarCantidad");

            if (btnMenos) {
                btnMenos.addEventListener("click", (e) => {
                    e.preventDefault(); // Prevenir comportamiento por defecto
                    e.stopPropagation(); // Detener propagación del evento
                    
                    if (cantidadInput) {
                        const currentValue = parseInt(cantidadInput.value) || 1;
                        if (currentValue > 1) {
                            cantidadInput.value = currentValue - 1;
                            if (selectedSizeData) {
                                StockValidator.validateQuantity(parseInt(cantidadInput.value), selectedSizeData.stock);
                            }
                        }
                    }
                });
            }

            if (btnMas) {
                btnMas.addEventListener("click", (e) => {
                    e.preventDefault(); // Prevenir comportamiento por defecto
                    e.stopPropagation(); // Detener propagación del evento
                    
                    if (cantidadInput && selectedSizeData) {
                        const currentValue = parseInt(cantidadInput.value) || 1;
                        const maxStock = selectedSizeData.stock;
                        if (currentValue < maxStock) {
                            cantidadInput.value = currentValue + 1;
                            StockValidator.validateQuantity(parseInt(cantidadInput.value), maxStock);
                        } else {
                            ToastSystem.show(`Stock máximo alcanzado: ${maxStock}`, 'warning');
                            // NO hacer nada más, solo mostrar el mensaje
                        }
                    }
                });
            }

            // Validación en tiempo real del input de cantidad
            if (cantidadInput) {
                cantidadInput.addEventListener("input", (e) => {
                    if (selectedSizeData) {
                        StockValidator.enforceStockLimit(e.target, selectedSizeData.stock);
                    }
                });

                cantidadInput.addEventListener("blur", (e) => {
                    if (selectedSizeData) {
                        StockValidator.enforceStockLimit(e.target, selectedSizeData.stock);
                    }
                });
            }

            if (btnConfirmarCantidad) {
                btnConfirmarCantidad.addEventListener("click", (e) => {
                    e.preventDefault(); // Prevenir comportamiento por defecto
                    e.stopPropagation(); // Detener propagación del evento
                    
                    if (!cantidadInput) return;

                    const quantity = parseInt(cantidadInput.value) || 1;

                    if (selectedProduct && selectedSize && selectedSizeData) {
                        // Validación final antes de agregar
                        if (StockValidator.validateQuantity(quantity, selectedSizeData.stock)) {
                            addItemToSale(selectedProduct, selectedSize, quantity);
                            ModalSystem.close("modalCantidad");
                            ModalSystem.close("modalArticulos");

                            selectedProduct = null;
                            selectedSize = null;
                            selectedSizeData = null;
                        }
                    }
                });
            }

            // Filtros para vista de tarjetas
            $('#filtroNombreCard').on('keyup', function() {
                filterAndDisplayCards();
            });

            $('#filtroGeneroCard').on('change', function() {
                filterAndDisplayCards();
            });

            $('#ordenCard').on('change', function() {
                filterAndDisplayCards();
            });

            // Filtro de cliente por nombre en ventas
            $('#filtroClienteNombre').on('keyup', function() {
                // El filtro se aplicará cuando se haga click en "Aplicar Filtros"
            });

            console.log('Event handlers inicializados');
        }

        // Inicializar vista de tarjetas
        function initializeCardView() {
            console.log('Inicializando vista de tarjetas...');
            filteredProducts = $('.product-card').toArray();
            filterAndDisplayCards();
        }

        // Filtrar y mostrar tarjetas con paginación
        function filterAndDisplayCards() {
            let nombreFiltro = $('#filtroNombreCard').val().toLowerCase();
            let generoFiltro = $('#filtroGeneroCard').val();
            let orden = $('#ordenCard').val();

            // Filtrar productos
            filteredProducts = $('.product-card').filter(function() {
                let nombre = $(this).data('nombre');
                let genero = $(this).data('genero');
                
                let matchNombre = !nombreFiltro || nombre.includes(nombreFiltro);
                let matchGenero = !generoFiltro || genero === generoFiltro;
                
                return matchNombre && matchGenero;
            }).toArray();

            // Ordenar productos
            filteredProducts.sort(function(a, b) {
                switch(orden) {
                    case 'precio_asc':
                        return $(a).data('precio') - $(b).data('precio');
                    case 'precio_desc':
                        return $(b).data('precio') - $(a).data('precio');
                    default: // nombre
                        return $(a).data('nombre').localeCompare($(b).data('nombre'));
                }
            });

            // Calcular total de páginas
            totalPages = Math.ceil(filteredProducts.length / itemsPerPageCard);
            if (totalPages === 0) totalPages = 1;

            // Ajustar página actual si es necesario
            if (currentPageCard > totalPages) {
                currentPageCard = totalPages;
            }

            displayCards();
            updatePagination();
        }

        // Mostrar tarjetas de la página actual
        function displayCards() {
            $('.product-card').hide();
            
            let startIndex = (currentPageCard - 1) * itemsPerPageCard;
            let endIndex = startIndex + itemsPerPageCard;
            let cardsToShow = filteredProducts.slice(startIndex, endIndex);
            
            cardsToShow.forEach(function(card) {
                $(card).show();
            });
        }

        // Actualizar controles de paginación
        function updatePagination() {
            const pageInfo = document.getElementById('pageInfo');
            const btnPrev = document.getElementById('btnPrevPage');
            const btnNext = document.getElementById('btnNextPage');

            if (pageInfo) {
                pageInfo.textContent = `Página ${currentPageCard} de ${totalPages}`;
            }

            if (btnPrev) {
                btnPrev.disabled = currentPageCard <= 1;
            }

            if (btnNext) {
                btnNext.disabled = currentPageCard >= totalPages;
            }
        }

        // Cambiar página
        function changePage(direction) {
            const newPage = currentPageCard + direction;
            
            if (newPage >= 1 && newPage <= totalPages) {
                currentPageCard = newPage;
                displayCards();
                updatePagination();
            }
        }

        // Funciones para cambiar vista en modal de artículos
        function showTableView() {
            console.log('Cambiando a vista de tabla...');
            $('#tableViewContainer').show();
            $('#cardViewContainer').hide();
            $('#tableViewTab').removeClass('btn-outline-primary').addClass('btn-primary');
            $('#cardViewTab').removeClass('btn-primary').addClass('btn-outline-primary');
            
            // Reinicializar DataTable
            setTimeout(function() {
                if (tablaArticulos) {
                    tablaArticulos.columns.adjust().responsive.recalc();
                    console.log('DataTable de artículos ajustada para vista de tabla');
                }
            }, 100);
        }

        function showCardView() {
            console.log('Cambiando a vista de tarjetas...');
            $('#tableViewContainer').hide();
            $('#cardViewContainer').show();
            $('#cardViewTab').removeClass('btn-outline-primary').addClass('btn-primary');
            $('#tableViewTab').removeClass('btn-primary').addClass('btn-outline-primary');
            
            // Reinicializar vista de tarjetas
            setTimeout(function() {
                currentPageCard = 1; // Resetear a primera página
                filterAndDisplayCards();
                console.log('Vista de tarjetas reinicializada');
            }, 100);
        }

        function addItemToSale(product, size, cantidad) {
            let subtotal = product.price * cantidad;
            let item = {
                id: product.id,
                nombre: product.name,
                size: size,
                cantidad: cantidad,
                precio: product.price,
                subtotal: subtotal,
                tallaId: selectedSizeData.tallaId, // Asegurar que se incluya el ID de talla
                tallaDescrip: selectedSizeData.tallaDescrip, // Nombre de la talla
                stock: selectedSizeData.stock
            };

            ventaItems.push(item);
            updateSaleTable();
            ToastSystem.show(`${product.name} (${size}) x${cantidad} agregado al carrito`, 'success');
        }

        function updateSaleTable() {
            let tbody = $('#detalleVenta');
            tbody.empty();

            let total = 0;
            ventaItems.forEach((item, index) => {
                total += item.subtotal;
                
                // Verificar si la cantidad excede el stock
                const stockClass = item.cantidad > item.stock ? 'table-row-invalid' : '';
                const stockWarning = item.cantidad > item.stock ? 
                    `<small class="text-danger"><i class="fas fa-exclamation-triangle"></i> Excede stock (${item.stock})</small>` : '';

                tbody.append(`
                    <tr class="${stockClass}" data-index="${index}">
                        <td class="d-none d-md-table-cell">
                            <button type="button" class="btn btn-danger btn-sm" onclick="removeItem(${index})" title="Eliminar">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                        <td class="text-truncate-2">
                            ${item.nombre}
                            ${stockWarning}
                        </td>
                        <td class="d-none d-sm-table-cell">${item.size}</td>
                        <td>
                            <div class="quantity-input-group">
                                <button type="button" class="btn btn-outline-secondary btn-sm" onclick="updateQuantity(${index}, -1)">
                                    <i class="fas fa-minus"></i>
                                </button>
                                <input type="number" 
                                       class="form-control form-control-sm" 
                                       value="${item.cantidad}" 
                                       min="1" 
                                       max="${item.stock}"
                                       onchange="updateQuantityDirect(${index}, this.value)"
                                       data-stock="${item.stock}">
                                <button type="button" class="btn btn-outline-secondary btn-sm" onclick="updateQuantity(${index}, 1)">
                                    <i class="fas fa-plus"></i>
                                </button>
                            </div>
                        </td>
                        <td class="d-none d-lg-table-cell">$${item.precio.toLocaleString()}</td>
                        <td class="d-none d-md-table-cell">0%</td>
                        <td class="fw-bold">$${item.subtotal.toLocaleString()}</td>
                    </tr>
                `);
            });

            tbody.append(`
                <tr id="filaTotal">
                    <td colspan="7" class="total-section">TOTAL: $${total.toLocaleString()}</td>
                </tr>
            `);
        }

        // Función para actualizar cantidad con botones +/-
        function updateQuantity(index, change) {
            if (index >= 0 && index < ventaItems.length) {
                const item = ventaItems[index];
                const newQuantity = item.cantidad + change;
                
                if (newQuantity >= 1) {
                    if (newQuantity > item.stock) {
                        ToastSystem.show(`Stock máximo para ${item.nombre} (${item.size}): ${item.stock}`, 'warning');
                        return;
                    }
                    
                    item.cantidad = newQuantity;
                    item.subtotal = item.precio * newQuantity;
                    updateSaleTable();
                }
            }
        }

        // Función para actualizar cantidad directamente desde input
        function updateQuantityDirect(index, newQuantity) {
            newQuantity = parseInt(newQuantity) || 1;
            
            if (index >= 0 && index < ventaItems.length) {
                const item = ventaItems[index];
                
                if (newQuantity < 1) {
                    newQuantity = 1;
                }
                
                if (newQuantity > item.stock) {
                    ToastSystem.show(`Stock máximo para ${item.nombre} (${item.size}): ${item.stock}`, 'warning');
                    newQuantity = item.stock;
                }
                
                item.cantidad = newQuantity;
                item.subtotal = item.precio * newQuantity;
                updateSaleTable();
            }
        }

        function removeItem(index) {
            const item = ventaItems[index];
            ventaItems.splice(index, 1);
            updateSaleTable();
            ToastSystem.show(`${item.nombre} eliminado del carrito`, 'info');
        }

        // Función para guardar la venta - CORREGIDA
        function guardarVenta() {
            console.log('Iniciando guardado de venta...');
            
            // Validar campos requeridos
            const clienteId = document.getElementById('idcliente').value;
            const fecha = document.getElementById('fecha').value;
            
            if (!clienteId) {
                ToastSystem.show('Debe seleccionar un cliente', 'error');
                return;
            }
            
            if (!fecha) {
                ToastSystem.show('Debe especificar una fecha', 'error');
                return;
            }
            
            if (ventaItems.length === 0) {
                ToastSystem.show('Debe agregar al menos un producto', 'error');
                return;
            }
            
            // Validar stock antes de guardar
            if (!StockValidator.validateTotalStock()) {
                ToastSystem.show('Corrija los errores de stock antes de guardar', 'error');
                return;
            }
            
            // Preparar datos para envío - CORREGIDO para incluir datos de talla correctos
            const ventaData = {
                cliente_id: clienteId,
                fecha: fecha,
                serie: document.getElementById('serie').value || '',
                numero: document.getElementById('numero').value || '',
                impuesto: document.getElementById('impuesto').value || 19,
                items: ventaItems.map(item => ({
                    id: item.id,
                    nombre: item.nombre,
                    tallaId: item.tallaId || 0,
                    tallaDescrip: item.tallaDescrip || item.size, // CORREGIDO: usar tallaDescrip como principal
                    size: item.size, // Mantener size como fallback
                    cantidad: item.cantidad,
                    precio: item.precio,
                    subtotal: item.subtotal
                })),
                total: ventaItems.reduce((sum, item) => sum + item.subtotal, 0)
            };
            
            console.log('Datos de venta a enviar:', ventaData);
            
            // Mostrar loading
            document.getElementById('loadingOverlay').style.display = 'flex';
            
            // Enviar datos al servidor
            fetch('guardar_venta.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(ventaData)
            })
            .then(response => response.json())
            .then(data => {
                document.getElementById('loadingOverlay').style.display = 'none';
                
                if (data.success) {
                    ToastSystem.show('Venta guardada exitosamente', 'success');
                    
                    // Limpiar formulario
                    document.getElementById('clienteSearch').value = '';
                    document.getElementById('idcliente').value = '';
                    document.getElementById('serie').value = '';
                    document.getElementById('numero').value = '';
                    document.getElementById('fecha').value = '<?php echo date('Y-m-d'); ?>';
                    document.getElementById('impuesto').value = '19';
                    ventaItems.length = 0;
                    updateSaleTable();
                    
                    // Cerrar modal
                    ModalSystem.close('modalAgregarVenta');
                    
                    // Recargar tabla de ventas si existe
                    if (tablaVentas) {
                        location.reload(); // Recargar página para mostrar nueva venta
                    }
                } else {
                    ToastSystem.show(`Error al guardar: ${data.message}`, 'error');
                }
            })
            .catch(error => {
                document.getElementById('loadingOverlay').style.display = 'none';
                console.error('Error:', error);
                ToastSystem.show('Error de conexión al guardar la venta', 'error');
            });
        }

        // Funciones de filtrado para Ventas
        function aplicarFiltrosVentas() {
            let fechaDesde = $('#fechaDesde').val();
            let fechaHasta = $('#fechaHasta').val();
            let clienteNombre = $('#filtroClienteNombre').val();
            let estado = $('#filtroEstado').val();
            let totalMin = $('#totalMin').val();
            let totalMax = $('#totalMax').val();

            // Limpiar filtros anteriores
            $.fn.dataTable.ext.search = $.fn.dataTable.ext.search.filter(function(fn) {
                return fn.toString().indexOf('tablaVentas') === -1;
            });

            // Aplicar filtros personalizados
            $.fn.dataTable.ext.search.push(function(settings, data, dataIndex) {
                if (settings.nTable.id !== 'tablaVentas') return true;

                let fecha = data[1]; // Columna de fecha
                let clienteData = data[2]; // Columna de cliente
                let estadoData = data[5] || data[4]; // Columna de estado
                let totalText = data[4]; // Columna de total
                let totalData = parseFloat(totalText.replace(/[$,\.]/g, '').replace(/\s/g, '')); // Limpiar formato

                // Filtro por fecha
                if (fechaDesde && fecha < fechaDesde) return false;
                if (fechaHasta && fecha > fechaHasta) return false;

                // Filtro por cliente (por nombre)
                if (clienteNombre && !clienteData.toLowerCase().includes(clienteNombre.toLowerCase())) return false;

                // Filtro por estado
                if (estado && !estadoData.toLowerCase().includes(estado.toLowerCase())) return false;

                // Filtro por total
                if (totalMin && totalData < parseFloat(totalMin)) return false;
                if (totalMax && totalData > parseFloat(totalMax)) return false;

                return true;
            });

            tablaVentas.draw();
            ToastSystem.show('Filtros aplicados correctamente', 'success');
        }

        function limpiarFiltrosVentas() {
            $('#fechaDesde, #fechaHasta, #filtroClienteNombre, #filtroEstado, #totalMin, #totalMax').val('');
            
            // Limpiar filtros de DataTables
            $.fn.dataTable.ext.search = $.fn.dataTable.ext.search.filter(function(fn) {
                return fn.toString().indexOf('tablaVentas') === -1;
            });
            
            tablaVentas.draw();
            ToastSystem.show('Filtros limpiados', 'info');
        }

        // Funciones existentes del sistema original
        
        // FUNCIÓN MODIFICADA - Ver venta sin subtotal ni impuestos
        function viewSale(id) {
            console.log('Ver venta:', id);
            
            // Abrir modal
            ModalSystem.open('modalVerVenta');
            
            // Mostrar estado de carga
            document.getElementById('ventaLoadingState').style.display = 'block';
            document.getElementById('ventaContentState').style.display = 'none';
            document.getElementById('ventaErrorState').style.display = 'none';
            
            // Crear FormData para enviar el ID
            const formData = new FormData();
            formData.append('venta_id', id);
            
            // Obtener detalles de la venta
            fetch('obtener_venta.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                // Ocultar loading
                document.getElementById('ventaLoadingState').style.display = 'none';
                
                if (data.success) {
                    // Mostrar contenido
                    document.getElementById('ventaContentState').style.display = 'block';
                    
                    // Llenar información general
                    document.getElementById('ventaNumero').textContent = `#${String(data.venta.VENTA_ID).padStart(3, '0')}`;
                    document.getElementById('ventaFecha').textContent = formatearFecha(data.venta.VENTA_FECHA);
                    document.getElementById('ventaSerie').textContent = data.venta.VENTA_SERIE || 'N/A';
                    
                    // Estado de la venta
                    const estadoElement = document.getElementById('ventaEstado');
                    const estado = data.venta.VENTA_ESTADO || 'accepted';
                    switch(estado) {
                        case 'accepted':
                            estadoElement.innerHTML = '<span class="badge bg-success">Aceptado</span>';
                            break;
                        case 'pending':
                            estadoElement.innerHTML = '<span class="badge bg-warning">Pendiente</span>';
                            break;
                        case 'rejected':
                            estadoElement.innerHTML = '<span class="badge bg-danger">Rechazado</span>';
                            break;
                        default:
                            estadoElement.innerHTML = '<span class="badge bg-success">Aceptado</span>';
                    }
                    
                    // Información del cliente
                    document.getElementById('clienteNombre').textContent = data.venta.CLIENTE_NOMBRE;
                    document.getElementById('clienteEmail').textContent = data.venta.CLIENTE_EMAIL || 'No especificado';
                    document.getElementById('clienteTelefono').textContent = data.venta.CLIENTE_TELEFONO || 'No especificado';
                    document.getElementById('clienteDireccion').textContent = data.venta.CLIENTE_DIRECCION || 'No especificada';
                    
                    // Llenar tabla de productos
                    const detallesTable = document.getElementById('ventaDetallesTable');
                    detallesTable.innerHTML = '';
                    
                    data.detalles.forEach(detalle => {
                        const row = document.createElement('tr');
                        row.innerHTML = `
                            <td class="d-none d-md-table-cell text-center">
                                <img src="https://blue-parrot-771704.hostingersite.com/404/images/${detalle.FOTO || 'default.jpg'}" 
                                     alt="${detalle.PRO_NOMBRE}" 
                                     class="img-thumbnail" 
                                     style="width: 50px; height: 50px; object-fit: cover;">
                            </td>
                            <td>
                                <div class="fw-bold">${detalle.PRO_NOMBRE}</div>
                                <small class="text-muted">${detalle.PRO_DESCRIP || ''}</small>
                                <div class="d-md-none">
                                    <small class="text-muted">Talla: ${detalle.TALLA_DESCRIP || 'N/A'}</small>
                                </div>
                            </td>
                            <td class="d-none d-sm-table-cell text-center">
                                <span class="badge bg-secondary">${detalle.TALLA_DESCRIP || 'N/A'}</span>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-primary">${detalle.CANTIDAD}</span>
                            </td>
                            <td class="text-end fw-bold">
                                $${Number(detalle.PRECIO_UNITARIO).toLocaleString()}
                            </td>
                            <td class="text-end fw-bold text-success">
                                $${Number(detalle.SUBTOTAL).toLocaleString()}
                            </td>
                        `;
                        detallesTable.appendChild(row);
                    });
                    
                    // MODIFICADO: Solo mostrar el total final, sin subtotal ni impuestos
                    document.getElementById('ventaTotal').textContent = `$${Number(data.totales.total).toLocaleString()}`;
                    
                    ToastSystem.show('Detalles de venta cargados correctamente', 'success');
                } else {
                    // Mostrar error
                    document.getElementById('ventaErrorState').style.display = 'block';
                    document.getElementById('ventaErrorMessage').textContent = data.message;
                    ToastSystem.show(`Error: ${data.message}`, 'error');
                }
            })
            .catch(error => {
                // Ocultar loading
                document.getElementById('ventaLoadingState').style.display = 'none';
                
                // Mostrar error
                document.getElementById('ventaErrorState').style.display = 'block';
                document.getElementById('ventaErrorMessage').textContent = 'Error de conexión al cargar los detalles';
                
                console.error('Error:', error);
                ToastSystem.show('Error de conexión al cargar los detalles', 'error');
            });
        }

        // Función auxiliar para formatear fechas
        function formatearFecha(fecha) {
            const date = new Date(fecha);
            const opciones = { 
                year: 'numeric', 
                month: 'long', 
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            };
            return date.toLocaleDateString('es-ES', opciones);
        }

        // Función para imprimir venta
        function imprimirVenta() {
            window.print();
        }

        // Función para descargar PDF (placeholder)
        function descargarPDF() {
            ToastSystem.show('Función de descarga PDF en desarrollo', 'info');
        }

        function deleteSale(id) {
            if (confirm('¿Está seguro de eliminar esta venta?')) {
                // Mostrar loading
                document.getElementById('loadingOverlay').style.display = 'flex';
                
                // Crear FormData para enviar el ID
                const formData = new FormData();
                formData.append('venta_id', id);
                
                // Enviar petición AJAX para eliminar
                fetch('eliminar_venta.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    // Ocultar loading
                    document.getElementById('loadingOverlay').style.display = 'none';
                    
                    if (data.success) {
                        ToastSystem.show('Venta eliminada correctamente', 'success');
                        
                        // Recargar la página para actualizar la tabla
                        setTimeout(() => {
                            location.reload();
                        }, 1000);
                    } else {
                        ToastSystem.show(`Error al eliminar: ${data.message}`, 'error');
                    }
                })
                .catch(error => {
                    // Ocultar loading
                    document.getElementById('loadingOverlay').style.display = 'none';
                    console.error('Error:', error);
                    ToastSystem.show('Error de conexión al eliminar la venta', 'error');
                });
            }
        }

       function generatePDF(ventaId) {
    console.log('Generando PDF para venta:', ventaId);
    
    // Mostrar mensaje de carga
    ToastSystem.show('Generando PDF...', 'info');
    
    // Crear un enlace temporal para descargar
    const link = document.createElement('a');
    link.href = `generar_pdf_factura.php?venta_id=${ventaId}`; 
    link.target = '_blank';
    link.style.display = 'none';
    
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
    
    // Mostrar mensaje de éxito
    setTimeout(() => {
        ToastSystem.show('PDF generado correctamente', 'success');
    }, 1000);
}

// Alternativa más simple y confiable
function generatePDFSimple(ventaId) {
    console.log('Generando PDF para venta:', ventaId);
    ToastSystem.show('Abriendo PDF...', 'info');
    
    // Abrir directamente en nueva ventana/pestaña
    const ventana = window.open(`generar_pdf_factura.php?venta_id=${ventaId}`, '_blank');
    
    if (ventana) {
        // Verificar si la ventana se abrió correctamente
        setTimeout(() => {
            if (!ventana.closed) {
                ToastSystem.show('PDF abierto correctamente', 'success');
            }
        }, 1000);
    } else {
        ToastSystem.show('Por favor, permite ventanas emergentes para ver el PDF', 'warning');
    }
}

// Función con descarga directa (más confiable)
function generatePDFDownload(ventaId) {
    console.log('Descargando PDF para venta:', ventaId);
    ToastSystem.show('Iniciando descarga...', 'info');
    
    const link = document.createElement('a');
    link.href = `generar_pdf_factura.php?venta_id=${ventaId}&download=1`;
    link.download = `Factura_${ventaId.toString().padStart(3, '0')}.pdf`;
    link.style.display = 'none';
    
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
    
    setTimeout(() => {
        ToastSystem.show('PDF descargado', 'success');
    }, 1000);
}
        

        console.log("Sistema inicializado correctamente");
    </script>
</body>
</html>
