document.addEventListener('DOMContentLoaded', function() {
    // Botón de procesar pago
    const btnPagar = document.getElementById('Pagar');
    
    if (btnPagar) {
        btnPagar.addEventListener('click', function(e) {
            e.preventDefault();
            
            // Obtener el precio total (con o sin descuento) del contenido del span
            const precioElement = document.getElementById('Precio');
            let precioTexto = precioElement.textContent || precioElement.innerText;
            
            // Limpiar el formato del precio (quitar puntos, símbolo $ y espacios)
            precioTexto = precioTexto.replace(/\$/g, '').replace(/\./g, '').replace(/\s/g, '').trim();
            const monto = parseInt(precioTexto);
            
            console.log('Precio obtenido:', precioTexto, 'Monto convertido:', monto); // Para debug
            
            if (isNaN(monto) || monto <= 0) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'No hay productos en el carrito para procesar el pago'
                });
                return;
            }
            
            // Validar campos necesarios
            const pais = document.querySelector('input[name="Pais"]').value;
            const ciudad = document.querySelector('input[name="Ciudad"]').value;
            const direccion = document.querySelector('textarea').value;
            
            if (!pais || !ciudad || !direccion) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Información incompleta',
                    text: 'Por favor complete todos los campos de envío'
                });
                return;
            }
            
            // Crear formulario para enviar a procesarWompi.php
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = 'Backend/Checkout/wompi.php';
            
            // Agregar el monto
            const inputMonto = document.createElement('input');
            inputMonto.type = 'hidden';
            inputMonto.name = 'amount';
            inputMonto.value = monto;
            form.appendChild(inputMonto);
            
            // Agregar process_payment flag
            const inputProcess = document.createElement('input');
            inputProcess.type = 'hidden';
            inputProcess.name = 'process_payment';
            inputProcess.value = '1';
            form.appendChild(inputProcess);
            
            // Enviar información adicional que puede ser útil
            const inputPais = document.createElement('input');
            inputPais.type = 'hidden';
            inputPais.name = 'pais';
            inputPais.value = pais;
            form.appendChild(inputPais);
            
            const inputCiudad = document.createElement('input');
            inputCiudad.type = 'hidden';
            inputCiudad.name = 'ciudad';
            inputCiudad.value = ciudad;
            form.appendChild(inputCiudad);
            
            const inputDireccion = document.createElement('input');
            inputDireccion.type = 'hidden';
            inputDireccion.name = 'direccion';
            inputDireccion.value = direccion;
            form.appendChild(inputDireccion);
            
            // Añadir al DOM y enviar
            document.body.appendChild(form);
            
            // Mostrar mensaje de procesamiento
            Swal.fire({
                title: 'Procesando...',
                text: 'Preparando el pago, serás redirigido a Wompi',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                    // Enviar el formulario después de mostrar el loading
                    setTimeout(() => {
                        form.submit();
                    }, 1000);
                }
            });
        });
    }
});