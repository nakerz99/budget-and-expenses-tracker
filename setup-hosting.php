<?php
/**
 * Hostinger Setup Script
 * Run this to configure your database and test the connection
 */

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>NR BUDGET Planner - Hostinger Setup</h1>";

// Step 1: Check if .env exists
echo "<h2>Step 1: Environment Configuration Check</h2>";
if (file_exists('.env')) {
    echo "✅ .env file exists<br>";
} else {
    echo "❌ .env file missing<br>";
    echo "Please copy env.example to .env and configure your settings.<br>";
    exit;
}

// Step 2: Check if database.php exists
echo "<h2>Step 2: Database Configuration Check</h2>";
if (file_exists('config/database.php')) {
    echo "✅ config/database.php exists<br>";
} else {
    echo "❌ config/database.php missing<br>";
    exit;
}

// Step 3: Test database connection
echo "<h2>Step 3: Database Connection Test</h2>";
try {
    require_once 'config/database.php';
    
    // Test connection
    $pdo = getDatabaseConnection();
    echo "✅ Database connection successful!<br>";
    echo "Database: " . DB_NAME . "<br>";
    echo "Host: " . DB_HOST . ":" . DB_PORT . "<br>";
    echo "User: " . DB_USER . "<br>";
    
} catch (Exception $e) {
    echo "❌ Database connection failed: " . $e->getMessage() . "<br>";
    echo "<br><strong>Please update your database credentials in .env file:</strong><br>";
    echo "1. Go to your Hostinger control panel<br>";
    echo "2. Navigate to 'Databases' section<br>";
    echo "3. Create a new database or use existing one<br>";
    echo "4. Update these values in your .env file:<br>";
    echo "   - DB_NAME=your_actual_database_name<br>";
    echo "   - DB_USER=your_actual_database_username<br>";
    echo "   - DB_PASS=your_actual_database_password<br>";
    echo "5. Refresh this page<br>";
    exit;
}

// Step 4: Check if tables exist
echo "<h2>Step 4: Database Tables Check</h2>";
try {
    $sql = "SHOW TABLES";
    $tables = fetchAll($sql);
    
    if ($tables) {
        echo "✅ Database tables found: " . count($tables) . "<br>";
        foreach ($tables as $table) {
            $tableName = array_values($table)[0];
            echo "- " . $tableName . "<br>";
        }
    } else {
        echo "⚠️ No tables found. You need to run the database setup.<br>";
        echo "<a href='database/setup.php' class='btn btn-primary'>Run Database Setup</a><br>";
    }
    
} catch (Exception $e) {
    echo "❌ Error checking tables: " . $e->getMessage() . "<br>";
}

// Step 5: Check PHP version and extensions
echo "<h2>Step 5: Server Environment Check</h2>";
echo "PHP Version: " . phpversion() . "<br>";
echo "PDO MySQL: " . (extension_loaded('pdo_mysql') ? '✅ Enabled' : '❌ Disabled') . "<br>";
echo "MySQL: " . (extension_loaded('mysqli') ? '✅ Enabled' : '❌ Disabled') . "<br>";

// Step 6: Environment variables check
echo "<h2>Step 6: Environment Variables Check</h2>";
if (class_exists('Environment')) {
    echo "✅ Environment class loaded<br>";
    echo "APP_ENV: " . (Environment::get('APP_ENV', 'Not set')) . "<br>";
    echo "APP_DEBUG: " . (Environment::get('APP_DEBUG', 'Not set')) . "<br>";
    echo "APP_URL: " . (Environment::get('APP_URL', 'Not set')) . "<br>";
} else {
    echo "❌ Environment class not found<br>";
}

// Step 7: Next steps
echo "<h2>Step 7: Next Steps</h2>";
echo "1. ✅ Update database credentials in .env file<br>";
echo "2. ✅ Test database connection (refresh this page)<br>";
echo "3. 🔄 Run database setup if no tables exist<br>";
echo "4. 🚀 Your application should be ready!<br>";

echo "<br><a href='index.php' class='btn btn-success'>Go to Dashboard</a>";
echo " <a href='login.php' class='btn btn-primary'>Go to Login</a>";
?>

<style>
body { font-family: Arial, sans-serif; margin: 20px; }
.btn { display: inline-block; padding: 10px 20px; margin: 5px; text-decoration: none; border-radius: 5px; }
.btn-primary { background: #007bff; color: white; }
.btn-success { background: #28a745; color: white; }
</style>
