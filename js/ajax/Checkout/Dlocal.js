$('#Pagar').on('click', function (e) {
    e.preventDefault();
    
    // Obtener el valor numérico del precio (quitar formato)
    var precioTexto = $('#Precio').attr('value') || $('#Precio').text().replace('$', '').replace(/\./g, '').trim();
    var total = parseFloat(precioTexto);
    
    // Verificar si hay productos en el carrito
    if (total <= 0) {
        alert("No hay productos en el carrito para procesar el pago.");
        return;
    }
    
    // Mostrar indicador de carga
    var btnOriginalText = $(this).html();
    $(this).html('<i class="fa fa-spinner fa-spin"></i> Procesando...');
    $(this).prop('disabled', true);
    
    // Obtener información del usuario
    var nombre = $('#Nombre').val(); 
    var email = $('#CorreoP').val();
    
    // Agregar logs para depuración
    console.log("Enviando solicitud de pago:");
    console.log({
        total: total,
        nombre: nombre,
        email: email,
        moneda: "COP"
    });
    
    $.ajax({
        url: 'Backend/Checkout/Dlocal.php',
        method: 'POST',
        dataType: 'json',
        data: {
            total: total,
            nombre: nombre,
            email: email,
            moneda: "COP"
        },
        success: function (response) {
            console.log("Respuesta recibida:", response);

    // Redirigir al checkout si existe la URL
    if (response.redirect_url) {
        window.location.href = response.redirect_url;
    } else {
        alert("No se pudo obtener la URL de redireccion.");
    }
},

        error: function (xhr, status, error) {
            console.error("Error AJAX:", status, error);
            console.log("Respuesta del servidor:", xhr.responseText);
            
            try {
                var response = JSON.parse(xhr.responseText);
                alert("Error: " + (response.error || "Hubo un problema al procesar el pago."));
            } catch (e) {
                alert("Hubo un error al procesar el pago. Por favor intente nuevamente.");
            }
            
            // Restaurar el botón
            $('#Pagar').html(btnOriginalText);
            $('#Pagar').prop('disabled', false);
        }
    });
});