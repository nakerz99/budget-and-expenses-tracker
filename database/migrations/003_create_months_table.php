<?php
/**
 * Migration: Create Months Table
 * Budget Planner Application
 */

class CreateMonthsTable {
    
    public function up($pdo) {
        $sql = "CREATE TABLE IF NOT EXISTS months (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            name VARCHAR(50) NOT NULL,
            year INT NOT NULL,
            month INT NOT NULL,
            is_active TINYINT(1) DEFAULT 0,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
            UNIQUE KEY unique_user_month_year (user_id, year, month),
            INDEX idx_user_id (user_id),
            INDEX idx_is_active (is_active),
            INDEX idx_year_month (year, month)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
        
        return $pdo->exec($sql);
    }
    
    public function down($pdo) {
        $sql = "DROP TABLE IF EXISTS months";
        return $pdo->exec($sql);
    }
}
