<?php

$host = "localhost";
$usuario = "root";
$clave = "";
$base_datos = "bd_gestion_contactos";

$conexion = mysqli_connect($host, $usuario, $clave, $base_datos);

if (!$conexion) {
    die("Error de conexión: " . mysqli_connect_error());
}

mysqli_set_charset($conexion, "utf8");

?>