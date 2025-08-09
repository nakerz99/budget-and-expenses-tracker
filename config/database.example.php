<?php
/**
 * Database Configuration Example
 * Copy this file to database.php and update with your database credentials
 */

// Database connection settings
define('DB_HOST', 'localhost');     // Database host
define('DB_NAME', 'budget_planner'); // Database name
define('DB_USER', 'your_username');  // Database username
define('DB_PASS', 'your_password');  // Database password

// Database charset
define('DB_CHARSET', 'utf8mb4');

// PDO options
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
        die("Connection failed: " . $e->getMessage());
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
