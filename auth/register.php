<?php
session_start();

if (isset($_SESSION['user_id'])) {
    header('Location: ../user/account.php');
    exit();
}

require_once '../includes/db.php';

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre_usuario = $_POST['nombre_usuario'];
    $email = $_POST['email'];
    $contrasena = $_POST['contrasena'];
    $contrasena_confirm = $_POST['contrasena_confirm'];

    if (empty($nombre_usuario) || empty($email) || empty($contrasena) || empty($contrasena_confirm)) {
        $message = 'Todos los campos son obligatorios.';
    } elseif ($contrasena !== $contrasena_confirm) {
        $message = 'Las contraseñas no coinciden.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = 'El formato del email no es válido.';
    } else {
        
        $contrasena_hash = password_hash($contrasena, PASSWORD_DEFAULT);

        try {
    
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM usuarios WHERE nombre_usuario = ? OR email = ?");
            $stmt->execute([$nombre_usuario, $email]);
            if ($stmt->fetchColumn() > 0) {
                $message = 'El nombre de usuario o el email ya están registrados.';
            } else {
           
                $stmt = $pdo->prepare("INSERT INTO usuarios (nombre_usuario, email, contrasena_hash, rol) VALUES (?, ?, ?, 'cliente')");
                $stmt->execute([$nombre_usuario, $email, $contrasena_hash]);
                $message = '¡Registro exitoso! Ahora puedes iniciar sesión.';
            
                header('Location: login.php?registered=true');
                exit();
            }
        } catch (PDOException $e) {
            $message = 'Error al registrar el usuario: ' . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrarse</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <div class="container">
        <h2>Registrarse</h2>
        <?php if ($message): ?>
            <p style="color: red;"><?php echo $message; ?></p>
        <?php endif; ?>
        <form action="register.php" method="POST">
            <div>
                <label for="nombre_usuario">Nombre de Usuario:</label>
                <input type="text" id="nombre_usuario" name="nombre_usuario" required>
            </div>
            <div>
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div>
                <label for="contrasena">Contraseña:</label>
                <input type="password" id="contrasena" name="contrasena" required>
            </div>
            <div>
                <label for="contrasena_confirm">Confirmar Contraseña:</label>
                <input type="password" id="contrasena_confirm" name="contrasena_confirm" required>
            </div>
            <button type="submit">Registrarse</button>
        </form>
        <p>¿Ya tienes una cuenta? <a href="login.php">Inicia sesión aquí</a></p>
    </div>
</body>
</html>