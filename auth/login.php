<?php
session_start();
// Si el usuario ya está logueado, redirigirlo a su cuenta
if (isset($_SESSION['user_id'])) {
    header('Location: ../user/account.php');
    exit();
}

require_once '../includes/db.php'; // Incluimos el archivo de conexión

$message = '';

if (isset($_GET['registered']) && $_GET['registered'] == 'true') {
    $message = '<p style="color: green;">¡Registro exitoso! Ahora puedes iniciar sesión.</p>';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre_usuario_o_email = $_POST['nombre_usuario_o_email'];
    $contrasena = $_POST['contrasena'];

    if (empty($nombre_usuario_o_email) || empty($contrasena)) {
        $message = 'Por favor, ingresa tu nombre de usuario/email y contraseña.';
    } else {
        try {
            // Buscar usuario por nombre de usuario o email
            $stmt = $pdo->prepare("SELECT id_usuario, nombre_usuario, contrasena_hash, rol FROM usuarios WHERE nombre_usuario = ? OR email = ?");
            $stmt->execute([$nombre_usuario_o_email, $nombre_usuario_o_email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user && password_verify($contrasena, $user['contrasena_hash'])) {
                // Contraseña correcta, iniciar sesión
                $_SESSION['user_id'] = $user['id_usuario'];
                $_SESSION['username'] = $user['nombre_usuario'];
                $_SESSION['role'] = $user['rol'];

                header('Location: ../user/account.php'); // Redirigir a la vista de cuenta de usuario
                exit();
            } else {
                $message = 'Nombre de usuario/email o contraseña incorrectos.';
            }
        } catch (PDOException $e) {
            $message = 'Error al iniciar sesión: ' . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión</title>
    <link rel="stylesheet" href="../css/style.css"> <!-- Opcional: si creas un archivo CSS -->
</head>
<body>
    <div class="container">
        <h2>Iniciar Sesión</h2>
        <?php if ($message): ?>
            <?php echo $message; ?>
        <?php endif; ?>
        <form action="login.php" method="POST">
            <div>
                <label for="nombre_usuario_o_email">Nombre de Usuario o Email:</label>
                <input type="text" id="nombre_usuario_o_email" name="nombre_usuario_o_email" required>
            </div>
            <div>
                <label for="contrasena">Contraseña:</label>
                <input type="password" id="contrasena" name="contrasena" required>
            </div>
            <button type="submit">Iniciar Sesión</button>
        </form>
        <p>¿No tienes una cuenta? <a href="register.php">Regístrate aquí</a></p>
    </div>
</body>
</html>