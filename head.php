<?php
require('Backend/BD.php');
// Cargar sesión de usuario y carrito de forma unificada
session_start();

// Obtener los datos del usuario desde la sesión
$Id = $_SESSION['usuario']['Id'] ?? null;
$Nombre = $_SESSION['usuario']['Nombre'] ?? null;

// Obtener el carrito desde la sesión
$carrito = $_SESSION['carrito'] ?? [];
?>

<div class="container-menu-desktop">
    <!-- Topbar -->
    <div class="top-bar">
        <div class="content-topbar flex-sb-m h-full container">
            <div class="left-top-bar">
                
            </div>

            <div class="right-top-bar flex-w h-full">

                <?php if ($Id): ?>

                    <a href="#" class="flex-c-m trans-04 p-lr-25">
                        <?php echo $Nombre; ?>
                    </a>

                <?php else: ?>

                    <a href="login" class="flex-c-m trans-04 p-lr-25">
                        Mi cuenta
                    </a>

                <?php endif; ?>

            </div>
        </div>
    </div>

    <div class="wrap-menu-desktop">
        <nav class="limiter-menu-desktop container">
            
            <!-- Logo desktop -->        
            <a href="index" class="logo">
                <img src="images/Imagen1l.png" alt="IMG-LOGO">
            </a>

            <!-- Menu desktop -->
            <div class="menu-desktop">
                <ul class="main-menu">
                    <li class="active-menu">
                        <a href="index">Inicio</a>
                    </li>

                    <li>
                        <a href="product">Productos</a>
                    </li>

                    <!--<li>-->
                    <!--    <a href="blog.html">Blog</a>-->
                    <!--</li>-->

                    <li>
                        <a href="about">Información</a>
                    </li>

                    <li>
                        <a href="contact">Contacto</a>
                    </li>
                </ul>
            </div>    

            <!-- Icon header -->
            <div class="wrap-icon-header flex-w flex-r-m">
                <div class="icon-header-item cl2 hov-cl1 trans-04 p-l-22 p-r-11 js-show-modal-search">
                    <i class="zmdi zmdi-search"></i>
                </div>
                <div class="icon-header-item cl2 hov-cl1 trans-04 p-r-11 p-l-10 icon-header-noti js-show-cart" data-notify="0"
                onclick="actualizarCarrito(); $('.js-panel-cart').addClass('show-header-cart');">
                    <i class="zmdi zmdi-shopping-cart"></i>
                </div>
            </div>
        </nav>
    </div>    
</div>

<div class="wrap-header-mobile">
    <!-- Logo mobile -->        
    <div class="logo-mobile">
        <img src="images/Imagen1l.png" alt="IMG-LOGO">
    </div>

    <!-- Icon header -->
    <div class="wrap-icon-header flex-w flex-r-m m-r-15">
        <div class="icon-header-item cl2 hov-cl1 trans-04 p-r-11 js-show-modal-search">
            <i class="zmdi zmdi-search"></i>
        </div>

        <div class="icon-header-item cl2 hov-cl1 trans-04 p-r-11 p-l-10 icon-header-noti js-show-cart" data-notify="0"
        onclick="actualizarCarrito(); $('.js-panel-cart').addClass('show-header-cart');">
        <i class="zmdi zmdi-shopping-cart"></i>
    </div>
    </div>

    <!-- Button show menu -->
    <div class="btn-show-menu-mobile hamburger hamburger--squeeze">
        <span class="hamburger-box">
            <span class="hamburger-inner"></span>
        </span>
    </div>
</div>

<div class="menu-mobile">
    <ul class="main-menu-m">
        <li>
            <a href="index.html">Home</a>
        </li>

        <li>
            <a href="product">Productos</a>
        </li>

        <li>
            <a href="blog.html">Blog</a>
        </li>

        <li>
            <a href="about.html">Información</a>
        </li>

        <li>
            <a href="contact.html">Contacto</a>
        </li>
    </ul>
</div>

<div class="modal-search-header flex-c-m trans-04 js-hide-modal-search">
    <div class="container-search-header">
        <button class="flex-c-m btn-hide-modal-search trans-04 js-hide-modal-search">
            <img src="images/icons/icon-close2.png" alt="CLOSE">
        </button>

        <form class="wrap-search-header flex-w p-l-15">
            <button class="flex-c-m trans-04">
                <i class="zmdi zmdi-search"></i>
            </button>
            <input class="plh3" type="text" name="search" placeholder="Search...">
        </form>
    </div>
</div>
