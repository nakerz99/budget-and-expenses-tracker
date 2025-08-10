<?php
/**
 * Database Configuration
 * Budget Planner Application
 * 
 * This file now uses .env configuration through the Environment class
 */

// Load environment configuration
require_once __DIR__ . '/Environment.php';
Environment::load();

// Get database configuration from environment
$dbConfig = Environment::getDatabaseConfig();

// Database connection settings from .env
define('DB_HOST', $dbConfig['host']);
define('DB_NAME', $dbConfig['name']);
define('DB_USER', $dbConfig['user']);
define('DB_PASS', $dbConfig['pass']);
define('DB_CHARSET', $dbConfig['charset']);
define('DB_PORT', $dbConfig['port']);

// PDO options - optimized for shared hosting
define('PDO_OPTIONS', [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
    PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci"
]);

/**
 * Database connection function
 * @return PDO
 */
function getDatabaseConnection() {
    try {
        $dsn = "mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
        $pdo = new PDO($dsn, DB_USER, DB_PASS, PDO_OPTIONS);
        return $pdo;
    } catch (PDOException $e) {
        error_log("Database connection failed: " . $e->getMessage());
        throw new Exception("Database connection failed. Please check your .env configuration.");
    }
}

/**
 * Execute a query
 * @param string $sql
 * @param array $params
 * @return bool
 */
function executeQuery($sql, $params = []) {
    try {
        $pdo = getDatabaseConnection();
        $stmt = $pdo->prepare($sql);
        return $stmt->execute($params);
    } catch (PDOException $e) {
        error_log("Query execution failed: " . $e->getMessage());
        return false;
    }
}

/**
 * Fetch a single row
 * @param string $sql
 * @param array $params
 * @return array|false
 */
function fetchOne($sql, $params = []) {
    try {
        $pdo = getDatabaseConnection();
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetch();
    } catch (PDOException $e) {
        error_log("Fetch one failed: " . $e->getMessage());
        return false;
    }
}

/**
 * Fetch all rows
 * @param string $sql
 * @param array $params
 * @return array|false
 */
function fetchAll($sql, $params = []) {
    try {
        $pdo = getDatabaseConnection();
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        error_log("Fetch all failed: " . $e->getMessage());
        return false;
    }
}

/**
 * Get the last inserted ID
 * @return string
 */
function getLastInsertId() {
    $pdo = getDatabaseConnection();
    return $pdo->lastInsertId();
}
?>
