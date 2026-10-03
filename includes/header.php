<?php
if (!isset($_SESSION)) session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: ' . (strpos($_SERVER['PHP_SELF'], 'modules') !== false ? '../index.php' : 'index.php'));
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $page_title ?? 'Dashboard' ?> | Centro Óptico La Económica F.P</title>
    <link rel="stylesheet" href="<?= strpos($_SERVER['PHP_SELF'], 'modules') !== false ? '../estilo1.css' : 'estilo1.css' ?>">
</head>
<body>
<nav class="navbar">
    <div class="nav-container">
        <a href="<?= strpos($_SERVER['PHP_SELF'], 'modules') !== false ? '../dashboard.php' : 'dashboard.php' ?>" class="nav-brand">
            👁️ La Económica F.P.
        </a>
        <button class="hamburger" id="hamburger">
            <span></span>
            <span></span>
            <span></span>
        </button>
        <ul class="nav-menu" id="nav-menu">
            <li class="nav-item">
                <a href="<?= strpos($_SERVER['PHP_SELF'], 'modules') !== false ? '../dashboard.php' : 'dashboard.php' ?>" class="nav-link">
                    Inicio
                </a>
            </li>
            <li class="nav-item">
                <a href="#" class="nav-link">Registros ▾</a>
                <ul class="dropdown-menu">
                    <li><a href="productos.php">Productos</a></li>
                    <li><a href="categorias.php">Categorías</a></li>
                    <li><a href="proveedores.php">Proveedores</a></li>
                    <li><a href="usuarios.php">Usuarios</a></li>
                </ul>
            </li>
            <li class="nav-item" style="margin-left: auto;">
                <span class="nav-link" style="color: var(--primary); font-weight:700;">
                    👤 <?= htmlspecialchars($_SESSION['user_name']) ?>
                </span>
            </li>
            <li class="nav-item">
                <a href="<?= strpos($_SERVER['PHP_SELF'], 'modules') !== false ? '../logout.php' : 'logout.php' ?>" 
                   class="nav-link" style="color: var(--danger);">
                    Salir
                </a>
            </li>
        </ul>
    </div>
</nav>
<main style="padding-top: 90px; min-height: 100vh; padding-bottom: 40px;">