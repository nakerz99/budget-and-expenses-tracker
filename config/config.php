<?php
/**
 * Main Configuration File
 * Provides easy access to all application configuration
 */

// Load environment configuration
require_once __DIR__ . '/Environment.php';
Environment::load();

/**
 * Get configuration value
 * @param string $key
 * @param mixed $default
 * @return mixed
 */
function config($key, $default = null)
{
    return Environment::get($key, $default);
}

/**
 * Get database configuration
 * @return array
 */
function getDatabaseConfig()
{
    return Environment::getDatabaseConfig();
}

/**
 * Get application configuration
 * @return array
 */
function getAppConfig()
{
    return Environment::getAppConfig();
}

/**
 * Get security configuration
 * @return array
 */
function getSecurityConfig()
{
    return Environment::getSecurityConfig();
}

/**
 * Check if application is in development mode
 * @return bool
 */
function isDevelopment()
{
    return Environment::isDevelopment();
}

/**
 * Check if application is in production mode
 * @return bool
 */
function isProduction()
{
    return Environment::isProduction();
}

/**
 * Check if debug mode is enabled
 * @return bool
 */
function isDebug()
{
    return Environment::isDebug();
}

/**
 * Get application name
 * @return string
 */
function getAppName()
{
    return config('APP_NAME', 'NR BUDGET Planner');
}

/**
 * Get application URL
 * @return string
 */
function getAppUrl()
{
    return config('APP_URL', 'http://localhost:8080');
}

/**
 * Get application timezone
 * @return string
 */
function getAppTimezone()
{
    return config('APP_TIMEZONE', 'Asia/Manila');
}

/**
 * Get session lifetime in seconds
 * @return int
 */
function getSessionLifetime()
{
    return (int) config('SESSION_LIFETIME', 3600);
}

/**
 * Get PIN length
 * @return int
 */
function getPinLength()
{
    return (int) config('PIN_LENGTH', 6);
}

/**
 * Get maximum login attempts
 * @return int
 */
function getMaxLoginAttempts()
{
    return (int) config('MAX_LOGIN_ATTEMPTS', 5);
}

/**
 * Get login timeout in seconds
 * @return int
 */
function getLoginTimeout()
{
    return (int) config('LOGIN_TIMEOUT', 300);
}

/**
 * Get maximum expenses per month
 * @return int
 */
function getMaxExpensesPerMonth()
{
    return (int) config('MAX_EXPENSES_PER_MONTH', 100);
}

/**
 * Get maximum categories per user
 * @return int
 */
function getMaxCategoriesPerUser()
{
    return (int) config('MAX_CATEGORIES_PER_USER', 50);
}

/**
 * Get maximum savings accounts
 * @return int
 */
function getMaxSavingsAccounts()
{
    return (int) config('MAX_SAVINGS_ACCOUNTS', 10);
}

/**
 * Get maximum payment methods
 * @return int
 */
function getMaxPaymentMethods()
{
    return (int) config('MAX_PAYMENT_METHODS', 20);
}

/**
 * Check if email notifications are enabled
 * @return bool
 */
function isEmailNotificationsEnabled()
{
    return config('ENABLE_EMAIL_NOTIFICATIONS', 'false') === 'true';
}

/**
 * Check if bill reminders are enabled
 * @return bool
 */
function isBillRemindersEnabled()
{
    return config('ENABLE_BILL_REMINDERS', 'true') === 'true';
}

/**
 * Get bill reminder days
 * @return int
 */
function getBillReminderDays()
{
    return (int) config('BILL_REMINDER_DAYS', 3);
}

/**
 * Get admin notification email
 * @return string
 */
function getAdminNotificationEmail()
{
    return config('ADMIN_NOTIFICATION_EMAIL', 'admin@budgetplanner.com');
}

/**
 * Get log level
 * @return string
 */
function getLogLevel()
{
    return config('LOG_LEVEL', 'info');
}

/**
 * Check if cache is enabled
 * @return bool
 */
function isCacheEnabled()
{
    return config('CACHE_ENABLED', 'false') === 'true';
}

/**
 * Check if debug mode is enabled
 * @return bool
 */
function isDebugMode()
{
    return config('DEBUG_MODE', 'false') === 'true';
}

/**
 * Check if errors should be shown
 * @return bool
 */
function shouldShowErrors()
{
    return config('SHOW_ERRORS', 'true') === 'true';
}

/**
 * Get SMTP configuration
 * @return array
 */
function getSmtpConfig()
{
    return [
        'host' => config('SMTP_HOST', ''),
        'port' => config('SMTP_PORT', ''),
        'username' => config('SMTP_USERNAME', ''),
        'password' => config('SMTP_PASSWORD', ''),
        'encryption' => config('SMTP_ENCRYPTION', ''),
    ];
}
?>
