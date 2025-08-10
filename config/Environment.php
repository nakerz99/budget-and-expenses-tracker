<?php
/**
 * Environment Configuration Loader
 * Loads configuration from .env files and provides easy access to environment variables
 */

class Environment
{
    private static $loaded = false;
    private static $variables = [];
    
    /**
     * Load environment variables from .env file
     * @param string $path Path to .env file
     * @return bool
     */
    public static function load($path = null)
    {
        if (self::$loaded) {
            return true;
        }
        
        if ($path === null) {
            $path = dirname(__DIR__) . '/.env';
        }
        
        if (!file_exists($path)) {
            // Try to load from env.example if .env doesn't exist
            $examplePath = dirname(__DIR__) . '/env.example';
            if (file_exists($examplePath)) {
                error_log("Warning: .env file not found. Please copy env.example to .env and configure your settings.");
                return false;
            }
            return false;
        }
        
        $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        
        foreach ($lines as $line) {
            // Skip comments and empty lines
            if (strpos(trim($line), '#') === 0 || empty(trim($line))) {
                continue;
            }
            
            // Parse key=value pairs
            if (strpos($line, '=') !== false) {
                $parts = explode('=', $line, 2);
                $key = trim($parts[0]);
                $value = trim($parts[1]);
                
                // Remove quotes if present
                if (preg_match('/^(["\'])(.*)\1$/', $value, $matches)) {
                    $value = $matches[2];
                }
                
                self::$variables[$key] = $value;
                
                // Set as environment variable if not already set
                if (!getenv($key)) {
                    putenv("$key=$value");
                }
            }
        }
        
        self::$loaded = true;
        return true;
    }
    
    /**
     * Get environment variable
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public static function get($key, $default = null)
    {
        if (!self::$loaded) {
            self::load();
        }
        
        // Check environment variables first
        $value = getenv($key);
        if ($value !== false) {
            return $value;
        }
        
        // Check loaded variables
        if (isset(self::$variables[$key])) {
            return self::$variables[$key];
        }
        
        return $default;
    }
    
    /**
     * Get all environment variables
     * @return array
     */
    public static function all()
    {
        if (!self::$loaded) {
            self::load();
        }
        
        return self::$variables;
    }
    
    /**
     * Check if environment variable exists
     * @param string $key
     * @return bool
     */
    public static function has($key)
    {
        if (!self::$loaded) {
            self::load();
        }
        
        return getenv($key) !== false || isset(self::$variables[$key]);
    }
    
    /**
     * Set environment variable
     * @param string $key
     * @param mixed $value
     */
    public static function set($key, $value)
    {
        self::$variables[$key] = $value;
        putenv("$key=$value");
    }
    
    /**
     * Get database configuration
     * @return array
     */
    public static function getDatabaseConfig()
    {
        return [
            'host' => self::get('DB_HOST', 'localhost'),
            'name' => self::get('DB_NAME', 'budget_planner'),
            'user' => self::get('DB_USER', 'root'),
            'pass' => self::get('DB_PASS', ''),
            'charset' => self::get('DB_CHARSET', 'utf8mb4'),
            'port' => self::get('DB_PORT', '3306'),
        ];
    }
    
    /**
     * Get application configuration
     * @return array
     */
    public static function getAppConfig()
    {
        return [
            'name' => self::get('APP_NAME', 'NR BUDGET Planner'),
            'env' => self::get('APP_ENV', 'development'),
            'debug' => self::get('APP_DEBUG', 'true') === 'true',
            'url' => self::get('APP_URL', 'http://localhost:8080'),
            'timezone' => self::get('APP_TIMEZONE', 'Asia/Manila'),
        ];
    }
    
    /**
     * Get security configuration
     * @return array
     */
    public static function getSecurityConfig()
    {
        return [
            'session_lifetime' => (int) self::get('SESSION_LIFETIME', 3600),
            'pin_length' => (int) self::get('PIN_LENGTH', 6),
            'max_login_attempts' => (int) self::get('MAX_LOGIN_ATTEMPTS', 5),
            'login_timeout' => (int) self::get('LOGIN_TIMEOUT', 300),
        ];
    }
    
    /**
     * Check if application is in development mode
     * @return bool
     */
    public static function isDevelopment()
    {
        return self::get('APP_ENV', 'development') === 'development';
    }
    
    /**
     * Check if application is in production mode
     * @return bool
     */
    public static function isProduction()
    {
        return self::get('APP_ENV', 'development') === 'production';
    }
    
    /**
     * Check if debug mode is enabled
     * @return bool
     */
    public static function isDebug()
    {
        return self::get('APP_DEBUG', 'true') === 'true';
    }
}
