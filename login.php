<?php
session_start();

require("conexion.php");

$mensaje = "";
$registroExitoso = false;

if (isset($_GET["mensaje"]) && $_GET["mensaje"] == "registrado") {

    $mensaje = "Usuario registrado correctamente. Ahora puedes iniciar sesión.";
    $registroExitoso = true;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $usuario = trim($_POST["usuario"]);
    $clave = trim($_POST["clave"]);

    if ($usuario == "" || $clave == "") {

        $mensaje = "Debe completar todos los campos.";
        $registroExitoso = false;

    } else {

        $sql = "SELECT * FROM usuarios WHERE username = ?";

        $stmt = mysqli_prepare($conexion, $sql);

        mysqli_stmt_bind_param($stmt, "s", $usuario);

        mysqli_stmt_execute($stmt);

        $resultado = mysqli_stmt_get_result($stmt);

        if (mysqli_num_rows($resultado) == 1) {

            $usuarioDB = mysqli_fetch_assoc($resultado);

            if (password_verify($clave, $usuarioDB["clave"])) {

                $_SESSION["usuario"] = $usuario;

                header("Location: contactos.php");
                exit();

            } else {

                $mensaje = "Usuario o contraseña incorrectos.";
                $registroExitoso = false;
            }

        } else {

            $mensaje = "Usuario o contraseña incorrectos.";
            $registroExitoso = false;
        }

        mysqli_stmt_close($stmt);
    }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Iniciar Sesión | Directorio de Contactos</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">

    <link rel="stylesheet" href="estilos.css">

</head>

<body class="login-body">

<div class="login-container">

    <div class="login-card">

        <div class="login-header">

            <h1>
                Bienvenido
            </h1>

            <p>
                Inicia sesión para acceder a tu directorio.
            </p>

        </div>

        <?php if ($mensaje != ""): ?>

         <div
            id="mensajeLogin"
            class="alert <?php echo $registroExitoso ? "alert-success" : "alert-danger"; ?> login-alert text-center"
    >
            <?php echo htmlspecialchars($mensaje); ?>
         </div>

       <?php endif; ?>

        <form method="POST" action="">

            <div class="login-input mb-3">

                <label for="usuario">
                    Usuario
                </label>

                <div class="input-wrapper">

                    <input
                        type="text"
                        id="usuario"
                        name="usuario"
                        placeholder="Ingrese su usuario"
                        autocomplete="username"
                    >

                </div>

            </div>

            <div class="login-input mb-4">

                <label for="clave">
                    Contraseña
                </label>

                <div class="input-wrapper">

                    <input
                        type="password"
                        id="clave"
                        name="clave"
                        placeholder="Ingrese su contraseña"
                        autocomplete="current-password"
                    >

                </div>

            </div>

            <button
                type="submit"
                class="login-button"
            >
                Iniciar sesión
            </button>

        </form>

        <div class="login-footer">

            <span>¿No tienes una cuenta?</span>

            <a href="registro.php">
                Regístrate aquí
            </a>

        </div>

        <div class="login-footer">

            <a href="index.php">
                ← Volver al inicio
            </a>

        </div>

    </div>

</div>
<script>

//Mensaje de login
setTimeout(function() {

    const mensaje = document.getElementById("mensajeLogin");

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