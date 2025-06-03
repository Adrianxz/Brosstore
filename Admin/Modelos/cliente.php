<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require "../../Backend/BD.php";

class Cliente {

  // Insertar nuevo cliente
public function insertar($nombre, $apellido, $numident, $correo, $tel, $direccion, $pais, $ciudad, $contra) {
  global $Conexion;

  // Escapar datos
  $nombre = mysqli_real_escape_string($Conexion, $nombre);
  $apellido = mysqli_real_escape_string($Conexion, $apellido);
  $numident = mysqli_real_escape_string($Conexion, $numident);
  $correo = mysqli_real_escape_string($Conexion, $correo);
  $tel = mysqli_real_escape_string($Conexion, $tel);
  $direccion = mysqli_real_escape_string($Conexion, $direccion);
  $pais = mysqli_real_escape_string($Conexion, $pais);
  $ciudad = mysqli_real_escape_string($Conexion, $ciudad);
  $contra = password_hash($contra, PASSWORD_DEFAULT); // encriptar contraseña

  $sql = "INSERT INTO cliente (
            CLIENTE_NOMBRE, CLIENTE_APELLIDO, CLIENTE_NUMIDENT,
            CLIENTE_CORREO, CLIENTE_TEL, CLIENTE_DIRECCION,
            PAIS, CIUDAD, CLIENTE_CONTRA
          ) VALUES (
            '$nombre', '$apellido', '$numident',
            '$correo', '$tel', '$direccion',
            '$pais', '$ciudad', '$contra'
          )";

  return mysqli_query($Conexion, $sql);
}


  // Verificar si existe cliente por número de identificación o correo (insensible a mayúsculas)
  public function existe($numIdent, $correo) {
    global $Conexion;

    $numIdent = mysqli_real_escape_string($Conexion, $numIdent);
    $correo = mysqli_real_escape_string($Conexion, $correo);

    $sql = "SELECT COUNT(*) as total FROM cliente 
            WHERE LOWER(CLIENTE_NUMIDENT) = LOWER('$numIdent') 
            OR LOWER(CLIENTE_CORREO) = LOWER('$correo')";
    $result = mysqli_query($Conexion, $sql);

    if ($result) {
      $row = mysqli_fetch_assoc($result);
      return $row['total'] > 0;
    }

    return false;
  }

  // Obtener cliente por ID
  public function obtenerPorId($id) {
    global $Conexion;
    $id = (int)$id;
    $sql = "SELECT * FROM cliente WHERE CLIENTE_ID = $id";
    $result = mysqli_query($Conexion, $sql);
    if ($result) {
      return mysqli_fetch_assoc($result);
    }
    return null;
  }

  // Actualizar cliente (sin cambiar contraseña)
  public function actualizar($id, $nombre, $apellido, $numident, $correo, $tel, $direccion, $pais, $ciudad, $contra) {
  global $Conexion;
  $id = (int)$id;

  // Escapar todos los datos
  $nombre = mysqli_real_escape_string($Conexion, $nombre);
  $apellido = mysqli_real_escape_string($Conexion, $apellido);
  $numident = mysqli_real_escape_string($Conexion, $numident);
  $correo = mysqli_real_escape_string($Conexion, $correo);
  $tel = mysqli_real_escape_string($Conexion, $tel);
  $direccion = mysqli_real_escape_string($Conexion, $direccion);
  $pais = mysqli_real_escape_string($Conexion, $pais);
  $ciudad = mysqli_real_escape_string($Conexion, $ciudad);
  $contra = mysqli_real_escape_string($Conexion, $contra);

  // Encriptar contraseña si se proporciona
  $contraHash = !empty($contra) ? password_hash($contra, PASSWORD_DEFAULT) : null;

  // Armar SQL
  $sql = "UPDATE cliente SET
            CLIENTE_NOMBRE = '$nombre',
            CLIENTE_APELLIDO = '$apellido',
            CLIENTE_NUMIDENT = '$numident',
            CLIENTE_CORREO = '$correo',
            CLIENTE_TEL = '$tel',
            CLIENTE_DIRECCION = '$direccion',
            PAIS = '$pais',
            CIUDAD = '$ciudad'";

  // Solo incluir la contraseña si fue proporcionada
  if ($contraHash !== null) {
    $sql .= ", CLIENTE_CONTRA = '$contraHash'";
  }

  $sql .= " WHERE CLIENTE_ID = $id";

  return mysqli_query($Conexion, $sql);
}



  // Eliminar cliente por ID
  public function eliminar($id) {
    global $Conexion;
    $id = (int)$id;
    $sql = "DELETE FROM cliente WHERE CLIENTE_ID = $id";
    return mysqli_query($Conexion, $sql);
  }
}
