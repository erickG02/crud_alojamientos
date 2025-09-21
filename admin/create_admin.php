<?php
require_once __DIR__.  '/../includes/db.php';

$nombre_usuario = 'admin1';
$email = 'admin@example.com';
$contrasena = '123456'; 
$rol = 'admin';

$contrasena_hash = password_hash($contrasena, PASSWORD_DEFAULT);

try {
    $stmt = $pdo->prepare("INSERT INTO usuarios (nombre_usuario, email, contrasena_hash, rol) VALUES (?, ?, ?, ?)");
    $stmt->execute([$nombre_usuario, $email, $contrasena_hash, $rol]);
    echo "Administrador 'admin1' creado exitosamente.";
} catch (PDOException $e) {
    echo "Error al crear el administrador: " . $e->getMessage();
}
?>