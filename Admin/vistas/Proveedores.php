
<?php

require('../../Backend/BD.php');
$SelectPro = "SELECT * FROM `proveedor`";
$QueryPro = mysqli_query($Conexion,$SelectPro);

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
                                                <th>IDENT ID</th>
                                                <th>NOMBRE</th>
                                                <th>NUMERO IDENTIDAD</th>
                                                <th>TELEFONO</th>
                                                <th>CORREO</th>
                                                <th>DIRECCION</th>
                                                <th>ACCIÓN</th>
                                              </tr>
                                            </thead>
                                          <tbody>
                                          <?php while($Usuario= mysqli_fetch_assoc($QueryPro)):?>
                                          <tr>
                                            <td><?php echo $Usuario['PROV_ID']?></td>
                                            <td><?php echo $Usuario['IDENT_ID']?></td>
                                            <td><?php echo $Usuario['PROV_NOMBRE']?></td>
                                            <td><?php echo $Usuario['PROV_NUMIDENT']?></td>
                                            <td><?php echo $Usuario['PROV_TEL']?></td>
                                            <td><?php echo $Usuario['PROV_CORREO']?></td>
                                            <td><?php echo $Usuario['PROV_DIRECC']?></td>
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