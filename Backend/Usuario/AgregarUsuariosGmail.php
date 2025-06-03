<?php
// Mostrar errores solo en desarrollo


header("Content-Type: application/json; charset=UTF-8");
session_start();

require_once('../BD.php');
require_once('../../vendor/autoload.php');

global $Conexion;

try {
    if (!$Conexion) {
        throw new Exception("Error de conexión a la base de datos");
    }

    $input = json_decode(file_get_contents('php://input'), true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception("Formato JSON inválido");
    }

    if (empty($input['id_token'])) {
        throw new Exception("Token de Google no proporcionado");
    }

    $idToken = $input['id_token'];

    $client = new Google_Client();
    $client->setClientId('626919515251-8shlre3ltg2hp18djivlgj2h47jrj9ra.apps.googleusercontent.com');
    $client->setHttpClient(new GuzzleHttp\Client(['verify' => false])); // SOLO EN DESARROLLO

    $payload = $client->verifyIdToken($idToken);

    if (!$payload) {
        throw new Exception("Token de Google inválido o expirado");
    }

    if (empty($payload['sub']) || empty($payload['email'])) {
        throw new Exception("El token no contiene los campos requeridos");
    }

    $googleId = mysqli_real_escape_string($Conexion, $payload['sub']);
    $email = mysqli_real_escape_string($Conexion, $payload['email']);
    $name = mysqli_real_escape_string($Conexion, $payload['name'] ?? '');

    // Buscar si el usuario ya existe
    $sql = "SELECT * FROM cliente_gmail 
            WHERE Google_Id = '$googleId' OR Cliente_Correo = '$email' LIMIT 1";
    $result = mysqli_query($Conexion, $sql);

    if (!$result) {
        throw new Exception("Error al verificar usuario: " . mysqli_error($Conexion));
    }

    if (mysqli_num_rows($result) > 0) {
        // Usuario ya existe
        $userData = mysqli_fetch_assoc($result);

        $_SESSION['usuario'] = [
            'Id' => $userData['Cliente_Id'],
            'Google_Id' => [$userData['Google_Id']],
            'Correo' => $userData['Cliente_Correo'],
            'Nombre' => $userData['Cliente_Nombre'],
            'Pais' => $userData['PAIS'] ?? null,
            'Ciudad' => $userData['CIUDAD'] ?? null,
            'Direcc' => $userData['DIRECCION'] ?? null
        ];

        echo json_encode([
            'success' => true,
            'message' => 'Inicio de sesión exitoso',
            'user' => $_SESSION['usuario']
        ]);
    } else {
        // Usuario nuevo, insertar en la BD
        $sqlInsert = "INSERT INTO cliente_gmail (Google_Id, Cliente_Nombre, Cliente_Correo) 
                      VALUES ('$googleId', '$name', '$email')";

        if (mysqli_query($Conexion, $sqlInsert)) {
            $userId = mysqli_insert_id($Conexion);

            $_SESSION['usuario'] = [
                'Id' => $userId,
                'Correo' => $email,
                'Nombre' => $name,
                'Google_Id' => [$googleId],
                'Pais' => null,
                'Ciudad' => null,
                'Direcc' => null
            ];

            echo json_encode([
                'success' => true,
                'message' => 'Usuario registrado exitosamente',
                'user' => $_SESSION['usuario']
            ]);
        } else {
            throw new Exception("Error al registrar usuario: " . mysqli_error($Conexion));
        }
    }

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
    exit;
}
?>
