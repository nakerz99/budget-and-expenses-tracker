<?php
/**
 * Quick Setup Script for Hostinger
 * This script will help you set up the database quickly
 */

echo "<h1>NR BUDGET Planner - Quick Setup</h1>";

// Check if database connection works
echo "<h2>Step 1: Test Database Connection</h2>";
try {
    require_once '../config/database.php';
    $pdo = getDatabaseConnection();
    echo "✅ Database connection successful!<br>";
    echo "Database: " . DB_NAME . "<br>";
    echo "Host: " . DB_HOST . ":" . DB_PORT . "<br>";
} catch (Exception $e) {
    echo "❌ Database connection failed: " . $e->getMessage() . "<br>";
    echo "<br><strong>Please update your .env file with correct database credentials first!</strong><br>";
    exit;
}

// Check if tables exist
echo "<h2>Step 2: Check Database Tables</h2>";
$sql = "SHOW TABLES";
$tables = fetchAll($sql);

if ($tables) {
    echo "✅ Database tables found: " . count($tables) . "<br>";
    foreach ($tables as $table) {
        $tableName = array_values($table)[0];
        echo "- " . $tableName . "<br>";
    }
    
    echo "<br><strong>Database is already set up!</strong><br>";
    echo "<a href='../index.php' class='btn btn-success'>Go to Dashboard</a>";
    
} else {
    echo "⚠️ No tables found. You need to import the database schema.<br>";
    echo "<br><strong>Option 1: Import SQL File (Recommended)</strong><br>";
    echo "1. Go to your Hostinger control panel<br>";
    echo "2. Navigate to phpMyAdmin<br>";
    echo "3. Select your database<br>";
    echo "4. Import the file: <code>database/hostinger-compatible.sql</code><br>";
    echo "5. Refresh this page<br>";
    
    echo "<br><strong>Option 2: Run Migrations</strong><br>";
    echo "<a href='setup.php' class='btn btn-primary'>Run Full Setup</a>";
}

echo "<br><br><a href='../setup-hosting.php' class='btn btn-info'>Back to Setup Guide</a>";
?>

<style>
body { font-family: Arial, sans-serif; margin: 20px; }
.btn { display: inline-block; padding: 10px 20px; margin: 5px; text-decoration: none; border-radius: 5px; }
.btn-primary { background: #007bff; color: white; }
.btn-success { background: #28a745; color: white; }
.btn-info { background: #17a2b8; color: white; }
code { background: #f8f9fa; padding: 2px 4px; border-radius: 3px; }
</style>
