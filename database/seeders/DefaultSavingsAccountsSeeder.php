<?php
/**
 * Default Savings Accounts Seeder
 * Seeds the database with default savings accounts
 */

class DefaultSavingsAccountsSeeder {
    private $pdo;
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }
    
    public function run() {
        $savingsAccounts = [
            ['name' => 'Emergency Fund', 'description' => 'Emergency savings fund', 'target_amount' => 10000.00],
            ['name' => 'Vacation Fund', 'description' => 'Savings for vacations and travel', 'target_amount' => 5000.00],
            ['name' => 'Investment Account', 'description' => 'Long-term investment savings', 'target_amount' => null],
            ['name' => 'Cash Savings', 'description' => 'Physical cash savings', 'target_amount' => 1000.00],
            ['name' => 'Digital Wallet', 'description' => 'Digital payment wallet', 'target_amount' => 500.00]
        ];
        
        $sql = "INSERT IGNORE INTO savings_accounts (user_id, name, description, target_amount, current_amount, is_active) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $this->pdo->prepare($sql);
        
        foreach ($savingsAccounts as $account) {
            $stmt->execute([
                1, // Default user ID for admin
                $account['name'],
                $account['description'],
                $account['target_amount'],
                0.00, // current_amount starts at 0
                1 // is_active
            ]);
        }
        
        echo "Default savings accounts created successfully\n";
    }
}
