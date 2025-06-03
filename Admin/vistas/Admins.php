<div class="main-panel">          
        <div class="content-wrapper">
          <div class="row" style="margin-bottom: 10px !important;">
            <div class="col-12 grid-margin stretch-card">
              <div class="card">
                <div class="row">
                            <div class="col-md-4 grid-margin stretch-card">
                                <div class="card">
                                  <div class="card-body">
                                    <h4 class="card-title">Administradores</h4>
                                      <p class="card-description">
                                        Formulario para gestionar los Administradores.
                                      </p>
                                        <form class="forms-sample" method="post" id="Unidades" >

                                          <div class="form-group">
                                            <label for="exampleInputUsername1">Id</label>
                                            <input type="text" class="form-control" id="exampleInputUsername1" placeholder="ID" value="ID" name="ID" readonly> 
                                          </div>

                                          <div class="form-group">
                                            <label for="exampleInputEmail1">Descripcion</label>
                                            <input type="text" class="form-control" id="Descripcion" name="DescripcionUnidades" placeholder="Ingrese una descripcion" required>
                                          </div>


                                          <div class="form-group">
                                            <label for="exampleInputEmail1">Alias</label>
                                            <input type="text" class="form-control" id="Alias" name="Alias" placeholder="Ingrese el alias" required>
                                          </div>
                                          
                                          
                                            <button type="button" id="AgregarUnidades" class="btn btn-primary mr-2">AGREGAR REGISTRO</button>
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
                                                <th>DESCRIPCION</th>
                                                <th>ALIAS</th>
                                                <th>ACCIÓN</th>
                                              </tr>
                                            </thead>
                                          <tbody id="CuertoTabla">
                        
                       
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


