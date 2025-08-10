<?php
/**
 * Database Configuration
 * Budget Planner Application
 * 
 * IMPORTANT: Update these credentials with your actual Hostinger database details
 * You can find these in your Hostinger control panel under "Databases" section
 */

// Database connection settings
define('DB_HOST', 'localhost');     // Usually 'localhost' for Hostinger
define('DB_NAME', 'your_hostinger_db_name'); // Your Hostinger database name
define('DB_USER', 'your_hostinger_db_user'); // Your Hostinger database username
define('DB_PASS', 'your_hostinger_db_password'); // Your Hostinger database password

// Database charset
define('DB_CHARSET', 'utf8mb4');

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
        $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
        $pdo = new PDO($dsn, DB_USER, DB_PASS, PDO_OPTIONS);
        return $pdo;
    } catch (PDOException $e) {
        error_log("Database connection failed: " . $e->getMessage());
        throw new Exception("Database connection failed. Please check your configuration.");
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
?>
