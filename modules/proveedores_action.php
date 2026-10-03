<?php
require '../config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: proveedores.php');
    exit;
}

$action = $_POST['action'] ?? '';
$id = (int)($_POST['id'] ?? 0);

$data = [
    'nombre' => trim($_POST['nombre'] ?? ''),
    'rif' => trim($_POST['rif'] ?? ''),
    'telefono' => trim($_POST['telefono'] ?? ''),
    'direccion' => trim($_POST['direccion'] ?? ''),
    'contacto' => trim($_POST['contacto'] ?? '')
];

if (empty($data['nombre'])) {
    die("Error: El nombre del proveedor es obligatorio.");
}

try {
    if ($action === 'create') {
        $sql = "INSERT INTO proveedores (nombre, rif, telefono, direccion, contacto) 
                VALUES (:nombre, :rif, :telefono, :direccion, :contacto)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($data);
        
    } elseif ($action === 'update' && $id > 0) {
        $sql = "UPDATE proveedores SET nombre=:nombre, rif=:rif, telefono=:telefono, 
                direccion=:direccion, contacto=:contacto WHERE id=:id";
        $data['id'] = $id;
        $stmt = $pdo->prepare($sql);
        $stmt->execute($data);
        
    } else {
        throw new Exception("Acción no válida");
    }
    
    header('Location: proveedores.php');
    exit;
    
} catch (PDOException $e) {
    if ($e->getCode() == 23000) {
        die("Error: El RIF '" . htmlspecialchars($data['rif']) . "' ya está registrado.");
    }
    die("Error de base de datos: " . $e->getMessage());
} catch (Exception $e) {
    die("Error: " . $e->getMessage());
}
?>