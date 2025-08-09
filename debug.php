<?php
/**
 * Debug Page - For troubleshooting session and authentication issues
 */

session_start();
require_once 'includes/functions.php';

echo "<h1>NR BUDGET Planner - Debug Information</h1>";

echo "<h2>Session Information</h2>";
echo "<pre>";
print_r($_SESSION);
echo "</pre>";

echo "<h2>Authentication Status</h2>";
echo "isAuthenticated(): " . (isAuthenticated() ? "TRUE" : "FALSE") . "<br>";
echo "Session ID: " . session_id() . "<br>";

echo "<h2>Database Connection Test</h2>";
try {
    $pdo = getDBConnection();
    echo "Database connection: SUCCESS<br>";
    
    // Test user query
    $sql = "SELECT COUNT(*) as count FROM users";
    $result = fetchOne($sql);
    echo "Users in database: " . ($result ? $result['count'] : 'ERROR') . "<br>";
    
} catch (Exception $e) {
    echo "Database connection: FAILED - " . $e->getMessage() . "<br>";
}

echo "<h2>Actions</h2>";
echo "<a href='login.php?clear=1'>Clear Session and Go to Login</a><br>";
echo "<a href='logout.php'>Logout</a><br>";
echo "<a href='debug.php'>Refresh Debug Page</a><br>";

echo "<h2>Server Information</h2>";
echo "PHP Version: " . phpversion() . "<br>";
echo "Session Save Path: " . session_save_path() . "<br>";
echo "Session Name: " . session_name() . "<br>";
echo "Current Time: " . date('Y-m-d H:i:s') . "<br>";
?>
