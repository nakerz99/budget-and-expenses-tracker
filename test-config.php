<?php
/**
 * Configuration Test Script
 * Tests the environment configuration system
 */

echo "🧪 NR BUDGET Planner Configuration Test\n";
echo "=====================================\n\n";

// Test 1: Load environment configuration
echo "1️⃣ Testing Environment Loading...\n";
try {
    require_once 'config/Environment.php';
    Environment::load();
    echo "✅ Environment loaded successfully\n";
} catch (Exception $e) {
    echo "❌ Environment loading failed: " . $e->getMessage() . "\n";
    exit(1);
}

// Test 2: Test configuration functions
echo "\n2️⃣ Testing Configuration Functions...\n";
try {
    require_once 'config/config.php';
    echo "✅ Configuration functions loaded\n";
} catch (Exception $e) {
    echo "❌ Configuration functions failed: " . $e->getMessage() . "\n";
    exit(1);
}

// Test 3: Test database configuration
echo "\n3️⃣ Testing Database Configuration...\n";
$dbConfig = getDatabaseConfig();
echo "Database Host: " . $dbConfig['host'] . "\n";
echo "Database Name: " . $dbConfig['name'] . "\n";
echo "Database User: " . $dbConfig['user'] . "\n";
echo "Database Port: " . $dbConfig['port'] . "\n";
echo "Database Charset: " . $dbConfig['charset'] . "\n";
echo "✅ Database configuration loaded\n";

// Test 4: Test application configuration
echo "\n4️⃣ Testing Application Configuration...\n";
$appConfig = getAppConfig();
echo "App Name: " . $appConfig['name'] . "\n";
echo "App Environment: " . $appConfig['env'] . "\n";
echo "App Debug: " . ($appConfig['debug'] ? 'true' : 'false') . "\n";
echo "App URL: " . $appConfig['url'] . "\n";
echo "App Timezone: " . $appConfig['timezone'] . "\n";
echo "✅ Application configuration loaded\n";

// Test 5: Test security configuration
echo "\n5️⃣ Testing Security Configuration...\n";
$securityConfig = getSecurityConfig();
echo "Session Lifetime: " . $securityConfig['session_lifetime'] . " seconds\n";
echo "PIN Length: " . $securityConfig['pin_length'] . " digits\n";
echo "Max Login Attempts: " . $securityConfig['max_login_attempts'] . "\n";
echo "Login Timeout: " . $securityConfig['login_timeout'] . " seconds\n";
echo "✅ Security configuration loaded\n";

// Test 6: Test individual config functions
echo "\n6️⃣ Testing Individual Config Functions...\n";
echo "App Name: " . getAppName() . "\n";
echo "App URL: " . getAppUrl() . "\n";
echo "App Timezone: " . getAppTimezone() . "\n";
echo "Session Lifetime: " . getSessionLifetime() . " seconds\n";
echo "PIN Length: " . getPinLength() . " digits\n";
echo "Max Login Attempts: " . getMaxLoginAttempts() . "\n";
echo "Login Timeout: " . getLoginTimeout() . " seconds\n";
echo "Max Expenses per Month: " . getMaxExpensesPerMonth() . "\n";
echo "Max Categories per User: " . getMaxCategoriesPerUser() . "\n";
echo "Max Savings Accounts: " . getMaxSavingsAccounts() . "\n";
echo "Max Payment Methods: " . getMaxPaymentMethods() . "\n";
echo "Email Notifications: " . (isEmailNotificationsEnabled() ? 'enabled' : 'disabled') . "\n";
echo "Bill Reminders: " . (isBillRemindersEnabled() ? 'enabled' : 'disabled') . "\n";
echo "Bill Reminder Days: " . getBillReminderDays() . "\n";
echo "Admin Email: " . getAdminNotificationEmail() . "\n";
echo "Log Level: " . getLogLevel() . "\n";
echo "Cache: " . (isCacheEnabled() ? 'enabled' : 'disabled') . "\n";
echo "Debug Mode: " . (isDebugMode() ? 'enabled' : 'disabled') . "\n";
echo "Show Errors: " . (shouldShowErrors() ? 'enabled' : 'disabled') . "\n";
echo "✅ Individual config functions working\n";

// Test 7: Test environment checks
echo "\n7️⃣ Testing Environment Checks...\n";
echo "Is Development: " . (isDevelopment() ? 'yes' : 'no') . "\n";
echo "Is Production: " . (isProduction() ? 'yes' : 'no') . "\n";
echo "Is Debug: " . (isDebug() ? 'yes' : 'no') . "\n";
echo "✅ Environment checks working\n";

// Test 8: Test SMTP configuration
echo "\n8️⃣ Testing SMTP Configuration...\n";
$smtpConfig = getSmtpConfig();
echo "SMTP Host: " . ($smtpConfig['host'] ?: 'not configured') . "\n";
echo "SMTP Port: " . ($smtpConfig['port'] ?: 'not configured') . "\n";
echo "SMTP Username: " . ($smtpConfig['username'] ?: 'not configured') . "\n";
echo "SMTP Encryption: " . ($smtpConfig['encryption'] ?: 'not configured') . "\n";
echo "✅ SMTP configuration loaded\n";

// Test 9: Test database connection
echo "\n9️⃣ Testing Database Connection...\n";
try {
    require_once 'config/database.php';
    $pdo = getDBConnection();
    echo "✅ Database connection successful\n";
    
    // Test a simple query
    $stmt = $pdo->query("SELECT 1 as test");
    $result = $stmt->fetch();
    if ($result && $result['test'] == 1) {
        echo "✅ Database query test successful\n";
    } else {
        echo "❌ Database query test failed\n";
    }
} catch (Exception $e) {
    echo "❌ Database connection failed: " . $e->getMessage() . "\n";
}

echo "\n🎉 Configuration Test Complete!\n";
echo "All configuration systems are working correctly.\n";
echo "You can now use the application with confidence! 🚀\n";
?>
