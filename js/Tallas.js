// Solución para el problema de "Cantidad: Cantidad: undefined"

$(document).ready(function() {
    // Función para limpiar y establecer correctamente el texto de cantidad
    function resetCantidadText() {
        // Primero vaciar completamente el elemento
        $('.cantidad').empty();
        
        // Luego establecer el texto sin el prefijo "Cantidad:"
        $('.cantidad').text('Seleccione una talla');
    }
    
    // Ejecutar al cargar la página
    resetCantidadText();
    
    // Ejecutar cuando se abre el modal
    $(document).on('click', '.js-show-modal1', function() {
        setTimeout(resetCantidadText, 50);
    });
    
    // Ejecutar cada vez que se cierra y abre el modal
    $(document).on('hidden.bs.modal', '.js-modal1', function() {
        resetCantidadText();
    });
    
    // Sobrescribir el evento de cambio de talla completamente
    // Solución mínima para mantener la talla seleccionada en el select

// Reemplaza tu código actual del evento change por este:
$('select[name="talla"]').on('change', function () {
    const tallaId = $(this).val();
    const productoId = $('.js-addcart-detail').data('id');
    const $select = $(this); // Guardar referencia al select
    
    // Limpiar cantidad si no se selecciona una talla válida
    if (!tallaId) {
        $('.cantidad').text('Seleccione una talla');
        return; // Salir del evento
    }
    
    if (productoId) {
        $('.cantidad').text('Consultando...');
        
        $.ajax({
            method: 'POST',
            url: 'Backend/Producto/Tallas.php',
            data: {
                productoId: productoId,
                tallaId: tallaId
            },
            dataType: 'json',
            success: function (response) {
                if (response.error) {
                    console.error(response.error);
                    $('.cantidad').text('Error al consultar stock');
                } else {
                    console.log("Stock disponible:", response.stock);
                    if (response.stock == 0) {
                        $('.cantidad').text('No hay stock disponible');
                    } else {
                        $('.cantidad').text('Stock disponible: ' + response.stock);
                    }
                    
                    // SOLUCIÓN: Restablecer el valor después de la respuesta AJAX
                    setTimeout(function() {
                        $select.val(tallaId);
                        
                        // Si usas Select2, actualiza también la interfaz visual
                        try {
                            $select.val(tallaId).trigger('change.select2');
                        } catch(e) {
                            console.log("No se pudo actualizar Select2");
                        }
                    }, 50);
                }
            },
            error: function (error) {
                console.log("Error en la consulta de stock:", error);
                $('.cantidad').text('Error al consultar stock');
            }
        });
    }
});
});