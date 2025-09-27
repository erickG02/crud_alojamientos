<?php
require_once 'includes/db.php';

// Obtener todos los alojamientos
try {
    $stmt = $pdo->query("SELECT id_alojamiento, nombre, descripcion, ubicacion, precio_por_noche, imagen_url FROM alojamientos ORDER BY fecha_creacion DESC");
    $alojamientos = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error al cargar alojamientos: " . $e->getMessage();
    exit();
}
?>

<!DOCTYPE html>
<html lang="es" class="h-auto">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Landing Page - Alojamientos</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link rel="stylesheet" href="./css/style.css">
</head>
<body class="mt-0 pt-0">

<div class="container-fluid justify-content-center p-0">
<!-- Navbar  -->
<nav class="navbar navbar-expand-lg bg-body-tertiary">
  <div class="container-fluid justify-content-between px-4"> 
        <a class="navbar-brand" href="#">Alojamientos</a> 
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav ms-auto">
        <li class="nav-item">
          <a class="nav-link" href="auth/login.php">Iniciar Sesión</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="auth/register.php">Registrate</a>
        </li>
      </ul>
    </div>
  </div>
</nav>

<div class="container-fluid my-5 w-75 p-4 rounded shadow alojamientos-bg">
    <div class="mx-auto" style="max-width: 900px;">
        <h1 class="text-center mb-4">Descubre Nuestros Alojamientos</h1>

        <?php if (empty($alojamientos)): ?>
            <p class="text-center">No hay alojamientos disponibles por el momento.</p>
        <?php else: ?>
            <div class="row row-cols-1 row-cols-md-3 g-4">
                <?php foreach ($alojamientos as $alojamiento): ?>
                    <div class="col">
                        <div class="card h-100">
                            <?php if ($alojamiento['imagen_url']): ?>
                                <img src="<?php echo htmlspecialchars($alojamiento['imagen_url']); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($alojamiento['nombre']); ?>">
                            <?php else: ?>
                                <img src="https://via.placeholder.com/300x200?text=Sin+imagen" class="card-img-top" alt="Sin imagen">
                            <?php endif; ?>
                            <div class="card-body text-start">
                                <h5 class="card-title"><?php echo htmlspecialchars($alojamiento['nombre']); ?></h5>
                                <p class="card-text"><?php echo htmlspecialchars(substr($alojamiento['descripcion'], 0, 100)); ?>...</p>
                                <p class="card-text"><small class="text-muted">Ubicación: <?php echo htmlspecialchars($alojamiento['ubicacion']); ?></small></p>
                                <p class="card-text"><strong>Precio/noche: $<?php echo number_format($alojamiento['precio_por_noche'], 2); ?></strong></p>
                                <a href="auth/login.php" class="btn btn-primary">Reservar</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

   <!-- Footer -->
    <footer class="py-3 mt-auto text-white pb-4">
            <p class="mb-0">&copy; 2025 Alojamientos. Todos los derechos reservados.</p>
    </footer>
</div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>