<?php
/**
 * Simple Seeder Runner for Hostinger
 * Run this script to populate your database with sample data
 */

echo "<h1>NR BUDGET Planner - Database Seeder</h1>";

// Check if we're in CLI mode
if (php_sapi_name() === 'cli') {
    echo "Running in CLI mode...\n";
} else {
    echo "<p>Running in web mode...</p>";
}

try {
    // Load database configuration
    require_once __DIR__ . '/../config/database.php';
    
    // Test database connection
    echo "<h2>Step 1: Testing Database Connection</h2>";
    $pdo = getDatabaseConnection();
    echo "✅ Database connection successful!<br>";
    
    // Check if tables exist
    echo "<h2>Step 2: Checking Database Tables</h2>";
    $sql = "SHOW TABLES";
    $tables = fetchAll($sql);
    
    if ($tables) {
        echo "✅ Found " . count($tables) . " tables<br>";
        foreach ($tables as $table) {
            $tableName = array_values($table)[0];
            echo "- " . $tableName . "<br>";
        }
    } else {
        echo "❌ No tables found. Please import the database schema first.<br>";
        exit;
    }
    
    // Run seeders
    echo "<h2>Step 3: Running Database Seeders</h2>";
    
    // Load all seeder files
    require_once __DIR__ . '/seeders/DefaultExpenseCategoriesSeeder.php';
    require_once __DIR__ . '/seeders/DefaultPaymentMethodsSeeder.php';
    require_once __DIR__ . '/seeders/DefaultSavingsAccountsSeeder.php';
    require_once __DIR__ . '/seeders/AdminUserSeeder.php';
    require_once __DIR__ . '/seeders/SampleDataSeeder.php';
    
    // Run seeders in order
    $seeders = [
        'DefaultExpenseCategoriesSeeder',
        'DefaultPaymentMethodsSeeder', 
        'DefaultSavingsAccountsSeeder',
        'AdminUserSeeder',
        'SampleDataSeeder'
    ];
    
    foreach ($seeders as $seederClass) {
        echo "<h3>Running: {$seederClass}</h3>";
        try {
            $seeder = new $seederClass($pdo);
            $seeder->run();
            echo "✅ {$seederClass} completed successfully<br>";
        } catch (Exception $e) {
            echo "❌ {$seederClass} failed: " . $e->getMessage() . "<br>";
        }
    }
    
    echo "<h2>✅ Seeding Complete!</h2>";
    echo "<p>Your database has been populated with sample data.</p>";
    echo "<p><strong>Default Admin Login:</strong></p>";
    echo "<ul>";
    echo "<li>Username: <code>admin</code></li>";
    echo "<li>Password: <code>admin123</code></li>";
    echo "<li>PIN: <code>123456</code></li>";
    echo "</ul>";
    echo "<p><strong>⚠️ Important:</strong> Please change these credentials after first login!</p>";
    
    echo "<br><a href='../index.php' class='btn btn-success'>Go to Dashboard</a>";
    echo "<br><a href='../setup-hosting.php' class='btn btn-info'>Back to Setup Guide</a>";
    
} catch (Exception $e) {
    echo "<h2>❌ Error</h2>";
    echo "<p>Error: " . $e->getMessage() . "</p>";
    echo "<p>Please check your database configuration in the .env file.</p>";
}

?>

<style>
body { font-family: Arial, sans-serif; margin: 20px; line-height: 1.6; }
.btn { display: inline-block; padding: 10px 20px; margin: 5px; text-decoration: none; border-radius: 5px; }
.btn-success { background: #28a745; color: white; }
.btn-info { background: #17a2b8; color: white; }
code { background: #f8f9fa; padding: 2px 4px; border-radius: 3px; }
h1 { color: #333; border-bottom: 2px solid #007bff; padding-bottom: 10px; }
h2 { color: #555; margin-top: 30px; }
h3 { color: #666; margin-top: 20px; }
ul { background: #f8f9fa; padding: 20px; border-radius: 5px; }
</style>
