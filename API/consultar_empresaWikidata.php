<?php
session_start();

if (!isset($_SESSION["usuario"])) {
    header("Location: ../login.php");
    exit();
}

// Consultar la API externa de Wikidata
function consultarWikidata($parametros)
{
    $url = "https://www.wikidata.org/w/api.php?"
        . http_build_query($parametros);

    $contexto = stream_context_create([
        "http" => [
            "method" => "GET",
            "timeout" => 10,
            "header" =>
                "Accept: application/json\r\n" .
                "User-Agent: DirectorioContactosBA/1.0 (https://proyectofinalv2-production-7190.up.railway.app/)\r\n"
        ]
    ]);

    $respuesta = @file_get_contents($url, false, $contexto);

    if ($respuesta === false) {
        return null;
    }

    $datos = json_decode($respuesta, true);

    return is_array($datos) ? $datos : null;
}

function escapar($texto)
{
    return htmlspecialchars(
        (string) $texto,
        ENT_QUOTES,
        "UTF-8"
    );
}

$busqueda = trim($_GET["empresa"] ?? "");
$resultados = [];
$error = "";

if ($busqueda !== "") {

    if (mb_strlen($busqueda) > 80) {
        $error = "El nombre de la empresa es demasiado largo.";
    } else {

        // Buscar por nombre
        $datos = consultarWikidata([
            "action" => "wbsearchentities",
            "search" => $busqueda,
            "language" => "es",
            "uselang" => "es",
            "type" => "item",
            "limit" => 8,
            "format" => "json"
        ]);

        if ($datos === null || !isset($datos["search"])) {
            $error = "No se pudo consultar Wikidata.";
        } else {

            $resultados = $datos["search"];
            $ids = [];

            foreach ($resultados as $resultado) {
                if (preg_match("/^Q[0-9]+$/", $resultado["id"] ?? "")) {
                    $ids[] = $resultado["id"];
                }
            }

            // Consultar sitios web oficiales disponibles
            if (!empty($ids)) {

                $detalles = consultarWikidata([
                    "action" => "wbgetentities",
                    "ids" => implode("|", $ids),
                    "props" => "claims",
                    "format" => "json"
                ]);

                foreach ($resultados as &$resultado) {

                    $id = $resultado["id"] ?? "";
                    $resultado["sitio_web"] = "";

                    $paginas = $detalles["entities"][$id]
                        ["claims"]["P856"] ?? [];

                    foreach ($paginas as $pagina) {

                        $sitio = $pagina["mainsnak"]
                            ["datavalue"]["value"] ?? "";

                        if (
                            is_string($sitio) &&
                            filter_var($sitio, FILTER_VALIDATE_URL) &&
                            parse_url($sitio, PHP_URL_SCHEME) === "https"
                        ) {
                            $resultado["sitio_web"] = $sitio;
                            break;
                        }
                    }
                }

                unset($resultado);
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Consultar Empresas | Directorio BA</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <style>
        body {
            background: #f5f7fb;
        }

        .navbar-empresa {
            background: linear-gradient(90deg, #0d6efd, #6f42c1);
            padding: 16px 0;
        }

        .contenedor {
            max-width: 1100px;
            margin: 45px auto;
        }

        .card {
            border: none;
            border-radius: 14px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.07);
        }
    </style>
</head>

<body>

<nav class="navbar navbar-dark navbar-empresa">
    <div class="container-fluid px-4">

        <a href="../contactos.php"
           class="navbar-brand fw-bold">
            Directorio de Contactos BA
        </a>

        <a href="panel_api.php"
           class="btn btn-outline-light btn-sm">
            Volver al Panel de APIs
        </a>

    </div>
</nav>

<main class="container contenedor">

    <h2 class="fw-bold">Consultar información de empresas</h2>

    <p class="text-muted">
        Consulta de información empresarial mediante
        la API pública de Wikidata.
    </p>

    <div class="card p-4 mb-4">

        <form method="GET" class="row g-3">

            <div class="col-md-9">
                <label class="form-label">
                    Nombre de la empresa
                </label>

                <input
                    type="text"
                    name="empresa"
                    class="form-control"
                    placeholder="Ejemplo: Microsoft"
                    value="<?= escapar($busqueda) ?>"
                    maxlength="80"
                    required
                >
            </div>

            <div class="col-md-3 d-flex align-items-end">
                <button
                    type="submit"
                    class="btn btn-primary w-100">
                    Buscar empresa
                </button>
            </div>

        </form>

    </div>

    <?php if ($error !== ""): ?>

        <div class="alert alert-danger">
            <?= escapar($error) ?>
        </div>

    <?php elseif ($busqueda !== ""): ?>

        <div class="card p-4">

            <h5 class="fw-bold mb-3">
                Resultados encontrados:
                <?= count($resultados) ?>
            </h5>

            <?php if (empty($resultados)): ?>

                <div class="alert alert-warning mb-0">
                    No se encontraron coincidencias.
                </div>

            <?php else: ?>

                <div class="table-responsive">

                    <table class="table table-hover align-middle">

                        <thead class="table-light">
                            <tr>
                                <th>Empresa</th>
                                <th>Descripción</th>
                                <th>Sitio web</th>
                                <th>Información</th>
                            </tr>
                        </thead>

                        <tbody>

                        <?php foreach ($resultados as $empresa): ?>

                            <tr>

                                <td class="fw-semibold">
                                    <?= escapar($empresa["label"] ?? "") ?>
                                </td>

                                <td>
                                    <?= escapar(
                                        $empresa["description"] ?? "Sin descripción"
                                    ) ?>
                                </td>

                                <td>

                                    <?php if (!empty($empresa["sitio_web"])): ?>

                                        <a
                                            href="<?= escapar($empresa["sitio_web"]) ?>"
                                            target="_blank"
                                            rel="noopener noreferrer">
                                            Visitar sitio
                                        </a>

                                    <?php else: ?>

                                        <span class="text-muted">
                                            No disponible
                                        </span>

                                    <?php endif; ?>

                                </td>

                                <td>
                                    <?php
                                        $id = $empresa["id"] ?? "";
                                    ?>

                                    <?php if (preg_match("/^Q[0-9]+$/", $id)): ?>

                                        <a
                                            href="https://www.wikidata.org/wiki/<?= escapar($id) ?>"
                                            target="_blank"
                                            rel="noopener noreferrer"
                                            class="btn btn-sm btn-outline-primary">
                                            Ver ficha
                                        </a>

                                    <?php endif; ?>
                                </td>

                            </tr>

                        <?php endforeach; ?>

                        </tbody>

                    </table>

                </div>

            <?php endif; ?>

        </div>

    <?php endif; ?>

    <p class="text-muted text-center mt-4 small">
        Información proporcionada por Wikidata.
        Los datos pueden estar incompletos o desactualizados.
    </p>

</main>

</body>
</html>
