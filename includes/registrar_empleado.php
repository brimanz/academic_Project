<?php
require_once 'includes/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $cedula    = trim($_POST['Cedula'] ?? '');
        $nombre    = trim($_POST['nombre'] ?? '');
        $apellido  = trim($_POST['Apellido'] ?? '');
        $correo    = trim($_POST['correo'] ?? '');
        $telefono  = trim($_POST['telefono'] ?? '');
        $direccion = trim($_POST['direccion'] ?? '');

        if (empty($cedula) || empty($nombre)) {
            throw new Exception('Cédula y nombre son obligatorios.');
        }

        $stmt = $pdo->prepare("INSERT INTO empleados 
            (cedula, nombre, apellido, correo, telefono, direccion)
            VALUES (?, ?, ?, ?, ?, ?)
            ON DUPLICATE KEY UPDATE
            nombre = VALUES(nombre), apellido = VALUES(apellido),
            correo = VALUES(correo), telefono = VALUES(telefono),
            direccion = VALUES(direccion)");
        
        $stmt->execute([$cedula, $nombre, $apellido, $correo, $telefono, $direccion]);

        header('Location: 8-Empleados.php?exito=1');
        exit;

    } catch (Exception $e) {
        header('Location: 8-Empleados.php?error=' . urlencode($e->getMessage()));
        exit;
    }
}
?>