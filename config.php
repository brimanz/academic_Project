<?php
// ============================================
// CONFIGURACIÓN DE BASE DE DATOS
// ============================================
$host = '127.0.0.1';
$dbname = 'optica_economica';
$username = 'root';      // Cambiar según tu servidor
$password = '';          // Cambiar según tu servidor
$charset = 'utf8mb4';
$dsn = "mysql:host=$host;dbname=$dbname;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $username, $password, $options);
} catch (\PDOException $e) {
    die('Error de conexión: ' . $e->getMessage());
}

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>