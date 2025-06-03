$(document).ready(function() {
    // Mostrar el loader al iniciar
    $('#loader').show();
    
    // Cargar automáticamente el contenido de home.php al iniciar
    $.ajax({
        url: 'vistas/home.php', // Asegúrate de que esta ruta es correcta
        type: 'GET',
        success: function(response) {
            $('#container-wrapper').html(response);
        },
        error: function(xhr, status, error) {
            console.error('Error al cargar el contenido inicial:', error);
        },
        complete: function() {
            $('#loader').hide();
        }
    });

    // Manejador para el enlace de productos
    $('#productos-link').click(function(e) {
        e.preventDefault();  // Prevenir el comportamiento predeterminado del enlace

        $('#loader').show();
        $('#container-wrapper').html('');  // Limpiar el contenido actual

        $.ajax({
            url: 'vistas/Productos.php',
            type: 'GET',
            success: function(response) {
                $('#container-wrapper').html(response);
            },
            error: function(xhr, status, error) {
                console.error('Hubo un error al cargar el contenido:', error);
            },
            complete: function() {
                $('#loader').hide();
                $('#collapseBootstrap').collapse('hide');
            }
        });
    });
    
    $('#cliente-link').click(function(e) {
        e.preventDefault();  // Prevenir el comportamiento predeterminado del enlace

        $('#loader').show();
        $('#container-wrapper').html('');  // Limpiar el contenido actual

        $.ajax({
            url: 'vistas/Clientes.php',
            type: 'GET',
            success: function(response) {
                $('#container-wrapper').html(response);
            },
            error: function(xhr, status, error) {
                console.error('Hubo un error al cargar el contenido:', error);
            },
            complete: function() {
                $('#loader').hide();
                $('#collapseBootstrap').collapse('hide');
            }
        });
    });
    
     $('#admin_link').click(function(e) {
        e.preventDefault();  // Prevenir el comportamiento predeterminado del enlace

        $('#loader').show();
        $('#container-wrapper').html('');  // Limpiar el contenido actual

        $.ajax({
            url: 'vistas/Empleados.php',
            type: 'GET',
            success: function(response) {
                $('#container-wrapper').html(response);
            },
            error: function(xhr, status, error) {
                console.error('Hubo un error al cargar el contenido:', error);
            },
            complete: function() {
                $('#loader').hide();
                $('#collapseBootstrap').collapse('hide');
            }
        });
    });
    
    $('#rol_link').click(function(e) {
        e.preventDefault();  // Prevenir el comportamiento predeterminado del enlace

        $('#loader').show();
        $('#container-wrapper').html('');  // Limpiar el contenido actual

        $.ajax({
            url: 'vistas/Roles.php',
            type: 'GET',
            success: function(response) {
                $('#container-wrapper').html(response);
            },
            error: function(xhr, status, error) {
                console.error('Hubo un error al cargar el contenido:', error);
            },
            complete: function() {
                $('#loader').hide();
                $('#collapseBootstrap').collapse('hide');
            }
        });
    });
    
    $('#venta_link').click(function(e) {
        e.preventDefault();  // Prevenir el comportamiento predeterminado del enlace

        $('#loader').show();
        $('#container-wrapper').html('');  // Limpiar el contenido actual

        $.ajax({
            url: 'vistas/Ventas.php',
            type: 'GET',
            success: function(response) {
                $('#container-wrapper').html(response);
                // Inicializar script específico de ventas después de cargar el contenido
                setTimeout(function() {
                    if (typeof window.initVentasScript === 'function') {
                        window.initVentasScript();
                    }
                }, 100);
            },
            error: function(xhr, status, error) {
                console.error('Hubo un error al cargar el contenido:', error);
            },
            complete: function() {
                $('#loader').hide();
                $('#collapseBootstrap').collapse('hide');
            }
        });
    });
    
    $('#compra_link').click(function(e) {
        e.preventDefault();  // Prevenir el comportamiento predeterminado del enlace

        $('#loader').show();
        $('#container-wrapper').html('');  // Limpiar el contenido actual

        $.ajax({
            url: 'vistas/Compras.php',
            type: 'GET',
            success: function(response) {
                $('#container-wrapper').html(response);
            },
            error: function(xhr, status, error) {
                console.error('Hubo un error al cargar el contenido:', error);
            },
            complete: function() {
                $('#loader').hide();
                $('#collapseBootstrap').collapse('hide');
            }
        });
    });
    
    $('#proveedor_link').click(function(e) {
        e.preventDefault();  // Prevenir el comportamiento predeterminado del enlace

        $('#loader').show();
        $('#container-wrapper').html('');  // Limpiar el contenido actual

        $.ajax({
            url: 'vistas/Proveedores.php',
            type: 'GET',
            success: function(response) {
                $('#container-wrapper').html(response);
            },
            error: function(xhr, status, error) {
                console.error('Hubo un error al cargar el contenido:', error);
            },
            complete: function() {
                $('#loader').hide();
                $('#collapseBootstrap').collapse('hide');
            }
        });
    });
    
     $('#Vender_link').click(function(e) {
        e.preventDefault();  // Prevenir el comportamiento predeterminado del enlace

        $('#loader').show();
        $('#container-wrapper').html('');  // Limpiar el contenido actual

        $.ajax({
            url: 'vistas/Vender.php',
            type: 'GET',
            success: function(response) {
                $('#container-wrapper').html(response);
            },
            error: function(xhr, status, error) {
                console.error('Hubo un error al cargar el contenido:', error);
            },
            complete: function() {
                $('#loader').hide();
                $('#collapseBootstrap').collapse('hide');
            }
        });
    });
});