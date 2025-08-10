<?php
/**
 * Deployment-Ready Clean URLs Router for Hostinger
 * This file will replace index.php to enable clean URLs
 * 
 * After testing locally, upload this to Hostinger and rename to index.php
 */

// Get the requested path
$requestUri = $_SERVER['REQUEST_URI'];
$path = parse_url($requestUri, PHP_URL_PATH);
$path = trim($path, '/');

// If no path, serve dashboard
if (empty($path)) {
    include 'dashboard.php';
    exit;
}

// Define clean URL mappings
$cleanRoutes = [
    'dashboard' => 'dashboard.php',
    'login' => 'login.php',
    'register' => 'register.php',
    'logout' => 'logout.php',
    'income' => 'pages/income.php',
    'expenses' => 'pages/expenses.php',
    'actual-expenses' => 'pages/actual-expenses.php',
    'quick-actions' => 'pages/quick-actions.php',
    'bills' => 'pages/bills.php',
    'savings' => 'pages/savings.php',
    'monthly-budget' => 'pages/monthly-budget.php',
    'analytics' => 'pages/monthly-analytics.php',
    'user-approvals' => 'pages/user-approvals.php',
    'pin-settings' => 'pages/pin-settings.php',
    'expense-categories' => 'pages/expense-categories.php',
    'reports' => 'pages/reports.php',
    'quick-expense' => 'pages/quick-expense.php'
];

// Check if route exists
if (isset($cleanRoutes[$path])) {
    $file = $cleanRoutes[$path];
    
    // Check if file exists
    if (file_exists($file)) {
        // Set proper headers
        header('Content-Type: text/html; charset=utf-8');
        
        // Include the file
        include $file;
        exit;
    }
}

// If no route matches, check if it's a direct file
$directFile = $path . '.php';
if (file_exists($directFile)) {
    include $directFile;
    exit;
}

// Check if it's a page in the pages directory
$pageFile = 'pages/' . $path . '.php';
if (file_exists($pageFile)) {
    include $pageFile;
    exit;
}

// 404 - Page not found
http_response_code(404);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Page Not Found | NR BUDGET Planner</title>
    <style>
        body { 
            font-family: Arial, sans-serif; 
            text-align: center; 
            padding: 50px; 
            background: #f5f5f5; 
        }
        .container { 
            background: white; 
            padding: 30px; 
            border-radius: 10px; 
            box-shadow: 0 2px 10px rgba(0,0,0,0.1); 
            max-width: 500px; 
            margin: 0 auto; 
        }
        h1 { color: #e74c3c; }
        .btn { 
            display: inline-block; 
            padding: 10px 20px; 
            background: #3498db; 
            color: white; 
            text-decoration: none; 
            border-radius: 5px; 
            margin: 10px; 
        }
        .btn:hover { background: #2980b9; }
        .available-routes {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
            text-align: left;
        }
        .route-list {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 10px;
            margin-top: 10px;
        }
        .route-item {
            background: white;
            padding: 8px;
            border-radius: 3px;
            border: 1px solid #dee2e6;
            font-family: monospace;
            font-size: 12px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🚫 404 - Page Not Found</h1>
        <p>The page you're looking for doesn't exist.</p>
        <p><strong>Requested URL:</strong> <?php echo htmlspecialchars($path); ?></p>
        
        <div class="available-routes">
            <h3>🔗 Available Clean URLs:</h3>
            <div class="route-list">
                <?php foreach ($cleanRoutes as $route => $file): ?>
                    <div class="route-item">/<?php echo $route; ?></div>
                <?php endforeach; ?>
            </div>
        </div>
        
        <div style="margin: 30px 0;">
            <a href="/dashboard" class="btn">🏠 Dashboard</a>
            <a href="/login" class="btn">🔐 Login</a>
            <a href="/income" class="btn">💰 Income</a>
            <a href="/expenses" class="btn">💸 Expenses</a>
        </div>
        
        <p><small>💡 Tip: Use clean URLs like <code>/income</code> instead of <code>/pages/income.php</code></small></p>
    </div>
</body>
</html>
