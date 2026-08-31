<?php
require_once 'includes/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $rif       = trim($_POST['codigo'] ?? ''); // El HTML usa name="codigo" para el RIF
        $nombre    = trim($_POST['nombre'] ?? '');
        $apellido  = trim($_POST['Apellido'] ?? '');
        $empresa   = trim($_POST['Empresa'] ?? '');
        $correo    = trim($_POST['Correo'] ?? '');
        $telefono  = trim($_POST['Telefono'] ?? '');
        $direccion = trim($_POST['Direccion'] ?? '');

        if (empty($rif) || empty($nombre)) {
            throw new Exception('RIF y nombre son obligatorios.');
        }

        $stmt = $pdo->prepare("INSERT INTO proveedores 
            (rif, nombre, apellido, empresa, correo, telefono, direccion) 
            VALUES (?, ?, ?, ?, ?, ?, ?)
            ON DUPLICATE KEY UPDATE
            nombre = VALUES(nombre), apellido = VALUES(apellido),
            empresa = VALUES(empresa), correo = VALUES(correo),
            telefono = VALUES(telefono), direccion = VALUES(direccion)");
        
        $stmt->execute([$rif, $nombre, $apellido, $empresa, $correo, $telefono, $direccion]);

        header('Location: 6-Proveedores.php?exito=1');
        exit;

    } catch (Exception $e) {
        header('Location: 6-Proveedores.php?error=' . urlencode($e->getMessage()));
        exit;
    }
}
?>