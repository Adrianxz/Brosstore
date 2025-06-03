<?php

session_start();
// Asegúrate de incluir la conexión a la BD si es necesario
// require_once('../BD.php');

// Si tienes una variable $carrito pero tienes los datos en $_SESSION['carrito']
$carrito = $_SESSION['carrito'] ?? [];
?>

<!-- Incluir la tabla de productos en el carrito -->
<div class="container">
    <div class="row">
        <div class="col-lg-10 col-xl-7 m-lr-auto m-b-50">
            <div class="m-l-25 m-r--38 m-lr-0-xl">
                <div class="wrap-table-shopping-cart">
                    <table class="table-shopping-cart">
                        <tr class="table_head">
                            <th class="column-1">Producto</th>
                            <th class="column-2">Nombre</th>
                            <th class="column-3">Precio</th>
                            <th class="column-4">Cantidad</th>
                            <th class="column-5">Total</th>
                        </tr>
                        
                        <?php if(!empty($_SESSION['carrito']) && is_array($_SESSION['carrito'])): ?>
                            <?php $total_general = 0; ?>
                            <?php foreach($carrito as $producto): ?>
                                <?php
                                    $precio_total = $producto['cantidad'] * $producto['precio'];
                                    $total_general += $precio_total;
                                ?>
                                <tr class="table_row">
                                    <td class="column-1">
                                        <div class="how-itemcart1">
                                            <img src="images/<?php echo $producto['foto'];?>" alt="IMG">
                                        </div>
                                    </td>
                                    <td class="column-2"><?php echo $producto['nombre'];?> <br> Talla: <?php echo $producto['tallaTexto'];?> </td> 
                                    <td class="column-3">$ <?php echo number_format($producto['precio'], 2); ?></td>
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
                                                data-id="<?php echo $producto['id']?>"
                                                data-precio="<?php echo $producto['precio']?>"
                                            >
                                            <div class="btn-num-product-up cl8 hov-btn3 trans-04 flex-c-m">
                                                <i class="fs-16 zmdi zmdi-plus"></i>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="column-5">$ <span class="total-producto"> <?php echo number_format($precio_total, 2); ?></span></td>
                                </tr>
                            <?php endforeach; ?>
                            <tr>
                                <td colspan="4" style="text-align:right; font-weight:bold;">Total general:</td>
                                <td>$ <span id="total-general"> <?php echo number_format($total_general, 2); ?></span></td>
                            </tr>
                        <?php else: ?>
                            <tr><td colspan="5">El carrito esta vacio</td></tr>
                        <?php endif; ?>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Incluir el script para actualizar los precios -->
<script src="js/cart-update.js"></script>