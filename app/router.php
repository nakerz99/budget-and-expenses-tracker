<?php
/**
 * Application Router
 * Handles all application routing with Laravel-inspired structure
 */

// Get the request URI (handle CLI execution)
$uri = $_SERVER['REQUEST_URI'] ?? '/';
$path = parse_url($uri, PHP_URL_PATH);

// Remove trailing slash and query string
$path = rtrim($path, '/');

// If path is empty, serve dashboard
if (empty($path) || $path === '/') {
    $path = '/dashboard';
}

// Define application routes
$routes = [
    '/dashboard' => 'dashboard',
    '/login' => 'login',
    '/register' => 'register',
    '/logout' => 'logout',
    '/income' => 'income',
    '/expenses' => 'expenses',
    '/actual-expenses' => 'actual-expenses',
    '/quick-actions' => 'quick-actions',
    '/bills' => 'bills',
    '/savings' => 'savings',
    '/monthly-budget' => 'monthly-budget',
    '/analytics' => 'monthly-analytics',
    '/reports' => 'reports',
    '/user-approvals' => 'user-approvals',
    '/pin-settings' => 'pin-settings',
    '/expense-categories' => 'expense-categories'
];

// Handle AJAX requests
if (strpos($path, '/ajax/') === 0) {
    $ajaxPath = str_replace('/ajax/', '', $path);
    $ajaxFile = APP_ROOT . '/ajax/' . $ajaxPath . '.php';
    
    if (file_exists($ajaxFile)) {
        require_once APP_ROOT . '/includes/functions.php';
        require_once $ajaxFile;
        return;
    }
}

// Check if the path matches any of our routes
if (isset($routes[$path])) {
    $pageName = $routes[$path];
    $pageFile = APP_ROOT . '/pages/' . $pageName . '.php';
    
    if (file_exists($pageFile)) {
        // Set a flag to indicate we're using the router
        define('USING_ROUTER', true);
        
        // Include functions and the page
        require_once APP_ROOT . '/includes/functions.php';
        require_once $pageFile;
        return;
    }
}

// If no route matches, show 404
http_response_code(404);
require_once APP_ROOT . '/includes/functions.php';
require_once APP_ROOT . '/pages/404.php';
