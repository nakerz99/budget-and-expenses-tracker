<?php
/**
 * Migration: Create Security PIN Table
 * Budget Planner Application
 */

class CreateSecurityPinTable {
    
    public function up($pdo) {
        $sql = "CREATE TABLE IF NOT EXISTS security_pin (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            pin_hash VARCHAR(255) NOT NULL,
            is_active TINYINT(1) DEFAULT 1,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
            INDEX idx_user_id (user_id),
            INDEX idx_is_active (is_active)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
        
        return $pdo->exec($sql);
    }
    
    public function down($pdo) {
        $sql = "DROP TABLE IF EXISTS security_pin";
        return $pdo->exec($sql);
    }
}
