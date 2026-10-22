<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Directorio de Contactos</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="estilos.css">
</head>

<body>

<nav class="navbar navbar-expand-lg navbar-dark navbar-principal sticky-top">
    <div class="container">
        <a class="navbar-brand fw-bold" href="index.php">
            Directorio de Contactos BA
        </a>

        <button
            class="navbar-toggler"
            type="button"
            data-bs-toggle="collapse"
            data-bs-target="#menuPrincipal"
        >
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="menuPrincipal">

            <ul class="navbar-nav ms-auto align-items-lg-center">

                <li class="nav-item">
                    <a class="nav-link active" href="#inicio">
                        Inicio
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="#funciones">
                        Funciones
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="#informacion">
                        Información
                    </a>
                </li>

                <li class="nav-item ms-lg-3 mt-2 mt-lg-0">
                    <a href="login.php" class="btn btn-light btn-login">
                        Iniciar sesión
                    </a>
                </li>

            </ul>

        </div>

    </div>
</nav>

<section class="hero" id="inicio">
    <div class="container">
        <div class="row align-items-center g-5">
            <div class="col-lg-6 hero-content">

                <span class="badge badge-hero mb-3">
                    Gestión sencilla y organizada
                </span>

                <h1 class="display-4 fw-bold">
                    Todos tus contactos en un solo lugar
                </h1>

                <p class="lead mt-4">
                    Guarda, consulta, modifica y administra la información
                    de tus contactos de una manera sencilla, rápida y organizada.
                </p>

                <div class="mt-4">

                    <a href="#funciones" class="btn btn-outline-light btn-lg px-4">
                        Ver funciones
                    </a>

                </div>

            </div>

            <div class="col-lg-6">
                <div class="hero-image">
                    <div class="image-overlay"></div>
                    <div class="image-content">

                        <h3>
                            Tu información organizada
                        </h3>

                        <p>
                            Administra tus contactos de forma práctica
                            y mantén toda tu información en un solo lugar.
                        </p>

                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="functions-section py-5" id="funciones">
    <div class="container py-4">
        <div class="text-center section-heading mb-5">
            <span class="section-label">
                FUNCIONES
            </span>

            <h2 class="fw-bold mt-2">
                Todo lo necesario para administrar tus contactos
            </h2>

            <p class="text-muted">
                Un sistema sencillo para mantener tu información organizada.
            </p>

        </div>

        <div class="row g-4">
            <div class="col-md-4">
                <div class="card feature-card p-4">
                    <div class="feature-icon">
                        +
                    </div>

                    <h4 class="fw-bold">
                        Agregar contactos
                    </h4>

                    <p class="text-muted mb-0">
                        Registra nuevos contactos ingresando su nombre,
                        teléfono, correo electrónico y dirección.
                    </p>

                </div>

            </div>

            <div class="col-md-4">
                <div class="card feature-card p-4">
                    <div class="feature-icon">
                        ✓
                    </div>

                    <h4 class="fw-bold">
                        Consultar información
                    </h4>

                    <p class="text-muted mb-0">
                        Visualiza todos los contactos registrados
                        de forma clara y organizada.
                    </p>

                </div>

            </div>

            <div class="col-md-4">
                <div class="card feature-card p-4">
                    <div class="feature-icon">
                        E
                    </div>

                    <h4 class="fw-bold">
                        Editar y eliminar
                    </h4>

                    <p class="text-muted mb-0">
                        Actualiza la información de tus contactos
                        o elimina registros cuando sea necesario.
                    </p>

                </div>
            </div>
        </div>
    </div>
</section>

<section class="about-section py-5" id="informacion">
    <div class="container py-4">
        <div class="row align-items-center g-5">
            <div class="col-lg-6">
                <div class="about-box p-5">
                    <span class="section-label">
                        SOBRE EL SISTEMA
                    </span>

                    <h2 class="fw-bold mt-2">
                        Una manera práctica de organizar tu información
                    </h2>

                    <p class="text-muted mt-3">
                        El Directorio de Contactos permite almacenar
                        y administrar información de diferentes personas
                        mediante una interfaz sencilla y moderna.
                    </p>

                    <p class="text-muted mb-0">
                        El sistema utiliza PHP y MySQL para gestionar
                        la información y permite realizar las operaciones
                        principales de un CRUD.
                    </p>

                </div>

            </div>

            <div class="col-lg-6">

                <h3 class="fw-bold">
                    ¿Cómo funciona?
                </h3>

                <div class="mt-4">

                    <div class="info-item">

                        <div class="info-number">
                            01
                        </div>

                        <div>

                            <h5 class="fw-bold">
                                Inicia sesión
                            </h5>

                            <p class="text-muted">
                                Accede al sistema utilizando tus credenciales.
                            </p>

                        </div>

                    </div>

                    <div class="info-item">

                        <div class="info-number">
                            02
                        </div>

                        <div>

                            <h5 class="fw-bold">
                                Administra tus contactos
                            </h5>

                            <p class="text-muted">
                                Agrega, consulta y modifica los contactos registrados.
                            </p>

                        </div>

                    </div>

                    <div class="info-item">

                        <div class="info-number">
                            03
                        </div>

                        <div>

                            <h5 class="fw-bold">
                                Mantén tu información actualizada
                            </h5>

                            <p class="text-muted">
                                Elimina registros que ya no sean necesarios.
                            </p>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<footer class="footer py-4">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-6">

                <strong>
                    Directorio de Contactos
                </strong>

                <p class="mb-0 mt-1">
                    Sistema de gestión de contactos.
                </p>

            </div>

            <div class="col-md-6 text-md-end mt-3 mt-md-0">

                <small>
                    Proyecto desarrollado con PHP, MySQL y Bootstrap.
                </small>

            </div>
        </div>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>