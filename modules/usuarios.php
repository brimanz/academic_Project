<?php
$page_title = "Gestión de Usuarios";
require '../config.php';
require '../includes/header.php';

// Procesar Eliminación
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    if ($id == $_SESSION['user_id']) {
        die("Error: No puede eliminar su propio usuario.");
    }
    $pdo->prepare("UPDATE usuarios SET estado = 0 WHERE id = ?")->execute([$id]);
    header('Location: usuarios.php');
    exit;
}

// Obtener datos para editar
$edit_data = null;
if (isset($_GET['edit'])) {
    $id = (int)$_GET['edit'];
    $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE id = ? AND estado = 1");
    $stmt->execute([$id]);
    $edit_data = $stmt->fetch();
}

// Listar usuarios
$usuarios = $pdo->query("SELECT * FROM usuarios WHERE estado = 1 ORDER BY nombre")->fetchAll();
?>

<div class="glass-card" style="max-width: 600px; margin-bottom: 30px;">
    <h2><?= $edit_data ? 'Editar' : 'Registrar' ?> Usuario</h2>
    <form action="usuarios_action.php" method="POST">
        <input type="hidden" name="id" value="<?= $edit_data['id'] ?? '' ?>">
        
        <div class="form-group">
            <label>Nombre Completo *</label>
            <input type="text" name="nombre" value="<?= htmlspecialchars($edit_data['nombre'] ?? '') ?>" 
                   placeholder="Ej: Juan Pérez" required>
        </div>
        
        <div class="form-group">
            <label>Usuario *</label>
            <input type="text" name="usuario" value="<?= htmlspecialchars($edit_data['usuario'] ?? '') ?>" 
                   placeholder="Ej: jperez" required>
        </div>
        
        <div class="form-group">
            <label>Contraseña <?= $edit_data ? '(dejar vacío para no cambiar)' : '*' ?></label>
            <input type="password" name="password" <?= $edit_data ? '' : 'required' ?> 
                   placeholder="<?= $edit_data ? 'Nueva contraseña (opcional)' : 'Mínimo 6 caracteres' ?>">
        </div>
        
        <div class="form-group">
            <label>Rol *</label>
            <select name="rol" required>
                <option value="Vendedor" <?= ($edit_data['rol'] ?? '') == 'Vendedor' ? 'selected' : '' ?>>Vendedor</option>
                <option value="Administrador" <?= ($edit_data['rol'] ?? '') == 'Administrador' ? 'selected' : '' ?>>Administrador</option>
            </select>
        </div>
        
        <div class="btn-group">
            <button type="submit" name="action" value="<?= $edit_data ? 'update' : 'create' ?>" class="btn btn-primary">
                <?= $edit_data ? '✓ Actualizar' : '💾 Guardar' ?>
            </button>
            <?php if($edit_data): ?>
                <a href="usuarios.php" class="btn btn-danger" style="text-decoration:none; display:flex; align-items:center; justify-content:center;">
                    ✕ Cancelar
                </a>
            <?php endif; ?>
        </div>
    </form>
</div>

<div class="table-container">
    <h2>Listado de Usuarios</h2>
    <?php if(count($usuarios) == 0): ?>
        <p style="text-align: center; color: var(--text-muted); padding: 40px;">
            No hay usuarios registrados.
        </p>
    <?php else: ?>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Usuario</th>
                    <th>Rol</th>
                    <th>Fecha Registro</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($usuarios as $usr): ?>
                <tr>
                    <td><strong><?= htmlspecialchars($usr['nombre']) ?></strong></td>
                    <td><?= htmlspecialchars($usr['usuario']) ?></td>
                    <td>
                        <span style="padding: 4px 12px; border-radius: 20px; font-size: 0.85rem; font-weight: 600;
                              background: <?= $usr['rol'] == 'Administrador' ? 'rgba(37, 99, 235, 0.15)' : 'rgba(34, 197, 94, 0.15)' ?>;
                              color: <?= $usr['rol'] == 'Administrador' ? 'var(--primary-dark)' : 'var(--success)' ?>;">
                            <?= $usr['rol'] ?>
                        </span>
                    </td>
                    <td><?= date('d/m/Y', strtotime($usr['created_at'])) ?></td>
                    <td>
                        <a href="?edit=<?= $usr['id'] ?>" style="color: var(--primary); margin-right: 10px; text-decoration: none;">
                            ✏️ Editar
                        </a>
                        <?php if($usr['id'] != $_SESSION['user_id']): ?>
                            <a href="?delete=<?= $usr['id'] ?>" style="color: var(--danger); text-decoration: none;" 
                               onclick="return confirm('¿Está seguro de eliminar este usuario?');">
                                🗑️ Eliminar
                            </a>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<?php require '../includes/footer.php'; ?>