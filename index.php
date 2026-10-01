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
    
    <!-- ESTILOS ESPECÍFICOS PARA EL NUEVO DISEÑO DE LOGIN -->
    <style>
        :root {
            --navy-deep: #0B192C;
            --navy-gradient-start: #0B192C;
            --navy-gradient-end: #1E3E62;
            --celeste-hover: #70C1B3;
            --celeste-hover-alt: #3AAFA9;
            --gold-dark-muted: #C5A059;
            --bg-pearl-base: #F8F9FA;
            --text-primary: #0B192C;
            --text-secondary: #6B7280;
            --danger: #EF4444;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Segoe UI', system-ui, sans-serif; }

        .login-wrapper {
            display: flex;
            min-height: 100vh;
            width: 100%;
        }

        /* Lado Izquierdo: Visual */
        .login-visual {
            flex: 1;
            background: linear-gradient(135deg, var(--navy-gradient-start) 0%, var(--navy-gradient-end) 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
            padding: 40px;
        }

        .visual-content {
            position: relative;
            z-index: 2;
            color: white;
            text-align: center;
            max-width: 400px;
            animation: fadeInLeft 0.8s ease-out;
        }

        .brand-mark {
            font-size: 64px;
            margin-bottom: 24px;
            display: inline-block;
            animation: float 4s ease-in-out infinite;
        }

        .visual-content h2 {
            font-size: 36px;
            font-weight: 700;
            margin-bottom: 16px;
            line-height: 1.2;
        }

        .visual-content p {
            font-size: 18px;
            opacity: 0.85;
            line-height: 1.6;
        }

        /* Formas decorativas de fondo */
        .visual-shapes .shape {
            position: absolute;
            border-radius: 50%;
            opacity: 0.1;
        }
        .shape-1 {
            width: 300px; height: 300px;
            background: var(--celeste-hover);
            top: -50px; left: -50px;
            animation: pulse 6s ease-in-out infinite;
        }
        .shape-2 {
            width: 200px; height: 200px;
            background: var(--gold-dark-muted);
            bottom: 10%; right: 10%;
            animation: pulse 8s ease-in-out infinite reverse;
        }

        /* Lado Derecho: Formulario */
        .login-form-section {
            flex: 1;
            background: var(--bg-pearl-base);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px;
        }

        .form-container {
            width: 100%;
            max-width: 420px;
            animation: fadeInRight 0.8s ease-out;
        }

        .form-header {
            margin-bottom: 40px;
        }

        .form-header h3 {
            font-size: 32px;
            color: var(--navy-deep);
            font-weight: 700;
            margin-bottom: 8px;
        }

        .form-header p {
            color: var(--text-secondary);
            font-size: 16px;
        }

        /* Inputs con etiqueta flotante */
        .input-group {
            position: relative;
            margin-bottom: 28px;
        }

        .input-group input {
            width: 100%;
            padding: 16px 16px 16px 48px;
            border: 2px solid #E5E7EB;
            border-radius: 12px;
            font-size: 16px;
            background: white;
            transition: all 0.3s ease;
            outline: none;
        }

        .input-group input:focus {
            border-color: var(--celeste-hover);
            box-shadow: 0 0 0 4px rgba(112, 193, 179, 0.15);
        }

        .input-group label {
            position: absolute;
            left: 48px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-secondary);
            font-size: 16px;
            pointer-events: none;
            transition: all 0.3s ease;
            background: white;
            padding: 0 4px;
        }

        .input-group .input-icon {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 18px;
            opacity: 0.6;
        }

        /* Efecto flotante cuando hay foco o texto */
        .input-group input:focus ~ label,
        .input-group input:not(:placeholder-shown) ~ label {
            top: 0;
            left: 14px;
            font-size: 13px;
            color: var(--celeste-hover-alt);
            font-weight: 600;
        }

        .input-group input:focus ~ .input-icon {
            opacity: 1;
        }

        /* Botón moderno */
        .btn-submit {
            width: 100%;
            padding: 16px;
            background: linear-gradient(135deg, var(--celeste-hover) 0%, var(--celeste-hover-alt) 100%);
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(112, 193, 179, 0.3);
        }

        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(112, 193, 179, 0.4);
        }

        .btn-submit:active {
            transform: translateY(0);
        }

        .btn-arrow {
            transition: transform 0.3s ease;
        }

        .btn-submit:hover .btn-arrow {
            transform: translateX(4px);
        }

        /* Alerta de error */
        .alert-error {
            background: rgba(239, 68, 68, 0.1);
            border-left: 4px solid var(--danger);
            color: var(--danger);
            padding: 14px 18px;
            border-radius: 8px;
            margin-bottom: 24px;
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 14px;
            animation: shake 0.4s ease;
        }

        /* Credenciales demo */
        .demo-credentials {
            margin-top: 32px;
            padding-top: 24px;
            border-top: 1px dashed #D1D5DB;
            text-align: center;
            font-size: 13px;
            color: var(--text-secondary);
        }

        .demo-credentials strong {
            color: var(--navy-deep);
        }

        /* Animaciones */
        @keyframes fadeInLeft {
            from { opacity: 0; transform: translateX(-40px); }
            to { opacity: 1; transform: translateX(0); }
        }
        @keyframes fadeInRight {
            from { opacity: 0; transform: translateX(40px); }
            to { opacity: 1; transform: translateX(0); }
        }
        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-15px); }
        }
        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.1); }
        }
        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-8px); }
            75% { transform: translateX(8px); }
        }

        /* Responsive */
        @media (max-width: 900px) {
            .login-visual { display: none; }
            .login-form-section { flex: 1; }
        }
    </style>
</head>
<body>
    <div class="login-wrapper">
        <!-- Lado Izquierdo: Branding y Visual -->
        <div class="login-visual">
            <div class="visual-shapes">
                <div class="shape shape-1"></div>
                <div class="shape shape-2"></div>
            </div>
            <div class="visual-content">
                <div class="brand-mark">📦</div>
                <h2>Sistema de Inventario</h2>
                <p>Accede a tu panel de control y gestiona tu inventario de forma inteligente, segura y eficiente.</p>
            </div>
        </div>

        <!-- Lado Derecho: Formulario -->
        <div class="login-form-section">
            <div class="form-container">
                <div class="form-header">
                    <h3>¡Bienvenido de nuevo!</h3>
                    <p>Ingresa tus credenciales para continuar</p>
                </div>
                
                <?php if ($error): ?>
                    <div class="alert-error">
                        <span>⚠️</span> <?= $error ?>
                    </div>
                <?php endif; ?>
                
                <form method="POST" class="modern-form">
                    <div class="input-group">
                        <input type="email" id="email" name="email" required 
                               placeholder=" " value="<?= htmlspecialchars($email ?? '') ?>">
                        <label for="email">Correo Electrónico</label>
                        <span class="input-icon">✉️</span>
                    </div>
                    
                    <div class="input-group">
                        <input type="password" id="password" name="password" required 
                               placeholder=" ">
                        <label for="password">Contraseña</label>
                        <span class="input-icon">🔒</span>
                    </div>
                    
                    <div class="form-actions">
                        <button type="submit" class="btn-submit">
                            <span>Ingresar al Sistema</span>
                            <span class="btn-arrow">→</span>
                        </button>
                    </div>
                </form>
                
                <div class="demo-credentials">
                    <p>🔑 <strong>Datos de prueba:</strong> admin@sistema.com / admin123</p>
                </div>
            </div>
        </div>
    </div>
    
    <script src="<?php echo APP_URL; ?>/assets/js/script.js"></script>
</body>
</html>