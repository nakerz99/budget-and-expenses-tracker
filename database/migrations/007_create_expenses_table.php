<?php
/**
 * Migration: Create Expenses Table
 * Budget Planner Application
 */

class CreateExpensesTable {
    
    public function up($pdo) {
        $sql = "CREATE TABLE IF NOT EXISTS expenses (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            month_id INT NOT NULL,
            category_id INT NOT NULL,
            name VARCHAR(100) NOT NULL,
            budgeted_amount DECIMAL(10,2) NOT NULL,
            schedule_type ENUM('monthly', 'weekly', 'one-time') DEFAULT 'monthly',
            description TEXT NULL,
            due_date DATE NULL,
            is_bill TINYINT(1) DEFAULT 0,
            bill_type ENUM('utility', 'subscription', 'credit_card', 'loan', 'insurance', 'other') DEFAULT 'other',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
            FOREIGN KEY (month_id) REFERENCES months(id) ON DELETE CASCADE,
            FOREIGN KEY (category_id) REFERENCES expense_categories(id) ON DELETE CASCADE,
            INDEX idx_user_id (user_id),
            INDEX idx_month_id (month_id),
            INDEX idx_category_id (category_id),
            INDEX idx_is_bill (is_bill),
            INDEX idx_bill_type (bill_type),
            INDEX idx_due_date (due_date)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
        
        return $pdo->exec($sql);
    }
    
    public function down($pdo) {
        $sql = "DROP TABLE IF EXISTS expenses";
        return $pdo->exec($sql);
    }
}
