<?php

// Detectar si existen variables de Railway
$host = getenv("DB_HOST") ?: //"127.0.0.1";
$usuario = getenv("DB_USERNAME") ?: //"root";
$clave = getenv("DB_PASSWORD");

if ($clave === false) {
    $clave = "";
}

$base_datos = getenv("DB_DATABASE") ?: "bd_gestion_contactos";
$puerto = (int) (getenv("DB_PORT") ?: 3306);

try {

    // Conexión mediante MySQLi
    $conexion = new mysqli(
        $host,
        $usuario,
        $clave,
        $base_datos,
        $puerto
    );

    // Configuración de caracteres
    $conexion->set_charset("utf8mb4");

} catch (mysqli_sql_exception $e) {

    error_log(
        "Error de conexión MySQL: " . $e->getMessage()
    );

    http_response_code(500);

    // Evitar revelar credenciales y detalles del servidor
    exit("No se pudo conectar con la base de datos.");

}
