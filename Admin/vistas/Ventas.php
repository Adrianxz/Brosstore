<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require('../../Backend/BD.php');
$SelectVe = "SELECT * FROM `ventas`";
$QueryVe = mysqli_query($Conexion, $SelectVe);
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

            <!-- Tabla de Datos -->
            <div class="col-lg-12 grid-margin stretch-card">
              <div class="card">
                <div class="card-body">
                  <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                      <h4 class="card-title">TABLA DE DATOS</h4>
                      <p class="card-description">En esta tabla podrás ver los datos que están almacenados en el sistema.</p>
                    </div>
                    <div class="btn-group" role="group">
                      <a href="https://blue-parrot-771704.hostingersite.com/404/Backend/Reportes/ventas_excel.php?descargar=excel" class="btn btn-success" target="_blank">
                        <i class="fas fa-file-excel"></i> Descargar Excel
                      </a>
                      <a href="https://blue-parrot-771704.hostingersite.com/404/Backend/Reportes/ventas_excel.php?descargar=csv" class="btn btn-info" target="_blank">
                        <i class="fas fa-file-csv"></i> Descargar CSV
                      </a>
                    </div>
                  </div>
                  
                  <div class="table-responsive" style="max-height: 600px; overflow-y: auto;">
                    <table id="miTabla" class="table table-hover">
                      <thead>
                        <tr>
                          <th>ID</th>
                          <th>CLIENTE ID</th>
                          <th>FECHA</th>
                          <th>ORDEN</th>
                          <th>TOTAL</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php while ($Venta = mysqli_fetch_assoc($QueryVe)) : ?>
                          <tr>
                            <td><?php echo $Venta['VENTA_ID'] ?></td>
                            <td><?php echo $Venta['CLIENTE_ID'] ?></td>
                            <td><?php echo $Venta['VENTA_FECHA'] ?></td>
                            <td><?php echo $Venta['VENTA_ORDEN'] ?></td>
                            <td><?php echo $Venta['VENTA_TOTAL'] ?></td>
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

<!-- Scripts para jQuery y DataTables -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<!-- Font Awesome para iconos -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

<!-- Script personalizado -->
<script src="js/Tabla.js"></script>

<script>
    // Script para mejorar la experiencia de descarga de reportes
document.addEventListener('DOMContentLoaded', function() {
    // Función para mostrar indicador de carga
    function mostrarCargando(boton, texto) {
        const textoOriginal = boton.innerHTML;
        boton.innerHTML = `<i class="fas fa-spinner fa-spin"></i> ${texto}`;
        boton.disabled = true;
        
        // Restaurar botón después de 3 segundos
        setTimeout(() => {
            boton.innerHTML = textoOriginal;
            boton.disabled = false;
        }, 3000);
    }
    
    // Agregar eventos a los botones de descarga
    const btnExcel = document.querySelector('a[href*="descargar=excel"]');
    const btnCSV = document.querySelector('a[href*="descargar=csv"]');
    
    if (btnExcel) {
        btnExcel.addEventListener('click', function(e) {
            // Convertir enlace a botón temporalmente para mostrar carga
            const botonTemp = document.createElement('button');
            botonTemp.className = this.className;
            botonTemp.innerHTML = this.innerHTML;
            
            mostrarCargando(botonTemp, 'Generando Excel...');
            this.parentNode.replaceChild(botonTemp, this);
            
            // Crear enlace invisible para descarga
            const enlaceDescarga = document.createElement('a');
            enlaceDescarga.href = this.href;
            enlaceDescarga.target = '_blank';
            enlaceDescarga.style.display = 'none';
            document.body.appendChild(enlaceDescarga);
            enlaceDescarga.click();
            document.body.removeChild(enlaceDescarga);
            
            // Restaurar enlace original
            setTimeout(() => {
                botonTemp.parentNode.replaceChild(this, botonTemp);
            }, 3000);
        });
    }
    
    if (btnCSV) {
        btnCSV.addEventListener('click', function(e) {
            const botonTemp = document.createElement('button');
            botonTemp.className = this.className;
            botonTemp.innerHTML = this.innerHTML;
            
            mostrarCargando(botonTemp, 'Generando CSV...');
            this.parentNode.replaceChild(botonTemp, this);
            
            const enlaceDescarga = document.createElement('a');
            enlaceDescarga.href = this.href;
            enlaceDescarga.target = '_blank';
            enlaceDescarga.style.display = 'none';
            document.body.appendChild(enlaceDescarga);
            enlaceDescarga.click();
            document.body.removeChild(enlaceDescarga);
            
            setTimeout(() => {
                botonTemp.parentNode.replaceChild(this, botonTemp);
            }, 3000);
        });
    }
    
    // Función para mostrar notificaciones
    function mostrarNotificacion(mensaje, tipo = 'success') {
        const notificacion = document.createElement('div');
        notificacion.className = `alert alert-${tipo} alert-dismissible fade show position-fixed`;
        notificacion.style.cssText = `
            top: 20px;
            right: 20px;
            z-index: 9999;
            min-width: 300px;
        `;
        notificacion.innerHTML = `
            ${mensaje}
            <button type="button" class="close" data-dismiss="alert">
                <span>&times;</span>
            </button>
        `;
        
        document.body.appendChild(notificacion);
        
        // Auto-remover después de 5 segundos
        setTimeout(() => {
            if (notificacion.parentNode) {
                notificacion.remove();
            }
        }, 5000);
    }
    
    // Verificar si la descarga fue exitosa (opcional)
    if (window.location.search.includes('descarga=exitosa')) {
        mostrarNotificacion('¡Reporte descargado exitosamente!', 'success');
    }
    
    if (window.location.search.includes('descarga=error')) {
        mostrarNotificacion('Error al generar el reporte. Inténtalo de nuevo.', 'danger');
    }
});

// Función para exportar tabla visible a Excel (alternativa local)
function exportarTablaExcel() {
    const tabla = document.getElementById('miTabla');
    const datos = [];
    
    // Obtener encabezados
    const encabezados = [];
    tabla.querySelectorAll('thead th').forEach(th => {
        encabezados.push(th.textContent.trim());
    });
    datos.push(encabezados);
    
    // Obtener filas de datos
    tabla.querySelectorAll('tbody tr').forEach(tr => {
        const fila = [];
        tr.querySelectorAll('td').forEach(td => {
            fila.push(td.textContent.trim());
        });
        datos.push(fila);
    });
    
    // Convertir a CSV
    const csv = datos.map(fila => 
        fila.map(celda => `"${celda}"`).join(',')).join('\n');
    
    // Crear y descargar archivo
    const blob = new Blob(['\ufeff' + csv], { type: 'text/csv;charset=utf-8;' });
    const enlace = document.createElement('a');
    enlace.href = URL.createObjectURL(blob);
    enlace.download = `Tabla_Ventas_${new Date().toISOString().split('T')[0]}.csv`;
    enlace.click();
}

// Función para imprimir tabla
function imprimirTabla() {
    const tabla = document.getElementById('miTabla').outerHTML;
    const ventana = window.open('', '_blank');
    ventana.document.write(`
        <html>
        <head>
            <title>Reporte de Ventas</title>
            <style>
                body { font-family: Arial, sans-serif; }
                table { border-collapse: collapse; width: 100%; }
                th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
                th { background-color: #f2f2f2; font-weight: bold; }
                @media print {
                    body { margin: 0; }
                    table { font-size: 12px; }
                }
            </style>
        </head>
        <body>
            <h2>Reporte de Ventas - ${new Date().toLocaleDateString()}</h2>
            ${tabla}
        </body>
        </html>
    `);
    ventana.document.close();
    ventana.print();
}
</script>

<script>
// Script para mejorar la experiencia de descarga de reportes
document.addEventListener('DOMContentLoaded', function() {
    // Función para mostrar indicador de carga
    function mostrarCargando(enlace, texto) {
        const textoOriginal = enlace.innerHTML;
        enlace.innerHTML = `<i class="fas fa-spinner fa-spin"></i> ${texto}`;
        enlace.style.pointerEvents = 'none';
        
        // Restaurar enlace después de 3 segundos
        setTimeout(() => {
            enlace.innerHTML = textoOriginal;
            enlace.style.pointerEvents = 'auto';
        }, 3000);
    }
    
    // Agregar eventos a los botones de descarga
    const btnExcel = document.querySelector('a[href*="descargar=excel"]');
    const btnCSV = document.querySelector('a[href*="descargar=csv"]');
    
    if (btnExcel) {
        btnExcel.addEventListener('click', function() {
            mostrarCargando(this, 'Generando Excel...');
        });
    }
    
    if (btnCSV) {
        btnCSV.addEventListener('click', function() {
            mostrarCargando(this, 'Generando CSV...');
        });
    }
});
</script>

<style>
.btn-group .btn {
    margin-left: 5px;
}
.btn-group .btn:first-child {
    margin-left: 0;
}
</style>