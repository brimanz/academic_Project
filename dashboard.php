<?php
require_once __DIR__ . '/config/config.php';
checkAuth();

$pdo = getDB();

// Obtener estadísticas
$stats = [
    'productos' => $pdo->query("SELECT COUNT(*) FROM productos WHERE estado = 'activo'")->fetchColumn(),
    'proveedores' => $pdo->query("SELECT COUNT(*) FROM proveedores WHERE estado = 'activo'")->fetchColumn(),
    'usuarios' => $pdo->query("SELECT COUNT(*) FROM usuarios WHERE estado = 'activo'")->fetchColumn(),
    'stock_bajo' => $pdo->query("SELECT COUNT(*) FROM productos WHERE stock <= stock_minimo AND estado = 'activo'")->fetchColumn()
];

// Productos con stock bajo
$stmt = $pdo->query("SELECT p.*, pr.nombre as proveedor_nombre 
                     FROM productos p 
                     LEFT JOIN proveedores pr ON p.proveedor_id = pr.id 
                     WHERE p.stock <= p.stock_minimo AND p.estado = 'activo' 
                     ORDER BY p.stock ASC LIMIT 5");
$stock_bajo = $stmt->fetchAll();

// Últimos productos agregados
$stmt = $pdo->query("SELECT p.*, pr.nombre as proveedor_nombre 
                     FROM productos p 
                     LEFT JOIN proveedores pr ON p.proveedor_id = pr.id 
                     WHERE p.estado = 'activo' 
                     ORDER BY p.fecha_creacion DESC LIMIT 5");
$ultimos_productos = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Sistema de Inventario</title>
    <link rel="stylesheet" href="./assets/css/styles.css">
</head>
<body>
    <!-- Navegación Superior -->
    <nav class="navbar">
        <div class="navbar-brand">
            <span class="logo">📦</span>
            <span class="brand-name">Sistema de Inventario</span>
        </div>
        <ul class="navbar-menu">
            <li><a href="<?php echo APP_URL; ?>/dashboard.php" class="nav-link active">Dashboard</a></li>
            <li><a href="<?php echo APP_URL; ?>/modules/productos.php" class="nav-link">Productos</a></li>
            <li><a href="<?php echo APP_URL; ?>/modules/proveedores.php" class="nav-link">Proveedores</a></li>
            <?php if ($_SESSION['user_rol'] === 'admin'): ?>
            <li><a href="<?php echo APP_URL; ?>/modules/usuarios.php" class="nav-link">Usuarios</a></li>
            <?php endif; ?>
        </ul>
        <div class="navbar-user">
            <span class="user-name"><?= htmlspecialchars($_SESSION['user_name']) ?></span>
            <span class="user-role"><?= ucfirst($_SESSION['user_rol']) ?></span>
            <a href="<?php echo APP_URL; ?>/logout.php" class="btn btn-logout">Cerrar Sesión</a>
        </div>
    </nav>

    <!-- Banner Principal -->
    <div class="banner">
        <div class="banner-content">
            <h1>Bienvenido, <?= htmlspecialchars($_SESSION['user_name']) ?></h1>
            <p>Gestiona tu inventario de manera eficiente y moderna</p>
        </div>
        <div class="banner-decoration">
            <div class="banner-icon"></div>
        </div>
    </div>

    <!-- Contenido Principal -->
    <main class="main-content">
        <!-- Estadísticas -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon">📦</div>
                <div class="stat-info">
                    <h3><?= $stats['productos'] ?></h3>
                    <p>Productos Activos</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">🏭</div>
                <div class="stat-info">
                    <h3><?= $stats['proveedores'] ?></h3>
                    <p>Proveedores</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon"></div>
                <div class="stat-info">
                    <h3><?= $stats['usuarios'] ?></h3>
                    <p>Usuarios</p>
                </div>
            </div>
            <div class="stat-card stat-warning">
                <div class="stat-icon">⚠️</div>
                <div class="stat-info">
                    <h3><?= $stats['stock_bajo'] ?></h3>
                    <p>Stock Bajo</p>
                </div>
            </div>
        </div>

        <!-- Secciones de contenido -->
        <div class="content-grid">
            <!-- Alertas de Stock Bajo -->
            <div class="content-card">
                <div class="card-header">
                    <h2>️ Alertas de Stock Bajo</h2>
                    <a href="<?php echo APP_URL; ?>/modules/productos.php" class="btn btn-small">Ver Todos</a>
                </div>
                <div class="card-body">
                    <?php if (empty($stock_bajo)): ?>
                        <p class="empty-state">No hay productos con stock bajo</p>
                    <?php else: ?>
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Código</th>
                                    <th>Producto</th>
                                    <th>Stock</th>
                                    <th>Mínimo</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($stock_bajo as $item): ?>
                                <tr class="row-warning">
                                    <td><?= htmlspecialchars($item['codigo']) ?></td>
                                    <td><?= htmlspecialchars($item['nombre']) ?></td>
                                    <td><strong><?= $item['stock'] ?></strong></td>
                                    <td><?= $item['stock_minimo'] ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Últimos Productos -->
            <div class="content-card">
                <div class="card-header">
                    <h2>🆕 Últimos Productos</h2>
                    <a href="<?php echo APP_URL; ?>/modules/productos.php" class="btn btn-small">Ver Todos</a>
                </div>
                <div class="card-body">
                    <?php if (empty($ultimos_productos)): ?>
                        <p class="empty-state">No hay productos registrados</p>
                    <?php else: ?>
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Código</th>
                                    <th>Producto</th>
                                    <th>Precio</th>
                                    <th>Stock</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($ultimos_productos as $item): ?>
                                <tr>
                                    <td><?= htmlspecialchars($item['codigo']) ?></td>
                                    <td><?= htmlspecialchars($item['nombre']) ?></td>
                                    <td>$<?= number_format($item['precio'], 2) ?></td>
                                    <td><?= $item['stock'] ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </main>

    <script src="<?php echo APP_URL; ?>/assets/js/script.js"></script>
</body>
</html>