<?php
require '../config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: categorias.php');
    exit;
}

$action = $_POST['action'] ?? '';
$id = (int)($_POST['id'] ?? 0);

$nombre = trim($_POST['nombre'] ?? '');
$descripcion = trim($_POST['descripcion'] ?? '');

if (empty($nombre)) {
    die("Error: El nombre de la categoría es obligatorio.");
}

try {
    if ($action === 'create') {
        $stmt = $pdo->prepare("INSERT INTO categorias (nombre, descripcion) VALUES (?, ?)");
        $stmt->execute([$nombre, $descripcion]);
        
    } elseif ($action === 'update' && $id > 0) {
        $stmt = $pdo->prepare("UPDATE categorias SET nombre=?, descripcion=? WHERE id=?");
        $stmt->execute([$nombre, $descripcion, $id]);
        
    } else {
        throw new Exception("Acción no válida");
    }
    
    header('Location: categorias.php');
    exit;
    
} catch (PDOException $e) {
    if ($e->getCode() == 23000) {
        die("Error: La categoría '" . htmlspecialchars($nombre) . "' ya existe.");
    }
    die("Error de base de datos: " . $e->getMessage());
} catch (Exception $e) {
    die("Error: " . $e->getMessage());
}
?>