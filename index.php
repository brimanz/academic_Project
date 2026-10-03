<?php
session_start();
if (isset($_SESSION['user_id'])) {
    header('Location: dashboard.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inicio de Sesión | Centro Óptico La Económica F.P.</title>
    <link rel="stylesheet" href="estilo1.css">
</head>
<body class="login-page">
    <div class="glass-card">
        <div class="login-logo">
            <h1>👁️ Centro Óptico</h1>
            <p>La Económica F.P.</p>
        </div>
        <h2>Inicio de Sesión</h2>
        <?php if(isset($_GET['error'])): ?>
            <p style="color: var(--danger); text-align: center; margin-bottom: 15px; font-weight: 600;">
                ❌ Usuario o contraseña incorrectos
            </p>
        <?php endif; ?>
        <form action="login_process.php" method="POST">
            <div class="form-group">
                <label>Usuario</label>
                <input type="text" name="usuario" placeholder="Ingrese su usuario" required autofocus>
            </div>
            <div class="form-group">
                <label>Contraseña</label>
                <input type="password" name="password" placeholder="Ingrese su contraseña" required>
            </div>
            <div class="btn-group">
                <button type="submit" class="btn btn-primary">Iniciar Sesión</button>
            </div>
        </form>
    </div>
</body>
</html>