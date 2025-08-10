<?php
/**
 * PHP Router for Clean URLs
 * This router works without requiring .htaccess or mod_rewrite
 * Place this file in your root directory and access it via: yourdomain.com/router.php/income
 */

// Get the requested path
$requestUri = $_SERVER['REQUEST_URI'];
$scriptName = $_SERVER['SCRIPT_NAME'];

// Remove the script name from the URI if it's there
if (strpos($requestUri, $scriptName) === 0) {
    $path = substr($requestUri, strlen($scriptName));
} else {
    $path = $requestUri;
}

// Clean up the path
$path = trim($path, '/');
$path = explode('?', $path)[0]; // Remove query string
$path = explode('#', $path)[0]; // Remove hash

// Define routes
$routes = [
    '' => 'index.php',
    'dashboard' => 'index.php',
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
if (isset($routes[$path])) {
    $file = $routes[$path];
    
    // Check if file exists
    if (file_exists($file)) {
        // Set the correct content type
        if (pathinfo($file, PATHINFO_EXTENSION) === 'php') {
            header('Content-Type: text/html; charset=utf-8');
        }
        
        // Include the file
        include $file;
        exit;
    } else {
        // File not found
        http_response_code(404);
        echo "<h1>404 - Page Not Found</h1>";
        echo "<p>The requested page could not be found.</p>";
        echo "<p><a href='/router.php/dashboard'>Go to Dashboard</a></p>";
        exit;
    }
} else {
    // Route not found
    http_response_code(404);
    echo "<h1>404 - Page Not Found</h1>";
    echo "<p>The requested route '{$path}' could not be found.</p>";
    echo "<p><a href='/router.php/dashboard'>Go to Dashboard</a></p>";
    exit;
}
?>
