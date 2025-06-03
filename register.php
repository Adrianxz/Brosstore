
<?php

require('Backend/BD.php');

$Select = "SELECT * FROM `identidad`";

$Query = mysqli_query($Conexion,$Select);
?>

<!Doctype html>
<html class="no-js" lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>Registro BROSSTORE</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="shortcut icon" type="image/png" href="images/Icon.png">
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/font-awesome.min.css">
    <link rel="stylesheet" href="css/themify-icons.css">
    <link rel="stylesheet" href="css/metisMenu.css">
    <link rel="stylesheet" href="css/owl.carousel.min.css">
    <link rel="stylesheet" href="css/slicknav.min.css">
    <!-- amchart css -->
    <link rel="stylesheet" href="https://www.amcharts.com/lib/3/plugins/export/export.css" type="text/css" media="all" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- others css -->
    <link rel="stylesheet" href="css/typography.css">
    <link rel="stylesheet" href="css/default-css.css">
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="css/responsive.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://accounts.google.com/gsi/client" async defer></script>
    
    <!-- modernizr css -->
    <script src="js/vendor/modernizr-2.8.3.min.js"></script>
</head>

<body>
    <!--[if lt IE 8]>
            <p class="browserupgrade">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.</p>
        <![endif]-->
    <!-- preloader area start -->
    <div id="preloader">
        <div class="loader"></div>
    </div>
    <!-- preloader area end -->
    <!-- login area start -->
    <div class="login-area login-s2">
        <div class="container">
            <div class="login-box ptb--100">
                <form method="POST" id="Register">

                    <div class="login-form-head">
                        <h4>Registrate</h4>
                        <p>Hola, unete y se parte de nosotros</p>
                    </div>
                    <div class="login-form-body">
                        <div class="form-gp">
                            <label for="exampleInputName1">Nombre</label>
                            <input type="text"  name="name">
                            <i class="bi bi-person" style="color: blue;"></i>
                        </div>

                          <div class="form-gp">
                            <label for="exampleInputName1">Apellidos</label>
                            <input type="text"  name="apellidos">
                            <i class="bi bi-person" style="color: blue;"></i>
                        </div>


                          <div class="form-gp">
                            <select name="tipoDocumento">
                                <option>Tipo de documento</option>
                                <?php while($Ident = mysqli_fetch_assoc($Query)):?>
                                    <option value="<?php echo $Ident['IDENT_ID']?>"><?php echo $Ident['IDENT_TIPO']?></option>
                                <?php endwhile;?>    
                                
                            </select>

                            <i class="bi bi-border-width" style="color:blue;"></i>
                        </div>


                        <div class="form-gp">
                            <label for="exampleInputEmail1">Numero de documento</label>
                            <input type="text"  name="NumDocumento">
                             <i class="bi bi-border-width" style="color:blue;"></i>
                        </div>

                        <div class="form-gp">
                            <label for="exampleInputEmail1">Email address</label>
                            <input type="email"  name="Correo">
                            <i class="bi bi-envelope-at-fill" style="color:blue;"></i>
                        </div>

                         <div class="form-gp">
                            <label for="exampleInputEmail1">Telefono</label>
                            <input type=text  name="Tel">
                            <i class="bi bi-telephone" style="color: blue;"></i>
                        </div>

                        <div class="form-gp">
                            <label for="exampleInputPassword1">Contraseña</label>
                            <input type="password"  name="contra">
                            <i class="bi bi-key" style="color:blue;"></i>
                        </div>
                        <div class="form-gp">
                            <label for="exampleInputPassword2">Confirmar contraseña</label>
                            <input type="password"  name="Ccontra">
                            <i class="bi bi-key" style="color:blue;"></i>
                        </div>

                        <div class="submit-btn-area">
                            <button type="submit" id="Registrar">Registrar</button>
                            <div class="login-other row mt-4 justify-content-center"> 
                                <div class="col-12 d-flex justify-content-center">
                                    <button class="google-login" id="Gmail">Registrar con <i class="bi bi-google"> </i></button>
                                </div>
                            </div>
                        </div>

                        <div class="form-footer text-center mt-5">
                            <p class="text-muted">Ya tienes una cuenta? <a href="login"> Login</a></p>
                        </div>
                    </div>

                </form>
            </div>
        </div>
    </div>
    <!-- login area end -->

    <!-- jquery latest version -->
    <script src="js/vendor/jquery-2.2.4.min.js"></script>
    <!-- bootstrap 4 js -->
    <script src="js/popper.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/owl.carousel.min.js"></script>
    <script src="js/metisMenu.min.js"></script>
    <script src="js/jquery.slimscroll.min.js"></script>
    <script src="js/jquery.slicknav.min.js"></script>
    
    <!-- others plugins -->
    <script src="js/plugins.js"></script>
    <script src="js/scripts.js"></script>
    <script type="text/javascript" src="js/ajax/Usuario/AgregarUsuario.js"></script>
    <script type="text/javascript" src="js/ajax/Usuario/AgregarUsuarioGmail.js"></script>
</body>

</html>