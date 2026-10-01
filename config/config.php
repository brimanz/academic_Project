<?php
/**
 * Configuración General del Sistema
 * Sistema de Inventario Moderno
 */

// ============================================
// CONFIGURACIÓN DE LA BASE DE DATOS
// ============================================
define('DB_HOST', 'localhost');
define('DB_NAME', 'sistema_inventario');
define('DB_USER', 'root');
define('DB_PASS', '');

// ============================================
// CONFIGURACIÓN DE RUTAS (CORREGIDO)
// ============================================

// Ruta base del servidor (Document Root)
define('BASE_PATH', $_SERVER['DOCUMENT_ROOT']);

// Ruta del proyecto (se calcula automáticamente)
define('PROJECT_PATH', dirname(__DIR__));

// URL base calculada dinámicamente
$scriptDir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME']));
$projectDir = str_replace('\\', '/', PROJECT_PATH);
$docRoot = str_replace('\\', '/', BASE_PATH);

// Calcular la ruta relativa desde el document root
$relativePath = str_replace($docRoot, '', $projectDir);
define('BASE_URL', $relativePath);

// URL completa de la aplicación
define('APP_URL', 'http://' . $_SERVER['HTTP_HOST'] . BASE_URL);

// Ruta de assets (siempre relativa al proyecto)
define('ASSETS_URL', BASE_URL . '/assets');

// ============================================
// CONFIGURACIÓN DE LA APLICACIÓN
// ============================================
define('APP_NAME', 'Sistema de Inventario Moderno');
define('APP_DEBUG', true);

// ============================================
// CONFIGURACIÓN DE SESIÓN
// ============================================
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ============================================
// INCLUIR CONEXIÓN A BASE DE DATOS
// ============================================
require_once __DIR__ . '/database.php';

// ============================================
// FUNCIONES AUXILIARES
// ============================================

function checkAuth() {
    if (!isset($_SESSION['user_id'])) {
        header('Location: ' . BASE_URL . '/index.php');
        exit();
    }
}

function checkAdmin() {
    checkAuth();
    if ($_SESSION['user_rol'] !== 'admin') {
        header('Location: ' . BASE_URL . '/dashboard.php');
        exit();
    }
}

function sanitize($data) {
    return htmlspecialchars(strip_tags(trim($data)), ENT_QUOTES, 'UTF-8');
}

function hashPassword($password) {
    return password_hash($password, PASSWORD_DEFAULT);
}

function verifyPassword($password, $hash) {
    return password_verify($password, $hash);
}

function redirect($url) {
    header('Location: ' . BASE_URL . '/' . $url);
    exit();
}

function setFlashMessage($type, $message) {
    $_SESSION['flash_message'] = [
        'type' => $type,
        'message' => $message
    ];
}

function getFlashMessage() {
    if (isset($_SESSION['flash_message'])) {
        $message = $_SESSION['flash_message'];
        unset($_SESSION['flash_message']);
        return $message;
    }
    return null;
}

// Función helper para obtener URL de assets
function asset($path) {
    return BASE_URL . '/assets/' . ltrim($path, '/');
}

// Función helper para obtener URL interna
function url($path) {
    return BASE_URL . '/' . ltrim($path, '/');
}
?>