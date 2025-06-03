// Mejora para el script de actualización del carrito
document.addEventListener('DOMContentLoaded', function() {
    // Seleccionar elementos relevantes
    const btnAplicarCupon = document.getElementById('btnAplicarCupon');
    const btnEliminarCupon = document.getElementById('btnEliminarCupon');
    const inputCantidades = document.querySelectorAll('.num-product');
    const botonesAumentar = document.querySelectorAll('.btn-num-product-up');
    const botonesDisminuir = document.querySelectorAll('.btn-num-product-down');
    
    // Función para aplicar cupón
    if (btnAplicarCupon) {
        btnAplicarCupon.addEventListener('click', function() {
            const inputCupon = document.querySelector('input[name="coupon"]');
            const codigoCupon = inputCupon.value.trim();
            
            if (codigoCupon === '') {
                mostrarAlerta('Por favor ingrese un código de cupón', 'error');
                return;
            }
            
            verificarCupon(codigoCupon);
        });
    }
    
    // Función para eliminar cupón
    if (btnEliminarCupon) {
        btnEliminarCupon.addEventListener('click', eliminarCupon);
    }
    
    // Eventos para cambiar cantidades
    botonesAumentar.forEach(btn => {
        btn.addEventListener('click', function() {
            const input = this.previousElementSibling;
            const nuevoValor = parseInt(input.value) + 1;
            input.value = nuevoValor;
            actualizarCantidad(input);
        });
    });
    
    botonesDisminuir.forEach(btn => {
        btn.addEventListener('click', function() {
            const input = this.nextElementSibling;
            const nuevoValor = Math.max(1, parseInt(input.value) - 1);
            input.value = nuevoValor;
            actualizarCantidad(input);
        });
    });
    
    inputCantidades.forEach(input => {
        input.addEventListener('change', function() {
            // Asegurar que el valor mínimo sea 1
            if (parseInt(this.value) < 1) {
                this.value = 1;
            }
            actualizarCantidad(this);
        });
    });
    
    // Función para actualizar cantidad mediante AJAX
    function actualizarCantidad(input) {
        const productoId = input.getAttribute('data-id');
        const nuevaCantidad = parseInt(input.value);
        const precio = parseFloat(input.getAttribute('data-precio'));
        const talla = input.getAttribute('data-talla'); // Obtener la talla del producto
        
        // Calcular el nuevo total para este producto
        const nuevoTotal = nuevaCantidad * precio;
        
        // Actualizar el total mostrado para este producto
        const totalProductoElement = input.closest('tr').querySelector('.total-producto');
        if (totalProductoElement) {
            totalProductoElement.textContent = formatearNumero(nuevoTotal);
        }
        
        // Enviar actualización al servidor
        const formData = new FormData();
        formData.append('producto_id', productoId);
        formData.append('cantidad', nuevaCantidad);
        formData.append('talla', talla); // Añadir la talla al formulario
        
        fetch('Backend/Checkout/ActualizarCantidad.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                // CORRECCIÓN: Actualizar TODOS los elementos que muestran el total general
                actualizarTodosLosSubtotales(data.total_general);
                
                // Si hay cupón aplicado, actualizar los montos con descuento
                if (data.cupon_aplicado) {
                    // Actualizar fila de descuento
                    agregarFilaDescuento(data.cupon_descuento, data.cupon_monto_descuento);
                    
                    // Actualizar total final
                    actualizarTotalConDescuento(data.cupon_nuevo_total);
                } else {
                    // Si no hay cupón, el total con descuento es igual al total general
                    actualizarTotalConDescuento(data.total_general);
                }
            } else {
                mostrarAlerta('Error al actualizar cantidad: ' + data.message, 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            mostrarAlerta('Ha ocurrido un error al actualizar la cantidad', 'error');
        });
    }
    
    // NUEVA FUNCIÓN: Actualizar todos los elementos que muestran el subtotal
    function actualizarTodosLosSubtotales(nuevoTotal) {
        // 1. Actualizar el total general en la tabla del carrito
        const totalGeneralElement = document.getElementById('total-general');
        if (totalGeneralElement) {
            totalGeneralElement.textContent = formatearNumero(nuevoTotal);
        }
        
        // 2. Actualizar el subtotal en el resumen del carrito
        const subtotalElement = document.querySelector('.flex-w.flex-t.bor12.p-b-13 .size-209 .mtext-110.cl2');
        if (subtotalElement) {
            subtotalElement.textContent = '$ ' + formatearNumero(nuevoTotal);
        }
    }
    
    // NUEVA FUNCIÓN: Actualizar el total con descuento
    function actualizarTotalConDescuento(nuevoTotal) {
        const totalFinalElement = document.querySelector('.flex-w.flex-t.p-t-27.p-b-33 .size-209 .mtext-110.cl2');
        if (totalFinalElement) {
            totalFinalElement.textContent = '$ ' + formatearNumero(nuevoTotal);
        }
    }
    
    // Función para verificar cupón mediante AJAX
    function verificarCupon(codigo) {
        const formData = new FormData();
        formData.append('codigo_cupon', codigo);
        
        mostrarCargando();
        
        fetch('Backend/Checkout/Cupon.php', {
            method: 'POST',
            body: formData
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Error en la respuesta del servidor');
            }
            return response.json();
        })
        .then(data => {
            ocultarCargando();
            
            console.log('Respuesta del servidor:', data);
            
            if (data.status === 'success') {
                mostrarAlerta(data.message, 'success');
                
                // Actualizar los totales en la interfaz
                actualizarTotalesCupon(data);
                
                // Deshabilitar el input de cupón
                const inputCupon = document.querySelector('input[name="coupon"]');
                inputCupon.disabled = true;
                
                // Reemplazar el botón de aplicar por el de eliminar
                reemplazarBotonCupon();
            } else {
                mostrarAlerta(data.message, 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            ocultarCargando();
            mostrarAlerta('Ha ocurrido un error al procesar su solicitud', 'error');
        });
    }
    
    // Función para eliminar el cupón aplicado
    function eliminarCupon() {
        fetch('Backend/Checkout/EliminarCupon.php', {
            method: 'POST'
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                mostrarAlerta('Cupón eliminado correctamente', 'success');
                
                // Eliminar la fila de descuento si existe
                const filaDescuento = document.getElementById('fila-descuento');
                if (filaDescuento) {
                    filaDescuento.remove();
                }
                
                // Actualizar el total final (ahora es igual al subtotal)
                const subtotalElement = document.querySelector('.flex-w.flex-t.bor12.p-b-13 .size-209 .mtext-110.cl2');
                const totalFinalElement = document.querySelector('.flex-w.flex-t.p-t-27.p-b-33 .size-209 .mtext-110.cl2');
                
                if (subtotalElement && totalFinalElement) {
                    totalFinalElement.textContent = subtotalElement.textContent;
                }
                
                // Habilitar el input de cupón y restablecer su valor
                const inputCupon = document.querySelector('input[name="coupon"]');
                if (inputCupon) {
                    inputCupon.disabled = false;
                    inputCupon.value = '';
                }
                
                // Reemplazar el botón de eliminar por el de aplicar
                const btnEliminar = document.getElementById('btnEliminarCupon');
                if (btnEliminar) {
                    const btnAplicar = document.createElement('div');
                    btnAplicar.id = 'btnAplicarCupon';
                    btnAplicar.className = 'flex-c-m stext-101 cl2 size-118 bg8 bor13 hov-btn3 p-lr-15 trans-04 pointer m-tb-5';
                    btnAplicar.textContent = 'Aplicar cupon';
                    btnAplicar.addEventListener('click', function() {
                        const inputCupon = document.querySelector('input[name="coupon"]');
                        verificarCupon(inputCupon.value.trim());
                    });
                    
                    btnEliminar.parentNode.replaceChild(btnAplicar, btnEliminar);
                }
            } else {
                mostrarAlerta(data.message, 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            mostrarAlerta('Ha ocurrido un error al eliminar el cupón', 'error');
        });
    }
    
    // Función para actualizar los totales después de aplicar el cupón
    function actualizarTotalesCupon(data) {
        // Actualizar el subtotal
        actualizarTodosLosSubtotales(data.total_original);
        
        // Agregar fila de descuento
        agregarFilaDescuento(data.descuento, data.monto_descuento);
        
        // Actualizar el total final
        actualizarTotalConDescuento(data.nuevo_total);
    }
    
    // Función para agregar la fila de descuento
    function agregarFilaDescuento(porcentaje, monto) {
        const existeDescuento = document.querySelector('#fila-descuento');
        
        if (!existeDescuento) {
            const nuevaFila = document.createElement('div');
            nuevaFila.id = 'fila-descuento';
            nuevaFila.className = 'flex-w flex-t bor12 p-t-15 p-b-15';
            nuevaFila.innerHTML = `
                <div class="size-208">
                    <span class="stext-110 cl2">
                        Descuento (${porcentaje}%):
                    </span>
                </div>
                <div class="size-209">
                    <span class="mtext-110 cl2" style="color: #e83e8c;">
                        -$ ${formatearNumero(monto)}
                    </span>
                </div>
            `;
            
            const filaShipping = document.querySelector('.flex-w.flex-t.bor12.p-t-15.p-b-30');
            filaShipping.parentNode.insertBefore(nuevaFila, filaShipping);
        } else {
            const porcentajeElement = existeDescuento.querySelector('.size-208 .stext-110.cl2');
            const montoElement = existeDescuento.querySelector('.size-209 .mtext-110.cl2');
            
            porcentajeElement.textContent = `Descuento (${porcentaje}%):`;
            montoElement.textContent = `-$ ${formatearNumero(monto)}`;
        }
    }
    
    // Función para reemplazar el botón de aplicar por el de eliminar
    function reemplazarBotonCupon() {
        const btnAplicar = document.getElementById('btnAplicarCupon');
        
        if (btnAplicar) {
            const btnEliminar = document.createElement('div');
            btnEliminar.id = 'btnEliminarCupon';
            btnEliminar.className = 'flex-c-m stext-101 cl0 size-118 bg3 bor13 hov-btn3 p-lr-15 trans-04 pointer m-tb-5';
            btnEliminar.textContent = 'Eliminar cupón';
            btnEliminar.addEventListener('click', eliminarCupon);
            
            btnAplicar.parentNode.replaceChild(btnEliminar, btnAplicar);
        }
    }
    
    // Función para mostrar alerta
    function mostrarAlerta(mensaje, tipo) {
        const alertaExistente = document.querySelector('.alerta-cupon');
        if (alertaExistente) {
            alertaExistente.remove();
        }
        
        const alerta = document.createElement('div');
        alerta.className = `alerta-cupon alerta-${tipo}`;
        alerta.style.padding = '10px 15px';
        alerta.style.marginTop = '10px';
        alerta.style.borderRadius = '5px';
        alerta.style.fontWeight = 'bold';
        
        if (tipo === 'error') {
            alerta.style.backgroundColor = '#f8d7da';
            alerta.style.color = '#721c24';
            alerta.style.border = '1px solid #f5c6cb';
        } else {
            alerta.style.backgroundColor = '#d4edda';
            alerta.style.color = '#155724';
            alerta.style.border = '1px solid #c3e6cb';
        }
        
        alerta.textContent = mensaje;
        
        const inputCupon = document.querySelector('input[name="coupon"]');
        inputCupon.parentNode.appendChild(alerta);
        
        setTimeout(() => {
            if (alerta && alerta.parentNode) {
                alerta.remove();
            }
        }, 5000);
    }
    
    // Función para mostrar indicador de carga
    function mostrarCargando() {
        const btnAplicarCupon = document.getElementById('btnAplicarCupon');
        if (btnAplicarCupon) {
            btnAplicarCupon.textContent = 'Aplicando...';
            btnAplicarCupon.disabled = true;
        }
    }
    
    // Función para ocultar indicador de carga
    function ocultarCargando() {
        const btnAplicarCupon = document.getElementById('btnAplicarCupon');
        if (btnAplicarCupon) {
            btnAplicarCupon.textContent = 'Aplicar cupon';
            btnAplicarCupon.disabled = false;
        }
    }
    
    // Función para formatear números con separadores de miles
    function formatearNumero(numero) {
        numero = parseFloat(numero);
        if (isNaN(numero)) numero = 0;
        
        return new Intl.NumberFormat('es-CO', { minimumFractionDigits: 0, maximumFractionDigits: 0 }).format(numero);
    }
});