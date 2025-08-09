<?php
/**
 * Migration Runner for NR BUDGET Planner
 * Handles database migrations in order
 */

require_once __DIR__ . '/../config/database.php';

// Get PDO connection
$pdo = getDBConnection();

class MigrationRunner {
    private $pdo;
    private $migrations = [];
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
        $this->loadMigrations();
    }
    
    private function loadMigrations() {
        $migrationFiles = glob(__DIR__ . '/migrations/*.php');
        sort($migrationFiles); // Ensure proper order
        
        foreach ($migrationFiles as $file) {
            require_once $file;
            $className = $this->getClassNameFromFile($file);
            if ($className) {
                $this->migrations[] = $className;
            }
        }
    }
    
    private function getClassNameFromFile($file) {
        $content = file_get_contents($file);
        if (preg_match('/class\s+(\w+)/', $content, $matches)) {
            return $matches[1];
        }
        return null;
    }
    
    public function migrate() {
        echo "Starting database migrations...\n";
        
        // Create migrations table if it doesn't exist
        $this->createMigrationsTable();
        
        foreach ($this->migrations as $migrationClass) {
            if (!$this->hasMigrationRun($migrationClass)) {
                echo "Running migration: {$migrationClass}\n";
                
                try {
                    $migration = new $migrationClass();
                    $result = $migration->up($this->pdo);
                    
                    if ($result !== false) {
                        $this->markMigrationAsRun($migrationClass);
                        echo "✓ {$migrationClass} completed successfully\n";
                    } else {
                        echo "✗ {$migrationClass} failed\n";
                        return false;
                    }
                } catch (Exception $e) {
                    echo "✗ {$migrationClass} failed: " . $e->getMessage() . "\n";
                    return false;
                }
            } else {
                echo "Skipping {$migrationClass} (already run)\n";
            }
        }
        
        echo "All migrations completed successfully!\n";
        return true;
    }
    
    public function rollback($steps = 1) {
        echo "Rolling back {$steps} migration(s)...\n";
        
        $runMigrations = $this->getRunMigrations();
        $migrationsToRollback = array_slice($runMigrations, -$steps);
        
        foreach (array_reverse($migrationsToRollback) as $migrationClass) {
            echo "Rolling back: {$migrationClass}\n";
            
            try {
                $migration = new $migrationClass();
                $result = $migration->down($this->pdo);
                
                if ($result !== false) {
                    $this->removeMigrationRecord($migrationClass);
                    echo "✓ {$migrationClass} rolled back successfully\n";
                } else {
                    echo "✗ {$migrationClass} rollback failed\n";
                    return false;
                }
            } catch (Exception $e) {
                echo "✗ {$migrationClass} rollback failed: " . $e->getMessage() . "\n";
                return false;
            }
        }
        
        echo "Rollback completed successfully!\n";
        return true;
    }
    
    private function createMigrationsTable() {
        $sql = "CREATE TABLE IF NOT EXISTS migrations (
            id INT AUTO_INCREMENT PRIMARY KEY,
            migration VARCHAR(255) NOT NULL,
            batch INT NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )";
        
        $this->pdo->exec($sql);
    }
    
    private function hasMigrationRun($migrationClass) {
        $sql = "SELECT COUNT(*) FROM migrations WHERE migration = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$migrationClass]);
        return $stmt->fetchColumn() > 0;
    }
    
    private function markMigrationAsRun($migrationClass) {
        $batch = $this->getNextBatchNumber();
        $sql = "INSERT INTO migrations (migration, batch) VALUES (?, ?)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$migrationClass, $batch]);
    }
    
    private function removeMigrationRecord($migrationClass) {
        $sql = "DELETE FROM migrations WHERE migration = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$migrationClass]);
    }
    
    private function getNextBatchNumber() {
        $sql = "SELECT MAX(batch) FROM migrations";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        $maxBatch = $stmt->fetchColumn();
        return $maxBatch ? $maxBatch + 1 : 1;
    }
    
    private function getRunMigrations() {
        $sql = "SELECT migration FROM migrations ORDER BY id ASC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }
    
    public function status() {
        echo "Migration Status:\n";
        echo str_repeat("-", 50) . "\n";
        
        $runMigrations = $this->getRunMigrations();
        
        foreach ($this->migrations as $migrationClass) {
            $status = in_array($migrationClass, $runMigrations) ? "✓ Run" : "✗ Pending";
            echo sprintf("%-40s %s\n", $migrationClass, $status);
        }
    }
}

// CLI Usage
if (php_sapi_name() === 'cli') {
    $action = $argv[1] ?? 'migrate';
    $steps = $argv[2] ?? 1;
    
    try {
        $runner = new MigrationRunner($pdo);
        
        switch ($action) {
            case 'migrate':
                $runner->migrate();
                break;
            case 'rollback':
                $runner->rollback($steps);
                break;
            case 'status':
                $runner->status();
                break;
            default:
                echo "Usage: php migrate.php [migrate|rollback|status] [steps]\n";
        }
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage() . "\n";
        exit(1);
    }
}
