<?php
session_start();

if (!isset($_SESSION["usuario"])) {
    header("Location: login.php");
    exit();
}

require("conexion.php");

if (!isset($_GET["id"]) || !is_numeric($_GET["id"])) {
    header("Location: contactos.php");
    exit();
}

$id = $_GET["id"];

$sql = "DELETE FROM contacto WHERE id = ?";
$stmt = mysqli_prepare($conexion, $sql);

if (!$stmt) {
    die("Error al preparar la consulta: " . mysqli_error($conexion));
}

mysqli_stmt_bind_param($stmt, "i", $id);

if (mysqli_stmt_execute($stmt)) {
    header("Location: contactos.php?mensaje=eliminado");
    exit();
} else {
    die("Error al eliminar el contacto: " . mysqli_stmt_error($stmt));
}
?>