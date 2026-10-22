<?php

$clave1 = "hola67";

$hash1 = password_hash($clave1, PASSWORD_DEFAULT);

echo "Contraseña: hola67<br>";
echo "Hash: " . $hash1 . "<br><br>";


?>