<?php
header("Content-Type: application/json; charset=UTF-8");

// Las credenciales se leen de variables de entorno del servidor, nunca se escriben aquí.
// En la VM, estas variables se configuran en /etc/environment o en la configuración de Apache.
$host = getenv('DB_HOST') ?: "localhost";
$db   = getenv('DB_NAME') ?: "mydb";
$user = getenv('DB_USER') ?: "app_webuser";
$pass = getenv('DB_PASS') ?: "";

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["status" => "error", "message" => "Error de conexión a la base de datos"]);
    exit;
}
