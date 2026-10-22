<?php
session_start();

if (!isset($_SESSION["usuario"])) {
    header("Location: login.php");
    exit();
}

require("conexion.php");

// Validar ID del contacto
if (
    !isset($_GET["id"]) ||
    !ctype_digit((string) $_GET["id"]) ||
    (int) $_GET["id"] <= 0
) {
    header("Location: contactos.php");
    exit();
}

$id = (int) $_GET["id"];

// Buscar contacto
$sql = "SELECT * FROM contacto WHERE id = ?";

$stmt = mysqli_prepare($conexion, $sql);
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);

$resultado = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($resultado) != 1) {
    mysqli_stmt_close($stmt);
    header("Location: contactos.php");
    exit();
}

$contacto = mysqli_fetch_assoc($resultado);
mysqli_stmt_close($stmt);

// Obtener categorías
$sqlCategorias = "SELECT id, nombre FROM categorias ORDER BY nombre ASC";

$resultadoCategorias = mysqli_query($conexion, $sqlCategorias);

$categorias = [];

while ($fila = mysqli_fetch_assoc($resultadoCategorias)) {
    $categorias[] = $fila;
}

$mensaje = "";
$tipo = "";

// Procesar formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $nombre = trim($_POST["nombre"] ?? "");
    $telefono = trim($_POST["telefono"] ?? "");
    $correo = trim($_POST["correo"] ?? "");
    $direccion = trim($_POST["direccion"] ?? "");
    $empresa = trim($_POST["empresa"] ?? "");
    $ubicacion = trim($_POST["ubicacion"] ?? "");
    $categoria_id = (int) ($_POST["categoria_id"] ?? 0);

    // Conservar los valores introducidos
    $contacto["nombre"] = $nombre;
    $contacto["telefono"] = $telefono;
    $contacto["correo"] = $correo;
    $contacto["direccion"] = $direccion;
    $contacto["empresa"] = $empresa;
    $contacto["ubicacion"] = $ubicacion;
    $contacto["categoria_id"] = $categoria_id;

    if (
        $nombre == "" ||
        $telefono == "" ||
        $correo == "" ||
        $direccion == "" ||
        $empresa == "" ||
        $ubicacion == "" ||
        $categoria_id == 0
    ) {

        $mensaje = "Todos los campos son obligatorios.";
        $tipo = "danger";

    } elseif (!is_numeric($telefono)) {

        $mensaje = "El teléfono debe contener únicamente números.";
        $tipo = "danger";

    } elseif (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {

        $mensaje = "Ingrese un correo electrónico válido.";
        $tipo = "danger";

    } else {

        // Verificar que la categoría exista
        $sqlVerificar = "SELECT id FROM categorias WHERE id = ?";

        $stmtVerificar = mysqli_prepare($conexion, $sqlVerificar);
        mysqli_stmt_bind_param($stmtVerificar, "i", $categoria_id);
        mysqli_stmt_execute($stmtVerificar);

        $resultadoVerificar = mysqli_stmt_get_result($stmtVerificar);
        $categoriaExiste = mysqli_num_rows($resultadoVerificar) > 0;

        mysqli_stmt_close($stmtVerificar);

        if (!$categoriaExiste) {

            $mensaje = "Seleccione una categoría válida.";
            $tipo = "danger";

        } else {

            // Actualizar contacto y categoría
            $sql = "UPDATE contacto
                    SET nombre = ?,
                        telefono = ?,
                        correo = ?,
                        direccion = ?,
                        empresa = ?,
                        ubicacion = ?,
                        categoria_id = ?
                    WHERE id = ?";

            try {

                $stmt = mysqli_prepare($conexion, $sql);

                mysqli_stmt_bind_param(
                    $stmt,
                    "ssssssii",
                    $nombre,
                    $telefono,
                    $correo,
                    $direccion,
                    $empresa,
                    $ubicacion,
                    $categoria_id,
                    $id
                );

                mysqli_stmt_execute($stmt);
                mysqli_stmt_close($stmt);

                header("Location: contactos.php?mensaje=editado");
                exit();

            } catch (mysqli_sql_exception $e) {

                error_log("Error al editar contacto: " . $e->getMessage());

                $mensaje = "Error al actualizar el contacto.";
                $tipo = "danger";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Contacto</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="estilos.css">
</head>

<body class="agregar-body">

<nav class="navbar navbar-dark agregar-navbar">
    <div class="container">

        <a href="contactos.php" class="navbar-brand fw-bold">
            Directorio de Contactos BA
        </a>

        <a href="logout.php" class="btn btn-light btn-sm cerrar-sesion">
            Cerrar sesión
        </a>

    </div>
</nav>

<div class="container py-5 agregar-contenedor">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-7">

            <div class="card agregar-card">
                <div class="card-body agregar-card-body">

                    <h2 class="agregar-titulo">
                        Editar contacto
                    </h2>

                    <p class="agregar-descripcion">
                        Modifica la información del contacto.
                    </p>

                    <?php if ($mensaje != ""): ?>

                        <div class="alert alert-<?php echo $tipo; ?> agregar-alert">
                            <?php echo htmlspecialchars($mensaje); ?>
                        </div>

                    <?php endif; ?>

                    <form method="POST" id="formContacto" class="agregar-form">

                        <div class="row">

                            <div class="col-md-6 mb-3">

                                <label for="nombre" class="form-label">
                                    Nombre
                                </label>

                                <input
                                    type="text"
                                    class="form-control"
                                    id="nombre"
                                    name="nombre"
                                    placeholder="Ingrese el nombre"
                                    value="<?php echo htmlspecialchars($contacto["nombre"]); ?>"
                                >

                            </div>

                            <div class="col-md-6 mb-3">

                                <label for="telefono" class="form-label">
                                    Teléfono
                                </label>

                                <input
                                    type="text"
                                    class="form-control"
                                    id="telefono"
                                    name="telefono"
                                    placeholder="Ingrese el teléfono"
                                    value="<?php echo htmlspecialchars($contacto["telefono"]); ?>"
                                >

                            </div>

                            <div class="col-md-6 mb-3">

                                <label for="correo" class="form-label">
                                    Correo electrónico
                                </label>

                                <input
                                    type="email"
                                    class="form-control"
                                    id="correo"
                                    name="correo"
                                    placeholder="Ingrese el correo"
                                    value="<?php echo htmlspecialchars($contacto["correo"]); ?>"
                                >

                            </div>

                            <div class="col-md-6 mb-3">

                                <label for="direccion" class="form-label">
                                    Dirección
                                </label>

                                <input
                                    type="text"
                                    class="form-control"
                                    id="direccion"
                                    name="direccion"
                                    placeholder="Ingrese la dirección"
                                    value="<?php echo htmlspecialchars($contacto["direccion"]); ?>"
                                >

                            </div>

                            <div class="col-md-6 mb-3">

                                <label for="empresa" class="form-label">
                                    Empresa
                                </label>

                                <input
                                    type="text"
                                    class="form-control"
                                    id="empresa"
                                    name="empresa"
                                    placeholder="Ingrese la empresa"
                                    value="<?php echo htmlspecialchars($contacto["empresa"]); ?>"
                                >

                            </div>

                            <div class="col-md-6 mb-3">

                                <label for="ubicacion" class="form-label">
                                    Ubicación
                                </label>

                                <input
                                    type="text"
                                    class="form-control"
                                    id="ubicacion"
                                    name="ubicacion"
                                    placeholder="Ingrese la ubicación"
                                    value="<?php echo htmlspecialchars($contacto["ubicacion"]); ?>"
                                >

                            </div>

                            <!-- Categoría del contacto -->
                            <div class="col-md-6 mb-4">

                                <label for="categoria_id" class="form-label">
                                    Categoría del contacto
                                </label>

                                <select
                                    class="form-select"
                                    id="categoria_id"
                                    name="categoria_id"
                                    required
                                >

                                    <option value="">
                                        Seleccione una categoría
                                    </option>

                                    <?php foreach ($categorias as $fila): ?>

                                        <option
                                            value="<?php echo (int) $fila["id"]; ?>"
                                            <?php echo (
                                                (int) $contacto["categoria_id"] ===
                                                (int) $fila["id"]
                                            ) ? "selected" : ""; ?>
                                        >
                                            <?php echo htmlspecialchars($fila["nombre"]); ?>
                                        </option>

                                    <?php endforeach; ?>

                                </select>

                            </div>

                        </div>

                        <div class="d-flex gap-2">

                            <button
                                type="submit"
                                class="btn btn-primary btn-guardar-contacto"
                            >
                                Guardar cambios
                            </button>

                            <a href="contactos.php" class="btn btn-cancelar-contacto">
                                Cancelar
                            </a>

                        </div>

                    </form>

                </div>
            </div>

        </div>
    </div>
</div>

<script src="script.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
