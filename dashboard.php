<?php
$page_title = "Dashboard";
require 'config.php';
require 'includes/header.php';

// Estadísticas
$total_productos = $pdo->query("SELECT COUNT(*) FROM productos WHERE estado = 1")->fetchColumn();
$total_categorias = $pdo->query("SELECT COUNT(*) FROM categorias WHERE estado = 1")->fetchColumn();
$total_proveedores = $pdo->query("SELECT COUNT(*) FROM proveedores WHERE estado = 1")->fetchColumn();
$total_usuarios = $pdo->query("SELECT COUNT(*) FROM usuarios WHERE estado = 1")->fetchColumn();
$stock_bajo = $pdo->query("SELECT COUNT(*) FROM productos WHERE estado = 1 AND stock <= stock_minimo")->fetchColumn();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $page_title ?> | Centro Óptico La Económica F.P</title>
    <link rel="stylesheet" href="estilo1.css">
</head>
<body>
<nav class="navbar">
    <div class="nav-container">
        <a href="dashboard.php" class="nav-brand">
            👁️ La Económica F.P.
        </a>
        <button class="hamburger" id="hamburger">
            <span></span>
            <span></span>
            <span></span>
        </button>
        <ul class="nav-menu" id="nav-menu">
            <li class="nav-item">
                <a href="dashboard.php" class="nav-link">
                    🏠 Inicio
                </a>
            </li>
            <li class="nav-item">
                <a href="#" class="nav-link">📋 Registros ▾</a>
                <ul class="dropdown-menu">
                    <li><a href="modules/productos.php">📦 Productos</a></li>
                    <li><a href="modules/categorias.php">🏷️ Categorías</a></li>
                    <li><a href="modules/proveedores.php">🏢 Proveedores</a></li>
                    <li><a href="modules/usuarios.php">👥 Usuarios</a></li>
                </ul>
            </li>
            <li class="nav-item">
                <a href="#" class="nav-link">📊 Reportes ▾</a>
                <ul class="dropdown-menu">
                    <li><a href="modules/reportes/inventario.php">📦 Inventario General</a></li>
                    <li><a href="modules/reportes/stock_bajo.php">⚠️ Stock Bajo</a></li>
                    <li><a href="modules/reportes/valor_inventario.php">💰 Valor del Inventario</a></li>
                    <li><a href="modules/reportes/productos_categoria.php">📊 Por Categoría</a></li>
                </ul>
            </li>
            <li class="nav-item">
                <a href="#" class="nav-link">📚 Documentación ▾</a>
                <ul class="dropdown-menu">
                    <li><a href="docs/manual_usuario.php">📖 Manual de Usuario</a></li>
                    <li><a href="docs/manual_tecnico.php">🔧 Manual Técnico</a></li>
                    <li><a href="docs/faq.php">❓ Preguntas Frecuentes</a></li>
                    <li><a href="docs/soporte.php">🆘 Soporte</a></li>
                </ul>
            </li>
            <li class="nav-item" style="margin-left: auto;">
                <span class="nav-link" style="color: var(--primary); font-weight:700;">
                    👤 <?= htmlspecialchars($_SESSION['user_name'] ?? 'Usuario') ?>
                </span>
            </li>
            <li class="nav-item">
                <a href="logout.php" class="nav-link" style="color: var(--danger);">
                    🚪 Salir
                </a>
            </li>
        </ul>
    </div>
</nav>
<main style="padding-top: 90px; min-height: 100vh; padding-bottom: 40px;">
    <div class="hero">
        <h1>Centro Óptico La Económica F.P.</h1>
        <p class="hero-subtitle">Sistema Integral de Gestión y Control</p>
        
        <div class="hero-stats">
            <div class="stat-card">
                <div class="stat-number"><?= $total_productos ?></div>
                <div class="stat-label">📦 Productos Registrados</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?= $total_categorias ?></div>
                <div class="stat-label">🏷️ Categorías</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?= $total_proveedores ?></div>
                <div class="stat-label">🏢 Proveedores</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?= $total_usuarios ?></div>
                <div class="stat-label">👥 Usuarios Activos</div>
            </div>
            <div class="stat-card" style="<?= $stock_bajo > 0 ? 'border: 2px solid var(--danger);' : '' ?>">
                <div class="stat-number" style="<?= $stock_bajo > 0 ? 'color: var(--danger);' : '' ?>">
                    <?= $stock_bajo ?>
                </div>
                <div class="stat-label">⚠️ Stock Bajo</div>
            </div>
        </div>
    </div>
</main>

<script>
    // Toggle Menú Móvil
    const hamburger = document.getElementById('hamburger');
    const navMenu = document.getElementById('nav-menu');
    
    if (hamburger && navMenu) {
        hamburger.addEventListener('click', () => {
            hamburger.classList.toggle('active');
            navMenu.classList.toggle('active');
        });
        
        // Dropdowns en móvil
        document.querySelectorAll('.nav-item').forEach(item => {
            item.addEventListener('click', function(e) {
                if (window.innerWidth <= 768 && this.querySelector('.dropdown-menu')) {
                    e.preventDefault();
                    this.classList.toggle('dropdown-active');
                }
            });
        });
    }
</script>
</body>
</html>