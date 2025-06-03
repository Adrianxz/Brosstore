
<?php

require('../../Backend/BD.php');
$SelectCup = "SELECT * FROM `rol_admin`";
$QueryCup = mysqli_query($Conexion,$SelectCup);

?>

<div class="modal fade" id="miniGaleriaModal" tabindex="-1" role="dialog"
     aria-labelledby="miniGaleriaLabel" aria-hidden="true">
  <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="miniGaleriaLabel">Mini galería</h5>
        <button type="button" class="close" data-dismiss="modal"
                aria-label="Cerrar"><span aria-hidden="true">&times;</span></button>
      </div>
      <div class="modal-body">
        <div id="miniGaleriaCarousel" class="carousel slide" data-ride="carousel">
          <div class="carousel-inner">
            
          </div>
          <a class="carousel-control-prev" href="#miniGaleriaCarousel" role="button"
             data-slide="prev"><span
             class="carousel-control-prev-icon" aria-hidden="true"></span>
             <span class="sr-only">Anterior</span>
          </a>
          <a class="carousel-control-next" href="#miniGaleriaCarousel" role="button"
             data-slide="next"><span
             class="carousel-control-next-icon" aria-hidden="true"></span>
             <span class="sr-only">Siguiente</span>
          </a>
        </div>
      </div>
    </div>
  </div>
</div>


<div class="main-panel">          
        <div class="content-wrapper">
          <div class="row" style="margin-bottom: 10px !important;">
            <div class="col-12 grid-margin stretch-card">
              <div class="card">
                <div class="row">
                        <div class="col-md-4 grid-margin stretch-card">
                                <div class="card">
                                  <div class="card-body">
                                    <h4 class="card-title">Productos</h4>
                                      <p class="card-description">
                                        Formulario para gestionar los productos.
                                      </p>
                                        <form class="forms-sample" method="post" id="Form" enctype="multipart/form-data">

                                          <div class="form-group">
                                            <label for="exampleInputEmail1">Rol</label>
                                            <input type="text" class="form-control" id="Nombre" name="Rol" placeholder="Rol" required>
                                          </div>
                                            <button type="button" id="AgregarRol" class="btn btn-primary mr-2">AGREGAR ROL</button>
                                        </form>
                                  </div>
                                </div>
                              </div>

                              <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                <div class="modal-dialog">
                                  <div class="modal-content">
                                    <div class="modal-header">
                                      <h1 class="modal-title fs-5" style="font-size: 35px; font-family: sans-serif" id="exampleModalLabel">Actualizar</h1>
                                    </div>
                                    <div class="modal-body">
                                     <form id="update-form">
                                      <div class="mb-3">
                                        <label for="recipient-name" class="col-form-label">Descripcion:</label>
                                        <input type="text" class="form-control" id="UnidadesModal" name="UnidadesModal">

                                        <label for="recipient-name" class="col-form-label">Alias:</label>
                                        <input type="text" class="form-control" id="AliasModal" name="AliasModal">

                                      
                                        <!-- Campo oculto para almacenar el ID -->
                                        <input type="hidden" id="rol-id" name="rol-id">
                                      </div>
                                    </form>

                                    </div>
                                    <div class="modal-footer">
                                      <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                                      <button type="button" class="btn btn-primary" id="Actualizar">Actualizar</button>
                                    </div>
                                  </div>
                                </div>
                              </div>


                            <div class="col-lg-8 grid-margin stretch-card">
                              <div class="card">
                                <div class="card-body">
                                  <h4 class="card-title">TABLA DE DATOS</h4>
                                    <p class="card-description">
                                      En esta tabla podrás ver los datos que están almacenados en el sistema.
                                    </p>
                                      <div class="table-responsive">
                                        <table class="table table-hover">
                                            <thead>
                                              <tr>
                                                <th>ID</th>
                                                <th>ROL</th>
                                              </tr>
                                            </thead>
                                          <tbody>
                                          <?php while($Roles= mysqli_fetch_assoc($QueryRol)):?>
                                          <tr>
                                            <td><?php echo $Roles['ROL_ID']?></td>
                                            <td><?php echo $Roles['ROLD_DESCRIP']?></td>
                                          </tr>
                                          <?php endwhile;?>
                                
                                          </tbody>
                                        </table>
                                      </div>
                                  </div>
                                </div>
                              </div>
                            </div>


                            </div>


                       
             </div>           
        </div>
    </div>
    
    
    <script src="../js/ajax/Rol/agregarRol.js"></script>
