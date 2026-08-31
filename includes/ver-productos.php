<?php require_once 'includes/config.php'; ?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Productos | Centro Óptico La Económica F.P.</title>
  <link rel="stylesheet" href="estilos-globales.css">
</head>
<body style="padding-top: 100px; padding-bottom: 40px;">
  
  <!-- NAVBAR (igual que en los otros archivos) -->
  <nav class="navbar">
    <div class="nav-container">
      <a href="2-pagina-principal2.html" class="nav-brand">👁️ La Económica F.P.</a>
      <button class="hamburger" onclick="toggleMenu()" aria-label="Menú">
        <span></span><span></span><span></span>
      </button>
      <ul class="nav-menu" id="navMenu">
        <li class="nav-item"><a href="2-pagina-principal2.html" class="nav-link">Inicio</a></li>
        <li class="nav-item"><a href="3-Informacion.html" class="nav-link">Información</a></li>
        <li class="nav-item">
          <a href="#" class="nav-link">Productos ▾</a>
          <ul class="dropdown-menu">
            <li><a href="4-Registro de Productos.php">Registrar Productos</a></li>
            <li><a href="ver-productos.php">Ver Productos</a></li>
          </ul>
        </li>
        <li class="nav-item">
          <a href="#" class="nav-link">Categorías ▾</a>
          <ul class="dropdown-menu">
            <li><a href="5-Categorias.php">Registrar Categorías</a></li>
            <li><a href="ver-categoria.php">Ver Categorías</a></li>
          </ul>
        </li>
        <li class="nav-item">
          <a href="#" class="nav-link">Proveedores ▾</a>
          <ul class="dropdown-menu">
            <li><a href="6-Proveedores.php">Registrar Proveedores</a></li>
            <li><a href="ver-proveedores.php">Ver Proveedores</a></li>
          </ul>
        </li>
        <li class="nav-item">
          <a href="#" class="nav-link">Usuarios ▾</a>
          <ul class="dropdown-menu">
            <li><a href="7-gestion de usuario.php">Registrar Usuarios</a></li>
            <li><a href="8-Empleados.php">Registrar Empleados</a></li>
          </ul>
        </li>
        <li class="nav-item"><a href="logout.php" class="nav-link">Salir</a></li>
      </ul>
    </div>
  </nav>

  <div class="table-container">
    <h2>📦 Lista de Productos</h2>
    
    <?php
    try {
        $stmt = $pdo->query("SELECT * FROM productos ORDER BY fecha_registro DESC");
        $productos = $stmt->fetchAll();
    } catch (PDOException $e) {
        echo '<p style="color: var(--danger); text-align: center;">Error al cargar productos.</p>';
        $productos = [];
    }
    ?>

    <table class="data-table">
      <thead>
        <tr>
          <th>Código</th>
          <th>Nombre</th>
          <th>Precio Venta</th>
          <th>Stock</th>
          <th>Fecha Registro</th>
        </tr>
      </thead>
      <tbody>
        <?php if (count($productos) > 0): ?>
          <?php foreach ($productos as $prod): ?>
          <tr>
            <td><strong><?= htmlspecialchars($prod['codigo']) ?></strong></td>
            <td><?= htmlspecialchars($prod['nombre']) ?></td>
            <td>$<?= number_format($prod['precio_venta'], 2) ?></td>
            <td><?= $prod['stock'] ?></td>
            <td><?= date('d/m/Y', strtotime($prod['fecha_registro'])) ?></td>
          </tr>
          <?php endforeach; ?>
        <?php else: ?>
          <tr>
            <td colspan="5" style="text-align: center; color: var(--text-muted); padding: 40px;">
              No hay productos registrados aún.
            </td>
          </tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>

  <script>
    function toggleMenu() {
      document.querySelector('.hamburger').classList.toggle('active');
      document.querySelector('.nav-menu').classList.toggle('active');
    }
    document.querySelectorAll('.nav-item').forEach(item => {
      if (item.querySelector('.dropdown-menu')) {
        item.querySelector('.nav-link').addEventListener('click', (e) => {
          if (window.innerWidth <= 768) {
            e.preventDefault();
            item.classList.toggle('dropdown-active');
          }
        });
      }
    });
  </script>
</body>
</html>