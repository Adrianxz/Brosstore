<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
require('../../Backend/BD.php');

$SelectPro = "SELECT  SUM(VENTA_TOTAL) AS 'AK47' FROM ventas ";

$Query = mysqli_query($Conexion,$SelectPro);

$Result = mysqli_fetch_assoc($Query);

$precio = $Result['AK47']; 
    $precioFormateado = '$' . number_format($precio, 0, ',', '.');
    
$SelectProductosVen = "SELECT 
    producto_ventas.PRODUCTO_VENTAS AS PRO_ID,
    producto.PRO_NOMBRE,
    SUM(producto_ventas.CANTIDAD) AS TOTAL_CANTIDAD
FROM 
    producto_ventas
JOIN 
    producto ON producto_ventas.PRODUCTO_VENTAS = producto.PRO_ID
GROUP BY 
    producto_ventas.PRODUCTO_VENTAS, producto.PRO_NOMBRE
ORDER BY 
    TOTAL_CANTIDAD DESC;
";
   
   
  $resultado = $Conexion->query($SelectProductosVen);

// Arreglos para almacenar datos
$labels = [];
$data = [];

while($fila = $resultado->fetch_assoc()) {
    $labels[] = $fila['PRO_NOMBRE'];
    $data[] = $fila['TOTAL_CANTIDAD'];
}

$TotalP = "SELECT count(PRO_ID) as AK47 FROM producto";

$queryPx = mysqli_query($Conexion,$TotalP);

$fetch = mysqli_fetch_array($queryPx); 

// Nueva consulta para productos más vistos
$SelectProductosVistos = "SELECT PRO_NOMBRE, PRO_VISTAS FROM producto ORDER BY PRO_VISTAS DESC LIMIT 5";
$resultadoVistos = mysqli_query($Conexion, $SelectProductosVistos);

// Obtener el máximo de vistas para calcular porcentajes
$MaxVistaQuery = "SELECT MAX(PRO_VISTAS) as MAX_VISTAS FROM producto";
$maxVistaResult = mysqli_query($Conexion, $MaxVistaQuery);
$maxVistas = mysqli_fetch_assoc($maxVistaResult)['MAX_VISTAS'];

?>


<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
          <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">Dashboard</h1>
            <ol class="breadcrumb">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item active" aria-current="page">Dashboard</li>
            </ol>
          </div>

          <div class="row mb-3">
            <!-- Earnings (Monthly) Card Example -->
            <div class="col-xl-3 col-md-6 mb-4">
              <div class="card h-100">
                <div class="card-body">
                  <div class="row align-items-center">
                    <div class="col mr-2">
                      <div class="text-xs font-weight-bold text-uppercase mb-1">Productos</div>
                      <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $fetch['AK47'];?></div>
                      <div class="mt-2 mb-0 text-muted text-xs">
                        <span class="text-success mr-2"><i class="fa fa-arrow-up"></i></span>
                        <!--<span>Since last month</span>-->
                      </div>
                    </div>
                    <div class="col-auto">
                      <i class="fas fa-shopping-cart fa-2x text-primary"></i>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <!-- Earnings (Annual) Card Example -->
            <div class="col-xl-3 col-md-6 mb-4">
              <div class="card h-100">
                <div class="card-body">
                  <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                      <div class="text-xs font-weight-bold text-uppercase mb-1">Venta totales</div>
                      <div class="h5 mb-0 font-weight-bold text-gray-800"><?php  echo $precioFormateado;?></div>
                      <div class="mt-2 mb-0 text-muted text-xs">
                        <span class="text-success mr-2"><i class="fas fa-arrow-up"></i> 12%</span>
                        <!--<span>Since last years</span>-->
                      </div>
                    </div>
                    <div class="col-auto">
                      <i class="fas fa-shopping-cart fa-2x text-success"></i>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <!-- New User Card Example -->
            <div class="col-xl-3 col-md-6 mb-4">
              <div class="card h-100">
                <div class="card-body">
                  <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                      <div class="text-xs font-weight-bold text-uppercase mb-1">New User</div>
                      <div class="h5 mb-0 mr-3 font-weight-bold text-gray-800">366</div>
                      <div class="mt-2 mb-0 text-muted text-xs">
                        <span class="text-success mr-2"><i class="fas fa-arrow-up"></i> 20.4%</span>
                        <span>Since last month</span>
                      </div>
                    </div>
                    <div class="col-auto">
                      <i class="fas fa-users fa-2x text-info"></i>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <!-- Pending Requests Card Example -->
            <div class="col-xl-3 col-md-6 mb-4">
              <div class="card h-100">
                <div class="card-body">
                  <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                      <div class="text-xs font-weight-bold text-uppercase mb-1">Pending Requests</div>
                      <div class="h5 mb-0 font-weight-bold text-gray-800">18</div>
                      <div class="mt-2 mb-0 text-muted text-xs">
                        <span class="text-danger mr-2"><i class="fas fa-arrow-down"></i> 1.10%</span>
                        <span>Since yesterday</span>
                      </div>
                    </div>
                    <div class="col-auto">
                      <i class="fas fa-comments fa-2x text-warning"></i>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Area Chart -->
            <div class="col-xl-8 col-lg-7">
  <div class="card mb-4">
    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
      <h6 class="m-0 font-weight-bold text-primary">Productos Más Vendidos</h6>
    </div>
    <div class="card-body">
      <div class="chart-area">
        <canvas id="myAreaChart"></canvas>
      </div>
    </div>
  </div>
</div>
            <!-- Pie Chart - Productos Más Vistos -->
            <div class="col-xl-4 col-lg-5">
              <div class="card mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                  <h6 class="m-0 font-weight-bold text-primary">Productos más vistos</h6>
                  <div class="dropdown no-arrow">
                    <!--<a class="dropdown-toggle btn btn-primary btn-sm" href="#" role="button" id="dropdownMenuLink"-->
                    <!--  data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">-->
                    <!--  Month <i class="fas fa-chevron-down"></i>-->
                    <!--</a>-->
                    <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in"
                      aria-labelledby="dropdownMenuLink">
                      <div class="dropdown-header">Select Periode</div>
                      <a class="dropdown-item" href="#">Today</a>
                      <a class="dropdown-item" href="#">Week</a>
                      <a class="dropdown-item active" href="#">Month</a>
                      <a class="dropdown-item" href="#">This Year</a>
                    </div>
                  </div>
                </div>
                <div class="card-body">
                  <?php 
                  $colors = ['bg-warning', 'bg-success', 'bg-danger', 'bg-info', 'bg-primary'];
                  $colorIndex = 0;
                  
                  while($producto = mysqli_fetch_assoc($resultadoVistos)): 
                    $porcentaje = $maxVistas > 0 ? ($producto['PRO_VISTAS'] / $maxVistas) * 100 : 0;
                    $colorClass = $colors[$colorIndex % count($colors)];
                    $colorIndex++;
                  ?>
                  <div class="mb-3">
                    <div class="small text-gray-500"><?php echo htmlspecialchars($producto['PRO_NOMBRE']); ?>
                      <div class="small float-right"><b><?php echo $producto['PRO_VISTAS']; ?> vistas</b></div>
                    </div>
                    <div class="progress" style="height: 12px;">
                      <div class="progress-bar <?php echo $colorClass; ?>" role="progressbar" 
                           style="width: <?php echo round($porcentaje); ?>%" 
                           aria-valuenow="<?php echo round($porcentaje); ?>"
                           aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                  </div>
                  <?php endwhile; ?>
                </div>
                <div class="card-footer text-center">
                  <!--<a class="m-0 small text-primary card-link" href="#">View More <i-->
                  <!--    class="fas fa-chevron-right"></i></a>-->
                </div>
              </div>
            </div>
            <!-- Invoice Example -->
            <!--<div class="col-xl-8 col-lg-7 mb-4">-->
            <!--  <div class="card">-->
            <!--    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">-->
            <!--      <h6 class="m-0 font-weight-bold text-primary">Invoice</h6>-->
            <!--      <a class="m-0 float-right btn btn-danger btn-sm" href="#">View More <i-->
            <!--          class="fas fa-chevron-right"></i></a>-->
            <!--    </div>-->
            <!--    <div class="table-responsive">-->
            <!--      <table class="table align-items-center table-flush">-->
            <!--        <thead class="thead-light">-->
            <!--          <tr>-->
            <!--            <th>Order ID</th>-->
            <!--            <th>Customer</th>-->
            <!--            <th>Item</th>-->
            <!--            <th>Status</th>-->
            <!--            <th>Action</th>-->
            <!--          </tr>-->
            <!--        </thead>-->
            <!--        <tbody>-->
            <!--          <tr>-->
            <!--            <td><a href="#">RA0449</a></td>-->
            <!--            <td>Udin Wayang</td>-->
            <!--            <td>Nasi Padang</td>-->
            <!--            <td><span class="badge badge-success">Delivered</span></td>-->
            <!--            <td><a href="#" class="btn btn-sm btn-primary">Detail</a></td>-->
            <!--          </tr>-->
            <!--          <tr>-->
            <!--            <td><a href="#">RA5324</a></td>-->
            <!--            <td>Jaenab Bajigur</td>-->
            <!--            <td>Gundam 90' Edition</td>-->
            <!--            <td><span class="badge badge-warning">Shipping</span></td>-->
            <!--            <td><a href="#" class="btn btn-sm btn-primary">Detail</a></td>-->
            <!--          </tr>-->
            <!--          <tr>-->
            <!--            <td><a href="#">RA8568</a></td>-->
            <!--            <td>Rivat Mahesa</td>-->
            <!--            <td>Oblong T-Shirt</td>-->
            <!--            <td><span class="badge badge-danger">Pending</span></td>-->
            <!--            <td><a href="#" class="btn btn-sm btn-primary">Detail</a></td>-->
            <!--          </tr>-->
            <!--          <tr>-->
            <!--            <td><a href="#">RA1453</a></td>-->
            <!--            <td>Indri Junanda</td>-->
            <!--            <td>Hat Rounded</td>-->
            <!--            <td><span class="badge badge-info">Processing</span></td>-->
            <!--            <td><a href="#" class="btn btn-sm btn-primary">Detail</a></td>-->
            <!--          </tr>-->
            <!--          <tr>-->
            <!--            <td><a href="#">RA1998</a></td>-->
            <!--            <td>Udin Cilok</td>-->
            <!--            <td>Baby Powder</td>-->
            <!--            <td><span class="badge badge-success">Delivered</span></td>-->
            <!--            <td><a href="#" class="btn btn-sm btn-primary">Detail</a></td>-->
            <!--          </tr>-->
            <!--        </tbody>-->
            <!--      </table>-->
            <!--    </div>-->
            <!--    <div class="card-footer"></div>-->
            <!--  </div>-->
            <!--</div>-->
            <!-- Message From Customer-->
            <!--<div class="col-xl-4 col-lg-5 ">-->
            <!--  <div class="card">-->
            <!--    <div class="card-header py-4 bg-primary d-flex flex-row align-items-center justify-content-between">-->
            <!--      <h6 class="m-0 font-weight-bold text-light">Message From Customer</h6>-->
            <!--    </div>-->
            <!--    <div>-->
            <!--      <div class="customer-message align-items-center">-->
            <!--        <a class="font-weight-bold" href="#">-->
            <!--          <div class="text-truncate message-title">Hi there! I am wondering if you can help me with a-->
            <!--            problem I've been having.</div>-->
            <!--          <div class="small text-gray-500 message-time font-weight-bold">Udin Cilok · 58m</div>-->
            <!--        </a>-->
            <!--      </div>-->
            <!--      <div class="customer-message align-items-center">-->
            <!--        <a href="#">-->
            <!--          <div class="text-truncate message-title">But I must explain to you how all this mistaken idea-->
            <!--          </div>-->
            <!--          <div class="small text-gray-500 message-time">Nana Haminah · 58m</div>-->
            <!--        </a>-->
            <!--      </div>-->
            <!--      <div class="customer-message align-items-center">-->
            <!--        <a class="font-weight-bold" href="#">-->
            <!--          <div class="text-truncate message-title">Lorem ipsum dolor sit amet, consectetur adipiscing elit-->
            <!--          </div>-->
            <!--          <div class="small text-gray-500 message-time font-weight-bold">Jajang Cincau · 25m</div>-->
            <!--        </a>-->
            <!--      </div>-->
            <!--      <div class="customer-message align-items-center">-->
            <!--        <a class="font-weight-bold" href="#">-->
            <!--          <div class="text-truncate message-title">At vero eos et accusamus et iusto odio dignissimos-->
            <!--            ducimus qui blanditiis-->
            <!--          </div>-->
            <!--          <div class="small text-gray-500 message-time font-weight-bold">Udin Wayang · 54m</div>-->
            <!--        </a>-->
            <!--      </div>-->
            <!--      <div class="card-footer text-center">-->
            <!--        <a class="m-0 small text-primary card-link" href="#">View More <i-->
            <!--            class="fas fa-chevron-right"></i></a>-->
            <!--      </div>-->
            <!--    </div>-->
            <!--  </div>-->
            <!--</div>-->
          </div>
          <!--Row-->

          <!--<div class="row">-->
          <!--  <div class="col-lg-12 text-center">-->
          <!--    <p>Do you like this template ? you can download from <a href="https://github.com/indrijunanda/RuangAdmin"-->
          <!--        class="btn btn-primary btn-sm" target="_blank"><i class="fab fa-fw fa-github"></i>&nbsp;GitHub</a></p>-->
          <!--  </div>-->
          <!--</div>-->

          <!-- Modal Logout -->
          <div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabelLogout"
            aria-hidden="true">
            <div class="modal-dialog" role="document">
              <div class="modal-content">
                <div class="modal-header">
                  <h5 class="modal-title" id="exampleModalLabelLogout">Ohh No!</h5>
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                  </button>
                </div>
                <div class="modal-body">
                  <p>Are you sure you want to logout?</p>
                </div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-outline-primary" data-dismiss="modal">Cancel</button>
                  <a href="login.html" class="btn btn-primary">Logout</a>
                </div>
              </div>
            </div>
          </div>


<script>
  $(document).ready(function() {
  const labels = <?php echo json_encode($labels); ?>;
  const data = <?php echo json_encode($data); ?>;

  const ctx = document.getElementById("myAreaChart").getContext('2d');
  const myAreaChart = new Chart(ctx, {
    type: 'line',
    data: {
      labels: labels,
      datasets: [{
        label: "Cantidad Vendida",
        data: data,
        lineTension: 0.3,
        backgroundColor: "rgba(78, 115, 223, 0.05)",
        borderColor: "rgba(78, 115, 223, 1)",
        pointRadius: 3,
        pointBackgroundColor: "rgba(78, 115, 223, 1)",
        pointBorderColor: "rgba(78, 115, 223, 1)",
        pointHoverRadius: 3,
        pointHoverBackgroundColor: "rgba(78, 115, 223, 1)",
        pointHoverBorderColor: "rgba(78, 115, 223, 1)",
        pointHitRadius: 10,
        pointBorderWidth: 2,
      }],
    },
    options: {
      maintainAspectRatio: false,
      scales: {
        x: {
          grid: {
            display: false,
            drawBorder: false
          }
        },
        y: {
          beginAtZero: true,
          grid: {
            color: "rgb(234, 236, 244)",
            drawBorder: false,
            borderDash: [2],
            zeroLineBorderDash: [2]
          }
        }
      },
      plugins: {
        legend: {
          display: true
        }
      }
    }
  });
});
</script>