<?php
/**
 * Public Entry Point for NR BUDGET Planner
 * Handles routing for Laravel Valet
 */

// Get the request URI
$uri = $_SERVER['REQUEST_URI'];
$path = parse_url($uri, PHP_URL_PATH);

// Remove trailing slash
$path = rtrim($path, '/');

// If path is empty, serve the main index.php
if (empty($path) || $path === '/') {
    require __DIR__ . '/../index.php';
    return;
}

// Map clean URLs to PHP files
$routes = [
    '/dashboard' => '/index.php',
    '/login' => '/login.php',
    '/register' => '/register.php',
    '/logout' => '/logout.php',
    '/income' => '/pages/income.php',
    '/expenses' => '/pages/expenses.php',
    '/actual-expenses' => '/pages/actual-expenses.php',
    '/quick-actions' => '/pages/quick-actions.php',
    '/bills' => '/pages/bills.php',
    '/savings' => '/pages/savings.php',
    '/monthly-budget' => '/pages/monthly-budget.php',
    '/analytics' => '/pages/monthly-analytics.php',
    '/reports' => '/pages/reports.php',
    '/user-approvals' => '/pages/user-approvals.php',
    '/pin-settings' => '/pages/pin-settings.php',
    '/expense-categories' => '/pages/expense-categories.php'
];

// Check if the path matches any of our routes
if (isset($routes[$path])) {
    $file = __DIR__ . '/../' . $routes[$path];
    if (file_exists($file)) {
        // Set a flag to indicate we're using the router
        define('USING_ROUTER', true);
        require $file;
        return;
    }
}

// Handle AJAX requests
if (strpos($path, '/ajax/') === 0) {
    $file = __DIR__ . '/../' . $path . '.php';
    if (file_exists($file)) {
        require $file;
        return;
    }
}

// Handle direct access to pages in the pages/ directory
if (strpos($path, '/pages/') === 0) {
    $file = __DIR__ . '/../' . $path . '.php';
    if (file_exists($file)) {
        // Set a flag to indicate we're using the router
        define('USING_ROUTER', true);
        require $file;
        return;
    }
}

// If no route matches, check if it's a direct PHP file
$file = __DIR__ . '/../' . $path . '.php';
if (file_exists($file)) {
    // Set a flag to indicate we're using the router
    define('USING_ROUTER', true);
    require $file;
    return;
}

// If still no match, serve the main index.php (for 404 handling)
require __DIR__ . '/../index.php';
