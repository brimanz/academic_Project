<?php
require_once 'includes/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $codigo   = trim($_POST['codigo'] ?? '');
        $nombre   = trim($_POST['nombre'] ?? '');
        $stock    = intval($_POST['disponibilidad'] ?? 0);
        $precio   = floatval(str_replace(['$', ','], '', $_POST['precio'] ?? 0));
        $categoria = trim($_POST['categoria'] ?? '');

        // Validaciones básicas
        if (empty($codigo) || empty($nombre)) {
            throw new Exception('Código y nombre son obligatorios.');
        }

        // Insertar producto (sin categoría FK por simplicidad inicial, o buscar id)
        $stmt = $pdo->prepare("INSERT INTO productos (codigo, nombre, precio_venta, stock) 
                               VALUES (?, ?, ?, ?)
                               ON DUPLICATE KEY UPDATE 
                               nombre = VALUES(nombre), 
                               precio_venta = VALUES(precio_venta), 
                               stock = VALUES(stock)");
        $stmt->execute([$codigo, $nombre, $precio, $stock]);

        header('Location: 4-Registro de Productos.php?exito=1');
        exit;

    } catch (Exception $e) {
        header('Location: 4-Registro de Productos.php?error=' . urlencode($e->getMessage()));
        exit;
    }
}
?>