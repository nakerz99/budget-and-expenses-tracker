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
            ['name' => 'Cash', 'description' => 'Physical cash payments'],
            ['name' => 'Credit Card', 'description' => 'Credit card payments'],
            ['name' => 'Debit Card', 'description' => 'Debit card payments'],
            ['name' => 'Online Banking', 'description' => 'Online bank transfers'],
            ['name' => 'Savings Account', 'description' => 'Direct from savings'],
            ['name' => 'Mobile Payment', 'description' => 'Mobile payment apps'],
            ['name' => 'Check', 'description' => 'Check payments'],
            ['name' => 'Other', 'description' => 'Other payment methods']
        ];
        
        $sql = "INSERT IGNORE INTO payment_methods (user_id, name, description, is_active) VALUES (?, ?, ?, ?)";
        $stmt = $this->pdo->prepare($sql);
        
        foreach ($paymentMethods as $method) {
            $stmt->execute([
                1, // Default user ID for admin
                $method['name'],
                $method['description'],
                1 // is_active
            ]);
        }
        
        echo "Default payment methods created successfully\n";
    }
}
