// Código mejorado para el manejo de carrito con sincronización localStorage-servidor

$(document).ready(function () {
    // IMPORTANTE: Sincronizar carrito al cargar la página
    sincronizarCarritoCompleto();
    
    // Función para limpiar y establecer correctamente el texto de cantidad
    function resetCantidadText() {
        $('.cantidad').empty();
        $('.cantidad').text('Seleccione una talla');
    }
    
    resetCantidadText();
    
    $(document).on('click', '.js-show-modal1', function() {
        setTimeout(resetCantidadText, 50);
    });
    
    $(document).on('hidden.bs.modal', '.js-modal1', function() {
        resetCantidadText();
    });
    
    // Evento de cambio de talla mejorado
    $('select[name="talla"]').on('change', function () {
        const tallaId = $(this).val();
        const productoId = $('.js-addcart-detail').data('id');
        const $select = $(this);
        
        if (!tallaId) {
            $('.cantidad').text('Seleccione una talla');
            return;
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
                        
                        setTimeout(function() {
                            $select.val(tallaId);
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

// 🔥 NUEVA FUNCIÓN: Sincronizar carrito completamente
function sincronizarCarritoCompleto() {
    console.log('🔄 Iniciando sincronización completa del carrito...');
    
    $.ajax({
        url: 'Backend/Carrito/Ver.php',
        type: 'GET',
        dataType: 'json',
        success: function(carritoServidor) {
            console.log('📦 Carrito del servidor:', carritoServidor);
            
            // Actualizar localStorage con los datos del servidor
            if (Array.isArray(carritoServidor)) {
                localStorage.setItem('carrito', JSON.stringify(carritoServidor));
                console.log('✅ localStorage sincronizado con el servidor');
            } else {
                // Si el servidor no devuelve un array válido, limpiar localStorage
                localStorage.setItem('carrito', JSON.stringify([]));
                console.log('🧹 localStorage limpiado - servidor devolvió datos inválidos');
            }
            
            // Actualizar la visualización
            actualizarCarrito();
        },
        error: function(err) {
            console.error('❌ Error al sincronizar con el servidor:', err);
            // En caso de error, mantener localStorage pero mostrar advertencia
            console.log('⚠️ Usando datos del localStorage como respaldo');
            actualizarCarrito();
        }
    });
}

// 🔥 NUEVA FUNCIÓN: Limpiar localStorage y sincronizar
function limpiarYSincronizarCarrito() {
    console.log('🧹 Limpiando localStorage y sincronizando...');
    
    // Primero limpiar localStorage
    localStorage.removeItem('carrito');
    
    // Luego sincronizar con el servidor
    sincronizarCarritoCompleto();
    
    mostrarToast("info", "Carrito sincronizado correctamente");
}

// Función mejorada para agregar al carrito
$(document).on('click', '.js-addcart-detail', function (e) {
    e.preventDefault();
    
    const $modal = $(this).closest('.js-modal1');
    
    let id = $(this).attr('data-id');
    
    if (!id || id === "" || id === "undefined" || id === "null") {
        mostrarAlertaSobreModal('Error', 'No se pudo identificar el producto', 'error');
        return;
    }
    
    id = String(id);
    
    let nombre = $('.modal-title').text().trim();
    let precioTexto = $('.precio').text().trim();
    let cantidad = parseInt($('.cantidad-producto').val() || 1);
    let foto = $('.slick3 .item-slick3:first img').attr('src');
    let talla = $('select[name="talla"]').val();
    let tallaTexto = $('select[name="talla"] option:selected').text().trim();
    
    // Verificar talla
    if (!talla || talla === "") {
        mostrarToastSobreModal("warning", "Por favor seleccione una talla");
        return;
    }
    
    // Obtener el stock disponible
    let cantidadText = $('.cantidad').text().trim();
    let stockDisponible = 0;
    
    if (cantidadText.includes('Stock disponible:')) {
        stockDisponible = parseInt(cantidadText.replace('Stock disponible:', '').trim());
    }
    
    if (isNaN(stockDisponible) || stockDisponible <= 0) {
        mostrarAlertaSobreModal('Error', 'No hay stock disponible para la talla seleccionada', 'error');
        return;
    }
    
    // Verificar cantidad vs stock
    if (cantidad > stockDisponible) {
        mostrarAlertaSobreModal('Error', `Solo hay ${stockDisponible} unidades disponibles para esta talla`, 'error');
        return;
    }
    
    // Procesar precio
    let precio = precioTexto.replace(/[^\d,]/g, '').replace(/\./g, '').replace(',', '.');
    precio = parseFloat(precio);
    
    if (isNaN(precio)) {
        mostrarAlertaSobreModal('Error', 'Precio inválido.', 'error');
        return;
    }
    
    if (cantidad <= 0 || isNaN(cantidad)) {
        mostrarToastSobreModal("warning", "La cantidad debe ser mayor que cero.");
        return;
    }
    
    let fotoNombre = foto.split('/').pop();
    let idCompuesto = `${id}-${talla}`;
    
    // 🔥 MEJORA CRÍTICA: Primero sincronizar con el servidor antes de validar
    console.log('🔄 Sincronizando antes de agregar producto...');
    
    $.ajax({
        url: 'Backend/Carrito/Ver.php',
        type: 'GET',
        dataType: 'json',
        success: function(carritoServidor) {
            console.log('📦 Carrito actual del servidor:', carritoServidor);
            
            // Actualizar localStorage con datos frescos del servidor
            let carritoActualizado = Array.isArray(carritoServidor) ? carritoServidor : [];
            localStorage.setItem('carrito', JSON.stringify(carritoActualizado));
            
            // Ahora validar con datos actualizados
            let productoExistente = null;
            let cantidadExistente = 0;
            
            for (let i = 0; i < carritoActualizado.length; i++) {
                let item = carritoActualizado[i];
                let itemId = item.idCompuesto || `${item.id}-${item.talla || ''}`;
                
                if (itemId === idCompuesto) {
                    productoExistente = item;
                    cantidadExistente = parseInt(item.cantidad);
                    console.log(`✅ Producto existente encontrado: ${item.nombre}, cantidad actual: ${cantidadExistente}`);
                    break;
                }
            }
            
            // Validar stock con datos actualizados
            if (productoExistente) {
                let nuevaCantidadTotal = cantidadExistente + cantidad;
                
                if (nuevaCantidadTotal > stockDisponible) {
                    mostrarAlertaSobreModal('Error', 
                        `No puedes agregar ${cantidad} unidades más. Solo hay ${stockDisponible} unidades disponibles en total y ya tienes ${cantidadExistente} en tu carrito.`, 
                        'error');
                    return;
                }
            }
            
            // Proceder a agregar el producto
            agregarProductoAlServidor();
            
        },
        error: function(err) {
            console.error('❌ Error al sincronizar antes de agregar:', err);
            // En caso de error, proceder con precaución
            mostrarAlertaSobreModal('Advertencia', 'No se pudo verificar el estado actual del carrito. ¿Desea continuar?', 'warning');
        }
    });
    
    // Función interna para agregar al servidor
    function agregarProductoAlServidor() {
        $.ajax({
            url: 'Backend/Carrito/Agregar.php',
            method: 'POST',
            dataType: 'json',
            data: {
                id: id,
                nombre: nombre,
                precio: precio,
                cantidad: cantidad,
                foto: fotoNombre,
                talla: talla,
                tallaTexto: tallaTexto,
                idCompuesto: idCompuesto
            },
            success: function (res) {
                if (res.success) {
                    // Sincronizar inmediatamente después de agregar
                    sincronizarCarritoCompleto();
                    
                    // Cerrar modal
                    try {
                        $('.js-hide-modal1').trigger('click');
                        
                        setTimeout(function() {
                            $('.js-modal1').removeClass('show-modal1');
                            $('body').removeClass('modal-open');
                            $('.overlay-modal1').removeClass('show-modal1');
                        }, 100);
                        
                        setTimeout(function() {
                            $('.wrap-modal1').hide();
                            $('.js-modal1').css('display', 'none');
                        }, 200);
                        
                    } catch (error) {
                        console.error('Error al cerrar modal:', error);
                        $('.js-modal1').hide();
                        $('.wrap-modal1').hide();
                    }
                    
                    setTimeout(function() {
                        mostrarToast("success", "Producto agregado con éxito.");
                    }, 400);
                    
                } else {
                    mostrarAlertaSobreModal('Error', res.error || 'No se pudo agregar al carrito.', 'error');
                }
            },
            error: function (err) {
                console.error('Error AJAX:', err);
                mostrarAlertaSobreModal('Error', 'Ocurrió un error al agregar el producto.', 'error');
            }
        });
    }
});

// Función mejorada para actualizar la visualización del carrito
function actualizarCarrito() {
    console.log('🔄 Actualizando visualización del carrito...');
    
    $.ajax({
        url: 'Backend/Carrito/Ver.php',
        type: 'GET',
        dataType: 'json',
        success: function(carrito) {
            console.log('📦 Datos del carrito recibidos:', carrito);
            
            // Actualizar localStorage con datos frescos
            if (Array.isArray(carrito)) {
                localStorage.setItem('carrito', JSON.stringify(carrito));
            }
            
            if (!Array.isArray(carrito)) {
                console.error('❌ El carrito no es un array:', carrito);
                return;
            }
            
            let html = '';
            let total = 0;
            let cantidadTotal = 0;
            
            if (carrito.length === 0) {
                $('.header-cart-wrapitem').html('No hay productos');
                $('.header-cart-total').html('Total: $0');
                $('.icon-header-noti').attr('data-notify', '0');
                return;
            }
            
            carrito.forEach(item => {
                let fotoSrc = item.foto;
                if (!fotoSrc.startsWith('http') && !fotoSrc.startsWith('/') && !fotoSrc.startsWith('images/')) {
                    fotoSrc = 'images/' + fotoSrc;
                }
                let itemPrecio = parseFloat(item.precio);
                let itemCantidad = parseInt(item.cantidad);
                
                let itemId = item.idCompuesto || `${item.id}-${item.talla || ''}`;
                let tallaDisplay = item.tallaTexto || item.talla;
                let tallaInfo = tallaDisplay ? `<span class="header-cart-item-info talla-info">Talla: ${tallaDisplay}</span>` : '';
                
                html += `
<li class="header-cart-item flex-w flex-t m-b-12">
    <div class="header-cart-item-img" data-id="${itemId}" data-original-id="${item.id}" data-talla="${item.talla}">
        <img src="${fotoSrc}" alt="${item.nombre}">
    </div>
    <div class="header-cart-item-txt p-t-8">
        <a href="#" class="header-cart-item-name m-b-18 hov-cl1 trans-04">
            ${item.nombre}
        </a>
        ${tallaInfo}
        <span class="header-cart-item-info">
            ${itemCantidad} x ${itemPrecio.toLocaleString('es-CO', { style: 'currency', currency: 'COP' })}
        </span>
    </div>
</li>
`;
                total += itemPrecio * itemCantidad;
                cantidadTotal += itemCantidad;
            });
            
            $('.header-cart-wrapitem').html(html);
            $('.header-cart-total').html('Total: ' + total.toLocaleString('es-CO', { style: 'currency', currency: 'COP' }));
            $('.icon-header-noti').attr('data-notify', cantidadTotal);
            
            initializeDeleteEvents();
        },
        error: function(err) {
            console.error('❌ Error al cargar el carrito:', err);
        }
    });
}

// Función para inicializar eventos de eliminación
function initializeDeleteEvents() {
    $('.header-cart-item-img').off('click').on('click', function() {
        const idCompuesto = $(this).data('id');
        const originalId = $(this).data('original-id');
        const talla = $(this).data('talla');
        
        console.log('🗑️ Eliminando producto:', {
            idCompuesto: idCompuesto,
            originalId: originalId,
            talla: talla
        });
        
        if (!idCompuesto) {
            console.error('❌ ID compuesto no válido');
            return;
        }
        
        eliminarProducto(idCompuesto, originalId, talla);
    });
}

// Función mejorada para eliminar un producto específico
function eliminarProducto(idCompuesto, originalId, talla) {
    $('.js-panel-cart').removeClass('show-header-cart');
    
    Swal.fire({
        title: "¿Estás seguro de eliminar este producto?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        cancelButtonText: "Cancelar",
        confirmButtonText: "Eliminar"
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: 'Backend/Carrito/Eliminar.php',
                type: 'POST',
                dataType: 'json',
                data: { 
                    id: idCompuesto,
                    idCompuesto: idCompuesto,
                    productoId: originalId,
                    talla: talla
                },
                success: function(res) {
                    console.log('✅ Respuesta del servidor:', res);
                    if (res.success) {
                        // Sincronizar después de eliminar
                        sincronizarCarritoCompleto();
                        
                        const Toast = Swal.mixin({
                            toast: true,
                            position: "top-end",
                            showConfirmButton: false,
                            timer: 2000,
                            timerProgressBar: true,
                            didOpen: (toast) => {
                                toast.style.marginTop = '60px';
                                toast.onmouseenter = Swal.stopTimer;
                                toast.onmouseleave = Swal.resumeTimer;
                            }
                        });
                        Toast.fire({
                            icon: "success",
                            title: "Producto eliminado con éxito"
                        });
                    } else {
                        Swal.fire('Error', res.error || 'No se pudo eliminar el producto.', 'error');
                    }
                },
                error: function(err) {
                    console.error('❌ Error AJAX:', err);
                    Swal.fire('Error', 'Error de comunicación con el servidor.', 'error');
                }
            });
        } else {
            $('.js-panel-cart').addClass('show-header-cart');
        }
    });
}

// Función para vaciar el carrito
function vaciarCarrito() {
    $.ajax({
        url: 'Backend/Carrito/Vaciar.php',
        type: 'GET',
        dataType: 'json',
        success: function(res) {
            if (res.success) {
                localStorage.removeItem('carrito');
                actualizarCarrito();
            }
        }
    });
}

// Funciones para mostrar alertas y toasts
function mostrarAlertaSobreModal(title, text, icon) {
    Swal.fire({
        title: title,
        text: text,
        icon: icon,
        customClass: {
            container: 'swal-container-higher-z-index'
        }
    });
}

function mostrarToastSobreModal(icon, title) {
    const Toast = Swal.mixin({
        toast: true,
        position: "top-end",
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true,
        customClass: {
            container: 'swal-container-higher-z-index'
        },
        didOpen: (toast) => {
            toast.style.marginTop = '60px';
            toast.onmouseenter = Swal.stopTimer;
            toast.onmouseleave = Swal.resumeTimer;
        }
    });
    
    Toast.fire({
        icon: icon,
        title: title
    });
}

function mostrarToast(icon, title) {
    const Toast = Swal.mixin({
        toast: true,
        position: "top-end",
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true,
        didOpen: (toast) => {
            toast.style.marginTop = '60px';
            toast.onmouseenter = Swal.stopTimer;
            toast.onmouseleave = Swal.resumeTimer;
        }
    });
    
    Toast.fire({
        icon: icon,
        title: title
    });
}

// 🔥 FUNCIONES DE UTILIDAD PARA DEBUG Y MANTENIMIENTO

// Función para verificar el estado del carrito
function verificarEstadoCarrito() {
    let carritoLocal = JSON.parse(localStorage.getItem('carrito')) || [];
    console.log('🏠 localStorage:', carritoLocal);
    
    $.ajax({
        url: 'Backend/Carrito/Ver.php',
        type: 'GET',
        dataType: 'json',
        success: function(carritoServidor) {
            console.log('🖥️ Servidor:', carritoServidor);
            
            if (JSON.stringify(carritoLocal) !== JSON.stringify(carritoServidor)) {
                console.warn('⚠️ INCONSISTENCIA DETECTADA');
                console.log('Diferencias encontradas - ejecuta limpiarYSincronizarCarrito()');
            } else {
                console.log('✅ Carrito sincronizado correctamente');
            }
        }
    });
    
    return "Verificación completada - revisa la consola";
}

// Función de emergencia para limpiar todo
function resetearCarritoCompleto() {
    console.log('🚨 RESETEO COMPLETO DEL CARRITO');
    
    // Limpiar localStorage
    localStorage.removeItem('carrito');
    
    // Vaciar carrito en el servidor
    $.ajax({
        url: 'Backend/Carrito/Vaciar.php',
        type: 'GET',
        dataType: 'json',
        success: function(res) {
            console.log('🧹 Carrito vaciado en el servidor');
            sincronizarCarritoCompleto();
            mostrarToast("info", "Carrito resetado completamente");
        },
        error: function(err) {
            console.error('❌ Error al vaciar carrito en servidor:', err);
        }
    });
    
    return "Reseteo completo iniciado";
}

// Añadir estilo CSS
$(document).ready(function() {
    if (!$('#swal-higher-z-index-style').length) {
        $('head').append(`
            <style id="swal-higher-z-index-style">
                .swal-container-higher-z-index {
                    z-index: 9999 !important;
                }
            </style>
        `);
    }
});