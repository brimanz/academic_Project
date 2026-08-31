-- ============================================
-- CENTRO ÓPTICO LA ECONÓMICA F.P.
-- Base de Datos Mejorada - MySQL/MariaDB
-- ============================================

CREATE DATABASE IF NOT EXISTS `optica_economica` 
CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE `optica_economica`;

-- -------------------------------------------
-- 1. TIPOS DE USUARIO
-- -------------------------------------------
CREATE TABLE `tipo_usuario` (
  `id_tipo_usuario` int(11) NOT NULL AUTO_INCREMENT,
  `descripcion` varchar(30) NOT NULL,
  PRIMARY KEY (`id_tipo_usuario`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `tipo_usuario` (`descripcion`) VALUES
('Jefe'),
('Gerente General'),
('Administrador'),
('Empleado');

-- -------------------------------------------
-- 2. USUARIOS (Login del sistema)
-- -------------------------------------------
CREATE TABLE `usuario` (
  `cedula` varchar(20) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `apellido` varchar(50) NOT NULL,
  `telefono` varchar(20) NOT NULL,
  `correo` varchar(100) NOT NULL,
  `direccion` text NOT NULL,
  `nombre_usuario` varchar(50) NOT NULL UNIQUE,
  `contrasena` varchar(255) NOT NULL,
  `id_tipo_usuario` int(11) DEFAULT NULL,
  `fecha_registro` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`cedula`),
  KEY `fk_usuario_tipo` (`id_tipo_usuario`),
  CONSTRAINT `fk_usuario_tipo` FOREIGN KEY (`id_tipo_usuario`) 
    REFERENCES `tipo_usuario` (`id_tipo_usuario`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------
-- 3. EMPLEADOS
-- -------------------------------------------
CREATE TABLE `empleados` (
  `cedula` varchar(20) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `apellido` varchar(50) NOT NULL,
  `correo` varchar(100) NOT NULL,
  `telefono` varchar(20) NOT NULL,
  `direccion` text NOT NULL,
  `fecha_registro` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`cedula`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------
-- 4. CATEGORÍAS
-- -------------------------------------------
CREATE TABLE `categoria` (
  `id_categoria` int(10) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(50) NOT NULL,
  `tipo` varchar(50) DEFAULT NULL,
  `descripcion` text NOT NULL,
  PRIMARY KEY (`id_categoria`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------
-- 5. PRODUCTOS
-- -------------------------------------------
CREATE TABLE `productos` (
  `codigo` varchar(20) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `costo_compra` decimal(10,2) DEFAULT 0.00,
  `precio_venta` decimal(10,2) DEFAULT 0.00,
  `stock` int(10) NOT NULL DEFAULT 0,
  `id_categoria` int(10) DEFAULT NULL,
  `fecha_registro` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`codigo`),
  KEY `fk_prod_categoria` (`id_categoria`),
  CONSTRAINT `fk_prod_categoria` FOREIGN KEY (`id_categoria`) 
    REFERENCES `categoria` (`id_categoria`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------
-- 6. PROVEEDORES
-- -------------------------------------------
CREATE TABLE `proveedores` (
  `rif` varchar(20) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `apellido` varchar(50) NOT NULL,
  `empresa` varchar(100) NOT NULL,
  `correo` varchar(100) NOT NULL,
  `telefono` varchar(20) NOT NULL,
  `direccion` text NOT NULL,
  `fecha_registro` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`rif`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;