<?php
/**
 * Quick Expense Handler
 * Budget Planner Application
 */

session_start();

// Handle path for router vs direct access
if (defined('USING_ROUTER')) {
    require_once __DIR__ . '/../includes/functions.php';
} else {
    require_once '../includes/functions.php';
}

// Require authentication
requireAuth();

// Handle quick expense submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action']) && $_POST['action'] === 'add_quick_expense') {
        $quickActionId = sanitizeInput($_POST['quick_action_id'], 'int');
        $amount = sanitizeInput($_POST['amount'], 'float');
        $date = sanitizeInput($_POST['date_paid']);
        $notes = sanitizeInput($_POST['notes']);
        $paymentMethodId = !empty($_POST['payment_method_id']) ? sanitizeInput($_POST['payment_method_id'], 'int') : null;
        
        if (addQuickExpense($quickActionId, $amount, $date, $notes, $paymentMethodId)) {
            showSuccess('Quick expense recorded successfully!');
        } else {
            showError('Failed to record quick expense.');
        }
        
        redirect('../index.php');
    }
}

// If not POST, redirect to dashboard
redirect('../index.php');
?>
