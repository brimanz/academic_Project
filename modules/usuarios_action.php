<?php
require '../config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: usuarios.php');
    exit;
}

$action = $_POST['action'] ?? '';
$id = (int)($_POST['id'] ?? 0);

$nombre = trim($_POST['nombre'] ?? '');
$usuario = trim($_POST['usuario'] ?? '');
$password = $_POST['password'] ?? '';
$rol = $_POST['rol'] ?? 'Vendedor';

if (empty($nombre) || empty($usuario)) {
    die("Error: Nombre y usuario son obligatorios.");
}

try {
    if ($action === 'create') {
        if (empty($password) || strlen($password) < 6) {
            die("Error: La contraseña debe tener al menos 6 caracteres.");
        }
        
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO usuarios (nombre, usuario, password, rol) VALUES (?, ?, ?, ?)");
        $stmt->execute([$nombre, $usuario, $hash, $rol]);
        
    } elseif ($action === 'update' && $id > 0) {
        if (!empty($password)) {
            if (strlen($password) < 6) {
                die("Error: La contraseña debe tener al menos 6 caracteres.");
            }
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE usuarios SET nombre=?, usuario=?, password=?, rol=? WHERE id=?");
            $stmt->execute([$nombre, $usuario, $hash, $rol, $id]);
        } else {
            $stmt = $pdo->prepare("UPDATE usuarios SET nombre=?, usuario=?, rol=? WHERE id=?");
            $stmt->execute([$nombre, $usuario, $rol, $id]);
        }
        
    } else {
        throw new Exception("Acción no válida");
    }
    
    header('Location: usuarios.php');
    exit;
    
} catch (PDOException $e) {
    if ($e->getCode() == 23000) {
        die("Error: El usuario '" . htmlspecialchars($usuario) . "' ya existe.");
    }
    die("Error de base de datos: " . $e->getMessage());
} catch (Exception $e) {
    die("Error: " . $e->getMessage());
}
?>