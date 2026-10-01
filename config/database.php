<?php
/**
 * Clase de Conexión a Base de Datos
 * Sistema de Inventario Moderno
 */

class Database {
    
    private $host;
    private $db_name;
    private $username;
    private $password;
    private $conn;
    private static $instance = null;
    
    private function __construct() {
        if (file_exists(__DIR__ . '/config.php')) {
            require_once __DIR__ . '/config.php';
        }
        
        $this->host = defined('DB_HOST') ? DB_HOST : 'localhost';
        $this->db_name = defined('DB_NAME') ? DB_NAME : 'sistema_inventario';
        $this->username = defined('DB_USER') ? DB_USER : 'root';
        $this->password = defined('DB_PASS') ? DB_PASS : '';
    }
    
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new Database();
        }
        return self::$instance;
    }
    
    public function getConnection() {
        if ($this->conn !== null) {
            return $this->conn;
        }
        
        try {
            $dsn = "mysql:host=" . $this->host . ";dbname=" . $this->db_name . ";charset=utf8mb4";
            
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
                PDO::ATTR_PERSISTENT => false,
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci"
            ];
            
            $this->conn = new PDO($dsn, $this->username, $this->password, $options);
            $this->conn->exec("SET time_zone = '-05:00'");
            
            return $this->conn;
            
        } catch (PDOException $e) {
            error_log("Error de conexión a BD: " . $e->getMessage());
            
            if (defined('APP_DEBUG') && APP_DEBUG === true) {
                die("Error de conexión: " . $e->getMessage());
            } else {
                die("Error al conectar con la base de datos.");
            }
        }
    }
    
    public function closeConnection() {
        $this->conn = null;
        self::$instance = null;
    }
    
    public function isConnected() {
        return $this->conn !== null;
    }
    
    private function __clone() {}
    
    public function __wakeup() {
        throw new Exception("No se puede deserializar el singleton");
    }
}

/**
 * Función auxiliar para obtener la conexión rápidamente
 */
function getDB() {
    return Database::getInstance()->getConnection();
}

/**
 * Función para ejecutar consultas preparadas
 */
function executeQuery($sql, $params = []) {
    $db = getDB();
    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    return $stmt;
}

/**
 * Función para obtener un solo registro
 */
function fetchOne($sql, $params = []) {
    $stmt = executeQuery($sql, $params);
    return $stmt->fetch();
}

/**
 * Función para obtener múltiples registros
 */
function fetchAll($sql, $params = []) {
    $stmt = executeQuery($sql, $params);
    return $stmt->fetchAll();
}

/**
 * Función para insertar y obtener el último ID
 */
function insertAndGetId($sql, $params = []) {
    $db = getDB();
    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    return $db->lastInsertId();
}

/**
 * Función para contar registros
 */
function countRecords($table, $where = '', $params = []) {
    $sql = "SELECT COUNT(*) as total FROM {$table}";
    if (!empty($where)) {
        $sql .= " WHERE {$where}";
    }
    $stmt = executeQuery($sql, $params);
    $result = $stmt->fetch();
    return (int)$result['total'];
}

/**
 * Función para verificar si existe un registro
 */
function recordExists($table, $column, $value) {
    $sql = "SELECT COUNT(*) as total FROM {$table} WHERE {$column} = ?";
    $stmt = executeQuery($sql, [$value]);
    $result = $stmt->fetch();
    return (int)$result['total'] > 0;
}

/**
 * Función para iniciar una transacción
 */
function beginTransaction() {
    $db = getDB();
    return $db->beginTransaction();
}

/**
 * Función para confirmar una transacción
 */
function commitTransaction() {
    $db = getDB();
    return $db->commit();
}

/**
 * Función para revertir una transacción
 */
function rollbackTransaction() {
    $db = getDB();
    return $db->rollBack();
}
?>