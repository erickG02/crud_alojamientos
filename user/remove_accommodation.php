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
        $stmt = $pdo->prepare("DELETE FROM usuario_alojamiento WHERE id_usuario = ? AND id_alojamiento = ?");
        $stmt->execute([$user_id, $accommodation_id]);
        if ($stmt->rowCount() > 0) {
            $_SESSION['message'] = 'Alojamiento eliminado exitosamente.';
        } else {
            $_SESSION['message'] = 'El alojamiento no se encontró en tu lista.';
        }
    } catch (PDOException $e) {
        $_SESSION['message'] = 'Error al eliminar alojamiento: ' . $e->getMessage();
    }
}

header('Location: account.php');
exit();
?>