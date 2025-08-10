<?php
/**
 * Mark notification as read - AJAX handler
 */

session_start();
require_once '../includes/functions.php';

// Require authentication
if (!isAuthenticated()) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

// Get JSON input
$input = json_decode(file_get_contents('php://input'), true);
$notificationId = $input['notification_id'] ?? null;

if (!$notificationId) {
    echo json_encode(['success' => false, 'message' => 'Notification ID required']);
    exit;
}

// Mark notification as read
if (markNotificationAsRead($notificationId)) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to mark notification as read']);
}
?>
