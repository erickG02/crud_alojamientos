<?php
session_start();


if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../auth/login.php'); // Redirigir si no es admin o no está logueado
    exit();
}

require_once '../includes/db.php';

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = $_POST['nombre'];
    $descripcion = $_POST['descripcion'];
    $ubicacion = $_POST['ubicacion'];
    $precio_por_noche = $_POST['precio_por_noche'];
    $imagen_url = $_POST['imagen_url']; // O manejar subida de archivos

    if (empty($nombre) || empty($descripcion) || empty($ubicacion) || empty($precio_por_noche)) {
        $message = '<p style="color: red;">Todos los campos son obligatorios, excepto la URL de la imagen.</p>';
    } else {
        try {
            $stmt = $pdo->prepare("INSERT INTO alojamientos (nombre, descripcion, ubicacion, precio_por_noche, imagen_url) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$nombre, $descripcion, $ubicacion, $precio_por_noche, $imagen_url]);
            $message = '<p style="color: green;">¡Alojamiento agregado exitosamente!</p>';
            // Limpiar los campos del formulario después de un éxito
            $_POST = array(); 
        } catch (PDOException $e) {
            $message = '<p style="color: red;">Error al agregar alojamiento: ' . $e->getMessage() . '</p>';
        }
    }
}


$all_accommodations = [];
try {
    $stmt = $pdo->query("SELECT * FROM alojamientos ORDER BY fecha_creacion DESC");
    $all_accommodations = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error al cargar alojamientos: " . $e->getMessage();
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Administración - Alojamientos</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <div class="container">
        <h1>Panel de Administración</h1>
        <p>Bienvenido, <?php echo htmlspecialchars($_SESSION['username']); ?> (Rol: <?php echo htmlspecialchars($_SESSION['role']); ?>)</p>
        <p><a href="../auth/logout.php" class="logout-btn">Cerrar Sesión</a></p>

        <h2>Agregar Nuevo Alojamiento</h2>
        <?php if ($message): ?>
            <?php echo $message; ?>
        <?php endif; ?>
        <form action="admin_dashboard.php" method="POST">
            <div>
                <label for="nombre">Nombre del Alojamiento:</label>
                <input type="text" id="nombre" name="nombre" required value="<?php echo htmlspecialchars($_POST['nombre'] ?? ''); ?>">
            </div>
            <div>
                <label for="descripcion">Descripción:</label>
                <textarea id="descripcion" name="descripcion" rows="4" required><?php echo htmlspecialchars($_POST['descripcion'] ?? ''); ?></textarea>
            </div>
            <div>
                <label for="ubicacion">Ubicación:</label>
                <input type="text" id="ubicacion" name="ubicacion" required value="<?php echo htmlspecialchars($_POST['ubicacion'] ?? ''); ?>">
            </div>
            <div>
                <label for="precio_por_noche">Precio por Noche:</label>
                <input type="number" id="precio_por_noche" name="precio_por_noche" step="0.01" required value="<?php echo htmlspecialchars($_POST['precio_por_noche'] ?? ''); ?>">
            </div>
            <div>
                <label for="imagen_url">URL de la Imagen (opcional):</label>
                <input type="url" id="imagen_url" name="imagen_url" value="<?php echo htmlspecialchars($_POST['imagen_url'] ?? ''); ?>">
            </div>
            <button type="submit">Agregar Alojamiento</button>
        </form>

        <h2>Todos los Alojamientos (para Administrador)</h2>
        <?php if (empty($all_accommodations)): ?>
            <p>No hay alojamientos registrados aún.</p>
        <?php else: ?>
            <div class="accommodations-grid">
                <?php foreach ($all_accommodations as $accommodation): ?>
                    <div class="accommodation-card">
                        <?php if ($accommodation['imagen_url']): ?>
                            <img src="<?php echo htmlspecialchars($accommodation['imagen_url']); ?>" alt="<?php echo htmlspecialchars($accommodation['nombre']); ?>">
                        <?php else: ?>
                            <img src="https://via.placeholder.com/100x100?text=No+Image" alt="Sin imagen">
                        <?php endif; ?>
                        <div>
                            <h3><?php echo htmlspecialchars($accommodation['nombre']); ?></h3>
                            <p>Ubicación: <?php echo htmlspecialchars($accommodation['ubicacion']); ?></p>
                            <p>Precio/noche: $<?php echo htmlspecialchars(number_format($accommodation['precio_por_noche'], 2)); ?></p>
                            <!-- El administrador NO PUEDE eliminar alojamientos desde aquí, según el requisito -->
                            <!-- Si quisiera añadir una función para editar o eliminar globalmente, sería aquí, pero el requisito dice que SOLO puede AGREGAR -->
                            <!-- <form action="delete_global_accommodation.php" method="POST" style="display:inline;">
                                <input type="hidden" name="accommodation_id" value="<?php echo $accommodation['id_alojamiento']; ?>">
                                <button type="submit" style="background-color: #dc3545;">Eliminar (Global)</button>
                            </form> -->
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>