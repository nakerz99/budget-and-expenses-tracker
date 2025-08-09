<?php
/**
 * Database Seeder for NR BUDGET Planner
 * Seeds the database with initial data
 */

require_once __DIR__ . '/../../config/database.php';

// Get PDO connection
$pdo = getDBConnection();

require_once __DIR__ . '/DefaultExpenseCategoriesSeeder.php';
require_once __DIR__ . '/DefaultPaymentMethodsSeeder.php';
require_once __DIR__ . '/DefaultSavingsAccountsSeeder.php';
require_once __DIR__ . '/AdminUserSeeder.php';

class DatabaseSeeder {
    private $pdo;
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }
    
    public function run() {
        echo "Starting database seeding...\n";
        
        // Run seeders in order
        $this->call(DefaultExpenseCategoriesSeeder::class);
        $this->call(DefaultPaymentMethodsSeeder::class);
        $this->call(DefaultSavingsAccountsSeeder::class);
        $this->call(AdminUserSeeder::class);
        
        echo "Database seeding completed successfully!\n";
    }
    
    private function call($seederClass) {
        echo "Running seeder: {$seederClass}\n";
        
        try {
            $seeder = new $seederClass($this->pdo);
            $seeder->run();
            echo "✓ {$seederClass} completed successfully\n";
        } catch (Exception $e) {
            echo "✗ {$seederClass} failed: " . $e->getMessage() . "\n";
            throw $e;
        }
    }
}

// CLI Usage
if (php_sapi_name() === 'cli') {
    try {
        $seeder = new DatabaseSeeder($pdo);
        $seeder->run();
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage() . "\n";
        exit(1);
    }
}
