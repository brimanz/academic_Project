-- Sistema de Inventario Moderno
-- Base de datos MySQL

CREATE DATABASE IF NOT EXISTS sistema_inventario;
USE sistema_inventario;

-- Tabla de usuarios
CREATE TABLE usuarios (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nombre VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    rol ENUM('admin', 'usuario') DEFAULT 'usuario',
    estado ENUM('activo', 'inactivo') DEFAULT 'activo',
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_actualizacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabla de proveedores
CREATE TABLE proveedores (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nombre VARCHAR(150) NOT NULL,
    contacto VARCHAR(100),
    telefono VARCHAR(20),
    email VARCHAR(100),
    direccion TEXT,
    estado ENUM('activo', 'inactivo') DEFAULT 'activo',
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_actualizacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabla de productos
CREATE TABLE productos (
    id INT PRIMARY KEY AUTO_INCREMENT,
    codigo VARCHAR(50) UNIQUE NOT NULL,
    nombre VARCHAR(150) NOT NULL,
    descripcion TEXT,
    categoria VARCHAR(100),
    precio DECIMAL(10,2) NOT NULL,
    stock INT NOT NULL DEFAULT 0,
    stock_minimo INT DEFAULT 10,
    proveedor_id INT,
    estado ENUM('activo', 'inactivo') DEFAULT 'activo',
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_actualizacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (proveedor_id) REFERENCES proveedores(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Usuario administrador por defecto (password: admin123)
INSERT INTO usuarios (nombre, email, password, rol) VALUES 
('Administrador', 'admin@sistema.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin');

-- Datos de ejemplo
INSERT INTO proveedores (nombre, contacto, telefono, email, direccion) VALUES 
('Proveedor ABC', 'Juan Pérez', '555-0101', 'contacto@abc.com', 'Calle Principal 123'),
('Distribuidora XYZ', 'María García', '555-0102', 'ventas@xyz.com', 'Av. Secundaria 456');

INSERT INTO productos (codigo, nombre, descripcion, categoria, precio, stock, stock_minimo, proveedor_id) VALUES 
('PROD001', 'Laptop Dell XPS', 'Laptop de alto rendimiento', 'Electrónica', 1299.99, 15, 5, 1),
('PROD002', 'Mouse Inalámbrico', 'Mouse ergonómico', 'Accesorios', 29.99, 50, 10, 2),
('PROD003', 'Teclado Mecánico', 'Teclado gaming RGB', 'Accesorios', 89.99, 8, 10, 2);