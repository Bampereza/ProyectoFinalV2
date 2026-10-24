<?php
session_start();

// Verificar que el usuario haya iniciado sesión
if (!isset($_SESSION["usuario"])) {
    header("Location: ../login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Consultar contactos | Directorio BA</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <style>
        body {
            background-color: #f5f7fb;
        }

        .contactos-navbar {
            background: linear-gradient(90deg, #0d6efd, #6f42c1);
            padding: 16px 0;
        }

        .contenedor {
            max-width: 1150px;
            margin: 45px auto;
        }

        .card {
            border: none;
            border-radius: 16px;
            box-shadow: 0 10px 35px rgba(0,0,0,0.08);
        }
    </style>
</head>

<body>

<!-- Barra de navegación -->
<nav class="navbar navbar-dark contactos-navbar">
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

    <h2 class="fw-bold mb-2">
        Consultar todos los contactos
    </h2>

    <p class="text-muted mb-4">
        Información de los contactos registrados,
        obtenida mediante la API REST del sistema.
    </p>

    <div id="mensaje" class="alert alert-info">
        Consultando contactos...
    </div>

    <section id="resultado"
             class="card p-4"
             style="display: none;">

        <h5 class="fw-bold mb-3">
            Contactos registrados:
            <span id="total">0</span>
        </h5>

        <div class="table-responsive">

            <table class="table table-hover align-middle">

                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Teléfono</th>
                        <th>Correo</th>
                        <th>Empresa</th>
                        <th>Ubicación</th>
                        <th>Categoría</th>
                    </tr>
                </thead>

                <tbody id="tablaContactos"></tbody>

            </table>

        </div>

    </section>

</main>

<script>
const mensaje = document.getElementById("mensaje");
const resultado = document.getElementById("resultado");
const tabla = document.getElementById("tablaContactos");
const total = document.getElementById("total");

async function consultarContactos() {
    try {
        const respuesta = await fetch("api_contactos.php", {
            method: "GET",
            headers: {
                "Accept": "application/json"
            }
        });

        if (!respuesta.ok) {
            throw new Error("Error al consultar la API.");
        }

        const datos = await respuesta.json();

        if (!Array.isArray(datos.contactos)) {
            throw new Error("Formato de respuesta no válido.");
        }

        tabla.replaceChildren();

        const campos = [
            "id",
            "nombre",
            "telefono",
            "correo",
            "empresa",
            "ubicacion",
            "categoria"
        ];

        datos.contactos.forEach(contacto => {
            const fila = document.createElement("tr");

            campos.forEach(campo => {
                const celda = document.createElement("td");

                celda.textContent =
                    contacto[campo] ?? "No disponible";

                fila.appendChild(celda);
            });

            tabla.appendChild(fila);
        });

        total.textContent = datos.contactos.length;

        mensaje.style.display = "none";
        resultado.style.display = "block";

    } catch (error) {
        mensaje.className = "alert alert-danger";
        mensaje.textContent =
            "No se pudieron consultar los contactos.";
    }
}

consultarContactos();
</script>

</body>
</html>
