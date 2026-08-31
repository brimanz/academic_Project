<?php
require_once 'includes/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $nombre      = trim($_POST['nombre'] ?? '');
        $descripcion = trim($_POST['Descipción'] ?? ''); // typo original preservado del HTML
        // Nota: el HTML original tenía name="Descipción" (con typo)

        if (empty($nombre)) {
            throw new Exception('El nombre de la categoría es obligatorio.');
        }

        $stmt = $pdo->prepare("INSERT INTO categoria (nombre, descripcion) VALUES (?, ?)");
        $stmt->execute([$nombre, $descripcion]);

        header('Location: 5-Categorias.php?exito=1');
        exit;

    } catch (Exception $e) {
        header('Location: 5-Categorias.php?error=' . urlencode($e->getMessage()));
        exit;
    }
}
?>