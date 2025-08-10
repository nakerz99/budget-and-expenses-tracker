<?php
/**
 * Environment Setup Script
 * Helps users create their .env file from env.example
 */

echo "🌍 NR BUDGET Planner Environment Setup\n";
echo "=====================================\n\n";

// Check if .env already exists
if (file_exists('.env')) {
    echo "⚠️  .env file already exists!\n";
    echo "Do you want to overwrite it? (y/N): ";
    $handle = fopen("php://stdin", "r");
    $overwrite = trim(fgets($handle));
    fclose($handle);
    
    if (strtolower($overwrite) !== 'y') {
        echo "Setup cancelled. .env file unchanged.\n";
        exit(0);
    }
}

// Check if env.example exists
if (!file_exists('env.example')) {
    echo "❌ env.example file not found!\n";
    echo "Please ensure env.example exists in the project root.\n";
    exit(1);
}

// Read env.example
$exampleContent = file_get_contents('env.example');
if ($exampleContent === false) {
    echo "❌ Could not read env.example file!\n";
    exit(1);
}

// Create .env file
$envContent = $exampleContent;

// Replace default values with user input
echo "Please provide the following configuration values:\n\n";

// Database configuration
echo "📊 DATABASE CONFIGURATION\n";
echo "------------------------\n";

echo "Database Host (default: localhost): ";
$handle = fopen("php://stdin", "r");
$dbHost = trim(fgets($handle));
if (empty($dbHost)) $dbHost = 'localhost';
fclose($handle);

echo "Database Name (default: budget_planner): ";
$handle = fopen("php://stdin", "r");
$dbName = trim(fgets($handle));
if (empty($dbName)) $dbName = 'budget_planner';
fclose($handle);

echo "Database User (default: root): ";
$handle = fopen("php://stdin", "r");
$dbUser = trim(fgets($handle));
if (empty($dbUser)) $dbUser = 'root';
fclose($handle);

echo "Database Password: ";
$handle = fopen("php://stdin", "r");
$dbPass = trim(fgets($handle));
fclose($handle);

echo "Database Port (default: 3306): ";
$handle = fopen("php://stdin", "r");
$dbPort = trim(fgets($handle));
if (empty($dbPort)) $dbPort = '3306';
fclose($handle);

// Application configuration
echo "\n🚀 APPLICATION CONFIGURATION\n";
echo "----------------------------\n";

echo "Application Name (default: NR BUDGET Planner): ";
$handle = fopen("php://stdin", "r");
$appName = trim(fgets($handle));
if (empty($appName)) $appName = 'NR BUDGET Planner';
fclose($handle);

echo "Application URL (default: http://localhost:8080): ";
$handle = fopen("php://stdin", "r");
$appUrl = trim(fgets($handle));
if (empty($appUrl)) $appUrl = 'http://localhost:8080';
fclose($handle);

echo "Application Environment (development/production, default: development): ";
$handle = fopen("php://stdin", "r");
$appEnv = trim(fgets($handle));
if (empty($appEnv)) $appEnv = 'development';
fclose($handle);

echo "Debug Mode (true/false, default: true): ";
$handle = fopen("php://stdin", "r");
$debugMode = trim(fgets($handle));
if (empty($debugMode)) $debugMode = 'true';
fclose($handle);

// Security configuration
echo "\n🔒 SECURITY CONFIGURATION\n";
echo "------------------------\n";

echo "Session Lifetime in seconds (default: 3600): ";
$handle = fopen("php://stdin", "r");
$sessionLifetime = trim(fgets($handle));
if (empty($sessionLifetime)) $sessionLifetime = '3600';
fclose($handle);

echo "PIN Length (default: 6): ";
$handle = fopen("php://stdin", "r");
$pinLength = trim(fgets($handle));
if (empty($pinLength)) $pinLength = '6';
fclose($handle);

echo "Max Login Attempts (default: 5): ";
$handle = fopen("php://stdin", "r");
$maxLoginAttempts = trim(fgets($handle));
if (empty($maxLoginAttempts)) $maxLoginAttempts = '5';
fclose($handle);

// Replace values in content
$envContent = str_replace('localhost', $dbHost, $envContent);
$envContent = str_replace('budget_planner', $dbName, $envContent);
$envContent = str_replace('root', $dbUser, $envContent);
$envContent = str_replace('your_password_here', $dbPass, $envContent);
$envContent = str_replace('3306', $dbPort, $envContent);
$envContent = str_replace('"NR BUDGET Planner"', '"' . $appName . '"', $envContent);
$envContent = str_replace('http://localhost:8080', $appUrl, $envContent);
$envContent = str_replace('development', $appEnv, $envContent);
$envContent = str_replace('true', $debugMode, $envContent);
$envContent = str_replace('3600', $sessionLifetime, $envContent);
$envContent = str_replace('6', $pinLength, $envContent);
$envContent = str_replace('5', $maxLoginAttempts, $envContent);

// Write .env file
if (file_put_contents('.env', $envContent) === false) {
    echo "❌ Could not create .env file!\n";
    exit(1);
}

echo "\n✅ .env file created successfully!\n\n";

// Test database connection
echo "🔍 Testing database connection...\n";
try {
    $dsn = "mysql:host=$dbHost;port=$dbPort;dbname=$dbName;charset=utf8mb4";
    $pdo = new PDO($dsn, $dbUser, $dbPass);
    echo "✅ Database connection successful!\n";
} catch (PDOException $e) {
    echo "❌ Database connection failed: " . $e->getMessage() . "\n";
    echo "Please check your database credentials and try again.\n";
}

echo "\n🎉 Environment setup complete!\n";
echo "You can now start the application.\n";
echo "\nNext steps:\n";
echo "1. Start the application: php -S localhost:8080 -t . router.php\n";
echo "2. Visit: $appUrl\n";
echo "3. Run database setup: php database/setup.php\n";
echo "\nHappy budgeting! 💰\n";
?>
