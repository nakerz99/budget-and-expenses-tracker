<?php
/**
 * Mark all notifications as read - AJAX handler
 */

session_start();
require_once '../includes/functions.php';

// Require authentication
if (!isAuthenticated()) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

// Mark all notifications as read
if (markAllNotificationsAsRead()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to mark notifications as read']);
}
?>
