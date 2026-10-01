<?php
require_once __DIR__ . '/config/config.php';

// Si ya está logueado, redirigir al dashboard
if (isset($_SESSION['user_id'])) {
    header('Location: ' . APP_URL . '/dashboard.php');
    exit();
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = sanitize($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if (empty($email) || empty($password)) {
        $error = 'Por favor complete todos los campos';
    } else {
        $pdo = getDB();
        $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE email = ? AND estado = 'activo'");
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        
        if ($user && verifyPassword($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['nombre'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_rol'] = $user['rol'];
            header('Location: ' . APP_URL . '/dashboard.php');
            exit();
        } else {
            $error = 'Credenciales incorrectas o usuario inactivo';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sistema de Inventario</title>
    <link rel="stylesheet" href="<?php echo APP_URL; ?>/assets/css/styles.css">
</head>
<body class="login-page">
    <div class="login-container">
        <div class="login-card">
            <div class="login-header">
                <div class="logo-icon">📦</div>
                <h1>Sistema de Inventario</h1>
                <p>Ingrese sus credenciales para acceder</p>
            </div>
            
            <?php if ($error): ?>
                <div class="alert alert-error">
                    <span class="alert-icon">⚠️</span>
                    <?= $error ?>
                </div>
            <?php endif; ?>
            
            <form method="POST" class="login-form">
                <div class="form-group">
                    <label for="email">Correo Electrónico</label>
                    <input type="email" id="email" name="email" required 
                           placeholder="admin@sistema.com" value="<?= htmlspecialchars($email ?? '') ?>">
                </div>
                
                <div class="form-group">
                    <label for="password">Contraseña</label>
                    <input type="password" id="password" name="password" required 
                           placeholder="••••••••">
                </div>
                
                <button type="submit" class="btn btn-primary btn-block">
                    Iniciar Sesión
                </button>
            </form>
            
            <div class="login-footer">
                <p><strong>Credenciales de prueba:</strong></p>
                <p>Email: admin@sistema.com</p>
                <p>Password: admin123</p>
            </div>
        </div>
    </div>
    
    <script src="<?php echo APP_URL; ?>/assets/js/script.js"></script>
</body>
</html>