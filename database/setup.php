<?php
/**
 * Database Setup Script for NR BUDGET Planner
 * Runs migrations and seeders to set up the database
 */

require_once __DIR__ . '/../config/database.php';

// Get PDO connection
$pdo = getDBConnection();

require_once __DIR__ . '/migrate.php';
require_once __DIR__ . '/seeders/DatabaseSeeder.php';

class DatabaseSetup {
    private $pdo;
    private $migrationRunner;
    private $seeder;
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
        $this->migrationRunner = new MigrationRunner($pdo);
        $this->seeder = new DatabaseSeeder($pdo);
    }
    
    public function setup() {
        echo "=== NR BUDGET Planner Database Setup ===\n\n";
        
        try {
            // Run migrations
            echo "Step 1: Running database migrations...\n";
            if (!$this->migrationRunner->migrate()) {
                throw new Exception("Migration failed");
            }
            echo "\n";
            
            // Run seeders
            echo "Step 2: Seeding database with initial data...\n";
            $this->seeder->run();
            echo "\n";
            
            echo "=== Database setup completed successfully! ===\n";
            echo "\nYou can now access the application with:\n";
            echo "Username: admin\n";
            echo "Password: admin123\n";
            echo "PIN: 123456\n";
            echo "\nPlease change these credentials after first login!\n";
            
        } catch (Exception $e) {
            echo "Error during setup: " . $e->getMessage() . "\n";
            exit(1);
        }
    }
    
    public function reset() {
        echo "=== Resetting Database ===\n";
        
        try {
            // Drop all tables
            $tables = [
                'notifications',
                'quick_actions',
                'actual_expenses',
                'savings_accounts',
                'payment_methods',
                'expenses',
                'income_sources',
                'expense_categories',
                'weeks',
                'months',
                'security_pin',
                'users',
                'migrations'
            ];
            
            foreach ($tables as $table) {
                $sql = "DROP TABLE IF EXISTS {$table}";
                $this->pdo->exec($sql);
                echo "Dropped table: {$table}\n";
            }
            
            echo "\nDatabase reset completed. Run setup again to recreate.\n";
            
        } catch (Exception $e) {
            echo "Error during reset: " . $e->getMessage() . "\n";
            exit(1);
        }
    }
    
    public function getMigrationRunner() {
        return $this->migrationRunner;
    }
    
    public function getSeeder() {
        return $this->seeder;
    }
}

// CLI Usage
if (php_sapi_name() === 'cli') {
    $action = $argv[1] ?? 'setup';
    
    try {
        $setup = new DatabaseSetup($pdo);
        
        switch ($action) {
            case 'setup':
                $setup->setup();
                break;
            case 'reset':
                $setup->reset();
                break;
            case 'migrate':
                $setup->getMigrationRunner()->migrate();
                break;
            case 'seed':
                $setup->getSeeder()->run();
                break;
            case 'status':
                $setup->getMigrationRunner()->status();
                break;
            default:
                echo "Usage: php setup.php [setup|reset|migrate|seed|status]\n";
                echo "  setup   - Run migrations and seeders (default)\n";
                echo "  reset   - Drop all tables and reset database\n";
                echo "  migrate - Run only migrations\n";
                echo "  seed    - Run only seeders\n";
                echo "  status  - Show migration status\n";
        }
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage() . "\n";
        exit(1);
    }
}
