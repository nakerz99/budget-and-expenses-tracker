<?php
/**
 * Migration: Create Actual Expenses Table
 * Budget Planner Application
 */

class CreateActualExpensesTable {
    
    public function up($pdo) {
        $sql = "CREATE TABLE IF NOT EXISTS actual_expenses (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            expense_id INT NOT NULL,
            week_id INT NULL,
            amount DECIMAL(10,2) NOT NULL,
            date_paid DATE NOT NULL,
            payment_method_id INT NULL,
            savings_account_id INT NULL,
            notes TEXT NULL,
            is_paid TINYINT(1) DEFAULT 1,
            paid_amount DECIMAL(10,2) NULL,
            paid_date DATE NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
            FOREIGN KEY (expense_id) REFERENCES expenses(id) ON DELETE CASCADE,
            FOREIGN KEY (week_id) REFERENCES weeks(id) ON DELETE SET NULL,
            FOREIGN KEY (payment_method_id) REFERENCES payment_methods(id) ON DELETE SET NULL,
            FOREIGN KEY (savings_account_id) REFERENCES savings_accounts(id) ON DELETE SET NULL,
            INDEX idx_user_id (user_id),
            INDEX idx_expense_id (expense_id),
            INDEX idx_week_id (week_id),
            INDEX idx_payment_method_id (payment_method_id),
            INDEX idx_savings_account_id (savings_account_id),
            INDEX idx_date_paid (date_paid),
            INDEX idx_is_paid (is_paid)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
        
        return $pdo->exec($sql);
    }
    
    public function down($pdo) {
        $sql = "DROP TABLE IF EXISTS actual_expenses";
        return $pdo->exec($sql);
    }
}
