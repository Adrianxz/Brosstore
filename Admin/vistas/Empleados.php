<?php
require('../../Backend/BD.php');
$SelectUser = "SELECT * FROM `user_admin`";
$QueryUser = mysqli_query($Conexion,$SelectUser);
?>

<!-- Mini Galería Modal -->
<div class="modal fade" id="miniGaleriaModal" tabindex="-1" role="dialog" aria-labelledby="miniGaleriaLabel" aria-hidden="true">
  <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="miniGaleriaLabel">Mini galería</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div id="miniGaleriaCarousel" class="carousel slide" data-ride="carousel">
          <div class="carousel-inner"></div>
          <a class="carousel-control-prev" href="#miniGaleriaCarousel" role="button" data-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="sr-only">Anterior</span>
          </a>
          <a class="carousel-control-next" href="#miniGaleriaCarousel" role="button" data-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
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
            <!-- Modal de Actualizar -->
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
<div class="col-md-4">
  <div class="card shadow mb-4">
    <div class="card-header py-3">
      <h6 class="m-0 font-weight-bold text-primary">Usuarios</h6>
      <small>Formulario para gestionar los usuarios del sistema.</small>
    </div>
    <div class="card-body">
      <form id="usuario-form">
        <div class="form-group">
          <label for="rol">Rol</label>
          <input type="text" class="form-control" id="rol" required>
        </div>
        <div class="form-group">
          <label for="nombre">Nombre completo</label>
          <input type="text" class="form-control" id="nombre" required>
        </div>
        <div class="form-group">
          <label for="usuario">Usuario</label>
          <input type="text" class="form-control" id="usuario" required>
        </div>
        <div class="form-group">
          <label for="telefono">Teléfono</label>
          <input type="tel" class="form-control" id="telefono" required>
        </div>
        <div class="form-group">
          <label for="correo">Correo electrónico</label>
          <input type="email" class="form-control" id="correo" required>
        </div>
        <button type="submit" class="btn btn-primary btn-block">Guardar Usuario</button>
      </form>
    </div>
  </div>
</div>

            <!-- Tabla de Datos -->
<div class="col-md-8">
  <div class="card">
    <div class="card-body">
      <h4 class="card-title">Lista de Usuarios</h4>
      <p class="card-description">Aquí se muestran los usuarios registrados.</p>
      <div class="table-responsive" style="max-height: 600px; overflow-y: auto;">
        <table id="tablaUsuarios" class="table table-hover">
          <thead>
            <tr>
              <th>ID</th>
              <th>Rol</th>
              <th>Nombre</th>
              <th>Usuario</th>
              <th>Teléfono</th>
              <th>Correo</th>
            </tr>
          </thead>
          <tbody>
                                                      <?php while($Usuario= mysqli_fetch_assoc($QueryUser)):?>
                                          <tr>
                                            <td><?php echo $Usuario['USER_ID']?></td>
                                            <td><?php echo $Usuario['ROL_ID']?></td>
                                            <td><?php echo $Usuario['USER_NOMBRE']?></td>
                                            <td><?php echo $Usuario['USER_USUARIO']?></td>
                                            <td><?php echo $Usuario['USER_TEL']?></td>
                                            <td><?php echo $Usuario['USER_CORREO']?></td>
                                          </tr>
                                          <?php endwhile;?>                                          <?php while($Usuario= mysqli_fetch_assoc($QueryUser)):?>
                                          <tr>
                                            <td><?php echo $Usuario['USER_ID']?></td>
                                            <td><?php echo $Usuario['ROL_ID']?></td>
                                            <td><?php echo $Usuario['USER_NOMBRE']?></td>
                                            <td><?php echo $Usuario['USER_USUARIO']?></td>
                                            <td><?php echo $Usuario['USER_TEL']?></td>
                                            <td><?php echo $Usuario['USER_CORREO']?></td>
                                          </tr>
                                          <?php endwhile;?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

            <!-- Fin tabla -->
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Scripts para jQuery y DataTables -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>

<!-- Script personalizado -->
<script src="js/Tabla.js"></script>
