<?php
/**
 * Budget Planner - Single Entry Point
 * Laravel-inspired secure entry point for the budget tracking application
 * 
 * This file serves as the only publicly accessible PHP file.
 * All application logic is handled through the app router.
 */

// Start session
session_start();

// Define application root path
define('APP_ROOT', __DIR__ . '/../app');

// Include the router to handle all requests
require_once APP_ROOT . '/router.php';
