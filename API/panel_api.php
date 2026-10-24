<?php
session_start();

// Verificar que el usuario haya iniciado sesión
if (!isset($_SESSION["usuario"])) {
    header("Location: ../login.php");
    exit();
}

// Ruta del archivo de la API
$endpoint = "api_contactos.php";

// URL base para mostrar en el panel
$httpsDirecto = !empty($_SERVER["HTTPS"])
    && $_SERVER["HTTPS"] !== "off";

$httpsProxy = strtolower(
    trim(explode(",", $_SERVER["HTTP_X_FORWARDED_PROTO"] ?? "")[0])
) === "https";

$protocolo = ($httpsDirecto || $httpsProxy) ? "https" : "http";

$host = $_SERVER["HTTP_HOST"] ?? "localhost";

$ruta = rtrim(
    str_replace("\\", "/", dirname($_SERVER["SCRIPT_NAME"])),
    "/"
);

$urlBase = $protocolo . "://" . $host . $ruta . "/";
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Panel de APIs | Directorio de Contactos BA</title>

    <!-- Bootstrap 5 -->
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <!-- Estilos originales de la aplicación -->
    <link rel="stylesheet" href="../estilos.css">

    <style>
        /* Estilos exclusivos del panel de APIs */

        .api-body {
            background-color: #f5f7fb;
            min-height: 100vh;
            color: #212529;
        }

        /* Misma barra de navegación que contactos.php */
        .contactos-navbar {
            background: linear-gradient(
                90deg,
                #0d6efd,
                #6f42c1
            );
            padding: 16px 0;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.12);
        }

        .contactos-navbar .navbar-brand {
            color: #ffffff;
            font-weight: 700;
            font-size: 1.15rem;
        }

        .btn-volver {
            border: 1px solid rgba(255, 255, 255, 0.9);
            border-radius: 6px;
            color: #ffffff;
            font-weight: 600;
            padding: 7px 14px;
            transition: 0.3s;
        }

        .btn-volver:hover {
            background-color: #ffffff;
            color: #6f42c1;
        }

        /* Contenedor */
        .contenedor-api {
            max-width: 1200px;
            margin: 45px auto;
        }

        .titulo-api {
            font-size: 2.2rem;
            font-weight: 700;
            color: #212529;
            margin-bottom: 8px;
        }

        .descripcion-api {
            color: #6c757d;
            margin-bottom: 0;
        }

        /* Tarjetas */
        .card-api {
            background-color: #ffffff;
            border-radius: 16px;
            border: none;
            overflow: hidden;
            box-shadow: 0 10px 35px rgba(0, 0, 0, 0.08);
        }

        .card-api-header {
            padding: 23px 26px;
            border-bottom: 1px solid #eeeeee;
        }

        .card-api-header h5 {
            font-weight: 700;
            margin-bottom: 5px;
        }

        /* URL base */
        .url-base {
            background-color: #ffffff;
            padding: 23px 26px;
            border-radius: 14px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.05);
            margin: 28px 0;
        }

        .url-base h6 {
            font-weight: 700;
            margin-bottom: 12px;
        }

        .url-base code {
            color: #6f42c1;
            font-size: 0.9rem;
            overflow-wrap: anywhere;
        }

        /* Tabla de endpoints */
        .tabla-api {
            margin-bottom: 0;
        }

        .tabla-api thead th {
            background-color: #f8f9ff;
            color: #495057;
            font-size: 0.85rem;
            font-weight: 700;
            white-space: nowrap;
        }

        .tabla-api th,
        .tabla-api td {
            padding: 18px 20px;
            vertical-align: middle;
        }

        .tabla-api tbody tr:hover td {
            background-color: #f8f9ff;
        }

        /* Métodos HTTP */
        .metodo {
            display: inline-block;
            min-width: 68px;
            padding: 6px 10px;
            border-radius: 6px;
            text-align: center;
            color: #ffffff;
            font-size: 0.75rem;
            font-weight: 700;
        }

        .metodo-get {
            background-color: #198754;
        }

        .metodo-post {
            background-color: #0d6efd;
        }

        .metodo-put {
            background-color: #e5a000;
        }

        .metodo-delete {
            background-color: #dc3545;
        }

        /* Enlaces */
        .ruta-api {
            color: #0d6efd;
            font-family: monospace;
            font-size: 0.9rem;
            text-decoration: none;
            overflow-wrap: anywhere;
        }

        a.ruta-api:hover {
            color: #6f42c1;
            text-decoration: underline;
        }

        /* Indicadores API key */
        .api-key-si {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 6px;
            background-color: #fff3cd;
            color: #856404;
            font-size: 0.8rem;
            font-weight: 600;
        }

        .api-key-no {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 6px;
            background-color: #d1e7dd;
            color: #0f5132;
            font-size: 0.8rem;
            font-weight: 600;
        }

        /* Información adicional */
        .informacion-api {
            padding: 26px;
            margin-top: 28px;
        }

        .informacion-api h5 {
            font-weight: 700;
            margin-bottom: 20px;
        }

        .informacion-api p {
            margin-bottom: 12px;
        }

        .footer-api {
            text-align: center;
            color: #6c757d;
            font-size: 0.85rem;
            margin: 35px 0;
        }

        @media (max-width: 768px) {

            .contactos-navbar .container-fluid {
                gap: 12px;
            }

            .contactos-navbar .navbar-brand {
                font-size: 1rem;
            }

            .contenedor-api {
                margin-top: 25px;
            }

            .titulo-api {
                font-size: 1.8rem;
            }

            .tabla-api th,
            .tabla-api td {
                padding: 14px;
            }

            .card-api-header,
            .informacion-api {
                padding: 20px;
            }
        }
    </style>
</head>

<body class="api-body">

<!-- Barra de navegación -->
<nav class="navbar navbar-dark contactos-navbar">

    <div class="container-fluid px-4">

        <a
            href="../contactos.php"
            class="navbar-brand fw-bold"
        >
            Directorio de Contactos BA
        </a>

        <a
            href="../contactos.php"
            class="btn btn-outline-light btn-sm btn-volver"
        >
            Volver a contactos
        </a>

    </div>

</nav>

<!-- Contenido principal -->
<main class="container contenedor-api">

    <!-- Título -->
    <div class="mb-4">

        <h1 class="titulo-api">
            Panel de APIs
        </h1>

        <p class="descripcion-api">
            Servicios REST para consultar y administrar
            los contactos registrados en el sistema.
        </p>

    </div>

    <!-- Dirección de la API -->
    <div class="url-base">

        <h6>
            URL base de la API
        </h6>

        <code>
            <?php
            echo htmlspecialchars(
                $urlBase . $endpoint,
                ENT_QUOTES,
                "UTF-8"
            );
            ?>
        </code>

    </div>

    <!-- Tabla de endpoints -->
    <section class="card-api">

        <div class="card-api-header">

            <h5>
                Endpoints de contactos
            </h5>

            <span class="text-muted">
                Métodos HTTP para consultar, agregar,
                actualizar y eliminar contactos.
            </span>

        </div>

        <div class="table-responsive">

            <table class="table table-hover tabla-api">

                <thead>
                    <tr>
                        <th>Método</th>
                        <th>Endpoint</th>
                        <th>Descripción</th>
                        <th>API Key</th>
                    </tr>
                </thead>

                <tbody>

                    <!-- GET: listar contactos -->
                    <tr>
                        <td>
                            <span class="metodo metodo-get">
                                GET
                            </span>
                        </td>

                        <td>
                            <a
                                href="api_contactos.php"
                                target="_blank"
                                rel="noopener noreferrer"
                                class="ruta-api"
                            >
                                api_contactos.php
                            </a>
                        </td>

                        <td>
                            Consultar todos los contactos registrados.
                        </td>

                        <td>
                            <span class="api-key-no">
                                No requerida
                            </span>
                        </td>
                    </tr>

                    <!-- GET: contacto por ID -->
                    <tr>
                        <td>
                            <span class="metodo metodo-get">
                                GET
                            </span>
                        </td>

                        <td>
                            <a
                               href="consultar_contacto.php"
                               target="_blank"
                               rel="noopener noreferrer"
                               class="ruta-api"
                            >
                              Consultar contacto por ID
                           </a>
                        </td>

                        <td>
                            Consultar la información de un contacto
                            mediante su ID.
                        </td>

                        <td>
                            <span class="api-key-no">
                                No requerida
                            </span>
                        </td>
                    </tr>

                    <!-- POST: agregar contacto -->
                    <tr>
                        <td>
                            <span class="metodo metodo-post">
                                POST
                            </span>
                        </td>

                        <td>
                            <code class="ruta-api">
                                api_contactos.php
                            </code>
                        </td>

                        <td>
                            Registrar un nuevo contacto mediante
                            datos JSON.
                        </td>

                        <td>
                            <span class="api-key-si">
                                Requerida
                            </span>
                        </td>
                    </tr>

                    <!-- PUT: editar contacto -->
                    <tr>
                        <td>
                            <span class="metodo metodo-put">
                                PUT
                            </span>
                        </td>

                        <td>
                            <code class="ruta-api">
                                api_contactos.php?id=2
                            </code>
                        </td>

                        <td>
                            Actualizar la información de un contacto
                            existente.
                        </td>

                        <td>
                            <span class="api-key-si">
                                Requerida
                            </span>
                        </td>
                    </tr>

                    <!-- DELETE: eliminar contacto -->
                    <tr>
                        <td>
                            <span class="metodo metodo-delete">
                                DELETE
                            </span>
                        </td>

                        <td>
                            <code class="ruta-api">
                                api_contactos.php?id=2
                            </code>
                        </td>

                        <td>
                            Eliminar un contacto de la base de datos.
                        </td>

                        <td>
                            <span class="api-key-si">
                                Requerida
                            </span>
                        </td>
                    </tr>

                </tbody>

            </table>

        </div>

    </section>

    <!-- Acceso a la API externa de Wikidata -->
    <section class="card-api p-4 mt-4">

        <h5 class="fw-bold mb-2">
            API externa - Consulta de empresas
        </h5>

        <p class="text-muted mb-3">
            Consulta información de empresas mediante
            la API pública de Wikidata.
        </p>

        <div>
            <a
                href="consultar_empresaWikidata.php"
                class="btn btn-primary"
            >
                Consultar empresas
            </a>
        </div>

    </section>

    <!-- Información de la API -->
    <section class="card-api informacion-api">

        <h5>
            Información de la API REST
        </h5>

        <p>
            <strong>Nombre:</strong>
            API de Contactos BA
        </p>

        <p>
            <strong>Formato de respuesta:</strong>
            JSON
        </p>

        <p>
            <strong>Tecnologías:</strong>
            PHP, MySQLi y MySQL
        </p>

        <p>
            <strong>Métodos HTTP:</strong>
            GET, POST, PUT y DELETE
        </p>

        <p>
            <strong>Seguridad:</strong>
            API Key para operaciones POST, PUT y DELETE
        </p>

        <p class="mb-0">
            <strong>Herramienta de pruebas:</strong>
            Postman
        </p>

    </section>

    <footer class="footer-api">
        Directorio de Contactos BA |
        Panel de servicios REST
    </footer>

</main>

</body>
</html>
