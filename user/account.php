<?php
session_start();

// Verificar si el usuario está logueado
if (!isset($_SESSION['user_id'])) {
    header('Location: ../auth/login.php'); // Redirigir al login si no está logueado
    exit();
}

require_once '../includes/db.php';

$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];
$role = $_SESSION['role'];

// Si el usuario es administrador, redirigirlo a su panel de administración
if ($role === 'admin') {
    header('Location: ../admin/admin_dashboard.php'); // Crearemos este archivo
    exit();
}

// Lógica para obtener alojamientos seleccionados por el usuario
$selected_accommodations = [];
try {
    $stmt = $pdo->prepare("
        SELECT a.id_alojamiento, a.nombre, a.descripcion, a.ubicacion, a.precio_por_noche, a.imagen_url
        FROM alojamientos a
        JOIN usuario_alojamiento ua ON a.id_alojamiento = ua.id_alojamiento
        WHERE ua.id_usuario = ?
        ORDER BY ua.fecha_seleccion DESC
    ");
    $stmt->execute([$user_id]);
    $selected_accommodations = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error al cargar alojamientos seleccionados: " . $e->getMessage();
}

// Lógica para obtener todos los alojamientos disponibles para seleccionar
$available_accommodations = [];
try {
    $stmt = $pdo->prepare("
        SELECT id_alojamiento, nombre, descripcion, ubicacion, precio_por_noche, imagen_url
        FROM alojamientos
        WHERE id_alojamiento NOT IN (SELECT id_alojamiento FROM usuario_alojamiento WHERE id_usuario = ?)
        ORDER BY fecha_creacion DESC
    ");
    $stmt->execute([$user_id]);
    $available_accommodations = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error al cargar alojamientos disponibles: " . $e->getMessage();
}


?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Cuenta - <?php echo htmlspecialchars($username); ?></title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link rel="stylesheet" href="../css/style.css">
 
</head>
<body>
    <div class="container-fluid my-5 w-75 p-4 rounded shadow alojamientos-bg">
        <h1>Bienvenido a tu cuenta, <?php echo htmlspecialchars($username); ?>!</h1>
        <p>Tu rol: <?php echo htmlspecialchars($role); ?></p>
        <p><a href="../auth/logout.php" class="logout-btn">Cerrar Sesión</a></p>

        <h2 class="pb-4">Mis Alojamientos Seleccionados</h2>
        <?php if (empty($selected_accommodations)): ?>
            <p>Aún no has seleccionado ningún alojamiento.</p>
        <?php else: ?>
            <div class="row row-cols-1 row-cols-md-3 g-4">
                <?php foreach ($selected_accommodations as $accommodation): ?>
                    <div class="col">
                        <div class="card h-100">
                             <?php if ($accommodation['imagen_url']): ?>
                                <img src="<?php echo htmlspecialchars($accommodation['imagen_url']); ?>" alt="<?php echo htmlspecialchars($accommodation['nombre']); ?>">
                            <?php else: ?>
                            <img class="card-img-top" src="https://via.placeholder.com/100" alt="Sin imagen">
                        <?php endif; ?>
                        <div class="card-body text-center">
                            <h5><?php echo htmlspecialchars($accommodation['nombre']); ?></h5>
                            <p>Ubicación: <?php echo htmlspecialchars($accommodation['ubicacion']); ?></p>
                            <p>Precio/noche: $<?php echo htmlspecialchars(number_format($accommodation['precio_por_noche'], 2)); ?></p>
                            <form action="remove_accommodation.php" method="POST" style="display:inline;">
                                <input type="hidden" name="accommodation_id" value="<?php echo $accommodation['id_alojamiento']; ?>">
                                <button type="submit" class="logout-btn ">Eliminar de mi cuenta</button>
                            </form>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

        <h2 class="pt-4">Alojamientos Disponibles para Seleccionar</h2>
        <?php if (empty($available_accommodations)): ?>
            <p>No hay más alojamientos disponibles para añadir o ya los has seleccionado todos.</p>
        <?php else: ?>
            <div class="row row-cols-1 row-cols-md-3 g-4">
                <?php foreach ($available_accommodations as $accommodation): ?>
                    <div class="col">
                         <div class="card h-100">
                            <?php if ($accommodation['imagen_url']): ?>
                            <img src="<?php echo htmlspecialchars($accommodation['imagen_url']); ?>" alt="<?php echo htmlspecialchars($accommodation['nombre']); ?>">
                        <?php else: ?>
                            <img src="https://via.placeholder.com/100" alt="Sin imagen">
                        <?php endif; ?>
                            <div class="card-body text-start">
                                <h5 class="text-center"><?php echo htmlspecialchars($accommodation['nombre']); ?></h5>
                                <p>Ubicación: <?php echo htmlspecialchars($accommodation['ubicacion']); ?></p>
                                <p>Precio/noche: $<?php echo htmlspecialchars(number_format($accommodation['precio_por_noche'], 2)); ?></p>
                                <form action="add_accommodation.php" method="POST" style="display:inline;">
                                <input type="hidden" name="accommodation_id" value="<?php echo $accommodation['id_alojamiento']; ?>">
                                <button type="submit">Añadir a mi cuenta</button>
                                </form>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
        <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>