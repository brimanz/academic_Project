<?php
$page_title = "Gestión de Categorías";
require '../config.php';
require '../includes/header.php';

// Procesar Eliminación
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $pdo->prepare("UPDATE categorias SET estado = 0 WHERE id = ?")->execute([$id]);
    header('Location: categorias.php');
    exit;
}

// Obtener datos para editar
$edit_data = null;
if (isset($_GET['edit'])) {
    $id = (int)$_GET['edit'];
    $stmt = $pdo->prepare("SELECT * FROM categorias WHERE id = ? AND estado = 1");
    $stmt->execute([$id]);
    $edit_data = $stmt->fetch();
}

// Listar categorías
$categorias = $pdo->query("SELECT * FROM categorias WHERE estado = 1 ORDER BY nombre")->fetchAll();
?>

<div class="glass-card" style="max-width: 600px; margin-bottom: 30px;">
    <h2><?= $edit_data ? 'Editar' : 'Registrar' ?> Categoría</h2>
    <form action="categorias_action.php" method="POST">
        <input type="hidden" name="id" value="<?= $edit_data['id'] ?? '' ?>">
        
        <div class="form-group">
            <label>Nombre de la Categoría *</label>
            <input type="text" name="nombre" value="<?= htmlspecialchars($edit_data['nombre'] ?? '') ?>" 
                   placeholder="Ej: Lentes de Contacto" required>
        </div>
        
        <div class="form-group">
            <label>Descripción</label>
            <textarea name="descripcion" rows="4" placeholder="Descripción opcional de la categoría"><?= htmlspecialchars($edit_data['descripcion'] ?? '') ?></textarea>
        </div>
        
        <div class="btn-group">
            <button type="submit" name="action" value="<?= $edit_data ? 'update' : 'create' ?>" class="btn btn-primary">
                <?= $edit_data ? '✓ Actualizar' : '💾 Guardar' ?>
            </button>
            <?php if($edit_data): ?>
                <a href="categorias.php" class="btn btn-danger" style="text-decoration:none; display:flex; align-items:center; justify-content:center;">
                    ✕ Cancelar
                </a>
            <?php endif; ?>
        </div>
    </form>
</div>

<div class="table-container">
    <h2>Listado de Categorías</h2>
    <?php if(count($categorias) == 0): ?>
        <p style="text-align: center; color: var(--text-muted); padding: 40px;">
            No hay categorías registradas.
        </p>
    <?php else: ?>
        <table class="data-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Descripción</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($categorias as $cat): ?>
                <tr>
                    <td><?= $cat['id'] ?></td>
                    <td><strong><?= htmlspecialchars($cat['nombre']) ?></strong></td>
                    <td><?= htmlspecialchars($cat['descripcion'] ?? 'Sin descripción') ?></td>
                    <td>
                        <a href="?edit=<?= $cat['id'] ?>" style="color: var(--primary); margin-right: 10px; text-decoration: none;">
                            ✏️ Editar
                        </a>
                        <a href="?delete=<?= $cat['id'] ?>" style="color: var(--danger); text-decoration: none;" 
                           onclick="return confirm('¿Está seguro de eliminar esta categoría?');">
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