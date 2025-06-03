
<?php
require('../../Backend/BD.php');
$SelectRol = "SELECT * FROM `rol_admin`";
$QueryRol = mysqli_query($Conexion, $SelectRol);
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
<div class="col-md-4">
    <div class="card shadow mb-4">
      <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Roles</h6>
        <small>Formulario para gestionar los roles del sistema.</small>
      </div>
      <div class="card-body">
        <form id="rol-form">
          <div class="form-group">
            <label for="ROLD_DESCRIP">Nombre del rol</label>
            <input type="text" class="form-control" id="ROLD_DESCRIP" name="ROLD_DESCRIP" required>
          </div>
          <button type="button" id="guardarRol" class="btn btn-primary btn-block">Guardar Rol</button>
        </form>
      </div>
    </div>
  </div>


<div class="modal fade" id="modalEditarRol" tabindex="-1" aria-labelledby="modalEditarRolLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalEditarRolLabel">Actualizar Rol</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar"><span aria-hidden="true">&times;</span></button>
      </div>
      <div class="modal-body">
        <form id="formEditarRol">
          <div class="form-group">
            <label for="descripcionRol">Descripción</label>
            <input type="text" class="form-control" id="descripcionRol" name="descripcionRol" required>
          </div>
          <input type="hidden" id="rolIdEditar" name="rolIdEditar">
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
        <button type="button" class="btn btn-primary" id="btnActualizarRol">Actualizar</button>
      </div>
    </div>
  </div>
</div>

<!-- Tabla de roles -->
<div class="col-md-8">
  <div class="card">
    <div class="card-body">
      <h4 class="card-title">Lista de Roles</h4>
      <p class="card-description">Roles actualmente registrados en el sistema.</p>
      <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
        <table id="tablaRoles" class="table table-hover">
          <thead>
            <tr>
              <th>ID</th>
              <th>Rol</th>
              <th>Accion</th>
            </tr>
          </thead>
          <tbody>
            <?php while($Rol = mysqli_fetch_assoc($QueryRol)): ?>
              <tr>
                <td><?php echo $Rol['ROL_ID'] ?></td>
                <td><?php echo htmlspecialchars($Rol['ROLD_DESCRIP']) ?></td>
                <td>
                  
<button 
  class="btn btn-sm btn-warning accion-editar" 
  data-id="<?php echo $Rol['ROL_ID']; ?>" 
  title="Editar"
>
  <i class="bi bi-pencil-fill"></i>
</button>

<button 
    class="btn btn-sm btn-danger btnEliminarRol" 
    data-id="<?php echo $Rol['ROL_ID']; ?>" 
    data-nombre="<?php echo htmlspecialchars($Rol['ROLD_DESCRIP'], ENT_QUOTES); ?>"
    title="Eliminar"
>
    <i class="bi bi-trash-fill"></i>
</button>



                </td>
              </tr>
            <?php endwhile; ?>
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
    <script src="../js/ajax/Rol/actualizar.js"></script>
    <script src="../js/ajax/Rol/eliminar.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <!-- SweetAlert2 CSS -->
<link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">

<!-- SweetAlert2 JS -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>


