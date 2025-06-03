<?php 
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require "../../Backend/BD.php";

class Rol {
  // Insertar un nuevo rol
  public function insertar($descripcion) {
    global $Conexion;
    $descripcion = mysqli_real_escape_string($Conexion, $descripcion);
    $sql = "INSERT INTO rol_admin (ROLD_DESCRIP) VALUES ('$descripcion')";
    return mysqli_query($Conexion, $sql);
  }

  // Verificar si un rol ya existe (insensible a mayúsculas)
  public function existe($descripcion) {
    global $Conexion;
    $descripcion = mysqli_real_escape_string($Conexion, $descripcion);
    $sql = "SELECT COUNT(*) as total FROM rol_admin WHERE LOWER(ROLD_DESCRIP) = LOWER('$descripcion')";
    $result = mysqli_query($Conexion, $sql);

    if ($result) {
      $row = mysqli_fetch_assoc($result);
      return $row['total'] > 0;
    }

    return false;
  }

  // Obtener rol por ID
  public function obtenerPorId($id) {
    global $Conexion;
    $id = (int)$id; // aseguramos entero para seguridad
    $sql = "SELECT * FROM rol_admin WHERE ROL_ID = $id";
    $result = mysqli_query($Conexion, $sql);
    if ($result) {
      return mysqli_fetch_assoc($result);
    }
    return null;
  }
  public function eliminar($id) {
  global $Conexion;
  $id = (int)$id;
  $sql = "DELETE FROM rol_admin WHERE ROL_ID = $id";
  return mysqli_query($Conexion, $sql);
}
public function actualizar($id, $descripcion) {
    global $Conexion;
    $id = (int)$id;
    $descripcion = mysqli_real_escape_string($Conexion, $descripcion);
    $sql = "UPDATE rol_admin SET ROLD_DESCRIP = '$descripcion' WHERE ROL_ID = $id";
    return mysqli_query($Conexion, $sql);
}

}



