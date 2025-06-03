<?php 

session_start();
$Id = $_SESSION['usuario']['Id'] ?? null;
     if(empty($Id))
     {
                header('location:index');
     }
session_write_close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
	<title>Checkout</title>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
<!--===============================================================================================-->	
	<link rel="icon" type="image/png" href="images/Icon.png"/>
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="vendor/bootstrap/css/bootstrap.min.css">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="fonts/font-awesome-4.7.0/css/font-awesome.min.css">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="fonts/iconic/css/material-design-iconic-font.min.css">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="fonts/linearicons-v1.0.0/icon-font.min.css">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="vendor/animate/animate.css">
<!--===============================================================================================-->	
	<link rel="stylesheet" type="text/css" href="vendor/css-hamburgers/hamburgers.min.css">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="vendor/animsition/css/animsition.min.css">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="vendor/select2/select2.min.css">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="vendor/perfect-scrollbar/perfect-scrollbar.css">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="css/util.css">
	<link rel="stylesheet" type="text/css" href="css/main.css">
	<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<!--===============================================================================================-->
</head>
<body class="animsition">
	
	<!-- Header -->
	<?php require('head.php');?>
	<!-- Cart -->
	<?php require('carrito.php');?>


	<!-- breadcrumb -->

<?php
// Iniciar sesión si no está iniciada
if (session_status() == PHP_SESSION_NONE) {
    session_start();
    
}

// Calcular el total general
$total_general = 0;
if (!empty($_SESSION['carrito']) && is_array($_SESSION['carrito'])) {
    foreach ($_SESSION['carrito'] as $producto) {
        $total_general += $producto['cantidad'] * $producto['precio'];
    }
}

// Aplicar descuento si hay un cupón en la sesión
$descuento = 0;
$monto_descuento = 0;
$total_con_descuento = $total_general; // Por defecto, el total con descuento es igual al total general
$cupon_codigo = '';

if (isset($_SESSION['cupon']) && isset($_SESSION['cupon']['nuevo_total'])) {
    $descuento = floatval($_SESSION['cupon']['descuento']);
    $monto_descuento = $total_general * ($descuento / 100); // Recalcular el monto de descuento basado en el total actual
    $total_con_descuento = $total_general - $monto_descuento;
    $cupon_codigo = $_SESSION['cupon']['codigo'];
    
    // Actualizar los valores en la sesión
    $_SESSION['cupon']['monto_descuento'] = $monto_descuento;
    $_SESSION['cupon']['total_original'] = $total_general;
    $_SESSION['cupon']['nuevo_total'] = $total_con_descuento;
    
    // Verificación de seguridad: el total con descuento nunca debe ser negativo
    if ($total_con_descuento < 0) {
        $total_con_descuento = 0;
        $_SESSION['cupon']['nuevo_total'] = 0;
    }
}

$Ciudad = $_SESSION['usuario']['Ciudad'] ?? null;
$Pais = $_SESSION['usuario']['Pais'] ?? null;
$Direc = $_SESSION['usuario']['Direcc'] ?? null;
$Correo = $_SESSION['usuario']['Correo'] ?? null
?>
	<!-- Shoping Cart -->
	<form class="bg0 p-t-75 p-b-85">
    <div class="container">
        <div class="row">
            <div class="col-lg-10 col-xl-7 m-lr-auto m-b-50">
                <div class="m-l-25 m-r--38 m-lr-0-xl">
                    <div class="wrap-table-shopping-cart">
                        <table class="table-shopping-cart">
                            <tr class="table_head">
                                <th class="column-1">Producto</th>
                                <th class="column-2"></th>
                                <th class="column-3">Precio</th>
                                <th class="column-4">Cantidad</th>
                                <th class="column-5">Total</th>
                            </tr>
                            <?php if(!empty($carrito) && is_array($carrito)): ?>
                                <?php foreach($carrito as $producto): ?>
                                    <?php
                                        $precio_total = $producto['cantidad'] * $producto['precio'];
                                    ?>
                                    <tr class="table_row">
                                        <td class="column-1">
                                            <div class="how-itemcart1" title="Haga clic para eliminar este producto">
                                                <img src="images/<?php echo $producto['foto'];?>" alt="<?php echo $producto['nombre'];?>" style="cursor: pointer;">
                                            </div>
                                        </td>
                                        <td class="column-2"><?php echo $producto['nombre'];?> <br> Talla: <?php echo $producto['tallaTexto'];?></td> 
                                        <td class="column-3">$ <?php echo number_format($producto['precio'], 0, ',', '.'); ?></td>
                                        <td class="column-4">
                                            <div class="wrap-num-product flex-w m-l-auto m-r-0">
                                                <div class="btn-num-product-down cl8 hov-btn3 trans-04 flex-c-m">
                                                    <i class="fs-16 zmdi zmdi-minus"></i>
                                                </div>
                                                <input class="mtext-104 cl3 txt-center num-product" 
                                                    type="number" 
                                                    name="num-product1" 
                                                    value="<?php echo $producto['cantidad']?>" 
                                                    min="1"
                                                    max="10"
                                                    data-id="<?php echo $producto['id']?>"
                                                    data-precio="<?php echo $producto['precio']?>"
                                                    data-talla=<?php echo $producto['tallaTexto']?>
                                                >
                                                <div class="btn-num-product-up cl8 hov-btn3 trans-04 flex-c-m">
                                                    <i class="fs-16 zmdi zmdi-plus"></i>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="column-5">$ <span class="total-producto"><?php echo number_format($precio_total, 0, ',', '.'); ?></span></td>
                                    </tr>
                                <?php endforeach; ?>
                                <tr>
                                    <td colspan="4" style="text-align:right; font-weight:bold;">Total general:</td>
                                    <td>$ <span id="total-general"><?php echo number_format($total_general, 0, ',', '.'); ?></span></td>
                                </tr>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" style="text-align:center; padding: 30px;">
                                        <div class="empty-cart-message">
                                            <i class="zmdi zmdi-shopping-cart" style="font-size: 48px; color: #888; display: block; margin-bottom: 15px;"></i>
                                            <p>El carrito está vacío</p>
                                            <a href="productos.php" class="flex-c-m stext-101 cl0 size-107 bg3 bor2 hov-btn3 p-lr-15 trans-04 m-b-10" style="display: inline-block; margin-top: 15px;">
                                                Continuar comprando
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </table>
                    </div>

                    <div class="flex-w flex-sb-m bor15 p-t-18 p-b-15 p-lr-40 p-lr-15-sm">
                    	<div class="flex-w flex-m m-r-20 m-tb-5">
                    		<input class="stext-104 cl2 plh4 size-117 bor13 p-lr-20 m-r-10 m-tb-5" type="text" 
                    		name="coupon" placeholder="Código cupon" 
                    		value="<?php echo $cupon_codigo; ?>" 
                    		<?php echo ($cupon_codigo) ? 'disabled' : ''; ?>>

                    		<?php if (!$cupon_codigo): ?>
                    			<div class="flex-c-m stext-101 cl2 size-118 bg8 bor13 hov-btn3 p-lr-15 trans-04 pointer m-tb-5" id="btnAplicarCupon">
                    				Aplicar cupon
                    			</div>
                    			<?php else: ?>
                    				<div class="flex-c-m stext-101 cl0 size-118 bg3 bor13 hov-btn3 p-lr-15 trans-04 pointer m-tb-5" id="btnEliminarCupon">
                    					Eliminar cupón
                    				</div>
                    			<?php endif; ?>
                    		</div>
                    	</div>
                </div>
            </div>

            <div class="col-sm-10 col-lg-7 col-xl-5 m-lr-auto m-b-50">
                <div class="bor10 p-lr-40 p-t-30 p-b-40 m-l-63 m-r-40 m-lr-0-xl p-lr-15-sm">
                    <h4 class="mtext-109 cl2 p-b-30">
                        Total carrito
                    </h4>

                    <div class="flex-w flex-t bor12 p-b-13">
                        <div class="size-208">
                            <span class="stext-110 cl2">
                                Subtotal:
                            </span>
                        </div>

                        <div class="size-209">
                            <span class="mtext-110 cl2">
                                $ <?php echo number_format($total_general, 0, ',', '.'); ?>
                            </span>
                        </div>
                    </div>

                    <?php if ($descuento > 0): ?>
                        <!-- Fila de descuento (si hay cupón aplicado) -->
                        <div id="fila-descuento" class="flex-w flex-t bor12 p-t-15 p-b-15">
                            <div class="size-208">
                                <span class="stext-110 cl2">
                                    Descuento (<?php echo $descuento; ?>%):
                                </span>
                            </div>
                            <div class="size-209">
                                <span class="mtext-110 cl2" style="color: #e83e8c;">
                                    -$ <?php echo number_format($monto_descuento, 0, ',', '.'); ?>
                                </span>
                            </div>
                        </div>
                    <?php endif; ?>

                    <div class="flex-w flex-t bor12 p-t-15 p-b-30">
                        <div class="size-208 w-full-ssm">
                            <span class="stext-110 cl2">
                                Información:
                            </span>
                        </div>

                        <div class="size-209 p-r-18 p-r-0-sm w-full-ssm">
                            <p class="stext-111 cl6 p-t-2">
                               .
                            </p>
                            
                            <div class="p-t-15">
                               <!--  <span class="stext-112 cl8">
                                    Calculate Shipping
                                </span>
 -->                       
                                <div class="flex-w flex-m m-r-20 m-tb-5">
                                        <input type="text" class="stext-104 cl2 plh4 size-117 bor13 p-lr-20 m-r-10 m-tb-5" placeholder="Ingrese el pais" name="Pais" value="<?php echo $Pais?>">
                                </div>

                                 <div class="flex-w flex-m m-r-20 m-tb-5">
                                    <input  class="stext-104 cl2 plh4 size-117 bor13 p-lr-20 m-r-10 m-tb-5" type="text"  placeholder="Ingrese la ciudad" name="Ciudad" value="<?php echo $Ciudad?>">
                                </div>
                                <label>Dirección</label>
                                <div class="bor8 bg0 m-b-12 m-t-9">
                                    <textarea value=""><?php echo $Direc?></textarea>
                                </div>

                                <div>
                                    <input type="hidden" name="" id="Nombre" value="<?php echo $Nombre ?? ''; ?>">
                                    <input type="hidden" name="" id="CorreoP" value="<?php echo $Correo ?? ''; ?>">
                                </div>
                                
                               <!--  <div class="flex-w">
                                    <div class="flex-c-m stext-101 cl2 size-115 bg8 bor13 hov-btn3 p-lr-15 trans-04 pointer">
                                        Update Totals
                                    </div>
                                </div>
                                     -->
                            </div>
                        </div>
                    </div>

                    <div class="flex-w flex-t p-t-27 p-b-33">
                        <div class="size-208">
                            <span class="mtext-101 cl2">
                                Total:
                            </span>
                        </div>

                                       <div class="size-209 p-t-1">
                    <span class="mtext-110 cl2" id="Precio" data-amount="<?php echo $total_con_descuento; ?>">
                        $ <?php echo number_format($total_con_descuento, 0, ',', '.'); ?>
                    </span>
                </div>
                        <br>
                        <br>

                         <button class="flex-c-m stext-101 cl0 size-116 bg3 bor14 hov-btn3 p-lr-15 trans-04 pointer" type="button" id="Pagar">
                            Procesar pago
                        </button> 
                        <div class="tu-contenedor"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
		
	
		

	<!-- Footer -->
	<footer class="bg3 p-t-75 p-b-32">
		<div class="container">
			<div class="row">
				<div class="col-sm-6 col-lg-3 p-b-50">
					<h4 class="stext-301 cl0 p-b-30">
						Categories
					</h4>

					<ul>
						<li class="p-b-10">
							<a href="#" class="stext-107 cl7 hov-cl1 trans-04">
								Women
							</a>
						</li>

						<li class="p-b-10">
							<a href="#" class="stext-107 cl7 hov-cl1 trans-04">
								Men
							</a>
						</li>

						<li class="p-b-10">
							<a href="#" class="stext-107 cl7 hov-cl1 trans-04">
								Shoes
							</a>
						</li>

						<li class="p-b-10">
							<a href="#" class="stext-107 cl7 hov-cl1 trans-04">
								Watches
							</a>
						</li>
					</ul>
				</div>

				<div class="col-sm-6 col-lg-3 p-b-50">
					<h4 class="stext-301 cl0 p-b-30">
						Help
					</h4>

					<ul>
						<li class="p-b-10">
							<a href="#" class="stext-107 cl7 hov-cl1 trans-04">
								Track Order
							</a>
						</li>

						<li class="p-b-10">
							<a href="#" class="stext-107 cl7 hov-cl1 trans-04">
								Returns 
							</a>
						</li>

						<li class="p-b-10">
							<a href="#" class="stext-107 cl7 hov-cl1 trans-04">
								Shipping
							</a>
						</li>

						<li class="p-b-10">
							<a href="#" class="stext-107 cl7 hov-cl1 trans-04">
								FAQs
							</a>
						</li>
					</ul>
				</div>

				<div class="col-sm-6 col-lg-3 p-b-50">
					<h4 class="stext-301 cl0 p-b-30">
						GET IN TOUCH
					</h4>

					<p class="stext-107 cl7 size-201">
						Any questions? Let us know in store at 8th floor, 379 Hudson St, New York, NY 10018 or call us on (+1) 96 716 6879
					</p>

					<div class="p-t-27">
						<a href="#" class="fs-18 cl7 hov-cl1 trans-04 m-r-16">
							<i class="fa fa-facebook"></i>
						</a>

						<a href="#" class="fs-18 cl7 hov-cl1 trans-04 m-r-16">
							<i class="fa fa-instagram"></i>
						</a>

						<a href="#" class="fs-18 cl7 hov-cl1 trans-04 m-r-16">
							<i class="fa fa-pinterest-p"></i>
						</a>
					</div>
				</div>

				<div class="col-sm-6 col-lg-3 p-b-50">
					<h4 class="stext-301 cl0 p-b-30">
						Newsletter
					</h4>

					<form>
						<div class="wrap-input1 w-full p-b-4">
							<input class="input1 bg-none plh1 stext-107 cl7" type="text" name="email" placeholder="email@example.com">
							<div class="focus-input1 trans-04"></div>
						</div>

						<div class="p-t-18">
							<button class="flex-c-m stext-101 cl0 size-103 bg1 bor1 hov-btn2 p-lr-15 trans-04">
								Subscribe
							</button>
						</div>
					</form>
				</div>
			</div>

			<div class="p-t-40">
				<div class="flex-c-m flex-w p-b-18">
					<a href="#" class="m-all-1">
						<img src="images/icons/icon-pay-01.png" alt="ICON-PAY">
					</a>

					<a href="#" class="m-all-1">
						<img src="images/icons/icon-pay-02.png" alt="ICON-PAY">
					</a>

					<a href="#" class="m-all-1">
						<img src="images/icons/icon-pay-03.png" alt="ICON-PAY">
					</a>

					<a href="#" class="m-all-1">
						<img src="images/icons/icon-pay-04.png" alt="ICON-PAY">
					</a>

					<a href="#" class="m-all-1">
						<img src="images/icons/icon-pay-05.png" alt="ICON-PAY">
					</a>
				</div>

				<p class="stext-107 cl6 txt-center">
					<!-- Link back to Colorlib can't be removed. Template is licensed under CC BY 3.0. -->
Copyright &copy;<script>document.write(new Date().getFullYear());</script> All rights reserved | Made with <i class="fa fa-heart-o" aria-hidden="true"></i> by <a href="https://colorlib.com" target="_blank">Colorlib</a> &amp; distributed by <a href="https://themewagon.com" target="_blank">ThemeWagon</a>
<!-- Link back to Colorlib can't be removed. Template is licensed under CC BY 3.0. -->

				</p>
			</div>
		</div>
	</footer>


	<!-- Back to top -->
	<div class="btn-back-to-top" id="myBtn">
		<span class="symbol-btn-back-to-top">
			<i class="zmdi zmdi-chevron-up"></i>
		</span>
	</div>

<!--===============================================================================================-->	
	<script src="vendor/jquery/jquery-3.2.1.min.js"></script>
<!--===============================================================================================-->
	<script src="vendor/animsition/js/animsition.min.js"></script>
<!--===============================================================================================-->
	<script src="vendor/bootstrap/js/popper.js"></script>
	<script src="vendor/bootstrap/js/bootstrap.min.js"></script>
<!--===============================================================================================-->
	<script src="vendor/select2/select2.min.js"></script>
	<script>
		$(".js-select2").each(function(){
			$(this).select2({
				minimumResultsForSearch: 20,
				dropdownParent: $(this).next('.dropDownSelect2')
			});
		})
	</script>
<!--===============================================================================================-->
	<script src="vendor/MagnificPopup/jquery.magnific-popup.min.js"></script>
<!--===============================================================================================-->
	<script src="vendor/perfect-scrollbar/perfect-scrollbar.min.js"></script>
	<script>
		$('.js-pscroll').each(function(){
			$(this).css('position','relative');
			$(this).css('overflow','hidden');
			var ps = new PerfectScrollbar(this, {
				wheelSpeed: 1,
				scrollingThreshold: 1000,
				wheelPropagation: false,
			});

			$(window).on('resize', function(){
				ps.update();
			})
		});
	</script>


<script type="text/javascript">
	// Modificación para sincronizar el carrito al actualizar cantidades en checkout
document.addEventListener('DOMContentLoaded', function() {
    // Seleccionar elementos originales
    const cantidadInputs = document.querySelectorAll('.num-product');
    const btnsDown = document.querySelectorAll('.btn-num-product-down');
    const btnsUp = document.querySelectorAll('.btn-num-product-up');
    const productImages = document.querySelectorAll('.how-itemcart1 img');
    
    // Variable para evitar actualizaciones dobles
    let procesandoActualizacion = false;

    // Formatear precios en formato COP
    function formatearPrecioCOP(valor) {
        // Usar NumberFormat sin el estilo 'currency' para evitar el símbolo automático
        return '' + new Intl.NumberFormat('es-CO', {
            minimumFractionDigits: 0,
            maximumFractionDigits: 0
        }).format(valor);
    }
    
    // Formatear todos los precios iniciales
    document.querySelectorAll('.total-producto').forEach(span => {
        const valorNumerico = parseFloat(span.textContent.replace(/\./g, '').replace(/,/g, '.'));
        if (!isNaN(valorNumerico)) {
            span.textContent = formatearPrecioCOP(valorNumerico);
        }
    });
    
    // Añadir funcionalidad para eliminar producto al hacer clic en la imagen
    productImages.forEach(img => {
        // Añadir estilo de cursor para indicar que es clickeable
        img.style.cursor = 'pointer';
        
        // Añadir evento de clic
        img.addEventListener('click', function() {
            // Verificar si ya estamos procesando una eliminación
            if (procesandoActualizacion) return false;
            procesandoActualizacion = true;
            
            // Confirmar eliminación
            if (confirm('¿Está seguro que desea eliminar este producto del carrito?')) {
                // Obtener la fila del producto
                const row = this.closest('.table_row');
                
                // Obtener el ID del producto y la talla
                const productoId = row.querySelector('.num-product').dataset.id;
                const productoInfo = row.querySelector('.column-2').textContent;
                const talla = extraerTalla(productoInfo);
                
                // Llamar a la función para eliminar el producto
                eliminarProducto(productoId, talla, row);
            } else {
                // Restablecer bandera si cancela
                procesandoActualizacion = false;
            }
        });
    });
    
    // Eliminar eventos previos y añadir nuevos para los botones de disminuir cantidad
    btnsDown.forEach(btn => {
        // Limpiar eventos previos
        const nuevoBtn = btn.cloneNode(true);
        btn.parentNode.replaceChild(nuevoBtn, btn);
        
        // Añadir nuevo controlador de eventos
        nuevoBtn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            // Evitar procesamiento múltiple
            if (procesandoActualizacion) return false;
            procesandoActualizacion = true;
            
            // Encontrar el input asociado
            const input = this.nextElementSibling;
            let value = parseInt(input.value);
            
            if (isNaN(value) || value <= 1) {
                value = 1;
            } else {
                value--;
            }
            
            // Actualizar valor
            input.value = value;
            
            // Procesar actualización
            actualizarTodo(input, value);
            
            // Restablecer bandera después de un breve retraso
            setTimeout(() => {
                procesandoActualizacion = false;
            }, 100);
            
            return false;
        });
    });
    
    // Eliminar eventos previos y añadir nuevos para los botones de aumentar cantidad
    btnsUp.forEach(btn => {
        // Limpiar eventos previos
        const nuevoBtn = btn.cloneNode(true);
        btn.parentNode.replaceChild(nuevoBtn, btn);
        
        // Añadir nuevo controlador de eventos
        nuevoBtn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            // Evitar procesamiento múltiple
            if (procesandoActualizacion) return false;
            procesandoActualizacion = true;
            
            // Encontrar el input asociado
            const input = this.previousElementSibling;
            let value = parseInt(input.value);
            
            if (isNaN(value)) {
                value = 1;
            }
            
            const productoId = input.dataset.id;
            const productoInfo = input.closest('tr').querySelector('.column-2').textContent;
            const talla = extraerTalla(productoInfo);
            
            verificarStock(productoId, talla).then(stock => {
                if (value < stock) {
                    value++;
                } else {
                    value = stock;
                    alert('No hay suficiente stock disponible.');
                }
                
                // Actualizar valor
                input.value = value;
                
                // Procesar actualización
                actualizarTodo(input, value);
                
                // Restablecer bandera
                procesandoActualizacion = false;
            }).catch(() => {
                procesandoActualizacion = false;
            });
            
            return false;
        });
    });
    
    // Configurar eventos para inputs de cantidad (cambio manual)
    cantidadInputs.forEach(input => {
        // Preservar atributos importantes
        const productoId = input.dataset.id;
        const precio = input.dataset.precio;
        
        // Limpiar eventos previos
        const nuevoInput = input.cloneNode(true);
        input.parentNode.replaceChild(nuevoInput, input);
        
        // Verificar que los datos importantes se conserven
        if (!nuevoInput.dataset.id && productoId) {
            nuevoInput.dataset.id = productoId;
        }
        if (!nuevoInput.dataset.precio && precio) {
            nuevoInput.dataset.precio = precio;
        }
        
        // Añadir controlador de evento para cambio manual
        nuevoInput.addEventListener('change', function(e) {
            // Evitar procesamiento múltiple
            if (procesandoActualizacion) return false;
            procesandoActualizacion = true;
            
            let value = parseInt(this.value);
            
            if (isNaN(value) || value < 1) {
                value = 1;
                this.value = 1;
            }
            
            const productoId = this.dataset.id;
            const productoInfo = this.closest('tr').querySelector('.column-2').textContent;
            const talla = extraerTalla(productoInfo);
            
            verificarStock(productoId, talla).then(stock => {
                if (value > stock) {
                    value = stock;
                    this.value = stock;
                    alert('La cantidad se ha ajustado al stock disponible.');
                }
                
                // Procesar actualización
                actualizarTodo(this, value);
                
                // Restablecer bandera
                procesandoActualizacion = false;
            }).catch(() => {
                procesandoActualizacion = false;
            });
        });
    });
    
    // Función para extraer la talla del texto del producto
    function extraerTalla(productoInfo) {
        if (!productoInfo || !productoInfo.includes('Talla:')) {
            return '';
        }
        return productoInfo.split('Talla:')[1].trim().split(' ')[0]; // Extraer solo la talla
    }
    
    // Función unificada para actualizar todo
    function actualizarTodo(input, cantidad) {
        const productoId = input.dataset.id;
        if (!productoId) {
            console.error('Error: No se pudo obtener el ID del producto para actualizar', input);
            return;
        }
        
        // Obtener la talla del producto
        const productoInfo = input.closest('tr').querySelector('.column-2').textContent;
        const talla = extraerTalla(productoInfo);
        
        actualizarPrecioProducto(input, cantidad);
        actualizarCantidadEnSesion(productoId, cantidad, talla);
    }
    
    // Actualiza el total por producto
    function actualizarPrecioProducto(input, cantidad) {
        const precio = parseFloat(input.dataset.precio);
        if (isNaN(precio)) {
            console.error('Error: No se pudo obtener el precio del producto', input);
            return;
        }
        
        const total = precio * cantidad;
        const columnaTotal = input.closest('tr').querySelector('.total-producto');
        
        if (columnaTotal) {
            // Formatear en pesos colombianos (COP)
            columnaTotal.textContent = formatearPrecioCOP(total);
            actualizarTotalGeneral();
        }
    }
    
    // Actualiza el total general
    function actualizarTotalGeneral() {
        let totalGeneral = 0;
        document.querySelectorAll('.total-producto').forEach(span => {
            // Eliminar puntos y convertir a número
            const valorTexto = span.textContent.replace(/\./g, '');
            const valor = parseFloat(valorTexto);
            if (!isNaN(valor)) {
                totalGeneral += valor;
            }
        });
        
        const totalElement = document.getElementById('total-general');
        if (totalElement) {
            // Formatear el total general en COP
            totalElement.textContent = formatearPrecioCOP(totalGeneral);
        }
    }
    
    // Actualiza la cantidad en la sesion (backend) Y en el carrito del header
    function actualizarCantidadEnSesion(productoId, cantidad, talla) {
        console.log('Actualizando producto ID:', productoId, 'Talla:', talla, 'con cantidad:', cantidad);
        
        fetch('Backend/Checkout/Actualizar.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `accion=actualizar&id=${productoId}&cantidad=${cantidad}&talla=${encodeURIComponent(talla)}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                console.error('Error al actualizar el carrito:', data.error);
            } else {
                console.log('Actualización exitosa del producto ID:', productoId, 'Talla:', talla);
                
                // ¡IMPORTANTE! - Actualizar el carrito del header después de cada actualización
                actualizarCarritoHeader();
            }
        })
        .catch(error => {
            console.error('Error en la petición:', error);
        });
    }
    
    // Verifica stock disponible desde backend
    function verificarStock(productoId, talla) {
        if (!talla) {
            console.error('Error: Información de talla inválida');
            return Promise.resolve(99); // Valor por defecto alto
        }
        
        return fetch('Backend/Checkout/Stock.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `productoId=${productoId}&tallaTexto=${encodeURIComponent(talla)}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                console.error('Error al verificar stock:', data.error);
                return 0;
            }
            return data.stock;
        })
        .catch(error => {
            console.error('Error en la petición:', error);
            return 0;
        });
    }
    
    // Función para eliminar producto
    function eliminarProducto(productoId, talla, row) {
        console.log('Eliminando producto ID:', productoId, 'Talla:', talla);
        
        fetch('Backend/Checkout/Actualizar.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `accion=eliminar&id=${productoId}&talla=${encodeURIComponent(talla)}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                console.error('Error al eliminar producto:', data.error);
                alert('No se pudo eliminar el producto: ' + data.error);
                procesandoActualizacion = false;
            } else {
                // Eliminar la fila visualmente
                row.remove();
                
                // Actualizar el total
                actualizarTotalGeneral();
                
                // ¡IMPORTANTE! - Actualizar también el carrito del header
                actualizarCarritoHeader();
                
                procesandoActualizacion = false;
                
                // Verificar si ya no hay productos y recargar si es necesario
                const filas = document.querySelectorAll('.table_row');
                if (filas.length === 0) {
                    window.location.reload();
                }
            }
        })
        .catch(error => {
            console.error('Error en la petición:', error);
            alert('Error en la conexión. Intente nuevamente.');
            procesandoActualizacion = false;
        });
    }
    
    // NUEVA FUNCIÓN para sincronizar con el carrito del header
    function actualizarCarritoHeader() {
        console.log('Sincronizando con el carrito del header...');
        // Llamar a la función existente que actualiza el carrito del header
        if (typeof window.actualizarCarrito === 'function') {
            window.actualizarCarrito();
        }
    }
    
    // Exponer la función actualizarCarritoHeader globalmente
    window.actualizarCarritoHeader = actualizarCarritoHeader;
});

// Asegurarse de que actualizarCarrito sea accesible globalmente
// En caso de que esté definida en otro ámbito
if (typeof actualizarCarrito !== 'function') {
    window.actualizarCarrito = function() {
        console.log('Updating cart display...');
        $.ajax({
            url: 'Backend/Carrito/Ver.php',
            type: 'GET',
            dataType: 'json',
            success: function(carrito) {
                console.log('Cart data received from server:', carrito);
                if (!Array.isArray(carrito)) {
                    console.error('Cart is not an array:', carrito);
                    return;
                }
                let html = '';
                let total = 0;
                let cantidadTotal = 0; // Variable para contar todos los productos
                
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
                    
                    // Usar el idCompuesto para la eliminación si está disponible
                    let itemId = item.idCompuesto || item.id;
                    
                    // Usar el texto descriptivo de la talla si está disponible o usar el ID de talla
                    let tallaDisplay = item.tallaTexto || item.talla;
                    let tallaInfo = tallaDisplay ? `<span class="header-cart-item-info talla-info">Talla: ${tallaDisplay}</span>` : '';
                    
                    html += `
<li class="header-cart-item flex-w flex-t m-b-12">
    <div class="header-cart-item-img" data-id="${itemId}">
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
                    cantidadTotal += itemCantidad; // Sumando cantidades
                });
                
                $('.header-cart-wrapitem').html(html);
                $('.header-cart-total').html('Total: ' + total.toLocaleString('es-CO', { style: 'currency', currency: 'COP' }));
                $('.icon-header-noti').attr('data-notify', cantidadTotal);
                
                // Añadir el evento click a las imágenes después de insertar el HTML
                initializeDeleteEvents();
            },
            error: function(err) {
                console.error('Error loading cart:', err);
            }
        });
    };
}
</script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('Script directo de eliminación cargado');

    document.querySelectorAll('.how-itemcart1').forEach(container => {
        container.style.cursor = 'pointer';
        container.addEventListener('click', function(e) {
            console.log('Click en imagen detectado');

            Swal.fire({
                title: '¿Está seguro?',
                text: 'Este producto se eliminará del carrito',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    const row = this.closest('.table_row');
                    const input = row.querySelector('.num-product');
                    const productoId = input.dataset.id;

                    const productoInfo = row.querySelector('.column-2').textContent;
                    let talla = '';
                    if (productoInfo && productoInfo.includes('Talla:')) {
                        talla = productoInfo.split('Talla:')[1].trim().split(' ')[0];
                    }

                    console.log('Eliminando producto ID:', productoId, 'Talla:', talla);

                    fetch('Backend/Checkout/Actualizar.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: `accion=eliminar&id=${productoId}&talla=${encodeURIComponent(talla)}`
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.error) {
                            Swal.fire('Error', 'No se pudo eliminar el producto: ' + data.error, 'error');
                        } else {
                            row.remove();

                            let totalGeneral = 0;
                            document.querySelectorAll('.total-producto').forEach(span => {
                                const valorTexto = span.textContent.replace(/\./g, '');
                                const valor = parseFloat(valorTexto);
                                if (!isNaN(valor)) {
                                    totalGeneral += valor;
                                }
                            });

                            const totalElement = document.getElementById('total-general');
                            if (totalElement) {
                                totalElement.textContent = new Intl.NumberFormat('es-CO', {
                                    minimumFractionDigits: 0,
                                    maximumFractionDigits: 0
                                }).format(totalGeneral);
                            }

                            Swal.fire('Eliminado', 'El producto fue eliminado del carrito.', 'success');

                            const filas = document.querySelectorAll('.table_row');
                            if (filas.length === 0) {
                                window.location.reload();
                            }
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        Swal.fire('Error', 'Error en la conexión. Intente nuevamente.', 'error');
                    });
                }
            });
        });
    });
});

</script>

<script>
// Este script es para depuración y verificación del cupón en caso de problemas
document.addEventListener('DOMContentLoaded', function() {
    <?php if (isset($_SESSION['cupon'])): ?>
    console.log("Cupón aplicado:", {
        codigo: "<?php echo isset($_SESSION['cupon']['codigo']) ? $_SESSION['cupon']['codigo'] : ''; ?>",
        descuento: <?php echo isset($_SESSION['cupon']['descuento']) ? $_SESSION['cupon']['descuento'] : 0; ?>,
        monto_descuento: <?php echo isset($_SESSION['cupon']['monto_descuento']) ? $_SESSION['cupon']['monto_descuento'] : 0; ?>,
        total_original: <?php echo isset($_SESSION['cupon']['total_original']) ? $_SESSION['cupon']['total_original'] : 0; ?>,
        nuevo_total: <?php echo isset($_SESSION['cupon']['nuevo_total']) ? $_SESSION['cupon']['nuevo_total'] : 0; ?>
    });
    <?php endif; ?>
});
</script>

<script type="text/javascript">
    $('#btnPagar').on('click', function () {
   
    var precioTexto = $('#Precio').text().replace('$', '').replace(/\./g, '').trim();
    var total = parseFloat(precioTexto);
    
   
    if (total <= 0) {
        alert("No hay productos en el carrito para procesar el pago.");
        return;
    }
    
   
    var nombre = "<?php echo $Nombre ?>"; 
    var email = "<?php echo $_SESSION['usuario']['Email'] ?? ''; ?>";
    
    $.ajax({
        url: 'Backend/Checkout/Dlocal.php',
        method: 'POST',
        data: {
            total: total,
            nombre: nombre,
            email: email,
            moneda: "COP"
        },
        success: function (response) {
            try {
                response = JSON.parse(response);
                if (response.checkout_url) {
                    window.location.href = response.checkout_url;
                } else {
                    alert("No se pudo generar el link de pago.");
                    console.log(response);
                }
            } catch (e) {
                alert("Error en la respuesta del servidor.");
                console.log(e, response);
            }
        },
        error: function (xhr, status, error) {
            console.error("Error AJAX:", error);
            alert("Hubo un error al generar el pago.");
        }
    });
});
</script>
<!--===============================================================================================-->
	<script src="js/main.js"></script>
	<script src="js/ajax/Producto/carrito.js"></script>
	<script src="js/ajax/Checkout/Cupon.js"></script>
   <script src="js/ajax/Checkout/Wompi.js"></script>
    <!--<script src="js/ajax/Checkout/Dlocal.js"></script>-->

</body>
</html>