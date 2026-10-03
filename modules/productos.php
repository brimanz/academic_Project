<?php
$page_title = "Gestión de Productos";
require '../config.php';
require '../includes/header.php';

// Procesar Eliminación (Soft Delete)
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $pdo->prepare("UPDATE productos SET estado = 0 WHERE id = ?")->execute([$id]);
    header('Location: productos.php');
    exit;
}

// Obtener datos para editar
$edit_data = null;
if (isset($_GET['edit'])) {
    $id = (int)$_GET['edit'];
    $stmt = $pdo->prepare("SELECT * FROM productos WHERE id = ? AND estado = 1");
    $stmt->execute([$id]);
    $edit_data = $stmt->fetch();
}

// Obtener categorías y proveedores para los selects
$categorias = $pdo->query("SELECT * FROM categorias WHERE estado = 1 ORDER BY nombre")->fetchAll();
$proveedores = $pdo->query("SELECT * FROM proveedores WHERE estado = 1 ORDER BY nombre")->fetchAll();

// Listar productos con JOIN
$productos = $pdo->query("SELECT p.*, c.nombre as categoria, pr.nombre as proveedor 
                          FROM productos p 
                          LEFT JOIN categorias c ON p.id_categoria = c.id 
                          LEFT JOIN proveedores pr ON p.id_proveedor = pr.id 
                          WHERE p.estado = 1 ORDER BY p.id DESC")->fetchAll();
?>

<div class="glass-card" style="max-width: 700px; margin-bottom: 30px;">
    <h2><?= $edit_data ? 'Editar' : 'Registrar' ?> Producto</h2>
    <form action="productos_action.php" method="POST">
        <input type="hidden" name="id" value="<?= $edit_data['id'] ?? '' ?>">
        
        <div class="form-group">
            <label>Código / SKU *</label>
            <input type="text" name="codigo" value="<?= htmlspecialchars($edit_data['codigo'] ?? '') ?>" 
                   placeholder="Ej: LENT-001" required>
        </div>
        
        <div class="form-group">
            <label>Nombre del Producto *</label>
            <input type="text" name="nombre" value="<?= htmlspecialchars($edit_data['nombre'] ?? '') ?>" 
                   placeholder="Ej: Lentes de contacto mensuales" required>
        </div>
        
        <div class="form-group">
            <label>Descripción</label>
            <textarea name="descripcion" rows="3" placeholder="Descripción opcional del producto"><?= htmlspecialchars($edit_data['descripcion'] ?? '') ?></textarea>
        </div>
        
        <div class="form-group">
            <label>Categoría *</label>
            <select name="id_categoria" required>
                <option value="">Seleccione una categoría...</option>
                <?php foreach($categorias as $cat): ?>
                    <option value="<?= $cat['id'] ?>" <?= ($edit_data['id_categoria'] == $cat['id']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($cat['nombre']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        
        <div class="form-group">
            <label>Proveedor *</label>
            <select name="id_proveedor" required>
                <option value="">Seleccione un proveedor...</option>
                <?php foreach($proveedores as $prov): ?>
                    <option value="<?= $prov['id'] ?>" <?= ($edit_data['id_proveedor'] == $prov['id']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($prov['nombre']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
            <div class="form-group">
                <label>Precio Compra *</label>
                <input type="number" step="0.01" min="0" name="precio_compra" 
                       value="<?= $edit_data['precio_compra'] ?? '0.00' ?>" required>
            </div>
            <div class="form-group">
                <label>Precio Venta *</label>
                <input type="number" step="0.01" min="0" name="precio_venta" 
                       value="<?= $edit_data['precio_venta'] ?? '0.00' ?>" required>
            </div>
        </div>
        
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
            <div class="form-group">
                <label>Stock Actual *</label>
                <input type="number" min="0" name="stock" 
                       value="<?= $edit_data['stock'] ?? '0' ?>" required>
            </div>
            <div class="form-group">
                <label>Stock Mínimo *</label>
                <input type="number" min="0" name="stock_minimo" 
                       value="<?= $edit_data['stock_minimo'] ?? '5' ?>" required>
            </div>
        </div>
        
        <div class="btn-group">
            <button type="submit" name="action" value="<?= $edit_data ? 'update' : 'create' ?>" class="btn btn-primary">
                <?= $edit_data ? '✓ Actualizar' : '💾 Guardar' ?>
            </button>
            <?php if($edit_data): ?>
                <a href="productos.php" class="btn btn-danger" style="text-decoration:none; display:flex; align-items:center; justify-content:center;">
                    ✕ Cancelar
                </a>
            <?php endif; ?>
        </div>
    </form>
</div>

<div class="table-container">
    <h2>Inventario de Productos</h2>
    <?php if(count($productos) == 0): ?>
        <p style="text-align: center; color: var(--text-muted); padding: 40px;">
            No hay productos registrados. Comience agregando el primero.
        </p>
    <?php else: ?>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Código</th>
                    <th>Producto</th>
                    <th>Categoría</th>
                    <th>Proveedor</th>
                    <th>Stock</th>
                    <th>P. Venta</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($productos as $p): ?>
                <tr>
                    <td><strong><?= htmlspecialchars($p['codigo']) ?></strong></td>
                    <td><?= htmlspecialchars($p['nombre']) ?></td>
                    <td><?= htmlspecialchars($p['categoria'] ?? 'N/A') ?></td>
                    <td><?= htmlspecialchars($p['proveedor'] ?? 'N/A') ?></td>
                    <td style="color: <?= $p['stock'] <= $p['stock_minimo'] ? 'var(--danger)' : 'var(--success)' ?>; font-weight: bold;">
                        <?= $p['stock'] ?> <?= $p['stock'] <= $p['stock_minimo'] ? '⚠️' : '✓' ?>
                    </td>
                    <td>$<?= number_format($p['precio_venta'], 2) ?></td>
                    <td>
                        <a href="?edit=<?= $p['id'] ?>" style="color: var(--primary); margin-right: 10px; text-decoration: none;">
                            ✏️ Editar
                        </a>
                        <a href="?delete=<?= $p['id'] ?>" style="color: var(--danger); text-decoration: none;" 
                           onclick="return confirm('¿Está seguro de eliminar este producto?');">
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