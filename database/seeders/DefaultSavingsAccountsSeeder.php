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
            ['name' => 'Emergency Fund', 'type' => 'bank', 'description' => 'Emergency savings fund', 'target_balance' => 10000.00],
            ['name' => 'Vacation Fund', 'type' => 'bank', 'description' => 'Savings for vacations and travel', 'target_balance' => 5000.00],
            ['name' => 'Investment Account', 'type' => 'investment', 'description' => 'Long-term investment savings', 'target_balance' => null],
            ['name' => 'Cash Savings', 'type' => 'cash', 'description' => 'Physical cash savings', 'target_balance' => 1000.00],
            ['name' => 'Digital Wallet', 'type' => 'digital', 'description' => 'Digital payment wallet', 'target_balance' => 500.00]
        ];
        
        $sql = "INSERT IGNORE INTO savings_accounts (user_id, name, type, bank_name, target_balance, icon, color) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->pdo->prepare($sql);
        
        foreach ($savingsAccounts as $account) {
            $stmt->execute([
                1, // Default user ID for admin
                $account['name'],
                $account['type'],
                $account['description'], // Use description as bank_name
                $account['target_balance'],
                'fas fa-piggy-bank', // Default icon
                '#28a745' // Default color
            ]);
        }
    }
}
