<?php
/**
 * Migration: Create Quick Actions Table
 * Budget Planner Application
 */

class CreateQuickActionsTable {
    
    public function up($pdo) {
        $sql = "CREATE TABLE IF NOT EXISTS quick_actions (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            name VARCHAR(100) NOT NULL,
            category_id INT NOT NULL,
            amount DECIMAL(10,2) NOT NULL,
            icon VARCHAR(50) DEFAULT 'fas fa-bolt',
            color VARCHAR(7) DEFAULT '#007bff',
            is_active TINYINT(1) DEFAULT 1,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
            FOREIGN KEY (category_id) REFERENCES expense_categories(id) ON DELETE CASCADE,
            INDEX idx_user_id (user_id),
            INDEX idx_category_id (category_id),
            INDEX idx_is_active (is_active)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
        
        return $pdo->exec($sql);
    }
    
    public function down($pdo) {
        $sql = "DROP TABLE IF EXISTS quick_actions";
        return $pdo->exec($sql);
    }
}
