<?php
session_start();
require_once '../includes/db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../auth/login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accommodation_id'])) {
    $user_id = $_SESSION['user_id'];
    $accommodation_id = $_POST['accommodation_id'];

    try {
        $stmt = $pdo->prepare("INSERT INTO usuario_alojamiento (id_usuario, id_alojamiento) VALUES (?, ?)");
        $stmt->execute([$user_id, $accommodation_id]);
        $_SESSION['message'] = 'Alojamiento añadido exitosamente.';
    } catch (PDOException $e) {
 
        if ($e->getCode() == '23505') {
            $_SESSION['message'] = 'Este alojamiento ya está en tu lista.';
        } else {
            $_SESSION['message'] = 'Error al añadir alojamiento: ' . $e->getMessage();
        }
    }
}

header('Location: account.php');
exit();
?>