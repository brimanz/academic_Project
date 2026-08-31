<?php
require_once 'includes/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario = trim($_POST['Usuario'] ?? '');
    $clave   = trim($_POST['Contraseña'] ?? '');

    if (empty($usuario) || empty($clave)) {
        header('Location: 1-inicio de sesion.php?error=campos');
        exit;
    }

    $stmt = $pdo->prepare("SELECT * FROM usuario WHERE nombre_usuario = ?");
    $stmt->execute([$usuario]);
    $user = $stmt->fetch();

    // Por ahora comparación directa; en producción usa password_verify()
    if ($user && $clave === $user['contrasena']) {
        $_SESSION['usuario_id'] = $user['cedula'];
        $_SESSION['usuario_nombre'] = $user['nombre_usuario'];
        $_SESSION['usuario_tipo'] = $user['id_tipo_usuario'];
        header('Location: 2-pagina-principal2.html');
        exit;
    } else {
        header('Location: 1-inicio de sesion.php?error=invalido');
        exit;
    }
}
?>