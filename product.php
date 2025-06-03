<?php

require('Backend/BD.php');
$Select1 = "SELECT p.PRO_ID, p.CAT_ID, p.PRO_NOMBRE, p.PROV_ID, p.PRO_DESCRIP, p.PRO_PRECIO, g.GENERO_DESCRIP, p.PRO_GENERO, (SELECT pf.FOTO FROM producto_fotos AS pf WHERE pf.PRO_ID = p.PRO_ID ORDER BY pf.FOTO_ID LIMIT 1) AS FOTO_PRINCIPAL, c.CAT_DESCRIP FROM producto AS p LEFT JOIN genero AS g ON p.PRO_GENERO = g.GENERO_ID LEFT JOIN categorias AS c ON p.CAT_ID = c.CAT_ID;";

$SelectCategoria = "SELECT * FROM `categorias`";

$Selectgenero = "SELECT * FROM `genero`";

$QueryPrin = mysqli_query($Conexion,$Select1);

$QueryCat = mysqli_query($Conexion,$SelectCategoria);

$QueryGe = mysqli_query($Conexion,$Selectgenero);

?>

<!DOCTYPE html>
<html lang="en">
<head>
	<title>Producto</title>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
<!--===============================================================================================-->	
	<link rel="icon" type="image/png" href="images/icons/favicon.png"/>
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
	<link rel="stylesheet" type="text/css" href="vendor/daterangepicker/daterangepicker.css">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="vendor/slick/slick.css">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="vendor/MagnificPopup/magnific-popup.css">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="vendor/perfect-scrollbar/perfect-scrollbar.css">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="css/util.css">
	<link rel="stylesheet" type="text/css" href="css/main.css">
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<!--===============================================================================================-->
</head>
<body class="animsition">
	
	<!-- Header -->
	<header class="header-v4">
		<!-- Header desktop -->
		<?php require('head.php')?>
		<!-- </div> -->

		<!-- Header Mobile -->

		<!-- Menu Mobile -->

		<!-- Modal Search -->

	</header>

	<!-- Cart -->
	<?php require('carrito.php');?>

<style type="text/css">
	/* Estilos adicionales para el panel de filtros */
.filter-item {
  transition: all 0.3s;
}

.filter-item:hover {
  background-color: #e8e8e8 !important;
}

.filter-checkbox input[type="checkbox"] {
  cursor: pointer;
}

.filter-checkbox label {
  cursor: pointer;
  margin-bottom: 0;
}

#filter-panel {
  transition: all 0.3s ease;
  box-shadow: 0 2px 5px rgba(0,0,0,0.1);
}

#show-filter-panel.active {
  background-color: #e6e6e6;
}

#apply-filter-changes:hover {
  background-color: #333;
}

#clear-all-filters {
  background-color: #e65540;
}

#clear-all-filters:hover {
  background-color: #d32f2f;
}

/* Animaciones */
@keyframes fadeIn {
  from { opacity: 0; }
  to { opacity: 1; }
}

.filter-item {
  animation: fadeIn 0.3s ease;
}
</style>
	
	<!-- Product -->
	<div class="bg0 m-t-23 p-b-140">
		<div class="container">
			<div class="flex-w flex-sb-m p-b-52">
				<div class="flex-w flex-l-m filter-tope-group m-tb-10">
					<!-- Todos -->
					<button class="stext-106 cl6 hov1 bor3 trans-04 m-r-32 m-tb-5 how-active1"
					data-filter="*" data-group="all">Todos los Productos</button>

					<!-- Genero -->
					<?php while($Gen = mysqli_fetch_assoc($QueryGe)):?>
					<button class="stext-106 cl6 hov1 bor3 trans-04 m-r-32 m-tb-5"
					data-filter=".<?php echo $Gen['GENERO_DESCRIP']?>" data-group="genero"><?php echo $Gen['GENERO_DESCRIP']?></button>
				   <?php endwhile;?>
					

					<!-- Categoria -->
					<?php while($Cat = mysqli_fetch_assoc($QueryCat)):?>
					<button class="stext-106 cl6 hov1 bor3 trans-04 m-r-32 m-tb-5"
					data-filter=".<?php echo $Cat['CAT_DESCRIP']?>" data-group="categoria"><?php echo $Cat['CAT_DESCRIP']?></button>
					<?php endwhile;?>
					

					<!-- Botón para abrir panel de filtros -->
					
				</div>

				<!-- Panel de administración de filtros (inicialmente oculto) -->
				<div id="filter-panel" class="p-tb-10 p-lr-15 m-b-20" style="display:none; border: 1px solid #e6e6e6; border-radius: 5px; background-color: #f9f9f9;">
					<div class="row">
						<div class="col-12">
							<h6 class="m-b-10">Filtros Activos</h6>
							<div id="active-filters-container" class="p-b-10">
								<p id="no-filters-message">No hay filtros activos</p>
								<!-- Los filtros activos se insertarán aquí dinámicamente -->
							</div>
							<div class="flex-w flex-sb-m">
								<button id="apply-filter-changes" class="flex-c-m stext-101 cl0 size-101 bg1 bor1 hov-btn1 p-lr-15 trans-04 m-r-10">
									Aplicar Cambios
								</button>
								<button id="clear-all-filters" class="flex-c-m stext-101 cl0 size-101 bg3 bor1 hov-btn3 p-lr-15 trans-04">
									Limpiar Todos
								</button>
							</div>
						</div>
					</div>
				</div>




				<div class="flex-w flex-c-m m-tb-10">
					<div class="flex-c-m stext-106 cl6 size-104 bor4 pointer hov-btn3 trans-04 m-r-8 m-tb-4 js-show-filter">
						<i class="icon-filter cl2 m-r-6 fs-15 trans-04 zmdi zmdi-filter-list"></i>
						<i class="icon-close-filter cl2 m-r-6 fs-15 trans-04 zmdi zmdi-close dis-none"></i>
						 Filtros
					</div>

					<div class="flex-c-m stext-106 cl6 size-105 bor4 pointer hov-btn3 trans-04 m-tb-4 js-show-search">
						<i class="icon-search cl2 m-r-6 fs-15 trans-04 zmdi zmdi-search"></i>
						<i class="icon-close-search cl2 m-r-6 fs-15 trans-04 zmdi zmdi-close dis-none"></i>
						Buscar
					</div>
				</div>
				
				<!-- Search product -->
				<div class="dis-none panel-search w-full p-t-10 p-b-15">
					<div class="bor8 dis-flex p-l-15">
						<button class="size-113 flex-c-m fs-16 cl2 hov-cl1 trans-04">
							<i class="zmdi zmdi-search"></i>
						</button>

						<input class="mtext-107 cl2 size-114 plh2 p-r-15" type="text" name="search-product" placeholder="Search">
					</div>	
				</div>

				<!-- Filter -->
				<div class="dis-none panel-filter w-full p-t-10">
					<div class="wrap-filter flex-w bg6 w-full p-lr-40 p-t-27 p-lr-15-sm">
						<div class="filter-col1 p-r-15 p-b-27">
							<div class="mtext-102 cl2 p-b-15">
								Clasificar por
							</div>
							<ul>
								<li class="p-b-6">
									<a href="#" class="filter-link stext-106 trans-04 filter-link-active" data-sortby="original" data-direction="asc">
										Defecto
									</a>
								</li>
								<li class="p-b-6">
									<a href="#" class="filter-link stext-106 trans-04" data-sortby="price" data-direction="asc">
										Precio: Bajo-Alto
									</a>
								</li>
								<li class="p-b-6">
									<a href="#" class="filter-link stext-106 trans-04" data-sortby="price" data-direction="desc">
										Precio: Alto-Bajo
									</a>
								</li>
							</ul>
						</div>

						<div class="filter-col2 p-r-15 p-b-27">
							<div class="mtext-102 cl2 p-b-15">
								Precios
							</div>
							<ul>
								<li class="p-b-6">
									<a href="#" class="filter-link stext-106 trans-04 filter-link-active" data-price="">
										All
									</a>
								</li>
								<li class="p-b-6">
									<a href="#" class="filter-link stext-106 trans-04" data-price="precio-0-350000">
										$0.00 - $350.000
									</a>
								</li>
								<li class="p-b-6">
									<a href="#" class="filter-link stext-106 trans-04" data-price="precio-350000-700000">
										$350.000 - $700.000
									</a>
								</li>
								<li class="p-b-6">
									<a href="#" class="filter-link stext-106 trans-04" data-price="precio-700000-1000000">
										$700.000 - $1.000.000
									</a>
								</li>
								<li class="p-b-6">
									<a href="#" class="filter-link stext-106 trans-04" data-price="precio-1000000-mas">
										$1.000.000+
									</a>
								</li>
							</ul>
						</div>

						<!-- <div class="filter-col3 p-r-15 p-b-27"> 
							<div class="mtext-102 cl2 p-b-15">
								Color
							</div>

							<ul>
								<li class="p-b-6">
									<span class="fs-15 lh-12 m-r-6" style="color: #222;">
										<i class="zmdi zmdi-circle"></i>
									</span>

									<a href="#" class="filter-link stext-106 trans-04">
										Black
									</a>
								</li>

								<li class="p-b-6">
									<span class="fs-15 lh-12 m-r-6" style="color: #4272d7;">
										<i class="zmdi zmdi-circle"></i>
									</span>

									<a href="#" class="filter-link stext-106 trans-04 filter-link-active">
										Blue
									</a>
								</li>

								<li class="p-b-6">
									<span class="fs-15 lh-12 m-r-6" style="color: #b3b3b3;">
										<i class="zmdi zmdi-circle"></i>
									</span>

									<a href="#" class="filter-link stext-106 trans-04">
										Grey
									</a>
								</li>

								<li class="p-b-6">
									<span class="fs-15 lh-12 m-r-6" style="color: #00ad5f;">
										<i class="zmdi zmdi-circle"></i>
									</span>

									<a href="#" class="filter-link stext-106 trans-04">
										Green
									</a>
								</li>

								<li class="p-b-6">
									<span class="fs-15 lh-12 m-r-6" style="color: #fa4251;">
										<i class="zmdi zmdi-circle"></i>
									</span>

									<a href="#" class="filter-link stext-106 trans-04">
										Red
									</a>
								</li>

								<li class="p-b-6">
									<span class="fs-15 lh-12 m-r-6" style="color: #aaa;">
										<i class="zmdi zmdi-circle-o"></i>
									</span>

									<a href="#" class="filter-link stext-106 trans-04">
										White
									</a>
								</li>
							</ul></div>-->

						<style>
							.tag-remove {
								margin-left: 5px;
								font-weight: bold;
							}

							.filter-tag:hover {
								background-color: #e9e9e9;
							}

							.filter-tags-container {
								margin-bottom: 20px;
							}
						</style>

						<div class="filter-col4 p-b-27 filter-tags-container" style="display: none;">
							<div class="mtext-102 cl2 p-b-15">
								Filtros Activos
							</div>
							<div class="flex-w p-t-4 m-r--5" id="active-filter-tags">
								<!-- Aquí se generarán dinámicamente los tags de filtros -->
							</div>
							<div class="flex-w p-t-10">
								<a href="javascript:void(0)" id="clear-all-filter-tags" class="flex-c-m stext-107 cl0 size-301 bg1 bor1 hov-btn2 p-lr-15 trans-04">
									Borrar todos los filtros
								</a>
							</div>
						</div>
					</div>
				</div>
			</div>

			<div class="row isotope-grid">				
				<?php while($PRODUCTO = mysqli_fetch_assoc($QueryPrin)): ?>
					<div class="col-sm-6 col-md-4 col-lg-3 p-b-35 isotope-item <?php echo $PRODUCTO['GENERO_DESCRIP']; ?> <?php echo $PRODUCTO['CAT_DESCRIP']; ?>" data-precio="<?php echo $PRODUCTO['PRO_PRECIO']; ?>">

						<!-- Block2 -->
						<div class="block2">
							<div class="block2-pic hov-img0">
								<img src="images/<?php echo $PRODUCTO['FOTO_PRINCIPAL']; ?>" alt="IMG-PRODUCT">
								<a href="#" class="block2-btn flex-c-m stext-103 cl2 size-102 bg0 bor2 hov-btn1 p-lr-15 trans-04 js-show-modal1" onclick="AK(<?php echo $PRODUCTO['PRO_ID']; ?>);">
									Ver producto
								</a>
							</div>
							<div class="block2-txt flex-w flex-t p-t-14">
								<div class="block2-txt-child1 flex-col-l ">
									<a href="#" class="stext-104 cl4 hov-cl1 trans-04 js-name-b2 p-b-6 js-show-modal1 "  onclick="AK(<?php echo $PRODUCTO['PRO_ID']; ?>);">
										<?php echo $PRODUCTO['PRO_NOMBRE']; ?>
									</a>
									<span class="stext-105 cl3">
										<?php echo '$' . number_format($PRODUCTO['PRO_PRECIO'], 0, ',', '.'); ?>
									</span>
								</div>
								<div class="block2-txt-child2 flex-r p-t-3">
									<a href="#" class="btn-addwish-b2 dis-block pos-relative js-addwish-b2">
										<img class="icon-heart1 dis-block trans-04" src="images/icons/icon-heart-01.png" alt="ICON">
										<img class="icon-heart2 dis-block trans-04 ab-t-l" src="images/icons/icon-heart-02.png" alt="ICON">
									</a>
								</div>
							</div>
						</div>
					</div>
				<?php endwhile; ?>
			</div>

			<!-- Load more -->
			
		</div>
	</div>
		

	<!-- Footer -->
	<footer class="bg3 p-t-75 p-b-32">
		<div class="container">
			<div class="row">
				<div class="col-sm-6 col-lg-3 p-b-50">
					<h4 class="stext-301 cl0 p-b-30">
						Categorias
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
Copyright &copy;<script>document.write(new Date().getFullYear());</script> All rights reserved |Made with <i class="fa fa-heart-o" aria-hidden="true"></i> by <a href="https://colorlib.com" target="_blank">Colorlib</a> &amp; distributed by <a href="https://themewagon.com" target="_blank">ThemeWagon</a>
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

<style type="text/css">
	.wrap-slick3-dots {
    margin-top: 20px;
    text-align: center;
}

.wrap-slick3-dots ul {
    display: flex;
    justify-content: center;
    flex-wrap: wrap;
    gap: 10px;
    padding: 0;
    margin: 0;
}

.wrap-slick3-dots ul li {
    list-style: none;
    border: 1px solid #ccc;
    border-radius: 8px;
    overflow: hidden;
    width: 60px;
    height: 60px;
    cursor: pointer;
    transition: all 0.3s ease;
}

.wrap-slick3-dots ul li img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
}

.wrap-slick3-dots ul li.slick-active {
    border: 2px solid #333; /* borde mas oscuro cuando esta activo */
}

.wrap-slick3-dots ul li:hover {
    border-color: #555; /* borde un poco mas fuerte en hover */
}

.slick-prev, .slick-next {
    position: absolute;
    top: 50%;
    z-index: 2;
    transform: translateY(-50%);
    background-color: rgba(0, 0, 0, 0.5); /* Fondo semitransparente */
    border: none;
    padding: 10px;
    border-radius: 10%;
    cursor: pointer;
    color: white;
}

.slick-prev {
    left: 20px; /* Ajusta la distancia desde la izquierda */
}

.slick-next {
    right: 20px; /* Ajusta la distancia desde la derecha */
}

.slick-prev i, .slick-next i {
    font-size: 20px;
}

.slick3 .slick-prev, .slick3 .slick-next {
    top: 40%;
}

.slick3 .slick-prev:hover, .slick3 .slick-next:hover {
    background-color: rgba(0, 0, 0, 0.7); /* Cambio de color al pasar el ratón */
}

</style>
	<!-- Modal1 -->
	<?php require('ModalProductos.php');?>

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
	<script src="vendor/daterangepicker/moment.min.js"></script>
	<script src="vendor/daterangepicker/daterangepicker.js"></script>
<!--===============================================================================================-->
	<script src="vendor/slick/slick.min.js"></script>
	<script src="js/slick-custom.js"></script>
<!--===============================================================================================-->
	<script src="vendor/parallax100/parallax100.js"></script>
	<script>
        $('.parallax100').parallax100();
	</script>
<!--===============================================================================================-->
	<script src="vendor/MagnificPopup/jquery.magnific-popup.min.js"></script>
	<script>
		$('.gallery-lb').each(function() { // the containers for all your galleries
			$(this).magnificPopup({
		        delegate: 'a', // the selector for gallery item
		        type: 'image',
		        gallery: {
		        	enabled:true
		        },
		        mainClass: 'mfp-fade'
		    });
		});
	</script>
<!--===============================================================================================-->
	<script src="vendor/isotope/isotope.pkgd.min.js"></script>
<!--===============================================================================================-->
	<script src="vendor/sweetalert/sweetalert.min.js"></script>
	<script>
		$('.js-addwish-b2, .js-addwish-detail').on('click', function(e){
			e.preventDefault();
		});

		$('.js-addwish-b2').each(function(){
			var nameProduct = $(this).parent().parent().find('.js-name-b2').html();
			$(this).on('click', function(){
				swal(nameProduct, "is added to wishlist !", "success");

				$(this).addClass('js-addedwish-b2');
				$(this).off('click');
			});
		});

		$('.js-addwish-detail').each(function(){
			var nameProduct = $(this).parent().parent().parent().find('.js-name-detail').html();

			$(this).on('click', function(){
				swal(nameProduct, "is added to wishlist !", "success");

				$(this).addClass('js-addedwish-detail');
				$(this).off('click');
			});
		});

		/*---------------------------------------------*/
	
	</script>
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
<!--===============================================================================================-->


	<script src="js/main.js"></script>
	<script src="js/ModalProducto.js"></script>
	<script src="js/ajax/Producto/carrito.js"></script>

</body>
</html>