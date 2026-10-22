<?php
session_start();

require("conexion.php");

$mensaje = "";
$tipoMensaje = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $usuario = trim($_POST["usuario"]);
    $clave = trim($_POST["clave"]);
    $confirmar = trim($_POST["confirmar"]);

    if ($usuario == "" || $clave == "" || $confirmar == "") {

        $mensaje = "Debe completar todos los campos.";
        $tipoMensaje = "danger";

    } elseif ($clave != $confirmar) {

        $mensaje = "Las contraseñas no coinciden.";
        $tipoMensaje = "danger";

    } else {

        $sql = "SELECT id FROM usuarios WHERE username = ?";

        $stmt = mysqli_prepare($conexion, $sql);

        mysqli_stmt_bind_param($stmt, "s", $usuario);
        mysqli_stmt_execute($stmt);

        $resultado = mysqli_stmt_get_result($stmt);

        if (mysqli_num_rows($resultado) > 0) {

            $mensaje = "El usuario ya existe.";
            $tipoMensaje = "danger";

        } else {

            $claveCifrada = password_hash($clave, PASSWORD_DEFAULT); // Cifra la clave con hash

            $sql = "INSERT INTO usuarios (username, clave) VALUES (?, ?)";

            $stmt = mysqli_prepare($conexion, $sql);

            mysqli_stmt_bind_param($stmt, "ss", $usuario, $claveCifrada);

            if (mysqli_stmt_execute($stmt)) {

                header("Location: login.php?mensaje=registrado");
                exit();

            } else {

                $mensaje = "No se pudo registrar el usuario.";
                $tipoMensaje = "danger";
            }
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

    <title>Crear cuenta | Directorio de Contactos</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">

    <link rel="stylesheet" href="estilos.css">

</head>

<body class="login-body">

<div class="login-container">

    <div class="login-card">

        <div class="login-header">

            <h1>Crear cuenta</h1>

            <p>
                Registra un nuevo usuario para acceder al directorio.
            </p>

        </div>

        <?php if ($mensaje != ""): ?>

            <div class="alert alert-<?php echo $tipoMensaje; ?> login-alert">
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

            <div class="login-input mb-3">

                <label for="clave">
                    Contraseña
                </label>

                <div class="input-wrapper">

                    <input
                        type="password"
                        id="clave"
                        name="clave"
                        placeholder="Ingrese su contraseña"
                        autocomplete="new-password"
                    >

                </div>

            </div>

            <div class="login-input mb-4">

                <label for="confirmar">
                    Confirmar contraseña
                </label>

                <div class="input-wrapper">

                    <input
                        type="password"
                        id="confirmar"
                        name="confirmar"
                        placeholder="Repita su contraseña"
                        autocomplete="new-password"
                    >

                </div>

            </div>

            <button
                type="submit"
                class="login-button"
            >
                Registrarse
            </button>

        </form>

        <div class="login-footer">

            <span>¿Ya tienes una cuenta?</span>

            <a href="login.php">
                Inicia sesión
            </a>

        </div>

        <div class="login-footer">

            <a href="index.php">
                ← Volver al inicio
            </a>

        </div>

    </div>

</div>

</body>
</html>