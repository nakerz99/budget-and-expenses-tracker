<?php
/**
 * Sample Data Seeder
 * Seeds the database with sample months, weeks, and other useful data
 */

class SampleDataSeeder {
    private $pdo;
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }
    
    public function run() {
        $this->seedMonths();
        $this->seedWeeks();
        $this->seedSampleExpenses();
        $this->seedSampleIncome();
    }
    
    private function seedMonths() {
        // Check if months already exist
        $sql = "SELECT COUNT(*) FROM months";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        
        if ($stmt->fetchColumn() > 0) {
            echo "Months already exist, skipping...\n";
            return;
        }
        
        $currentYear = date('Y');
        $months = [
            ['name' => 'January', 'year' => $currentYear, 'start_date' => $currentYear . '-01-01', 'end_date' => $currentYear . '-01-31'],
            ['name' => 'February', 'year' => $currentYear, 'start_date' => $currentYear . '-02-01', 'end_date' => $currentYear . '-02-28'],
            ['name' => 'March', 'year' => $currentYear, 'start_date' => $currentYear . '-03-01', 'end_date' => $currentYear . '-03-31'],
            ['name' => 'April', 'year' => $currentYear, 'start_date' => $currentYear . '-04-01', 'end_date' => $currentYear . '-04-30'],
            ['name' => 'May', 'year' => $currentYear, 'start_date' => $currentYear . '-05-01', 'end_date' => $currentYear . '-05-31'],
            ['name' => 'June', 'year' => $currentYear, 'start_date' => $currentYear . '-06-01', 'end_date' => $currentYear . '-06-30'],
            ['name' => 'July', 'year' => $currentYear, 'start_date' => $currentYear . '-07-01', 'end_date' => $currentYear . '-07-31'],
            ['name' => 'August', 'year' => $currentYear, 'start_date' => $currentYear . '-08-01', 'end_date' => $currentYear . '-08-31'],
            ['name' => 'September', 'year' => $currentYear, 'start_date' => $currentYear . '-09-01', 'end_date' => $currentYear . '-09-30'],
            ['name' => 'October', 'year' => $currentYear, 'start_date' => $currentYear . '-10-01', 'end_date' => $currentYear . '-10-31'],
            ['name' => 'November', 'year' => $currentYear, 'start_date' => $currentYear . '-11-01', 'end_date' => $currentYear . '-11-30'],
            ['name' => 'December', 'year' => $currentYear, 'start_date' => $currentYear . '-12-01', 'end_date' => $currentYear . '-12-31']
        ];
        
        $sql = "INSERT INTO months (name, year, start_date, end_date, is_active) VALUES (?, ?, ?, ?, ?)";
        $stmt = $this->pdo->prepare($sql);
        
        foreach ($months as $month) {
            $stmt->execute([
                $month['name'],
                $month['year'],
                $month['start_date'],
                $month['end_date'],
                1
            ]);
        }
        
        echo "Sample months created for year {$currentYear}\n";
    }
    
    private function seedWeeks() {
        // Check if weeks already exist
        $sql = "SELECT COUNT(*) FROM weeks";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        
        if ($stmt->fetchColumn() > 0) {
            echo "Weeks already exist, skipping...\n";
            return;
        }
        
        // Get current month for sample weeks
        $sql = "SELECT id, start_date, end_date FROM months WHERE is_active = 1 ORDER BY start_date LIMIT 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        $month = $stmt->fetch();
        
        if (!$month) {
            echo "No months found, skipping weeks...\n";
            return;
        }
        
        $startDate = new DateTime($month['start_date']);
        $endDate = new DateTime($month['end_date']);
        
        $weekNumber = 1;
        $currentDate = clone $startDate;
        
        $sql = "INSERT INTO weeks (month_id, week_number, start_date, end_date, is_active) VALUES (?, ?, ?, ?, ?)";
        $stmt = $this->pdo->prepare($sql);
        
        while ($currentDate <= $endDate) {
            $weekStart = clone $currentDate;
            $weekEnd = clone $currentDate;
            $weekEnd->modify('+6 days');
            
            // Ensure week doesn't exceed month end
            if ($weekEnd > $endDate) {
                $weekEnd = clone $endDate;
            }
            
            $stmt->execute([
                $month['id'],
                $weekNumber,
                $weekStart->format('Y-m-d'),
                $weekEnd->format('Y-m-d'),
                1
            ]);
            
            $currentDate->modify('+7 days');
            $weekNumber++;
        }
        
        echo "Sample weeks created for month: {$month['start_date']}\n";
    }
    
    private function seedSampleExpenses() {
        // Check if sample expenses already exist
        $sql = "SELECT COUNT(*) FROM expenses";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        
        if ($stmt->fetchColumn() > 0) {
            echo "Sample expenses already exist, skipping...\n";
            return;
        }
        
        // Get first month and category
        $sql = "SELECT id FROM months WHERE is_active = 1 ORDER BY start_date LIMIT 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        $monthId = $stmt->fetchColumn();
        
        $sql = "SELECT id FROM expense_categories WHERE user_id = 1 LIMIT 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        $categoryId = $stmt->fetchColumn();
        
        if (!$monthId || !$categoryId) {
            echo "No months or categories found, skipping sample expenses...\n";
            return;
        }
        
        $sampleExpenses = [
            ['amount' => 500.00, 'description' => 'Monthly rent payment', 'due_date' => date('Y-m-01'), 'is_bill' => 1],
            ['amount' => 150.00, 'description' => 'Electricity bill', 'due_date' => date('Y-m-15'), 'is_bill' => 1],
            ['amount' => 80.00, 'description' => 'Internet service', 'due_date' => date('Y-m-20'), 'is_bill' => 1],
            ['amount' => 200.00, 'description' => 'Grocery shopping', 'due_date' => null, 'is_bill' => 0],
            ['amount' => 50.00, 'description' => 'Gas for car', 'due_date' => null, 'is_bill' => 0]
        ];
        
        $sql = "INSERT INTO expenses (user_id, category_id, month_id, amount, description, due_date, is_bill) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->pdo->prepare($sql);
        
        foreach ($sampleExpenses as $expense) {
            $stmt->execute([
                1, // Admin user ID
                $categoryId,
                $monthId,
                $expense['amount'],
                $expense['description'],
                $expense['due_date'],
                $expense['is_bill']
            ]);
        }
        
        echo "Sample expenses created\n";
    }
    
    private function seedSampleIncome() {
        // Check if sample income already exists
        $sql = "SELECT COUNT(*) FROM income_sources";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        
        if ($stmt->fetchColumn() > 0) {
            echo "Sample income already exists, skipping...\n";
            return;
        }
        
        // Get first month
        $sql = "SELECT id FROM months WHERE is_active = 1 ORDER BY start_date LIMIT 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        $monthId = $stmt->fetchColumn();
        
        if (!$monthId) {
            echo "No months found, skipping sample income...\n";
            return;
        }
        
        $sampleIncome = [
            ['name' => 'Salary', 'amount' => 3000.00],
            ['name' => 'Freelance Work', 'amount' => 500.00],
            ['name' => 'Investment Returns', 'amount' => 200.00]
        ];
        
        $sql = "INSERT INTO income_sources (user_id, name, amount, month_id) VALUES (?, ?, ?, ?)";
        $stmt = $this->pdo->prepare($sql);
        
        foreach ($sampleIncome as $income) {
            $stmt->execute([
                1, // Admin user ID
                $income['name'],
                $income['amount'],
                $monthId
            ]);
        }
        
        echo "Sample income sources created\n";
    }
}
