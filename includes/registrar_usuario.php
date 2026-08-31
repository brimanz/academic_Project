<?php
require_once 'includes/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $cedula    = trim($_POST['Cedula'] ?? '');
        $nombre    = trim($_POST['nombre'] ?? '');
        $apellido  = trim($_POST['Apellido'] ?? '');
        $telefono  = trim($_POST['Telefono'] ?? '');
        $correo    = trim($_POST['correo'] ?? '');
        $direccion = trim($_POST['Direccion'] ?? '');
        $tipo      = $_POST['Tipo-user'] ?? null; // A=1, B=2, C=3, D=4
        $user      = trim($_POST['user'] ?? '');
        $clave     = trim($_POST['clave'] ?? '');

        // Mapeo de tipos del HTML a IDs de la BD
        $mapaTipos = ['A' => 1, 'B' => 2, 'C' => 3, 'D' => 4];
        $idTipo = $mapaTipos[$tipo] ?? null;

        if (empty($cedula) || empty($nombre) || empty($user) || empty($clave)) {
            throw new Exception('Cédula, nombre, usuario y clave son obligatorios.');
        }

        $stmt = $pdo->prepare("INSERT INTO usuario 
            (cedula, nombre, apellido, telefono, correo, direccion, nombre_usuario, contrasena, id_tipo_usuario)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
            ON DUPLICATE KEY UPDATE
            nombre = VALUES(nombre), apellido = VALUES(apellido),
            telefono = VALUES(telefono), correo = VALUES(correo),
            direccion = VALUES(direccion), nombre_usuario = VALUES(nombre_usuario),
            contrasena = VALUES(contrasena), id_tipo_usuario = VALUES(id_tipo_usuario)");
        
        $stmt->execute([$cedula, $nombre, $apellido, $telefono, $correo, $direccion, $user, $clave, $idTipo]);

        header('Location: 7-gestion de usuario.php?exito=1');
        exit;

    } catch (Exception $e) {
        header('Location: 7-gestion de usuario.php?error=' . urlencode($e->getMessage()));
        exit;
    }
}
?>