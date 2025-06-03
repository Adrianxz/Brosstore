
<?php

require('../../Backend/BD.php');
$SelectCat = "SELECT * FROM `categorias`";
$QueryCat = mysqli_query($Conexion,$SelectCat);

$SelectGen = "SELECT * FROM `genero`";
$QueryGen = mysqli_query($Conexion,$SelectGen);

$SelectProv = "SELECT * FROM `proveedor`";
$QueryProv = mysqli_query($Conexion,$SelectProv);

$SelectProductos = "SELECT producto.PRO_ID, producto.CAT_ID, producto.PROV_ID,producto.PRO_NOMBRE,producto.PRO_DESCRIP,producto.PRO_PRECIO, categorias.CAT_DESCRIP, proveedor.PROV_NOMBRE FROM `producto` INNER JOIN categorias ON producto.CAT_ID = categorias.CAT_ID INNER JOIN proveedor ON producto.PROV_ID = proveedor.PROV_ID";

$QueryProduct = mysqli_query($Conexion,$SelectProductos);

$SelectFoto = "SELECT pf.*, p.*
FROM producto_fotos AS pf
INNER JOIN producto     AS p
  ON pf.PRO_ID = p.PRO_ID;
";

$SelectTallas = "SELECT * FROM `tallas`";
$QueryTallas = mysqli_query($Conexion,$SelectTallas);

$QueryFoto = mysqli_query($Conexion,$SelectFoto);
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
                                            <label for="exampleInputEmail1">Nombre</label>
                                            <input type="text" class="form-control" id="Nombre" name="Nombre" placeholder="Nombre" required>
                                          </div>

                                          <div class="form-group">
                                            <label for="exampleInputEmail1">Descripcion</label>
                                            <textarea id="summernote" name="descripcion"></textarea>
                                          </div>

                                          <div class="form-group">
                                            <label for="exampleInputEmail1">Genero</label>
                                            <select class="form-control" name="Genero">
                                              <option>Elija el genero</option>
                                              <?php while($Gen = mysqli_fetch_assoc($QueryGen)):?>
                                                <option value="<?php echo $Gen['GENERO_ID']?>"><?php echo $Gen['GENERO_DESCRIP']?></option>
                                              <?php endwhile;?>
                                            </select>
                                          </div>

                                           <div class="form-group">
                                            <label for="exampleInputEmail1">Categoria</label>
                                            <select class="form-control" name="Categoria">
                                              <option>Elija la categoria</option>
                                              <?php while($Cat = mysqli_fetch_assoc($QueryCat)):?>
                                                <option value="<?php echo $Cat['CAT_ID']?>"><?php echo $Cat['CAT_DESCRIP']?></option>
                                              <?php endwhile;?>
                                            </select>
                                          </div>



                                          <div class="form-group">
                                            <label for="exampleInputEmail1">Proveedor</label>
                                            <select class="form-control" name="Proveedor">
                                              <option>Elija el proveedor</option>
                                              <?php while($Prov = mysqli_fetch_assoc($QueryProv)):?>
                                                <option value="<?php echo $Prov['PROV_ID']?>"><?php echo $Prov['PROV_NOMBRE']?></option>
                                              <?php endwhile;?>
                                            </select>
                                          </div>

                                          <div class="form-group">
                                            <label for="exampleInputEmail1">Tallas</label>
                                            <?php while($talla = mysqli_fetch_assoc($QueryTallas)): ?>
                                              <div class="talla-item" style="margin-bottom: 8px;">
                                                <input type="checkbox" name="tallas[]" value="<?php echo $talla['TALLA_ID']; ?>" class="talla-checkbox" id="talla_<?php echo $talla['TALLA_ID']; ?>">
                                                <label for="talla_<?php echo $talla['TALLA_ID']; ?>"><?php echo $talla['TALLA_DESCRIP']; ?></label>

                                                <label for="cantidad_<?php echo $talla['TALLA_ID']; ?>" style="margin-left:10px;">Cantidad:</label>
                                                <input type="number" name="cantidad[<?php echo $talla['TALLA_ID']; ?>]" id="cantidad_<?php echo $talla['TALLA_ID']; ?>" class="form-control cantidad-input" min="1" style="display:inline-block; width: 100px;" disabled>
                                              </div>
                                            <?php endwhile; ?>
                                          </div>    


                                          <div id="cantidades-container"></div>

                                          <div class="form-group">
                                            <label for="exampleInputEmail1">Precio</label>
                                            <input type="text" class="form-control" id="Precio" name="Precio" placeholder="Ingrese el precio" required>
                                          </div>

                                          <div class="form-group">
                                            <label for="exampleInputEmail1">Foto</label>
                                            <input type="file" class="form-control" id="FotoInput" accept=".png, .jpg, .jpeg" name="Foto[]" multiple>
                                            <small class="form-text text-muted">Maximo 3 imagenes</small>
                                            <div id="preview" style="margin-top: 10px; display: flex; flex-wrap: wrap; gap: 10px;"></div>

                                          </div>  
                                                  
                                          <input type="hidden" id="ImagenPrincipalLimpiada" name="ImagenPrincipalLimpiada">
                                           
                                            <button type="button" id="AgregarProducto" class="btn btn-primary mr-2">AGREGAR REGISTRO</button>
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
                                                <th>NOMBRE</th>
                                                <th>DESCRIPCION</th>
                                                <th>CATEGORIA</th>
                                                <th>PROVEEDOR</th>
                                                <!-- <th>CANTIDAD</th> -->
                                                <th>PRECIO</th>
                                                <th>FOTO</th>
                                                <th>ACCIÓN</th>
                                              </tr>
                                            </thead>
                                          <tbody>
                                          <?php while($Producto = mysqli_fetch_assoc($QueryProduct)):?>
                                          <tr>
                                            <td><?php echo $Producto['PRO_ID']?></td>
                                            <td><?php echo $Producto['PRO_NOMBRE']?></td>
                                            <td><?php echo $Producto['PRO_DESCRIP']?></td>
                                            <td><?php echo $Producto['CAT_DESCRIP']?></td>
                                            <td><?php echo $Producto['PROV_NOMBRE']?></td>
                                            
                                            <td><?php echo $Producto['PRO_PRECIO']?></td>
                                            <td><button type="button" class="btn btn-primary" data-toggle="modal" data-target="#miniGaleriaModal"  data-id="<?php echo $Producto['PRO_ID']?>">
                                              Ver imagenes
                                            </button></td>
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

 

  <script type="text/javascript">
      $('#summernote').summernote({
  callbacks: {
    onImageUpload: function(files) {
      // Evitar que las imágenes se carguen automáticamente
      console.log("Carga de imagenes bloqueada en Summernote");
      return;
    }
  }
});
  </script>

<script>
  document.querySelectorAll('.talla-checkbox').forEach(checkbox => {
    checkbox.addEventListener('change', function () {
      const tallaId = this.value;
      const input = document.getElementById('cantidad_' + tallaId);
      if (this.checked) {
        input.disabled = false;
        input.required = true;
      } else {
        input.disabled = true;
        input.required = false;
        input.value = ''; // opcional: limpia el valor
      }
    });
  });
</script>
  <script>
  document.querySelectorAll('.talla-checkbox').forEach(checkbox => {
    checkbox.addEventListener('change', function () {
      const container = document.getElementById('cantidades-container');
      const tallaId = this.value;
      const inputId = 'cantidad_' + tallaId;

      if (this.checked) {
        // Crear el input si no existe
        if (!document.getElementById(inputId)) {
          const inputGroup = document.createElement('div');
          inputGroup.className = 'form-group';
          inputGroup.id = 'grupo_' + tallaId;

          const label = document.createElement('label');
          label.setAttribute('for', inputId);
          label.innerText = 'Cantidad para talla ' + tallaId;

          const input = document.createElement('input');
          input.type = 'number';
          input.name = 'cantidad[' + tallaId + ']';
          input.id = inputId;
          input.className = 'form-control';
          input.required = true;
          input.min = 1;

          inputGroup.appendChild(label);
          inputGroup.appendChild(input);
          container.appendChild(inputGroup);
        }
      } else {
        // Eliminar el input si se desmarca
        const toRemove = document.getElementById('grupo_' + tallaId);
        if (toRemove) {
          container.removeChild(toRemove);
        }
      }
    });
  });
</script>


  <script>
const precioInput = document.getElementById('Precio');

precioInput.addEventListener('input', function (e) {
  // Eliminar todo menos numeros y punto
  let valor = this.value.replace(/[^\d.]/g, '');

  // Solo permitir un punto decimal
  const partes = valor.split('.');
  if (partes.length > 2) {
    valor = partes[0] + '.' + partes[1]; // ignorar puntos extra
  }

  // Formatear parte entera con separador de miles
  let [entero, decimal] = valor.split('.');
  entero = entero.replace(/\B(?=(\d{3})+(?!\d))/g, ',');

  this.value = decimal !== undefined ? `${entero}.${decimal}` : entero;
});
</script>




<script>
  $(document).ready(function(){
    $('#miniGaleriaModal').on('show.bs.modal', function (e) {
      var btn     = $(e.relatedTarget);          
      var proId   = btn.data('id');               
      var carousel= $('#miniGaleriaCarousel');
      var inner   = carousel.find('.carousel-inner');

     
      inner.empty();

      
      $.ajax({
        url: 'vistas/Fotos.php',  
        method: 'GET',
        data: { pro_id: proId },
        dataType: 'json'
      })
      .done(function(urls){
        if (urls.length) {
          urls.forEach(function(src, idx){
            var item = $('<div>')
              .addClass('carousel-item' + (idx===0 ? ' active' : ''));
            $('<img>')
              .addClass('d-block w-100')
              .attr('src', src)
              .attr('alt', 'Imagen ' + (idx+1))
              .appendTo(item);
            inner.append(item);
          });
        } else {
          // Sin imágenes
          inner.append(
            '<div class="carousel-item active">' +
            '<p class="text-center mb-0">Sin imágenes</p>' +
            '</div>'
          );
        }
        // Volver al slide #0 siempre
        carousel.carousel(0);
      })
      .fail(function(){
        inner.append(
          '<div class="carousel-item active">' +
          '<p class="text-center text-danger mb-0">Error cargando imágenes</p>' +
          '</div>'
        );
      });
    });
  });
</script>



  <script src="../js/ajax/Producto/AgregarProducto.js"></script>  

