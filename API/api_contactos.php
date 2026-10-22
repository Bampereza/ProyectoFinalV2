<?php

header("Content-Type: application/json; charset=UTF-8");
header("Cache-Control: no-store");

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

// Respuestas JSON
function responder($codigo, $datos = null)
{
    http_response_code($codigo);

    if ($codigo === 204) {
        exit;
    }

    echo json_encode(
        $datos,
        JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT
    );

    exit;
}

// Obtener los datos enviados en JSON
function obtenerJSON()
{
    $contenido = file_get_contents("php://input");
    $datos = json_decode($contenido, true);

    if (
        trim($contenido) === "" ||
        json_last_error() !== JSON_ERROR_NONE ||
        !is_array($datos) ||
        array_is_list($datos)
    ) {
        responder(400, [
            "estado" => "error",
            "mensaje" => "Debe enviar un objeto JSON válido."
        ]);
    }

    return $datos;
}

// Verificar ID de la URL
function obtenerID()
{
    $id = $_GET["id"] ?? null;

    if (
        !is_string($id) ||
        !ctype_digit($id) ||
        (int) $id <= 0
    ) {
        responder(400, [
            "estado" => "error",
            "mensaje" => "Debe proporcionar un ID válido."
        ]);
    }

    return (int) $id;
}

// Validar los campos del contacto
function validarContacto($datos)
{
    $campos = [
        "nombre",
        "telefono",
        "correo",
        "direccion",
        "empresa",
        "ubicacion",
        "usuario_id",
        "categoria_id"
    ];

    foreach ($campos as $campo) {
        if (
            !isset($datos[$campo]) ||
            !is_scalar($datos[$campo]) ||
            trim((string) $datos[$campo]) === ""
        ) {
            responder(422, [
                "estado" => "error",
                "mensaje" => "El campo '$campo' es obligatorio."
            ]);
        }
    }

    $datos["nombre"] = trim((string) $datos["nombre"]);
    $datos["telefono"] = trim((string) $datos["telefono"]);
    $datos["correo"] = trim((string) $datos["correo"]);
    $datos["direccion"] = trim((string) $datos["direccion"]);
    $datos["empresa"] = trim((string) $datos["empresa"]);
    $datos["ubicacion"] = trim((string) $datos["ubicacion"]);

    $limites = [
        "nombre" => 100,
        "telefono" => 30,
        "correo" => 150,
        "direccion" => 200,
        "empresa" => 100,
        "ubicacion" => 150
    ];

    foreach ($limites as $campo => $limite) {
        if (mb_strlen($datos[$campo], "UTF-8") > $limite) {
            responder(422, [
                "estado" => "error",
                "mensaje" => "El campo '$campo' supera el límite permitido."
            ]);
        }
    }

    if (!ctype_digit($datos["telefono"])) {
        responder(422, [
            "estado" => "error",
            "mensaje" => "El teléfono debe contener solo números."
        ]);
    }

    if (!filter_var($datos["correo"], FILTER_VALIDATE_EMAIL)) {
        responder(422, [
            "estado" => "error",
            "mensaje" => "El correo electrónico no es válido."
        ]);
    }

    foreach (["usuario_id", "categoria_id"] as $campo) {
        $valor = filter_var(
            $datos[$campo],
            FILTER_VALIDATE_INT,
            ["options" => ["min_range" => 1]]
        );

        if ($valor === false) {
            responder(422, [
                "estado" => "error",
                "mensaje" => "El campo '$campo' debe ser un ID válido."
            ]);
        }

        $datos[$campo] = $valor;
    }

    return $datos;
}

// Verificar que exista un registro relacionado
function existeRegistro($conexion, $tabla, $id)
{
    // Solo se usan nombres internos definidos por el programa.
    if (!in_array($tabla, ["usuarios", "categorias"], true)) {
        return false;
    }

    $sql = "SELECT id FROM $tabla WHERE id = ? LIMIT 1";

    $stmt = mysqli_prepare($conexion, $sql);
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);

    $resultado = mysqli_stmt_get_result($stmt);
    $existe = mysqli_num_rows($resultado) > 0;

    mysqli_stmt_close($stmt);

    return $existe;
}

// Comprobar las claves foráneas
function validarRelaciones($conexion, $datos)
{
    if (!existeRegistro($conexion, "usuarios", $datos["usuario_id"])) {
        responder(422, [
            "estado" => "error",
            "mensaje" => "El usuario indicado no existe."
        ]);
    }

    if (!existeRegistro($conexion, "categorias", $datos["categoria_id"])) {
        responder(422, [
            "estado" => "error",
            "mensaje" => "La categoría indicada no existe."
        ]);
    }
}

// Verificar existencia de un contacto
function existeContacto($conexion, $id)
{
    $stmt = mysqli_prepare(
        $conexion,
        "SELECT id FROM contacto WHERE id = ? LIMIT 1"
    );

    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);

    $resultado = mysqli_stmt_get_result($stmt);
    $existe = mysqli_num_rows($resultado) > 0;

    mysqli_stmt_close($stmt);

    return $existe;
}

// Método HTTP recibido
$metodo = $_SERVER["REQUEST_METHOD"];

if (!in_array($metodo, ["GET", "POST", "PUT", "DELETE"], true)) {
    header("Allow: GET, POST, PUT, DELETE");

    responder(405, [
        "estado" => "error",
        "mensaje" => "Método HTTP no permitido."
    ]);
}

// Proteger operaciones de escritura
if ($metodo !== "GET") {

    $apiKeyConfigurada = getenv("API_KEY");
    $apiKeyRecibida = $_SERVER["HTTP_X_API_KEY"] ?? "";

    if (
        !is_string($apiKeyConfigurada) ||
        $apiKeyConfigurada === ""
    ) {
        responder(503, [
            "estado" => "error",
            "mensaje" => "La API key no está configurada en el servidor."
        ]);
    }

    if (
        !is_string($apiKeyRecibida) ||
        !hash_equals($apiKeyConfigurada, $apiKeyRecibida)
    ) {
        responder(401, [
            "estado" => "error",
            "mensaje" => "API key inválida o no proporcionada."
        ]);
    }
}

try {

    // Conexión MySQLi del proyecto
    require_once dirname(__DIR__) . "/conexion.php";

    if (!isset($conexion) || !($conexion instanceof mysqli)) {
        throw new RuntimeException("Conexión MySQLi no disponible.");
    }

    // ========================================
    // GET - LISTAR O CONSULTAR UN CONTACTO
    // ========================================
    if ($metodo === "GET") {

        $sql = "SELECT
                    c.id,
                    c.nombre,
                    c.telefono,
                    c.correo,
                    c.direccion,
                    c.empresa,
                    c.ubicacion,
                    c.fecha_creacion,
                    c.foto_perfil,
                    c.usuario_id,
                    c.categoria_id,
                    cat.nombre AS categoria
                FROM contacto c
                LEFT JOIN categorias cat
                    ON c.categoria_id = cat.id";

        if (isset($_GET["id"])) {

            $id = obtenerID();

            $sql .= " WHERE c.id = ?";

            $stmt = mysqli_prepare($conexion, $sql);
            mysqli_stmt_bind_param($stmt, "i", $id);
            mysqli_stmt_execute($stmt);

            $resultado = mysqli_stmt_get_result($stmt);
            $contacto = mysqli_fetch_assoc($resultado);

            mysqli_stmt_close($stmt);

            if (!$contacto) {
                responder(404, [
                    "estado" => "error",
                    "mensaje" => "Contacto no encontrado."
                ]);
            }

            responder(200, [
                "estado" => "exito",
                "contacto" => $contacto
            ]);
        }

        $sql .= " ORDER BY c.id ASC";

        $stmt = mysqli_prepare($conexion, $sql);
        mysqli_stmt_execute($stmt);

        $resultado = mysqli_stmt_get_result($stmt);
        $contactos = [];

        while ($fila = mysqli_fetch_assoc($resultado)) {
            $contactos[] = $fila;
        }

        mysqli_stmt_close($stmt);

        responder(200, [
            "estado" => "exito",
            "total" => count($contactos),
            "contactos" => $contactos
        ]);
    }

    // ========================================
    // POST - CREAR CONTACTO
    // ========================================
    if ($metodo === "POST") {

        $datos = validarContacto(obtenerJSON());
        validarRelaciones($conexion, $datos);

        $sql = "INSERT INTO contacto
                (
                    nombre,
                    telefono,
                    correo,
                    direccion,
                    empresa,
                    ubicacion,
                    usuario_id,
                    categoria_id
                )
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = mysqli_prepare($conexion, $sql);

        mysqli_stmt_bind_param(
            $stmt,
            "ssssssii",
            $datos["nombre"],
            $datos["telefono"],
            $datos["correo"],
            $datos["direccion"],
            $datos["empresa"],
            $datos["ubicacion"],
            $datos["usuario_id"],
            $datos["categoria_id"]
        );

        mysqli_stmt_execute($stmt);

        $nuevoID = mysqli_insert_id($conexion);

        mysqli_stmt_close($stmt);

        responder(201, [
            "estado" => "exito",
            "mensaje" => "Contacto agregado correctamente.",
            "id" => $nuevoID
        ]);
    }

    // ========================================
    // PUT - ACTUALIZAR CONTACTO
    // ========================================
    if ($metodo === "PUT") {

        $id = obtenerID();

        if (!existeContacto($conexion, $id)) {
            responder(404, [
                "estado" => "error",
                "mensaje" => "Contacto no encontrado."
            ]);
        }

        $datos = validarContacto(obtenerJSON());
        validarRelaciones($conexion, $datos);

        $sql = "UPDATE contacto SET
                    nombre = ?,
                    telefono = ?,
                    correo = ?,
                    direccion = ?,
                    empresa = ?,
                    ubicacion = ?,
                    usuario_id = ?,
                    categoria_id = ?
                WHERE id = ?";

        $stmt = mysqli_prepare($conexion, $sql);

        mysqli_stmt_bind_param(
            $stmt,
            "ssssssiii",
            $datos["nombre"],
            $datos["telefono"],
            $datos["correo"],
            $datos["direccion"],
            $datos["empresa"],
            $datos["ubicacion"],
            $datos["usuario_id"],
            $datos["categoria_id"],
            $id
        );

        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        responder(200, [
            "estado" => "exito",
            "mensaje" => "Contacto actualizado correctamente.",
            "id" => $id
        ]);
    }

    // ========================================
    // DELETE - ELIMINAR CONTACTO
    // ========================================
    if ($metodo === "DELETE") {

        $id = obtenerID();

        $stmt = mysqli_prepare(
            $conexion,
            "DELETE FROM contacto WHERE id = ?"
        );

        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);

        $filasAfectadas = mysqli_stmt_affected_rows($stmt);

        mysqli_stmt_close($stmt);

        if ($filasAfectadas === 0) {
            responder(404, [
                "estado" => "error",
                "mensaje" => "Contacto no encontrado."
            ]);
        }

        // 204 significa eliminación exitosa, sin cuerpo.
        responder(204);
    }

} catch (Throwable $e) {

    error_log("Error API contactos: " . $e->getMessage());

    responder(500, [
        "estado" => "error",
        "mensaje" => "Ocurrió un error interno en la API."
    ]);
}
