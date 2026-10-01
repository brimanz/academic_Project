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
        $codigo = sanitize($_POST['codigo']);
        $nombre = sanitize($_POST['nombre']);
        $descripcion = sanitize($_POST['descripcion']);
        $categoria = sanitize($_POST['categoria']);
        $precio = floatval($_POST['precio']);
        $stock = intval($_POST['stock']);
        $stock_minimo = intval($_POST['stock_minimo']);
        $proveedor_id = !empty($_POST['proveedor_id']) ? intval($_POST['proveedor_id']) : null;
        $estado = sanitize($_POST['estado']);
        
        if ($accion === 'crear') {
            $stmt = $pdo->prepare("INSERT INTO productos (codigo, nombre, descripcion, categoria, precio, stock, stock_minimo, proveedor_id, estado) 
                                   VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
            if ($stmt->execute([$codigo, $nombre, $descripcion, $categoria, $precio, $stock, $stock_minimo, $proveedor_id, $estado])) {
                $mensaje = 'Producto creado exitosamente';
                $tipo_mensaje = 'success';
            } else {
                $mensaje = 'Error al crear el producto';
                $tipo_mensaje = 'error';
            }
        } else {
            $id = intval($_POST['id']);
            $stmt = $pdo->prepare("UPDATE productos SET codigo=?, nombre=?, descripcion=?, categoria=?, precio=?, stock=?, stock_minimo=?, proveedor_id=?, estado=? 
                                   WHERE id=?");
            if ($stmt->execute([$codigo, $nombre, $descripcion, $categoria, $precio, $stock, $stock_minimo, $proveedor_id, $estado, $id])) {
                $mensaje = 'Producto actualizado exitosamente';
                $tipo_mensaje = 'success';
            } else {
                $mensaje = 'Error al actualizar el producto';
                $tipo_mensaje = 'error';
            }
        }
    } elseif ($accion === 'eliminar') {
        $id = intval($_POST['id']);
        $stmt = $pdo->prepare("UPDATE productos SET estado='inactivo' WHERE id=?");
        if ($stmt->execute([$id])) {
            $mensaje = 'Producto eliminado exitosamente';
            $tipo_mensaje = 'success';
        } else {
            $mensaje = 'Error al eliminar el producto';
            $tipo_mensaje = 'error';
        }
    }
}

// Obtener producto para editar
$producto_editar = null;
if (isset($_GET['editar'])) {
    $id = intval($_GET['editar']);
    $stmt = $pdo->prepare("SELECT * FROM productos WHERE id = ? AND estado = 'activo'");
    $stmt->execute([$id]);
    $producto_editar = $stmt->fetch();
}

// Obtener todos los productos
$stmt = $pdo->query("SELECT p.*, pr.nombre as proveedor_nombre 
                     FROM productos p 
                     LEFT JOIN proveedores pr ON p.proveedor_id = pr.id 
                     WHERE p.estado = 'activo' 
                     ORDER BY p.fecha_creacion DESC");
$productos = $stmt->fetchAll();

// Obtener proveedores
$proveedores = $pdo->query("SELECT * FROM proveedores WHERE estado = 'activo' ORDER BY nombre")->fetchAll();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Productos - Sistema de Inventario</title>
    <link rel="stylesheet" href="./assets/css/styles.css">
</head>
<body>
    <!-- Navegación Superior -->
    <nav class="navbar">
        <div class="navbar-brand">
            <span class="logo">📦</span>
            <span class="brand-name">Sistema de Inventario</span>
        </div>
        <ul class="navbar-menu">
            <li><a href="<?php echo APP_URL; ?>/dashboard.php" class="nav-link">Dashboard</a></li>
            <li><a href="<?php echo APP_URL; ?>/modules/productos.php" class="nav-link active">Productos</a></li>
            <li><a href="<?php echo APP_URL; ?>/modules/proveedores.php" class="nav-link">Proveedores</a></li>
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
            <h1>Gestión de Productos</h1>
            <button class="btn btn-primary" onclick="openModal('crear')">+ Nuevo Producto</button>
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
                                <th>Código</th>
                                <th>Nombre</th>
                                <th>Categoría</th>
                                <th>Precio</th>
                                <th>Stock</th>
                                <th>Proveedor</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($productos)): ?>
                                <tr>
                                    <td colspan="7" class="empty-state">No hay productos registrados</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($productos as $producto): ?>
                                <tr class="<?= $producto['stock'] <= $producto['stock_minimo'] ? 'row-warning' : '' ?>">
                                    <td><?= htmlspecialchars($producto['codigo']) ?></td>
                                    <td><?= htmlspecialchars($producto['nombre']) ?></td>
                                    <td><?= htmlspecialchars($producto['categoria']) ?></td>
                                    <td>$<?= number_format($producto['precio'], 2) ?></td>
                                    <td>
                                        <span class="badge <?= $producto['stock'] <= $producto['stock_minimo'] ? 'badge-warning' : 'badge-success' ?>">
                                            <?= $producto['stock'] ?>
                                        </span>
                                    </td>
                                    <td><?= htmlspecialchars($producto['proveedor_nombre'] ?? 'N/A') ?></td>
                                    <td class="actions-cell">
                                        <button class="btn btn-small btn-info" onclick="verProducto(<?= $producto['id'] ?>)">👁️</button>
                                        <button class="btn btn-small btn-warning" onclick="openModal('editar', <?= $producto['id'] ?>)">✏️</button>
                                        <button class="btn btn-small btn-danger" onclick="confirmarEliminar(<?= $producto['id'] ?>, '<?= htmlspecialchars($producto['nombre']) ?>')">🗑️</button>
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

    <!-- Modal Producto -->
    <div id="productoModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 id="modalTitle">Nuevo Producto</h2>
                <button class="modal-close" onclick="closeModal()">&times;</button>
            </div>
            <form method="POST" class="modal-body">
                <input type="hidden" name="accion" id="accion" value="crear">
                <input type="hidden" name="id" id="producto_id">
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="codigo">Código *</label>
                        <input type="text" id="codigo" name="codigo" required>
                    </div>
                    <div class="form-group">
                        <label for="nombre">Nombre *</label>
                        <input type="text" id="nombre" name="nombre" required>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="descripcion">Descripción</label>
                    <textarea id="descripcion" name="descripcion" rows="3"></textarea>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="categoria">Categoría *</label>
                        <input type="text" id="categoria" name="categoria" required>
                    </div>
                    <div class="form-group">
                        <label for="proveedor_id">Proveedor</label>
                        <select id="proveedor_id" name="proveedor_id">
                            <option value="">Seleccionar...</option>
                            <?php foreach ($proveedores as $prov): ?>
                                <option value="<?= $prov['id'] ?>"><?= htmlspecialchars($prov['nombre']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="precio">Precio *</label>
                        <input type="number" id="precio" name="precio" step="0.01" min="0" required>
                    </div>
                    <div class="form-group">
                        <label for="stock">Stock *</label>
                        <input type="number" id="stock" name="stock" min="0" required>
                    </div>
                    <div class="form-group">
                        <label for="stock_minimo">Stock Mínimo</label>
                        <input type="number" id="stock_minimo" name="stock_minimo" min="0" value="10">
                    </div>
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

    <!-- Modal Ver Producto -->
    <div id="verModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Detalles del Producto</h2>
                <button class="modal-close" onclick="closeVerModal()">&times;</button>
            </div>
            <div class="modal-body" id="verModalBody">
                <!-- Contenido dinámico -->
            </div>
        </div>
    </div>

    <!-- Formulario Eliminar -->
    <form method="POST" id="formEliminar" style="display:none;">
        <input type="hidden" name="accion" value="eliminar">
        <input type="hidden" name="id" id="eliminar_id">
    </form>

    <script src="<?php echo APP_URL; ?>/assets/js/script.js"></script>
    <script>
        const productos = <?= json_encode($productos) ?>;
        
        function openModal(accion, id = null) {
            const modal = document.getElementById('productoModal');
            const title = document.getElementById('modalTitle');
            const form = modal.querySelector('form');
            
            form.reset();
            document.getElementById('accion').value = accion;
            
            if (accion === 'editar' && id) {
                title.textContent = 'Editar Producto';
                const producto = productos.find(p => p.id === id);
                if (producto) {
                    document.getElementById('producto_id').value = producto.id;
                    document.getElementById('codigo').value = producto.codigo;
                    document.getElementById('nombre').value = producto.nombre;
                    document.getElementById('descripcion').value = producto.descripcion || '';
                    document.getElementById('categoria').value = producto.categoria;
                    document.getElementById('precio').value = producto.precio;
                    document.getElementById('stock').value = producto.stock;
                    document.getElementById('stock_minimo').value = producto.stock_minimo;
                    document.getElementById('proveedor_id').value = producto.proveedor_id || '';
                    document.getElementById('estado').value = producto.estado;
                }
            } else {
                title.textContent = 'Nuevo Producto';
            }
            
            modal.classList.add('active');
        }
        
        function closeModal() {
            document.getElementById('productoModal').classList.remove('active');
        }
        
        function verProducto(id) {
            const producto = productos.find(p => p.id === id);
            if (producto) {
                const body = document.getElementById('verModalBody');
                body.innerHTML = `
                    <div class="producto-detalle">
                        <div class="detalle-row"><strong>Código:</strong> <span>${producto.codigo}</span></div>
                        <div class="detalle-row"><strong>Nombre:</strong> <span>${producto.nombre}</span></div>
                        <div class="detalle-row"><strong>Descripción:</strong> <span>${producto.descripcion || 'N/A'}</span></div>
                        <div class="detalle-row"><strong>Categoría:</strong> <span>${producto.categoria}</span></div>
                        <div class="detalle-row"><strong>Precio:</strong> <span>$${parseFloat(producto.precio).toFixed(2)}</span></div>
                        <div class="detalle-row"><strong>Stock:</strong> <span>${producto.stock}</span></div>
                        <div class="detalle-row"><strong>Stock Mínimo:</strong> <span>${producto.stock_minimo}</span></div>
                        <div class="detalle-row"><strong>Proveedor:</strong> <span>${producto.proveedor_nombre || 'N/A'}</span></div>
                        <div class="detalle-row"><strong>Estado:</strong> <span class="badge badge-success">${producto.estado}</span></div>
                        <div class="detalle-row"><strong>Fecha Creación:</strong> <span>${new Date(producto.fecha_creacion).toLocaleDateString()}</span></div>
                    </div>
                `;
                document.getElementById('verModal').classList.add('active');
            }
        }
        
        function closeVerModal() {
            document.getElementById('verModal').classList.remove('active');
        }
        
        function confirmarEliminar(id, nombre) {
            if (confirm(`¿Está seguro de eliminar el producto "${nombre}"?`)) {
                document.getElementById('eliminar_id').value = id;
                document.getElementById('formEliminar').submit();
            }
        }
    </script>
</body>
</html>