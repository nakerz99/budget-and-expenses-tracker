<?php
/**
 * Migration: Create Savings Accounts Table
 * Budget Planner Application
 */

class CreateSavingsAccountsTable {
    
    public function up($pdo) {
        $sql = "CREATE TABLE IF NOT EXISTS savings_accounts (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            name VARCHAR(100) NOT NULL,
            type ENUM('cash', 'bank', 'digital', 'investment', 'other') DEFAULT 'bank',
            current_balance DECIMAL(10,2) DEFAULT 0.00,
            target_balance DECIMAL(10,2) NULL,
            description TEXT NULL,
            is_active TINYINT(1) DEFAULT 1,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
            INDEX idx_user_id (user_id),
            INDEX idx_type (type),
            INDEX idx_is_active (is_active)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
        
        return $pdo->exec($sql);
    }
    
    public function down($pdo) {
        $sql = "DROP TABLE IF EXISTS savings_accounts";
        return $pdo->exec($sql);
    }
}
