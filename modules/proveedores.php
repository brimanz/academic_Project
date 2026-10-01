<?php
require_once __DIR__ . '/../config/config.php';
checkAuth();

$pdo = getDB();
$mensaje = '';
$tipo_mensaje = '';

// Procesar formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $accion = sanitize($_POST['accion'] ?? '');
    
    if ($accion === 'crear' || $accion === 'editar') {
        $nombre = sanitize($_POST['nombre']);
        $contacto = sanitize($_POST['contacto']);
        $telefono = sanitize($_POST['telefono']);
        $email = sanitize($_POST['email']);
        $direccion = sanitize($_POST['direccion']);
        $estado = sanitize($_POST['estado']);
        
        if ($accion === 'crear') {
            $stmt = $pdo->prepare("INSERT INTO proveedores (nombre, contacto, telefono, email, direccion, estado) 
                                   VALUES (?, ?, ?, ?, ?, ?)");
            if ($stmt->execute([$nombre, $contacto, $telefono, $email, $direccion, $estado])) {
                $mensaje = 'Proveedor creado exitosamente';
                $tipo_mensaje = 'success';
            } else {
                $mensaje = 'Error al crear el proveedor';
                $tipo_mensaje = 'error';
            }
        } else {
            $id = intval($_POST['id']);
            $stmt = $pdo->prepare("UPDATE proveedores SET nombre=?, contacto=?, telefono=?, email=?, direccion=?, estado=? 
                                   WHERE id=?");
            if ($stmt->execute([$nombre, $contacto, $telefono, $email, $direccion, $estado, $id])) {
                $mensaje = 'Proveedor actualizado exitosamente';
                $tipo_mensaje = 'success';
            } else {
                $mensaje = 'Error al actualizar el proveedor';
                $tipo_mensaje = 'error';
            }
        }
    } elseif ($accion === 'eliminar') {
        $id = intval($_POST['id']);
        $stmt = $pdo->prepare("UPDATE proveedores SET estado='inactivo' WHERE id=?");
        if ($stmt->execute([$id])) {
            $mensaje = 'Proveedor eliminado exitosamente';
            $tipo_mensaje = 'success';
        } else {
            $mensaje = 'Error al eliminar el proveedor';
            $tipo_mensaje = 'error';
        }
    }
}

// Obtener todos los proveedores
$proveedores = $pdo->query("SELECT * FROM proveedores WHERE estado = 'activo' ORDER BY nombre")->fetchAll();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Proveedores - Sistema de Inventario</title>
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
            <li><a href="<?php echo APP_URL; ?>/modules/proveedores.php" class="nav-link active">Proveedores</a></li>
            <?php if ($_SESSION['user_rol'] === 'admin'): ?>
            <li><a href="<?php echo APP_URL; ?>/modules/usuarios.php" class="nav-link">Usuarios</a></li>
            <?php endif; ?>
        </ul>
        <div class="navbar-user">
            <span class="user-name"><?= htmlspecialchars($_SESSION['user_name']) ?></span>
            <a href="<?php echo APP_URL; ?>/logout.php" class="btn btn-logout">Cerrar Sesión</a>
        </div>
    </nav>

    <main class="main-content">
        <div class="page-header">
            <h1>Gestión de Proveedores</h1>
            <button class="btn btn-primary" onclick="openModal('crear')">+ Nuevo Proveedor</button>
        </div>

        <?php if ($mensaje): ?>
            <div class="alert alert-<?= $tipo_mensaje ?>">
                <span class="alert-icon"><?= $tipo_mensaje === 'success' ? '✓' : '️' ?></span>
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
                                <th>Contacto</th>
                                <th>Teléfono</th>
                                <th>Email</th>
                                <th>Dirección</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($proveedores)): ?>
                                <tr>
                                    <td colspan="6" class="empty-state">No hay proveedores registrados</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($proveedores as $proveedor): ?>
                                <tr>
                                    <td><?= htmlspecialchars($proveedor['nombre']) ?></td>
                                    <td><?= htmlspecialchars($proveedor['contacto']) ?></td>
                                    <td><?= htmlspecialchars($proveedor['telefono']) ?></td>
                                    <td><?= htmlspecialchars($proveedor['email']) ?></td>
                                    <td><?= htmlspecialchars($proveedor['direccion']) ?></td>
                                    <td class="actions-cell">
                                        <button class="btn btn-small btn-info" onclick="verProveedor(<?= $proveedor['id'] ?>)">👁️</button>
                                        <button class="btn btn-small btn-warning" onclick="openModal('editar', <?= $proveedor['id'] ?>)">✏️</button>
                                        <button class="btn btn-small btn-danger" onclick="confirmarEliminar(<?= $proveedor['id'] ?>, '<?= htmlspecialchars($proveedor['nombre']) ?>')">🗑️</button>
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

    <!-- Modal Proveedor -->
    <div id="proveedorModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 id="modalTitle">Nuevo Proveedor</h2>
                <button class="modal-close" onclick="closeModal()">&times;</button>
            </div>
            <form method="POST" class="modal-body">
                <input type="hidden" name="accion" id="accion" value="crear">
                <input type="hidden" name="id" id="proveedor_id">
                
                <div class="form-group">
                    <label for="nombre">Nombre *</label>
                    <input type="text" id="nombre" name="nombre" required>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="contacto">Contacto</label>
                        <input type="text" id="contacto" name="contacto">
                    </div>
                    <div class="form-group">
                        <label for="telefono">Teléfono</label>
                        <input type="tel" id="telefono" name="telefono">
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email">
                </div>
                
                <div class="form-group">
                    <label for="direccion">Dirección</label>
                    <textarea id="direccion" name="direccion" rows="3"></textarea>
                </div>
                
                <div class="form-group">
                    <label for="estado">Estado</label>
                    <select id="estado" name="estado">
                        <option value="activo">Activo</option>
                        <option value="inactivo">Inactivo</option>
                    </select>
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="closeModal()">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Guardar</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Ver Proveedor -->
    <div id="verModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Detalles del Proveedor</h2>
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
        const proveedores = <?= json_encode($proveedores) ?>;
        
        function openModal(accion, id = null) {
            const modal = document.getElementById('proveedorModal');
            const title = document.getElementById('modalTitle');
            const form = modal.querySelector('form');
            
            form.reset();
            document.getElementById('accion').value = accion;
            
            if (accion === 'editar' && id) {
                title.textContent = 'Editar Proveedor';
                const proveedor = proveedores.find(p => p.id === id);
                if (proveedor) {
                    document.getElementById('proveedor_id').value = proveedor.id;
                    document.getElementById('nombre').value = proveedor.nombre;
                    document.getElementById('contacto').value = proveedor.contacto || '';
                    document.getElementById('telefono').value = proveedor.telefono || '';
                    document.getElementById('email').value = proveedor.email || '';
                    document.getElementById('direccion').value = proveedor.direccion || '';
                    document.getElementById('estado').value = proveedor.estado;
                }
            } else {
                title.textContent = 'Nuevo Proveedor';
            }
            
            modal.classList.add('active');
        }
        
        function closeModal() {
            document.getElementById('proveedorModal').classList.remove('active');
        }
        
        function verProveedor(id) {
            const proveedor = proveedores.find(p => p.id === id);
            if (proveedor) {
                const body = document.getElementById('verModalBody');
                body.innerHTML = `
                    <div class="producto-detalle">
                        <div class="detalle-row"><strong>Nombre:</strong> <span>${proveedor.nombre}</span></div>
                        <div class="detalle-row"><strong>Contacto:</strong> <span>${proveedor.contacto || 'N/A'}</span></div>
                        <div class="detalle-row"><strong>Teléfono:</strong> <span>${proveedor.telefono || 'N/A'}</span></div>
                        <div class="detalle-row"><strong>Email:</strong> <span>${proveedor.email || 'N/A'}</span></div>
                        <div class="detalle-row"><strong>Dirección:</strong> <span>${proveedor.direccion || 'N/A'}</span></div>
                        <div class="detalle-row"><strong>Estado:</strong> <span class="badge badge-success">${proveedor.estado}</span></div>
                    </div>
                `;
                document.getElementById('verModal').classList.add('active');
            }
        }
        
        function closeVerModal() {
            document.getElementById('verModal').classList.remove('active');
        }
        
        function confirmarEliminar(id, nombre) {
            if (confirm(`¿Está seguro de eliminar el proveedor "${nombre}"?`)) {
                document.getElementById('eliminar_id').value = id;
                document.getElementById('formEliminar').submit();
            }
        }
    </script>
</body>
</html>