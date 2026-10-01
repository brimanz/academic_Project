<?php
require_once __DIR__ . '/../config/config.php';
checkAdmin();

$pdo = getDB();
$mensaje = '';
$tipo_mensaje = '';

// Procesar formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $accion = sanitize($_POST['accion'] ?? '');
    
    if ($accion === 'crear' || $accion === 'editar') {
        $nombre = sanitize($_POST['nombre']);
        $email = sanitize($_POST['email']);
        $rol = sanitize($_POST['rol']);
        $estado = sanitize($_POST['estado']);
        $password = $_POST['password'] ?? '';
        
        if ($accion === 'crear') {
            if (empty($password)) {
                $mensaje = 'La contraseña es obligatoria';
                $tipo_mensaje = 'error';
            } else {
                $hashedPassword = hashPassword($password);
                $stmt = $pdo->prepare("INSERT INTO usuarios (nombre, email, password, rol, estado) VALUES (?, ?, ?, ?, ?)");
                if ($stmt->execute([$nombre, $email, $hashedPassword, $rol, $estado])) {
                    $mensaje = 'Usuario creado exitosamente';
                    $tipo_mensaje = 'success';
                } else {
                    $mensaje = 'Error al crear el usuario';
                    $tipo_mensaje = 'error';
                }
            }
        } else {
            $id = intval($_POST['id']);
            if (!empty($password)) {
                $hashedPassword = hashPassword($password);
                $stmt = $pdo->prepare("UPDATE usuarios SET nombre=?, email=?, password=?, rol=?, estado=? WHERE id=?");
                $result = $stmt->execute([$nombre, $email, $hashedPassword, $rol, $estado, $id]);
            } else {
                $stmt = $pdo->prepare("UPDATE usuarios SET nombre=?, email=?, rol=?, estado=? WHERE id=?");
                $result = $stmt->execute([$nombre, $email, $rol, $estado, $id]);
            }
            
            if ($result) {
                $mensaje = 'Usuario actualizado exitosamente';
                $tipo_mensaje = 'success';
            } else {
                $mensaje = 'Error al actualizar el usuario';
                $tipo_mensaje = 'error';
            }
        }
    } elseif ($accion === 'eliminar') {
        $id = intval($_POST['id']);
        if ($id == $_SESSION['user_id']) {
            $mensaje = 'No puede eliminar su propio usuario';
            $tipo_mensaje = 'error';
        } else {
            $stmt = $pdo->prepare("UPDATE usuarios SET estado='inactivo' WHERE id=?");
            if ($stmt->execute([$id])) {
                $mensaje = 'Usuario eliminado exitosamente';
                $tipo_mensaje = 'success';
            } else {
                $mensaje = 'Error al eliminar el usuario';
                $tipo_mensaje = 'error';
            }
        }
    }
}

// Obtener todos los usuarios
$usuarios = $pdo->query("SELECT * FROM usuarios WHERE estado = 'activo' ORDER BY nombre")->fetchAll();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Usuarios - Sistema de Inventario</title>
    <link rel="stylesheet" href="<?php echo APP_URL; ?>/assets/css/styles.css">
</head>
<body>
    <nav class="navbar">
        <div class="navbar-brand">
            <span class="logo">📦</span>
            <span class="brand-name">Sistema de Inventario</span>
        </div>
        <ul class="navbar-menu">
            <li><a href="<?php echo APP_URL; ?>/dashboard.php" class="nav-link">Dashboard</a></li>
            <li><a href="<?php echo APP_URL; ?>/modules/productos.php" class="nav-link">Productos</a></li>
            <li><a href="<?php echo APP_URL; ?>/modules/proveedores.php" class="nav-link">Proveedores</a></li>
            <li><a href="<?php echo APP_URL; ?>/modules/usuarios.php" class="nav-link active">Usuarios</a></li>
        </ul>
        <div class="navbar-user">
            <span class="user-name"><?= htmlspecialchars($_SESSION['user_name']) ?></span>
            <a href="<?php echo APP_URL; ?>/logout.php" class="btn btn-logout">Cerrar Sesión</a>
        </div>
    </nav>

    <main class="main-content">
        <div class="page-header">
            <h1>Gestión de Usuarios</h1>
            <button class="btn btn-primary" onclick="openModal('crear')">+ Nuevo Usuario</button>
        </div>

        <?php if ($mensaje): ?>
            <div class="alert alert-<?= $tipo_mensaje ?>">
                <span class="alert-icon"><?= $tipo_mensaje === 'success' ? '✓' : '⚠️' ?></span>
                <?= $mensaje ?>
            </div>
        <?php endif; ?>

        <div class="content-card">
            <div class="card-body">
                <div class="table-container">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Nombre</th>
                                <th>Email</th>
                                <th>Rol</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($usuarios)): ?>
                                <tr>
                                    <td colspan="5" class="empty-state">No hay usuarios registrados</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($usuarios as $usuario): ?>
                                <tr>
                                    <td><?= htmlspecialchars($usuario['nombre']) ?></td>
                                    <td><?= htmlspecialchars($usuario['email']) ?></td>
                                    <td><span class="badge badge-<?= $usuario['rol'] === 'admin' ? 'badge-warning' : 'badge-info' ?>"><?= ucfirst($usuario['rol']) ?></span></td>
                                    <td><span class="badge badge-success"><?= ucfirst($usuario['estado']) ?></span></td>
                                    <td class="actions-cell">
                                        <button class="btn btn-small btn-info" onclick="verUsuario(<?= $usuario['id'] ?>)">👁️</button>
                                        <button class="btn btn-small btn-warning" onclick="openModal('editar', <?= $usuario['id'] ?>)">✏️</button>
                                        <?php if ($usuario['id'] != $_SESSION['user_id']): ?>
                                        <button class="btn btn-small btn-danger" onclick="confirmarEliminar(<?= $usuario['id'] ?>, '<?= htmlspecialchars($usuario['nombre']) ?>')">🗑️</button>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>

    <!-- Modal Usuario -->
    <div id="usuarioModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 id="modalTitle">Nuevo Usuario</h2>
                <button class="modal-close" onclick="closeModal()">&times;</button>
            </div>
            <form method="POST" class="modal-body">
                <input type="hidden" name="accion" id="accion" value="crear">
                <input type="hidden" name="id" id="usuario_id">
                
                <div class="form-group">
                    <label for="nombre">Nombre *</label>
                    <input type="text" id="nombre" name="nombre" required>
                </div>
                
                <div class="form-group">
                    <label for="email">Email *</label>
                    <input type="email" id="email" name="email" required>
                </div>
                
                <div class="form-group">
                    <label for="password">Contraseña <span id="passwordRequired">*</span></label>
                    <input type="password" id="password" name="password">
                    <small class="form-hint" id="passwordHint">Mínimo 6 caracteres</small>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="rol">Rol *</label>
                        <select id="rol" name="rol" required>
                            <option value="usuario">Usuario</option>
                            <option value="admin">Administrador</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="estado">Estado</label>
                        <select id="estado" name="estado">
                            <option value="activo">Activo</option>
                            <option value="inactivo">Inactivo</option>
                        </select>
                    </div>
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="closeModal()">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Guardar</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Ver Usuario -->
    <div id="verModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Detalles del Usuario</h2>
                <button class="modal-close" onclick="closeVerModal()">&times;</button>
            </div>
            <div class="modal-body" id="verModalBody"></div>
        </div>
    </div>

    <form method="POST" id="formEliminar" style="display:none;">
        <input type="hidden" name="accion" value="eliminar">
        <input type="hidden" name="id" id="eliminar_id">
    </form>

    <script src="<?php echo APP_URL; ?>/assets/js/script.js"></script>
    <script>
        const usuarios = <?= json_encode($usuarios) ?>;
        
        function openModal(accion, id = null) {
            const modal = document.getElementById('usuarioModal');
            const title = document.getElementById('modalTitle');
            const form = modal.querySelector('form');
            const passwordField = document.getElementById('password');
            const passwordRequired = document.getElementById('passwordRequired');
            const passwordHint = document.getElementById('passwordHint');
            
            form.reset();
            document.getElementById('accion').value = accion;
            
            if (accion === 'editar' && id) {
                title.textContent = 'Editar Usuario';
                const usuario = usuarios.find(u => u.id === id);
                if (usuario) {
                    document.getElementById('usuario_id').value = usuario.id;
                    document.getElementById('nombre').value = usuario.nombre;
                    document.getElementById('email').value = usuario.email;
                    document.getElementById('rol').value = usuario.rol;
                    document.getElementById('estado').value = usuario.estado;
                    passwordField.required = false;
                    passwordRequired.style.display = 'none';
                    passwordHint.textContent = 'Dejar vacío para mantener la contraseña actual';
                }
            } else {
                title.textContent = 'Nuevo Usuario';
                passwordField.required = true;
                passwordRequired.style.display = 'inline';
                passwordHint.textContent = 'Mínimo 6 caracteres';
            }
            
            modal.classList.add('active');
        }
        
        function closeModal() {
            document.getElementById('usuarioModal').classList.remove('active');
        }
        
        function verUsuario(id) {
            const usuario = usuarios.find(u => u.id === id);
            if (usuario) {
                const body = document.getElementById('verModalBody');
                body.innerHTML = `
                    <div class="producto-detalle">
                        <div class="detalle-row"><strong>Nombre:</strong> <span>${usuario.nombre}</span></div>
                        <div class="detalle-row"><strong>Email:</strong> <span>${usuario.email}</span></div>
                        <div class="detalle-row"><strong>Rol:</strong> <span class="badge badge-${usuario.rol === 'admin' ? 'badge-warning' : 'badge-info'}">${usuario.rol}</span></div>
                        <div class="detalle-row"><strong>Estado:</strong> <span class="badge badge-success">${usuario.estado}</span></div>
                        <div class="detalle-row"><strong>Fecha Creación:</strong> <span>${new Date(usuario.fecha_creacion).toLocaleDateString()}</span></div>
                    </div>
                `;
                document.getElementById('verModal').classList.add('active');
            }
        }
        
        function closeVerModal() {
            document.getElementById('verModal').classList.remove('active');
        }
        
        function confirmarEliminar(id, nombre) {
            if (confirm(`¿Está seguro de eliminar el usuario "${nombre}"?`)) {
                document.getElementById('eliminar_id').value = id;
                document.getElementById('formEliminar').submit();
            }
        }
    </script>
</body>
</html>