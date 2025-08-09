<?php
/**
 * Migration: Create Income Sources Table
 * Budget Planner Application
 */

class CreateIncomeSourcesTable {
    
    public function up($pdo) {
        $sql = "CREATE TABLE IF NOT EXISTS income_sources (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            month_id INT NOT NULL,
            name VARCHAR(100) NOT NULL,
            amount DECIMAL(10,2) NOT NULL,
            schedule_type ENUM('monthly', 'weekly', 'one-time') DEFAULT 'monthly',
            description TEXT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
            FOREIGN KEY (month_id) REFERENCES months(id) ON DELETE CASCADE,
            INDEX idx_user_id (user_id),
            INDEX idx_month_id (month_id),
            INDEX idx_schedule_type (schedule_type)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
        
        return $pdo->exec($sql);
    }
    
    public function down($pdo) {
        $sql = "DROP TABLE IF EXISTS income_sources";
        return $pdo->exec($sql);
    }
}
