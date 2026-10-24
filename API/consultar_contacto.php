<?php
session_start();

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

    <title>Consultar contacto | Directorio BA</title>

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
            max-width: 850px;
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
        Consultar contacto por ID
    </h2>

    <p class="text-muted mb-4">
        Ingresa el ID de un contacto para consultar
        su información mediante la API REST.
    </p>

    <section class="card p-4 mb-4">

        <form id="formBusqueda" class="row g-3">

            <div class="col-md-9">
                <label for="contactoId"
                       class="form-label">
                    ID del contacto
                </label>

                <input
                    type="number"
                    id="contactoId"
                    class="form-control"
                    placeholder="Ejemplo: 18"
                    min="1"
                    step="1"
                    required
                >
            </div>

            <div class="col-md-3 d-flex align-items-end">
                <button
                    type="submit"
                    class="btn btn-primary w-100">
                    Buscar
                </button>
            </div>

        </form>

    </section>

    <div id="mensaje" role="status"></div>

    <section id="resultado"
             class="card p-4"
             style="display: none;">

        <h5 class="fw-bold mb-3">
            Información del contacto
        </h5>

        <div class="table-responsive">
            <table class="table table-bordered">
                <tbody id="tablaContacto"></tbody>
            </table>
        </div>

    </section>

</main>

<script>
const formulario = document.getElementById("formBusqueda");
const mensaje = document.getElementById("mensaje");
const resultado = document.getElementById("resultado");
const tabla = document.getElementById("tablaContacto");

const campos = {
    id: "ID",
    nombre: "Nombre",
    telefono: "Teléfono",
    correo: "Correo",
    direccion: "Dirección",
    empresa: "Empresa",
    ubicacion: "Ubicación",
    categoria: "Categoría"
};

formulario.addEventListener("submit", async function(evento) {
    evento.preventDefault();

    const id = document.getElementById("contactoId").value;

    if (!/^[1-9]\d*$/.test(id)) {
        mensaje.textContent = "Ingresa un ID válido.";
        return;
    }

    resultado.style.display = "none";
    tabla.replaceChildren();

    mensaje.className = "alert alert-info";
    mensaje.textContent = "Consultando contacto...";

    try {
        const respuesta = await fetch(
            "api_contactos.php?id=" + encodeURIComponent(id),
            {
                method: "GET",
                headers: {
                    "Accept": "application/json"
                }
            }
        );

        const datos = await respuesta.json();

        if (!respuesta.ok || datos.estado === "error") {
            mensaje.className = "alert alert-warning";
            mensaje.textContent =
                datos.mensaje || "No se encontró el contacto.";
            return;
        }

        const contacto =
            datos.contacto ??
            datos.contactos?.[0] ??
            datos;

        if (!contacto || typeof contacto !== "object"
            || !("id" in contacto)) {
            throw new Error("Respuesta de contacto no válida.");
        }

        for (const [campo, etiqueta] of Object.entries(campos)) {
            const fila = document.createElement("tr");
            const encabezado = document.createElement("th");
            const valor = document.createElement("td");

            encabezado.textContent = etiqueta;
            valor.textContent = contacto[campo] ?? "No disponible";

            fila.append(encabezado, valor);
            tabla.appendChild(fila);
        }

        mensaje.className = "";
        mensaje.textContent = "";
        resultado.style.display = "block";

    } catch (error) {
        mensaje.className = "alert alert-danger";
        mensaje.textContent =
            "No fue posible consultar el contacto.";
    }
});
</script>

</body>
</html>
