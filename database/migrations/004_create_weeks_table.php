<?php
/**
 * Migration: Create Weeks Table
 * Budget Planner Application
 */

class CreateWeeksTable {
    
    public function up($pdo) {
        $sql = "CREATE TABLE IF NOT EXISTS weeks (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            month_id INT NOT NULL,
            week_number INT NOT NULL,
            start_date DATE NOT NULL,
            end_date DATE NOT NULL,
            is_active TINYINT(1) DEFAULT 0,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
            FOREIGN KEY (month_id) REFERENCES months(id) ON DELETE CASCADE,
            UNIQUE KEY unique_user_month_week (user_id, month_id, week_number),
            INDEX idx_user_id (user_id),
            INDEX idx_month_id (month_id),
            INDEX idx_is_active (is_active),
            INDEX idx_start_date (start_date),
            INDEX idx_end_date (end_date)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
        
        return $pdo->exec($sql);
    }
    
    public function down($pdo) {
        $sql = "DROP TABLE IF EXISTS weeks";
        return $pdo->exec($sql);
    }
}
