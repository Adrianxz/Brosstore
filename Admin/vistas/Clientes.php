<?php
require('../../Backend/BD.php');
$SelectCat = "SELECT * FROM `cliente`";
$QueryClientes = mysqli_query($Conexion,$SelectCat);

$SelectGen = "SELECT * FROM `cliente_gmail`";
$QueryClienG = mysqli_query($Conexion,$SelectGen);

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
            <div class="modal fade" id="modalEditarCliente" tabindex="-1" aria-labelledby="modalEditarClienteLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalEditarClienteLabel">Editar Cliente</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>
      <div class="modal-body">
        <form id="formEditarCliente">
          <input type="hidden" id="CLIENTE_ID" name="CLIENTE_ID">
          <div class="row">
            <div class="col-md-6 mb-3">
              <label for="CLIENTE_NOMBRE" class="form-label">Nombre</label>
              <input type="text" class="form-control" id="CLIENTE_NOMBRE" name="CLIENTE_NOMBRE" required>
            </div>
            <div class="col-md-6 mb-3">
              <label for="CLIENTE_APELLIDO" class="form-label">Apellido</label>
              <input type="text" class="form-control" id="CLIENTE_APELLIDO" name="CLIENTE_APELLIDO" required>
            </div>
            <div class="col-md-6 mb-3">
              <label for="CLIENTE_NUMIDENT" class="form-label">Número de Identificación</label>
              <input type="text" class="form-control" id="CLIENTE_NUMIDENT" name="CLIENTE_NUMIDENT" required>
            </div>
            <div class="col-md-6 mb-3">
              <label for="CLIENTE_CORREO" class="form-label">Correo</label>
              <input type="email" class="form-control" id="CLIENTE_CORREO" name="CLIENTE_CORREO" required>
            </div>
            <div class="col-md-6 mb-3">
              <label for="CLIENTE_TEL" class="form-label">Teléfono</label>
              <input type="text" class="form-control" id="CLIENTE_TEL" name="CLIENTE_TEL" required>
            </div>
            <div class="col-md-6 mb-3">
              <label for="CLIENTE_DIRECCION" class="form-label">Dirección</label>
              <input type="text" class="form-control" id="CLIENTE_DIRECCION" name="CLIENTE_DIRECCION" required>
            </div>
            <div class="col-md-6 mb-3">
              <label for="PAIS" class="form-label">País</label>
              <input type="text" class="form-control" id="PAIS" name="PAIS" required>
            </div>
            <div class="col-md-6 mb-3">
              <label for="CIUDAD" class="form-label">Ciudad</label>
              <input type="text" class="form-control" id="CIUDAD" name="CIUDAD" required>
            </div>
            <div class="col-md-6 mb-3">
              <label for="CLIENTE_CONTRA" class="form-label">
  Contraseña (deja en blanco para no cambiar)
</label>
<input type="password" class="form-control" id="CLIENTE_CONTRA" name="CLIENTE_CONTRA">
            </div>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
        <button type="button" class="btn btn-primary" id="btnActualizarCliente">Actualizar</button>
      </div>
    </div>
  </div>
</div>

  <!-- Formulario -->
<div class="col-md-4">
              <div class="card shadow mb-4">
                <div class="card-header py-3">
                  <h6 class="m-0 font-weight-bold text-primary">Clientes</h6>
                  <small>Formulario para registrar clientes.</small>
                </div>
                <div class="card-body">
                  <form id="cliente-form">
                    <div class="form-group">
                      <label for="CLIENTE_NOMBRE">Nombre</label>
                      <input type="text" class="form-control" id="CLIENTE_NOMBRE" name="CLIENTE_NOMBRE" required>
                    </div>
                    <div class="form-group">
                      <label for="CLIENTE_APELLIDO">Apellido</label>
                      <input type="text" class="form-control" id="CLIENTE_APELLIDO" name="CLIENTE_APELLIDO" required>
                    </div>
                    <div class="form-group">
                      <label for="CLIENTE_NUMIDENT">Número de Identificación</label>
                      <input type="text" class="form-control" id="CLIENTE_NUMIDENT" name="CLIENTE_NUMIDENT" required>
                    </div>
                    <div class="form-group">
                      <label for="CLIENTE_CORREO">Correo electrónico</label>
                      <input type="email" class="form-control" id="CLIENTE_CORREO" name="CLIENTE_CORREO" required>
                    </div>
                    <div class="form-group">
                      <label for="CLIENTE_TEL">Teléfono</label>
                      <input type="text" class="form-control" id="CLIENTE_TEL" name="CLIENTE_TEL" required>
                    </div>
                    <div class="form-group">
                      <label for="CLIENTE_DIRECCION">Dirección</label>
                      <input type="text" class="form-control" id="CLIENTE_DIRECCION" name="CLIENTE_DIRECCION" required>
                    </div>
                    <div class="form-group">
                      <label for="PAIS">País</label>
                      <input type="text" class="form-control" id="PAIS" name="PAIS" required>
                    </div>
                    <div class="form-group">
                      <label for="CIUDAD">Ciudad</label>
                      <input type="text" class="form-control" id="CIUDAD" name="CIUDAD" required>
                    </div>
                    <div class="form-group">
                      <label for="CLIENTE_CONTRA">Contraseña</label>
                      <input type="password" class="form-control" id="CLIENTE_CONTRA" name="CLIENTE_CONTRA" required>
                    </div>
                    <button type="button" id="guardarCliente" class="btn btn-primary btn-block">Guardar Cliente</button>
                  </form>
                </div>
              </div>
            </div>



  <!-- Tabla -->
  <div class="col-md-8">
              <div class="card">
                <div class="card-body">
                  <h4 class="card-title">Lista de Clientes</h4>
                  <p class="card-description">Clientes registrados en el sistema.</p>
                  <div style="overflow-x:auto;">
                    <table id="tablaClientes" class="table table-hover">
                      <thead>
                        <tr>
                          <th>ID</th>
                          <th>Nombre</th>
                          <th>Apellido</th>
                          <th>Identificación</th>
                          <th>Correo</th>
                          <th>Teléfono</th>
                          <th>Dirección</th>
                          <th>País</th>
                          <th>Ciudad</th>
                          <th>Acciones</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php while($cliente = mysqli_fetch_assoc($QueryClientes)): ?>
                          <tr>
                            <td><?php echo $cliente['CLIENTE_ID'] ?></td>
                            <td><?php echo htmlspecialchars($cliente['CLIENTE_NOMBRE']) ?></td>
                            <td><?php echo htmlspecialchars($cliente['CLIENTE_APELLIDO']) ?></td>
                            <td><?php echo htmlspecialchars($cliente['CLIENTE_NUMIDENT']) ?></td>
                            <td><?php echo htmlspecialchars($cliente['CLIENTE_CORREO']) ?></td>
                            <td><?php echo htmlspecialchars($cliente['CLIENTE_TEL']) ?></td>
                            <td><?php echo htmlspecialchars($cliente['CLIENTE_DIRECCION']) ?></td>
                            <td><?php echo htmlspecialchars($cliente['PAIS']) ?></td>
                            <td><?php echo htmlspecialchars($cliente['CIUDAD']) ?></td>
                            <td>
                              <button
  class="btn btn-sm btn-warning btnEditarCliente"
  data-id="<?php echo $cliente['CLIENTE_ID']; ?>"
  title="Editar"
>
  <i class="bi bi-pencil-fill"></i>
</button>


                              <button 
                                class="btn btn-sm btn-danger btnEliminarCliente" 
                                data-id="<?php echo $cliente['CLIENTE_ID']; ?>" 
                                data-nombre="<?php echo htmlspecialchars($cliente['CLIENTE_NOMBRE'] . ' ' . $cliente['CLIENTE_APELLIDO'], ENT_QUOTES); ?>"
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


            <!-- Fin tabla -->
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Script personalizado -->
<!-- SweetAlert2 CSS -->
<link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
<!-- SweetAlert2 JS -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<!-- Bootstrap Icons -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

<!-- Archivos JS específicos para Clientes -->
<script src="../js/ajax/Cliente/agregarCliente.js"></script>
<script src="../js/ajax/Cliente/actualizarCliente.js"></script>
<script src="../js/ajax/Cliente/eliminarCliente.js"></script>


