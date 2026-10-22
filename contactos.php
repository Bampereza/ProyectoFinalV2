<?php
session_start();

if (!isset($_SESSION["usuario"])) {
    header("Location: login.php");
    exit();
}

require("conexion.php");

$mensaje = "";
$tipoMensaje = "";

if (isset($_GET["mensaje"])) {

    if ($_GET["mensaje"] == "guardado") {
        $mensaje = "El contacto se agregó correctamente.";
        $tipoMensaje = "success";
    }

    if ($_GET["mensaje"] == "eliminado") {
        $mensaje = "El contacto se eliminó correctamente.";
        $tipoMensaje = "danger";
    }

    if ($_GET["mensaje"] == "editado") {
        $mensaje = "El contacto se editó correctamente.";
        $tipoMensaje = "primary";
    }
}

// Obtener categorías
$sqlCategorias = "SELECT id, nombre FROM categorias ORDER BY nombre ASC";
$resultadoCategorias = mysqli_query($conexion, $sqlCategorias);

$categorias = [];
$idTrabajo = null;

while ($fila = mysqli_fetch_assoc($resultadoCategorias)) {

    $categorias[] = $fila;

    if (strcasecmp($fila["nombre"], "Trabajo") === 0) {
        $idTrabajo = (int) $fila["id"];
    }
}

// Obtener filtros
$busqueda = isset($_GET["busqueda"]) ? trim($_GET["busqueda"]) : "";
$ubicacion = isset($_GET["ubicacion"]) ? trim($_GET["ubicacion"]) : "";
$orden = isset($_GET["orden"]) ? $_GET["orden"] : "id";
$categoria = isset($_GET["categoria"]) ? trim($_GET["categoria"]) : "";

// Determinar cuándo mostrar Empresa
$mostrarEmpresa = false;

if (
    $categoria !== "" &&
    ctype_digit($categoria) &&
    $idTrabajo !== null &&
    (int) $categoria === $idTrabajo
) {
    $mostrarEmpresa = true;
}

if ($orden == "empresa") {
    $mostrarEmpresa = true;
}

// Mostrar empresa cuando la búsqueda coincida
if ($busqueda !== "") {

    $sqlEmpresa = "SELECT id FROM contacto
                   WHERE empresa LIKE ? LIMIT 1";

    $stmtEmpresa = mysqli_prepare($conexion, $sqlEmpresa);

    $buscarEmpresa = "%" . $busqueda . "%";

    mysqli_stmt_bind_param(
        $stmtEmpresa,
        "s",
        $buscarEmpresa
    );

    mysqli_stmt_execute($stmtEmpresa);

    $resultadoEmpresa = mysqli_stmt_get_result($stmtEmpresa);

    if (mysqli_num_rows($resultadoEmpresa) > 0) {
        $mostrarEmpresa = true;
    }

    mysqli_stmt_close($stmtEmpresa);
}

// Consulta principal
$sql = "SELECT * FROM contacto WHERE 1=1";
$parametros = [];
$tipos = "";

// Búsqueda
if ($busqueda != "") {

    $sql .= " AND (nombre LIKE ? OR correo LIKE ? OR empresa LIKE ?)";

    $busquedaSQL = "%" . $busqueda . "%";

    $parametros[] = $busquedaSQL;
    $parametros[] = $busquedaSQL;
    $parametros[] = $busquedaSQL;

    $tipos .= "sss";
}

// Filtrar por ubicación
if ($ubicacion != "") {

    $sql .= " AND ubicacion = ?";

    $parametros[] = $ubicacion;
    $tipos .= "s";
}

// Filtrar por categoría
if ($categoria !== "" && ctype_digit($categoria)) {

    $sql .= " AND categoria_id = ?";

    $parametros[] = (int) $categoria;
    $tipos .= "i";
}

// Ordenamiento
if ($orden == "nombre") {

    $sql .= " ORDER BY nombre ASC";

} elseif ($orden == "fecha") {

    $sql .= " ORDER BY fecha_creacion DESC";

} elseif ($orden == "empresa") {

    $sql .= " ORDER BY empresa ASC";

} else {

    $sql .= " ORDER BY id ASC";
}

// Ejecutar consulta preparada
$stmt = mysqli_prepare($conexion, $sql);

if (!empty($parametros)) {
    mysqli_stmt_bind_param($stmt, $tipos, ...$parametros);
}

mysqli_stmt_execute($stmt);

$resultado = mysqli_stmt_get_result($stmt);

// Obtener ubicaciones
$sqlUbicaciones = "SELECT DISTINCT ubicacion
                   FROM contacto
                   ORDER BY ubicacion ASC";

$resultadoUbicaciones = mysqli_query(
    $conexion,
    $sqlUbicaciones
);
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Contactos | Directorio BA</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">

    <link rel="stylesheet" href="estilos.css">

    <style>
        /* Ajustes visuales sin cambiar los colores originales */

        .contactos-navbar .container-fluid {
            gap: 15px;
        }

        .contactos-navbar .navbar-brand {
            letter-spacing: 0.2px;
            margin-right: 0;
        }

        .navbar-acciones {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .btn-panel-api {
            font-weight: 500;
            padding: 7px 15px;
            transition: background-color 0.2s ease,
                        transform 0.2s ease;
        }

        .btn-panel-api:hover {
            transform: translateY(-1px);
        }

        .navbar-acciones .cerrar-sesion {
            padding: 7px 15px;
        }

        .contactos-header {
            gap: 20px;
        }

        .contactos-header h1 {
            margin-bottom: 8px;
        }

        .buscador-avanzado .form-label {
            font-weight: 500;
            margin-bottom: 8px;
        }

        .contactos-table th {
            white-space: nowrap;
        }

        .contactos-table td {
            vertical-align: middle;
        }

        @media (max-width: 768px) {

            .contactos-navbar .container-fluid {
                flex-wrap: wrap;
            }

            .navbar-acciones {
                width: 100%;
                justify-content: flex-end;
                flex-wrap: wrap;
            }

            .contactos-navbar .navbar-brand {
                font-size: 1rem;
            }

            .contactos-header {
                display: flex;
                flex-direction: column;
                align-items: flex-start;
            }

            .agregar-contacto {
                width: 100%;
                text-align: center;
            }

            .buscador-avanzado .card-body {
                padding: 20px !important;
            }
        }
    </style>
</head>

<body class="contactos-body">

<!-- Barra de navegación -->
<nav class="navbar navbar-dark contactos-navbar">

    <div class="container-fluid px-4">

        <a href="contactos.php" class="navbar-brand fw-bold">
            Directorio de Contactos BA
        </a>

        <div class="navbar-acciones">

            <!-- Nuevo acceso al panel de APIs -->
            <a
                href="api/panel_api.php"
                class="btn btn-outline-light btn-sm btn-panel-api"
            >
                Panel de APIs
            </a>

            <a
                href="logout.php"
                class="btn btn-light btn-sm cerrar-sesion"
            >
                Cerrar sesión
            </a>

        </div>

    </div>

</nav>

<div class="container-fluid px-4 py-5">

    <div class="contactos-header">

        <div>

            <h1 class="fw-bold">
                Contactos
            </h1>

            <p class="text-muted">
                Administra y consulta todos los contactos registrados.
            </p>

        </div>

        <a href="agregar.php" class="btn btn-primary agregar-contacto">
            Agregar contacto
        </a>

    </div>

    <?php if ($mensaje != ""): ?>

        <div
            id="mensajeExito"
            class="alert alert-<?php echo $tipoMensaje; ?> shadow-sm"
        >
            <?php echo htmlspecialchars($mensaje); ?>
        </div>

    <?php endif; ?>

    <!-- Buscador avanzado -->
    <div class="card shadow-sm border-0 mb-4 buscador-avanzado">

        <div class="card-body p-4">

            <h5 class="fw-bold mb-3">
                Buscador avanzado
            </h5>

            <form method="GET">

                <div class="row g-3">

                    <!-- Buscar -->
                    <div class="col-lg-4 col-md-6">

                        <label for="busqueda" class="form-label">
                            Buscar
                        </label>

                        <input
                            type="text"
                            class="form-control"
                            id="busqueda"
                            name="busqueda"
                            placeholder="Nombre, correo o empresa"
                            value="<?php echo htmlspecialchars($busqueda); ?>"
                        >

                    </div>

                    <!-- Ubicación -->
                    <div class="col-lg-2 col-md-6">

                        <label for="ubicacion" class="form-label">
                            Ubicación
                        </label>

                        <select
                            class="form-select"
                            id="ubicacion"
                            name="ubicacion"
                        >

                            <option value="">
                                Todas las ubicaciones
                            </option>

                            <?php while ($ubicacionFila = mysqli_fetch_assoc($resultadoUbicaciones)): ?>

                                <option
                                    value="<?php echo htmlspecialchars($ubicacionFila["ubicacion"]); ?>"
                                    <?php echo ($ubicacion == $ubicacionFila["ubicacion"]) ? "selected" : ""; ?>
                                >
                                    <?php echo htmlspecialchars($ubicacionFila["ubicacion"]); ?>
                                </option>

                            <?php endwhile; ?>

                        </select>

                    </div>

                    <!-- Categoría -->
                    <div class="col-lg-2 col-md-6">

                        <label for="categoria" class="form-label">
                            Categoría
                        </label>

                        <select
                            class="form-select"
                            id="categoria"
                            name="categoria"
                        >

                            <option value="">
                                Todas las categorías
                            </option>

                            <?php foreach ($categorias as $fila): ?>

                                <option
                                    value="<?php echo (int) $fila["id"]; ?>"
                                    <?php echo ($categoria == $fila["id"]) ? "selected" : ""; ?>
                                >
                                    <?php echo htmlspecialchars($fila["nombre"]); ?>
                                </option>

                            <?php endforeach; ?>

                        </select>

                    </div>

                    <!-- Ordenamiento -->
                    <div class="col-lg-2 col-md-6">

                        <label for="orden" class="form-label">
                            Ordenar por
                        </label>

                        <select
                            class="form-select"
                            id="orden"
                            name="orden"
                        >

                            <option value="id" <?php echo ($orden == "id") ? "selected" : ""; ?>>
                                Orden original
                            </option>

                            <option value="nombre" <?php echo ($orden == "nombre") ? "selected" : ""; ?>>
                                Nombre A-Z
                            </option>

                            <option value="fecha" <?php echo ($orden == "fecha") ? "selected" : ""; ?>>
                                Fecha de creación
                            </option>

                            <option value="empresa" <?php echo ($orden == "empresa") ? "selected" : ""; ?>>
                                Empresa A-Z
                            </option>

                        </select>

                    </div>

                    <!-- Buscar -->
                    <div class="col-lg-2 col-md-6 d-flex align-items-end">

                        <button
                            type="submit"
                            class="btn btn-primary w-75 text-nowrap"
                        >
                            Buscar
                        </button>

                    </div>

                </div>

                <?php if (
                    $busqueda != "" ||
                    $ubicacion != "" ||
                    $categoria != "" ||
                    $orden != "id"
                ): ?>

                    <div class="mt-3">

                        <a
                            href="contactos.php"
                            class="btn btn-outline-secondary btn-sm"
                        >
                            Limpiar filtros
                        </a>

                    </div>

                <?php endif; ?>

            </form>

        </div>

    </div>

    <!-- Tabla de contactos -->
    <div class="contactos-card">

        <div class="contactos-card-header">

            <div>

                <h5 class="fw-bold mb-1">
                    Lista de contactos
                </h5>

                <span class="text-muted">
                    Información registrada
                </span>

            </div>

            <div class="contactos-total">
                <?php echo mysqli_num_rows($resultado); ?> contactos
            </div>

        </div>

        <div class="table-responsive">

            <table class="table table-hover contactos-table align-middle mb-0">

                <thead>
                    <tr>

                        <th class="text-center">ID</th>
                        <th>Nombre</th>
                        <th>Teléfono</th>
                        <th>Correo</th>
                        <th>Dirección</th>

                        <?php if ($mostrarEmpresa): ?>
                            <th>Empresa</th>
                        <?php endif; ?>

                        <th>Ubicación</th>
                        <th>Fecha de creación</th>
                        <th>Acciones</th>

                    </tr>
                </thead>

                <tbody>

                    <?php if (mysqli_num_rows($resultado) > 0): ?>

                        <?php while ($contacto = mysqli_fetch_assoc($resultado)): ?>

                            <tr>

                                <td class="text-center">

                                    <span class="contacto-id">
                                        <?php echo $contacto["id"]; ?>
                                    </span>

                                </td>

                                <td class="contacto-nombre">
                                    <?php echo htmlspecialchars($contacto["nombre"]); ?>
                                </td>

                                <td>
                                    <?php echo htmlspecialchars($contacto["telefono"]); ?>
                                </td>

                                <td>
                                    <?php echo htmlspecialchars($contacto["correo"]); ?>
                                </td>

                                <td>
                                    <?php echo htmlspecialchars($contacto["direccion"]); ?>
                                </td>

                                <?php if ($mostrarEmpresa): ?>

                                    <td>
                                        <?php echo htmlspecialchars($contacto["empresa"] ?? ""); ?>
                                    </td>

                                <?php endif; ?>

                                <td>
                                    <?php echo htmlspecialchars($contacto["ubicacion"]); ?>
                                </td>

                                <td>
                                    <?php echo htmlspecialchars($contacto["fecha_creacion"]); ?>
                                </td>

                                <td>

                                    <div class="acciones-contacto">

                                        <a
                                            href="editar.php?id=<?php echo $contacto["id"]; ?>"
                                            class="btn btn-sm btn-outline-primary"
                                        >
                                            Editar
                                        </a>

                                        <a
                                            href="eliminar.php?id=<?php echo $contacto["id"]; ?>"
                                            class="btn btn-sm btn-outline-danger"
                                            onclick="return confirm('¿Está seguro de eliminar este contacto?');"
                                        >
                                            Eliminar
                                        </a>

                                    </div>

                                </td>

                            </tr>

                        <?php endwhile; ?>

                    <?php else: ?>

                        <tr>

                            <td
                                colspan="<?php echo $mostrarEmpresa ? 9 : 8; ?>"
                                class="text-center contactos-vacio"
                            >
                                No hay contactos registrados.
                            </td>

                        </tr>

                    <?php endif; ?>

                </tbody>

            </table>

        </div>

    </div>

</div>

<script>

setTimeout(function() {

    const mensaje = document.getElementById("mensajeExito");

    if (mensaje) {

        mensaje.style.opacity = "0";
        mensaje.style.transition = "opacity 0.5s ease";

        setTimeout(function() {
            mensaje.remove();
        }, 500);

    }

}, 2000);

</script>

</body>
</html>
