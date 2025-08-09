<?php
/**
 * Default Payment Methods Seeder
 * Seeds the database with default payment methods
 */

class DefaultPaymentMethodsSeeder {
    private $pdo;
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }
    
    public function run() {
        $paymentMethods = [
            ['name' => 'Cash', 'type' => 'cash', 'description' => 'Physical cash payments'],
            ['name' => 'Credit Card', 'type' => 'credit_card', 'description' => 'Credit card payments'],
            ['name' => 'Debit Card', 'type' => 'debit_card', 'description' => 'Debit card payments'],
            ['name' => 'Online Banking', 'type' => 'online', 'description' => 'Online bank transfers'],
            ['name' => 'Savings Account', 'type' => 'savings', 'description' => 'Direct from savings'],
            ['name' => 'Mobile Payment', 'type' => 'online', 'description' => 'Mobile payment apps'],
            ['name' => 'Check', 'type' => 'other', 'description' => 'Check payments'],
            ['name' => 'Other', 'type' => 'other', 'description' => 'Other payment methods']
        ];
        
        $sql = "INSERT IGNORE INTO payment_methods (user_id, name, type, bank_name, icon, color) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $this->pdo->prepare($sql);
        
        foreach ($paymentMethods as $method) {
            $stmt->execute([
                1, // Default user ID for admin
                $method['name'],
                $method['type'],
                $method['description'], // Use description as bank_name
                'fas fa-credit-card', // Default icon
                '#007bff' // Default color
            ]);
        }
    }
}
