<?php
$page_title = "Gestión de Proveedores";
require '../config.php';
require '../includes/header.php';

// Procesar Eliminación
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $pdo->prepare("UPDATE proveedores SET estado = 0 WHERE id = ?")->execute([$id]);
    header('Location: proveedores.php');
    exit;
}

// Obtener datos para editar
$edit_data = null;
if (isset($_GET['edit'])) {
    $id = (int)$_GET['edit'];
    $stmt = $pdo->prepare("SELECT * FROM proveedores WHERE id = ? AND estado = 1");
    $stmt->execute([$id]);
    $edit_data = $stmt->fetch();
}

// Listar proveedores
$proveedores = $pdo->query("SELECT * FROM proveedores WHERE estado = 1 ORDER BY nombre")->fetchAll();
?>

<div class="glass-card" style="max-width: 700px; margin-bottom: 30px;">
    <h2><?= $edit_data ? 'Editar' : 'Registrar' ?> Proveedor</h2>
    <form action="proveedores_action.php" method="POST">
        <input type="hidden" name="id" value="<?= $edit_data['id'] ?? '' ?>">
        
        <div class="form-group">
            <label>Nombre del Proveedor *</label>
            <input type="text" name="nombre" value="<?= htmlspecialchars($edit_data['nombre'] ?? '') ?>" 
                   placeholder="Ej: Óptica Central C.A." required>
        </div>
        
        <div class="form-group">
            <label>RIF</label>
            <input type="text" name="rif" value="<?= htmlspecialchars($edit_data['rif'] ?? '') ?>" 
                   placeholder="Ej: J-12345678-9">
        </div>
        
        <div class="form-group">
            <label>Teléfono</label>
            <input type="tel" name="telefono" value="<?= htmlspecialchars($edit_data['telefono'] ?? '') ?>" 
                   placeholder="Ej: 0271-1234567">
        </div>
        
        <div class="form-group">
            <label>Dirección</label>
            <textarea name="direccion" rows="3" placeholder="Dirección fiscal del proveedor"><?= htmlspecialchars($edit_data['direccion'] ?? '') ?></textarea>
        </div>
        
        <div class="form-group">
            <label>Persona de Contacto</label>
            <input type="text" name="contacto" value="<?= htmlspecialchars($edit_data['contacto'] ?? '') ?>" 
                   placeholder="Nombre del contacto">
        </div>
        
        <div class="btn-group">
            <button type="submit" name="action" value="<?= $edit_data ? 'update' : 'create' ?>" class="btn btn-primary">
                <?= $edit_data ? '✓ Actualizar' : '💾 Guardar' ?>
            </button>
            <?php if($edit_data): ?>
                <a href="proveedores.php" class="btn btn-danger" style="text-decoration:none; display:flex; align-items:center; justify-content:center;">
                    ✕ Cancelar
                </a>
            <?php endif; ?>
        </div>
    </form>
</div>

<div class="table-container">
    <h2>Listado de Proveedores</h2>
    <?php if(count($proveedores) == 0): ?>
        <p style="text-align: center; color: var(--text-muted); padding: 40px;">
            No hay proveedores registrados.
        </p>
    <?php else: ?>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>RIF</th>
                    <th>Teléfono</th>
                    <th>Contacto</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($proveedores as $prov): ?>
                <tr>
                    <td><strong><?= htmlspecialchars($prov['nombre']) ?></strong></td>
                    <td><?= htmlspecialchars($prov['rif'] ?? 'N/A') ?></td>
                    <td><?= htmlspecialchars($prov['telefono'] ?? 'N/A') ?></td>
                    <td><?= htmlspecialchars($prov['contacto'] ?? 'N/A') ?></td>
                    <td>
                        <a href="?edit=<?= $prov['id'] ?>" style="color: var(--primary); margin-right: 10px; text-decoration: none;">
                            ✏️ Editar
                        </a>
                        <a href="?delete=<?= $prov['id'] ?>" style="color: var(--danger); text-decoration: none;" 
                           onclick="return confirm('¿Está seguro de eliminar este proveedor?');">
                            🗑️ Eliminar
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<?php require '../includes/footer.php'; ?>