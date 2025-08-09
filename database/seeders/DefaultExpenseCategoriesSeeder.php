<?php
/**
 * Default Expense Categories Seeder
 * Seeds the database with default expense categories
 */

class DefaultExpenseCategoriesSeeder {
    private $pdo;
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }
    
    public function run() {
        $categories = [
            ['name' => 'Food & Dining', 'description' => 'Groceries, restaurants, and food delivery', 'color' => '#28a745'],
            ['name' => 'Transportation', 'description' => 'Gas, public transport, and vehicle maintenance', 'color' => '#007bff'],
            ['name' => 'Housing', 'description' => 'Rent, mortgage, utilities, and home maintenance', 'color' => '#6f42c1'],
            ['name' => 'Entertainment', 'description' => 'Movies, games, and leisure activities', 'color' => '#fd7e14'],
            ['name' => 'Healthcare', 'description' => 'Medical expenses, insurance, and prescriptions', 'color' => '#e83e8c'],
            ['name' => 'Shopping', 'description' => 'Clothing, electronics, and personal items', 'color' => '#20c997'],
            ['name' => 'Education', 'description' => 'Tuition, books, and educational materials', 'color' => '#17a2b8'],
            ['name' => 'Utilities', 'description' => 'Electricity, water, internet, and phone bills', 'color' => '#ffc107'],
            ['name' => 'Insurance', 'description' => 'Life, health, auto, and property insurance', 'color' => '#6c757d'],
            ['name' => 'Savings', 'description' => 'Emergency fund and long-term savings', 'color' => '#28a745'],
            ['name' => 'Debt Payment', 'description' => 'Credit cards, loans, and debt repayment', 'color' => '#dc3545'],
            ['name' => 'Subscriptions', 'description' => 'Streaming services, software, and memberships', 'color' => '#6610f2'],
            ['name' => 'Travel', 'description' => 'Vacations, business trips, and travel expenses', 'color' => '#fd7e14'],
            ['name' => 'Gifts', 'description' => 'Birthday gifts, holidays, and charitable donations', 'color' => '#e83e8c'],
            ['name' => 'Other', 'description' => 'Miscellaneous expenses', 'color' => '#6c757d']
        ];
        
        $sql = "INSERT IGNORE INTO expense_categories (user_id, name, description, color) VALUES (?, ?, ?, ?)";
        $stmt = $this->pdo->prepare($sql);
        
        foreach ($categories as $category) {
            $stmt->execute([
                1, // Default user ID for admin
                $category['name'],
                $category['description'],
                $category['color']
            ]);
        }
    }
}
