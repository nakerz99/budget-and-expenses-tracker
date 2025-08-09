<?php
/**
 * Admin User Seeder
 * Seeds the database with a default admin user
 */

class AdminUserSeeder {
    private $pdo;
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }
    
    public function run() {
        // Check if admin user already exists
        $sql = "SELECT COUNT(*) FROM users WHERE username = 'admin'";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        
        if ($stmt->fetchColumn() > 0) {
            echo "Admin user already exists, skipping...\n";
            return;
        }
        
        // Create admin user
        $sql = "INSERT INTO users (username, email, password, is_approved, approved_at, approved_by) VALUES (?, ?, ?, ?, NOW(), ?)";
        $stmt = $this->pdo->prepare($sql);
        
        $adminPassword = password_hash('admin123', PASSWORD_DEFAULT);
        $stmt->execute([
            'admin',
            'admin@budgetplanner.com',
            $adminPassword,
            1,
            1
        ]);
        
        $adminUserId = $this->pdo->lastInsertId();
        
        // Create admin PIN
        $sql = "INSERT INTO security_pin (user_id, pin_hash) VALUES (?, ?)";
        $stmt = $this->pdo->prepare($sql);
        
        $adminPin = password_hash('123456', PASSWORD_DEFAULT);
        $stmt->execute([$adminUserId, $adminPin]);
        
        echo "Admin user created with credentials:\n";
        echo "Username: admin\n";
        echo "Password: admin123\n";
        echo "PIN: 123456\n";
        echo "Please change these credentials after first login!\n";
    }
}
