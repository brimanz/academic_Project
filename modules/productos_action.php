<?php
require '../config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: productos.php');
    exit;
}

$action = $_POST['action'] ?? '';
$id = (int)($_POST['id'] ?? 0);

// Validación de datos
$data = [
    'codigo' => trim($_POST['codigo'] ?? ''),
    'nombre' => trim($_POST['nombre'] ?? ''),
    'descripcion' => trim($_POST['descripcion'] ?? ''),
    'id_categoria' => (int)($_POST['id_categoria'] ?? 0),
    'id_proveedor' => (int)($_POST['id_proveedor'] ?? 0),
    'precio_compra' => (float)($_POST['precio_compra'] ?? 0),
    'precio_venta' => (float)($_POST['precio_venta'] ?? 0),
    'stock' => (int)($_POST['stock'] ?? 0),
    'stock_minimo' => (int)($_POST['stock_minimo'] ?? 5)
];

// Validaciones
if (empty($data['codigo']) || empty($data['nombre']) || $data['id_categoria'] == 0 || $data['id_proveedor'] == 0) {
    die("Error: Todos los campos obligatorios deben estar completos.");
}

if ($data['precio_venta'] < $data['precio_compra']) {
    die("Error: El precio de venta no puede ser menor al precio de compra.");
}

try {
    if ($action === 'create') {
        $sql = "INSERT INTO productos (codigo, nombre, descripcion, id_categoria, id_proveedor, precio_compra, precio_venta, stock, stock_minimo) 
                VALUES (:codigo, :nombre, :descripcion, :id_categoria, :id_proveedor, :precio_compra, :precio_venta, :stock, :stock_minimo)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($data);
        
    } elseif ($action === 'update' && $id > 0) {
        $sql = "UPDATE productos SET codigo=:codigo, nombre=:nombre, descripcion=:descripcion, id_categoria=:id_categoria, 
                id_proveedor=:id_proveedor, precio_compra=:precio_compra, precio_venta=:precio_venta, 
                stock=:stock, stock_minimo=:stock_minimo WHERE id=:id";
        $data['id'] = $id;
        $stmt = $pdo->prepare($sql);
        $stmt->execute($data);
        
    } else {
        throw new Exception("Acción no válida");
    }
    
    header('Location: productos.php');
    exit;
    
} catch (PDOException $e) {
    if ($e->getCode() == 23000) {
        die("Error: El código de producto '" . htmlspecialchars($data['codigo']) . "' ya existe.");
    }
    die("Error de base de datos: " . $e->getMessage());
} catch (Exception $e) {
    die("Error: " . $e->getMessage());
}
?>