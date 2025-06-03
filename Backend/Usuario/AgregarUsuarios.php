
<?php

session_start();

require('../BD.php');

$Name = mysqli_real_escape_string($Conexion,$_POST['name']);

$Apellido = mysqli_real_escape_string($Conexion,$_POST['apellidos']);

$TipoDocumento = mysqli_real_escape_string($Conexion,$_POST['tipoDocumento']);

$NumDocumento = mysqli_real_escape_string($Conexion,$_POST['NumDocumento']);

$Correo = mysqli_real_escape_string($Conexion,$_POST['Correo']);

$Tel = mysqli_real_escape_string($Conexion,$_POST['Tel']);

$Contra = mysqli_real_escape_string($Conexion,$_POST['contra']);

$Ccontra = mysqli_real_escape_string($Conexion,$_POST['Ccontra']);


if(empty($Name) || empty($Apellido) || empty($TipoDocumento) || empty($NumDocumento) || empty($Correo) || empty($Tel) || empty($Ccontra) || empty($Ccontra))
{
	http_response_code(400);

	exit();
}

$SelectCorreo = "SELECT * FROM `cliente` WHERE `CLIENTE_CORREO` = '$Correo' ";

$SelectTel = "SELECT * FROM `cliente` WHERE `CLIENTE_TEL` = '$Tel' ";

if(mysqli_num_rows(mysqli_query($Conexion,$SelectCorreo))>0)
{
  http_response_code(400);
  $response = array("message" => "Correo en uso"."<br>");
  $errorMessage[] = $response['message'];
}

if(mysqli_num_rows(mysqli_query($Conexion,$SelectTel))>0)
{
  http_response_code(400);
  $response = array("message" => "Telefono en uso"."<br>");
  $errorMessage[] = $response['message'];	
}

if($Contra !== $Ccontra)
{
	http_response_code(400);
	$response = array("message"=>"las contraseñas no coinciden");
	$errorMessage[] = $response['message'];	
}

else
{
	$Econtra = password_hash($Contra, PASSWORD_BCRYPT);
}

if (!empty($errorMessage)) {
  http_response_code(400);
  header('Content-Type: application/json');
  echo json_encode(array('errorMessage' => $errorMessage));
   
   exit();
}

$Insert = "INSERT INTO `cliente`(
    `CLIENTE_ID`,
    `IDENT_ID`,
    `CLIENTE_NOMBRE`,
    `CLIENTE_APELLIDO`,
    `CLIENTE_NUMIDENT`,
    `CLIENTE_CORREO`,
    `CLIENTE_TEL`,
    `CLIENTE_CONTRA`
)
VALUES(
    NULL,
    $TipoDocumento,
    '$Name',
    '$Apellido',
    '$NumDocumento',
    '$Correo',
    '$Tel',
    '$Econtra'
)";

if (mysqli_query($Conexion, $Insert)) 
{
  
  $SelectPersona = "SELECT * FROM `cliente` WHERE `CLIENTE_CORREO` = '$Correo' AND `CLIENTE_CONTRA` = '$Econtra' ";

  if(mysqli_num_rows(mysqli_query($Conexion,$SelectPersona))>0)
  {
    if($Query = mysqli_fetch_assoc(mysqli_query($Conexion,$SelectPersona)))
    { 
      $_SESSION['Id'] = $Query['CLIENTE_ID'];
      $_SESSION['Correo'] = $Query['CLIENTE_CORREO'];
      $_SESSION['Nombre'] = $Query['CLIENTE_NOMBRE']; 

      //var_dump($Query['CLIENTE_ID']);
    }
     
       
  }

    http_response_code(200); // OK
    $response = array("message" => "Cuentra creada correctamente.");
    echo json_encode($response); 


} 

else {
    http_response_code(500); // Internal Server Error
    $response = array("message" => "Error al insertar registro: " . mysqli_error($Conexion));
    echo json_encode($response);
}

mysqli_close($Conexion);

?>