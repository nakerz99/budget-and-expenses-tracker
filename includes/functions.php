<?php
/**
 * Helper Functions
 * Budget Planner Application
 */

require_once __DIR__ . '/../config/database.php';

/**
 * Check if user is authenticated with PIN
 * @return bool
 */
function isAuthenticated() {
    // Check if session is properly set and user_id exists
    if (!isset($_SESSION['authenticated']) || $_SESSION['authenticated'] !== true) {
        return false;
    }
    
    if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
        return false;
    }
    
    // Additional check: verify user still exists in database
    $userId = $_SESSION['user_id'];
    $sql = "SELECT id, is_approved FROM users WHERE id = ?";
    $result = fetchOne($sql, [$userId]);
    
    if (!$result || !$result['is_approved']) {
        // User doesn't exist or is not approved, clear session
        session_destroy();
        return false;
    }
    
    return true;
}

/**
 * Verify PIN
 * @param string $pin
 * @return bool
 */
function verifyPIN($pin) {
    $sql = "SELECT pin_hash FROM security_pin ORDER BY id DESC LIMIT 1";
    $result = fetchOne($sql);
    
    if ($result) {
        return password_verify($pin, $result['pin_hash']);
    }
    
    return false;
}

/**
 * Verify user PIN
 * @param string $username
 * @param string $pin
 * @return bool
 */
function verifyUserPIN($username, $pin) {
    $sql = "SELECT sp.pin_hash, u.is_approved FROM security_pin sp 
            JOIN users u ON sp.user_id = u.id 
            WHERE u.username = ? AND sp.is_active = 1";
    $result = fetchOne($sql, [$username]);
    
    if (!$result) {
        return false;
    }
    
    // Check if user is approved
    if (!$result['is_approved']) {
        return false;
    }
    
    return password_verify($pin, $result['pin_hash']);
}

/**
 * Get user ID by username
 * @param string $username
 * @return int|null
 */
function getUserIdByUsername($username) {
    $sql = "SELECT id FROM users WHERE username = ?";
    $result = fetchOne($sql, [$username]);
    
    return $result ? $result['id'] : null;
}

/**
 * Get current user ID from session
 * @return int|null
 */
function getCurrentUserId() {
    return $_SESSION['user_id'] ?? null;
}

/**
 * Get current username from session
 * @return string|null
 */
function getCurrentUsername() {
    return $_SESSION['username'] ?? null;
}

/**
 * Check if current user is admin
 * @return bool
 */
function isAdmin() {
    $userId = getCurrentUserId();
    if (!$userId) return false;
    
    $sql = "SELECT is_approved FROM users WHERE id = ?";
    $result = fetchOne($sql, [$userId]);
    return $result && $result['is_approved'] == 1;
}

/**
 * Get pending user registrations (for admin)
 * @return array
 */
function getPendingRegistrations() {
    $sql = "SELECT id, username, email, created_at FROM users WHERE is_approved = 0 ORDER BY created_at DESC";
    return fetchAll($sql);
}

/**
 * Approve user registration
 * @param int $userId
 * @return bool
 */
function approveUser($userId) {
    $adminId = getCurrentUserId();
    $sql = "UPDATE users SET is_approved = 1, approved_at = NOW(), approved_by = ? WHERE id = ?";
    return executeQuery($sql, [$adminId, $userId]);
}

/**
 * Deny user registration
 * @param int $userId
 * @return bool
 */
function denyUser($userId) {
    $sql = "DELETE FROM users WHERE id = ? AND is_approved = 0";
    return executeQuery($sql, [$userId]);
}

/**
 * Create notification
 * @param int $userId
 * @param string $type
 * @param string $title
 * @param string $message
 * @return bool
 */
function createNotification($userId, $type, $title, $message) {
    $sql = "INSERT INTO notifications (user_id, type, title, message) VALUES (?, ?, ?, ?)";
    return executeQuery($sql, [$userId, $type, $title, $message]);
}

/**
 * Get notifications for current user
 * @param bool $unreadOnly
 * @return array
 */
function getNotifications($unreadOnly = false) {
    $userId = getCurrentUserId();
    if (!$userId) return [];
    
    $sql = "SELECT * FROM notifications WHERE user_id = ?";
    if ($unreadOnly) {
        $sql .= " AND is_read = 0";
    }
    $sql .= " ORDER BY created_at DESC";
    
    return fetchAll($sql, [$userId]);
}

/**
 * Mark notification as read
 * @param int $notificationId
 * @return bool
 */
function markNotificationAsRead($notificationId) {
    $userId = getCurrentUserId();
    $sql = "UPDATE notifications SET is_read = 1 WHERE id = ? AND user_id = ?";
    return executeQuery($sql, [$notificationId, $userId]);
}

/**
 * Mark all notifications as read
 * @return bool
 */
function markAllNotificationsAsRead() {
    $userId = getCurrentUserId();
    $sql = "UPDATE notifications SET is_read = 1 WHERE user_id = ?";
    return executeQuery($sql, [$userId]);
}

/**
 * Get unread notification count
 * @return int
 */
function getUnreadNotificationCount() {
    $userId = getCurrentUserId();
    if (!$userId) return 0;
    
    $sql = "SELECT COUNT(*) as count FROM notifications WHERE user_id = ? AND is_read = 0";
    $result = fetchOne($sql, [$userId]);
    return $result ? $result['count'] : 0;
}

/**
 * Check if current script is in pages directory
 * @return bool
 */
function isInPagesDirectory() {
    $scriptPath = $_SERVER['SCRIPT_NAME'];
    return strpos($scriptPath, '/pages/') !== false;
}

/**
 * Get bills with due dates
 * @param int $monthId
 * @return array
 */
function getBills($monthId = null) {
    $userId = getCurrentUserId();
    if ($monthId) {
        $sql = "SELECT e.*, ec.name as category_name, ec.color as category_color 
                FROM expenses e 
                JOIN expense_categories ec ON e.category_id = ec.id 
                WHERE e.month_id = ? AND e.user_id = ? AND e.is_bill = 1 
                ORDER BY e.due_date ASC";
        return fetchAll($sql, [$monthId, $userId]);
    } else {
        $sql = "SELECT e.*, ec.name as category_name, ec.color as category_color 
                FROM expenses e 
                JOIN expense_categories ec ON e.category_id = ec.id 
                WHERE e.month_id = (SELECT id FROM months WHERE is_active = 1 AND user_id = ?) 
                AND e.user_id = ? AND e.is_bill = 1 
                ORDER BY e.due_date ASC";
        return fetchAll($sql, [$userId, $userId]);
    }
}

/**
 * Get upcoming bills (due within next 7 days)
 * @return array
 */
function getUpcomingBills() {
    $userId = getCurrentUserId();
    $currentMonth = getCurrentMonth();
    if (!$currentMonth) return [];
    
    $sql = "SELECT e.*, ec.name as category_name, ec.color as category_color,
                   DATEDIFF(e.due_date, CURDATE()) as days_until_due
            FROM expenses e 
            JOIN expense_categories ec ON e.category_id = ec.id 
            WHERE e.month_id = ? AND e.user_id = ? AND e.is_bill = 1 
            AND e.due_date >= CURDATE() AND e.due_date <= DATE_ADD(CURDATE(), INTERVAL 7 DAY)
            ORDER BY e.due_date ASC";
    
    return fetchAll($sql, [$currentMonth['id'], $userId]);
}

/**
 * Get overdue bills
 * @return array
 */
function getOverdueBills() {
    $userId = getCurrentUserId();
    $currentMonth = getCurrentMonth();
    if (!$currentMonth) return [];
    
    $sql = "SELECT e.*, ec.name as category_name, ec.color as category_color,
                   DATEDIFF(CURDATE(), e.due_date) as days_overdue
            FROM expenses e 
            JOIN expense_categories ec ON e.category_id = ec.id 
            WHERE e.month_id = ? AND e.user_id = ? AND e.is_bill = 1 
            AND e.due_date < CURDATE()
            ORDER BY e.due_date ASC";
    
    return fetchAll($sql, [$currentMonth['id'], $userId]);
}

/**
 * Get bill statistics
 * @return array
 */
function getBillStatistics() {
    $userId = getCurrentUserId();
    $currentMonth = getCurrentMonth();
    if (!$currentMonth) return [];
    
    $sql = "SELECT 
                COUNT(*) as total_bills,
                SUM(CASE WHEN due_date < CURDATE() THEN 1 ELSE 0 END) as overdue_bills,
                SUM(CASE WHEN due_date >= CURDATE() AND due_date <= DATE_ADD(CURDATE(), INTERVAL 7 DAY) THEN 1 ELSE 0 END) as upcoming_bills,
                SUM(budgeted_amount) as total_amount,
                SUM(CASE WHEN due_date < CURDATE() THEN budgeted_amount ELSE 0 END) as overdue_amount
            FROM expenses 
            WHERE month_id = ? AND user_id = ? AND is_bill = 1";
    
    $result = fetchOne($sql, [$currentMonth['id'], $userId]);
    return $result ?: [];
}

/**
 * Update bill due date
 * @param int $expenseId
 * @param string $dueDate
 * @return bool
 */
function updateBillDueDate($expenseId, $dueDate) {
    $userId = getCurrentUserId();
    $sql = "UPDATE expenses SET due_date = ? WHERE id = ? AND user_id = ? AND is_bill = 1";
    return executeQuery($sql, [$dueDate, $expenseId, $userId]);
}

/**
 * Mark bill as paid
 * @param int $expenseId
 * @param float $amount
 * @param string $date
 * @param string $notes
 * @param int $paymentMethodId
 * @return bool
 */
function markBillAsPaid($expenseId, $amount, $date, $notes = '', $paymentMethodId = null) {
    $userId = getCurrentUserId();
    $currentMonth = getCurrentMonth();
    $currentWeek = getCurrentWeek();
    
    // Debug logging
    error_log("markBillAsPaid - User ID: $userId, Month: " . ($currentMonth ? $currentMonth['id'] : 'null') . ", Week: " . ($currentWeek ? $currentWeek['id'] : 'null'));
    
    if (!$currentMonth) {
        error_log("markBillAsPaid failed - No current month found");
        return false;
    }
    
    if (!$currentWeek) {
        error_log("markBillAsPaid failed - No current week found");
        return false;
    }
    
    $data = [
        'expense_id' => $expenseId,
        'month_id' => $currentMonth['id'],
        'week_id' => $currentWeek['id'],
        'actual_amount' => $amount,
        'date_paid' => $date,
        'notes' => $notes,
        'payment_method_id' => $paymentMethodId
    ];
    
    $result = addActualExpense($data);
    error_log("markBillAsPaid result: " . ($result ? 'success' : 'failed'));
    
    return $result;
}

/**
 * Update PIN
 * @param string $newPin
 * @return bool
 */
function updatePIN($newPin) {
    $pinHash = password_hash($newPin, PASSWORD_DEFAULT);
    $sql = "UPDATE security_pin SET pin_hash = ? WHERE id = (SELECT id FROM (SELECT id FROM security_pin ORDER BY id DESC LIMIT 1) as temp)";
    return executeQuery($sql, [$pinHash]);
}

/**
 * Require authentication - redirect to login if not authenticated
 */
function requireAuth() {
    if (!isAuthenticated()) {
        // Clear any corrupted session data
        session_destroy();
        session_start();
        header("Location: login.php");
        exit();
    }
}

/**
 * Format currency
 * @param float $amount
 * @return string
 */
function formatCurrency($amount) {
    return '₱' . number_format($amount, 2);
}

/**
 * Get current month data
 * @return array
 */
function getCurrentMonth() {
    $userId = getCurrentUserId();
    
    // If no user is logged in, return null
    if (!$userId) {
        return null;
    }
    
    // Get current date
    $currentDate = new DateTime();
    $currentYear = $currentDate->format('Y');
    $currentMonth = $currentDate->format('n');
    
    // Find the month for current date
    $sql = "SELECT * FROM months WHERE year = ? AND month = ? AND user_id = ? ORDER BY id DESC LIMIT 1";
    $month = fetchOne($sql, [$currentYear, $currentMonth, $userId]);
    
    // If no month found for current date, get the active month
    if (!$month) {
        $sql = "SELECT * FROM months WHERE is_active = 1 AND user_id = ? ORDER BY id DESC LIMIT 1";
        $month = fetchOne($sql, [$userId]);
    }
    
    return $month;
}

/**
 * Get current week data
 * @return array
 */
function getCurrentWeek() {
    $userId = getCurrentUserId();
    
    // If no user is logged in, return null
    if (!$userId) {
        return null;
    }
    
    // Get current date
    $currentDate = new DateTime();
    $currentYear = $currentDate->format('Y');
    $currentMonth = $currentDate->format('n');
    $currentDay = $currentDate->format('j');
    
    // Find the week that contains today's date
    $sql = "SELECT * FROM weeks 
            WHERE year = ? AND month = ? 
            AND ? BETWEEN DAY(start_date) AND DAY(end_date)
            AND user_id = ?
            ORDER BY week_number LIMIT 1";
    
    $week = fetchOne($sql, [$currentYear, $currentMonth, $currentDay, $userId]);
    
    // If no week found for current date, get the active week
    if (!$week) {
        $sql = "SELECT * FROM weeks WHERE is_active = 1 AND user_id = ? ORDER BY id DESC LIMIT 1";
        $week = fetchOne($sql, [$userId]);
    }
    
    return $week;
}

/**
 * Get all months
 * @return array
 */
function getAllMonths() {
    $userId = getCurrentUserId();
    
    // If no user is logged in, return empty array
    if (!$userId) {
        return [];
    }
    
    $sql = "SELECT * FROM months WHERE user_id = ? ORDER BY year DESC, month DESC";
    return fetchAll($sql, [$userId]);
}

/**
 * Get all weeks for current month
 * @return array
 */
function getAllWeeks() {
    $userId = getCurrentUserId();
    
    // If no user is logged in, return empty array
    if (!$userId) {
        return [];
    }
    
    $currentMonth = getCurrentMonth();
    
    // If no current month, return empty array
    if (!$currentMonth) {
        return [];
    }
    
    $sql = "SELECT * FROM weeks WHERE year = ? AND month = ? AND user_id = ? ORDER BY week_number";
    return fetchAll($sql, [$currentMonth['year'], $currentMonth['month'], $userId]);
}

/**
 * Get total income for current month
 * @return float
 */
function getTotalIncome($monthId = null) {
    $userId = getCurrentUserId();
    if ($monthId) {
        $sql = "SELECT COALESCE(SUM(amount), 0) as total FROM income_sources WHERE month_id = ? AND user_id = ?";
        $result = fetchOne($sql, [$monthId, $userId]);
    } else {
        $sql = "SELECT COALESCE(SUM(amount), 0) as total FROM income_sources WHERE month_id = (SELECT id FROM months WHERE is_active = 1 AND user_id = ?) AND user_id = ?";
        $result = fetchOne($sql, [$userId, $userId]);
    }
    return $result['total'] ?? 0;
}

/**
 * Get total budgeted expenses for current month
 * @return float
 */
function getTotalBudgetedExpenses($monthId = null) {
    $userId = getCurrentUserId();
    if ($monthId) {
        $sql = "SELECT COALESCE(SUM(budgeted_amount), 0) as total FROM expenses WHERE month_id = ? AND user_id = ?";
        $result = fetchOne($sql, [$monthId, $userId]);
    } else {
        $sql = "SELECT COALESCE(SUM(budgeted_amount), 0) as total FROM expenses WHERE month_id = (SELECT id FROM months WHERE is_active = 1 AND user_id = ?) AND user_id = ?";
        $result = fetchOne($sql, [$userId, $userId]);
    }
    return $result['total'] ?? 0;
}

/**
 * Get total actual expenses for current month
 * @param int $monthId
 * @return float
 */
function getTotalActualExpenses($monthId = null) {
    $userId = getCurrentUserId();
    if ($monthId === null) {
        $currentMonth = getCurrentMonth();
        if (!$currentMonth) {
            return 0;
        }
        $monthId = $currentMonth['id'];
    }
    
    $sql = "SELECT SUM(actual_amount) as total FROM actual_expenses WHERE month_id = ? AND user_id = ?";
    $result = fetchOne($sql, [$monthId, $userId]);
    return ($result && isset($result['total'])) ? $result['total'] : 0;
}

/**
 * Get total actual expenses for current week
 * @param int $weekId
 * @return float
 */
function getTotalActualExpensesWeek($weekId = null) {
    $userId = getCurrentUserId();
    if ($weekId === null) {
        $currentWeek = getCurrentWeek();
        if (!$currentWeek) {
            return 0;
        }
        $weekId = $currentWeek['id'];
    }
    
    $sql = "SELECT SUM(actual_amount) as total FROM actual_expenses WHERE week_id = ? AND user_id = ?";
    $result = fetchOne($sql, [$weekId, $userId]);
    return ($result && isset($result['total'])) ? $result['total'] : 0;
}

/**
 * Get savings (income - expenses)
 * @param int $monthId
 * @return float
 */
function getSavings($monthId = null) {
    $income = getTotalIncome($monthId);
    $budgeted = getTotalBudgetedExpenses($monthId);
    $actual = getTotalActualExpenses($monthId);
    
    // Use actual expenses if available, otherwise use budgeted
    $expenses = $actual > 0 ? $actual : $budgeted;
    
    return $income - $expenses;
}

/**
 * Get all income sources
 * @return array
 */
function getIncomeSources($monthId = null) {
    $userId = getCurrentUserId();
    if ($monthId) {
        $sql = "SELECT * FROM income_sources WHERE month_id = ? AND user_id = ? ORDER BY schedule_day, name";
        return fetchAll($sql, [$monthId, $userId]);
    } else {
        $sql = "SELECT * FROM income_sources WHERE month_id = (SELECT id FROM months WHERE is_active = 1 AND user_id = ?) AND user_id = ? ORDER BY schedule_day, name";
        return fetchAll($sql, [$userId, $userId]);
    }
}

/**
 * Get all expense categories
 * @return array
 */
function getExpenseCategories() {
    $sql = "SELECT * FROM expense_categories WHERE is_active = 1 ORDER BY name";
    return fetchAll($sql);
}

/**
 * Get expense category by ID
 * @param int $id
 * @return array|false
 */
function getExpenseCategoryById($id) {
    $sql = "SELECT * FROM expense_categories WHERE id = ?";
    return fetchOne($sql, [$id]);
}

/**
 * Add new expense category
 * @param array $data
 * @return bool
 */
function addExpenseCategory($data) {
    $sql = "INSERT INTO expense_categories (name, description, color) VALUES (?, ?, ?)";
    $params = [
        sanitizeInput($data['name']),
        sanitizeInput($data['description']),
        sanitizeInput($data['color'])
    ];
    return executeQuery($sql, $params);
}

/**
 * Update expense category
 * @param int $id
 * @param array $data
 * @return bool
 */
function updateExpenseCategory($id, $data) {
    $sql = "UPDATE expense_categories SET name = ?, description = ?, color = ? WHERE id = ?";
    $params = [
        sanitizeInput($data['name']),
        sanitizeInput($data['description']),
        sanitizeInput($data['color']),
        $id
    ];
    return executeQuery($sql, $params);
}

/**
 * Delete expense category (soft delete)
 * @param int $id
 * @return bool
 */
function deleteExpenseCategory($id) {
    $sql = "UPDATE expense_categories SET is_active = 0 WHERE id = ?";
    return executeQuery($sql, [$id]);
}

/**
 * Get all budgeted expenses with category info
 * @return array
 */
function getBudgetedExpenses($monthId = null) {
    $userId = getCurrentUserId();
    if ($monthId) {
        $sql = "SELECT e.*, ec.name as category_name, ec.color as category_color 
                FROM expenses e 
                JOIN expense_categories ec ON e.category_id = ec.id 
                WHERE e.month_id = ? AND e.user_id = ? AND ec.user_id = ? 
                ORDER BY ec.name, e.name";
        return fetchAll($sql, [$monthId, $userId, $userId]);
    } else {
        $sql = "SELECT e.*, ec.name as category_name, ec.color as category_color 
                FROM expenses e 
                JOIN expense_categories ec ON e.category_id = ec.id 
                WHERE e.month_id = (SELECT id FROM months WHERE is_active = 1 AND user_id = ?) 
                AND e.user_id = ? AND ec.user_id = ? 
                ORDER BY ec.name, e.name";
        return fetchAll($sql, [$userId, $userId, $userId]);
    }
}

/**
 * Get actual expenses for a month
 * @param int $monthId
 * @return array
 */
function getActualExpenses($monthId = null) {
    $userId = getCurrentUserId();
    if ($monthId === null) {
        $currentMonth = getCurrentMonth();
        if (!$currentMonth) {
            return [];
        }
        $monthId = $currentMonth['id'];
    }
    
    $sql = "SELECT ae.*, e.name as expense_name, e.budgeted_amount, 
                   ec.name as category_name, ec.color as category_color,
                   pm.name as payment_method_name, pm.icon as payment_method_icon, pm.color as payment_method_color,
                   sa.name as savings_account_name, sa.icon as savings_account_icon, sa.color as savings_account_color
            FROM actual_expenses ae
            JOIN expenses e ON ae.expense_id = e.id
            JOIN expense_categories ec ON e.category_id = ec.id
            LEFT JOIN payment_methods pm ON ae.payment_method_id = pm.id
            LEFT JOIN savings_accounts sa ON ae.savings_account_id = sa.id
            WHERE ae.month_id = ? AND ae.user_id = ? AND e.user_id = ? AND ec.user_id = ?
            ORDER BY ae.date_paid DESC";
    return fetchAll($sql, [$monthId, $userId, $userId, $userId]);
}

/**
 * Get actual expenses for a week
 * @param int $weekId
 * @return array
 */
function getActualExpensesWeek($weekId = null) {
    $userId = getCurrentUserId();
    if ($weekId === null) {
        $currentWeek = getCurrentWeek();
        if (!$currentWeek) {
            return [];
        }
        $weekId = $currentWeek['id'];
    }
    
    $sql = "SELECT ae.*, e.name as expense_name, e.budgeted_amount, 
                   ec.name as category_name, ec.color as category_color
            FROM actual_expenses ae
            JOIN expenses e ON ae.expense_id = e.id
            JOIN expense_categories ec ON e.category_id = ec.id
            WHERE ae.week_id = ? AND ae.user_id = ? AND e.user_id = ? AND ec.user_id = ?
            ORDER BY ae.date_paid DESC";
    return fetchAll($sql, [$weekId, $userId, $userId, $userId]);
}

/**
 * Get budget vs actual comparison
 * @param int $monthId
 * @return array
 */
function getBudgetVsActual($monthId = null) {
    $userId = getCurrentUserId();
    if ($monthId === null) {
        $currentMonth = getCurrentMonth();
        if (!$currentMonth) {
            return [];
        }
        $monthId = $currentMonth['id'];
    }
    
    $sql = "SELECT e.id, e.name, e.budgeted_amount, ec.name as category_name,
                   COALESCE(SUM(ae.actual_amount), 0) as actual_amount,
                   (e.budgeted_amount - COALESCE(SUM(ae.actual_amount), 0)) as difference
            FROM expenses e
            JOIN expense_categories ec ON e.category_id = ec.id
            LEFT JOIN actual_expenses ae ON e.id = ae.expense_id AND ae.month_id = ?
            WHERE e.is_active = 1 AND e.user_id = ? AND ec.user_id = ?
            GROUP BY e.id, e.name, e.budgeted_amount, ec.name
            ORDER BY ec.name, e.name";
    
    return fetchAll($sql, [$monthId, $userId, $userId]);
}

/**
 * Get quick actions
 * @return array
 */
function getQuickActions() {
    $userId = getCurrentUserId();
    $sql = "SELECT qa.*, ec.name as category_name, ec.color as category_color 
            FROM quick_actions qa 
            JOIN expense_categories ec ON qa.category_id = ec.id 
            WHERE qa.is_active = 1 AND qa.user_id = ? AND ec.user_id = ? 
            ORDER BY qa.name";
    return fetchAll($sql, [$userId, $userId]);
}

/**
 * Add new income source
 * @param array $data
 * @return bool
 */
function addIncomeSource($data) {
    $userId = getCurrentUserId();
    $sql = "INSERT INTO income_sources (name, amount, schedule_type, schedule_day, description, month_id, user_id) 
            VALUES (?, ?, ?, ?, ?, ?, ?)";
    
    // Get current month if not specified
    $currentMonth = getCurrentMonth();
    $monthId = $data['month_id'] ?? ($currentMonth ? $currentMonth['id'] : null);
    
    return executeQuery($sql, [
        $data['name'],
        $data['amount'],
        $data['schedule_type'],
        $data['schedule_day'],
        $data['description'] ?? '',
        $monthId,
        $userId
    ]);
}

/**
 * Add new expense
 * @param array $data
 * @return bool
 */
function addExpense($data) {
    $userId = getCurrentUserId();
    $sql = "INSERT INTO expenses (category_id, name, budgeted_amount, schedule_type, schedule_day, description, due_date, is_bill, bill_type, month_id, user_id) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    
    // Get current month if not specified
    $currentMonth = getCurrentMonth();
    $monthId = $data['month_id'] ?? ($currentMonth ? $currentMonth['id'] : null);
    
    return executeQuery($sql, [
        $data['category_id'],
        $data['name'],
        $data['budgeted_amount'],
        $data['schedule_type'],
        $data['schedule_day'],
        $data['description'] ?? '',
        $data['due_date'] ?? null,
        $data['is_bill'] ?? 0,
        $data['bill_type'] ?? 'other',
        $monthId,
        $userId
    ]);
}

/**
 * Add actual expense
 * @param array $data
 * @return bool
 */
function addActualExpense($data) {
    $sql = "INSERT INTO actual_expenses (expense_id, month_id, week_id, actual_amount, date_paid, notes, payment_method_id, savings_account_id) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    
    // Handle empty values for optional fields
    $paymentMethodId = !empty($data['payment_method_id']) ? $data['payment_method_id'] : null;
    $savingsAccountId = !empty($data['savings_account_id']) ? $data['savings_account_id'] : null;
    
    return executeQuery($sql, [
        $data['expense_id'],
        $data['month_id'],
        $data['week_id'] ?? null,
        $data['actual_amount'],
        $data['date_paid'],
        $data['notes'] ?? '',
        $paymentMethodId,
        $savingsAccountId
    ]);
}

/**
 * Add quick expense from quick action
 * @param int $quickActionId
 * @param float $amount
 * @param string $date
 * @param string $notes
 * @return bool
 */
function addQuickExpense($quickActionId, $amount, $date, $notes = '', $paymentMethodId = null, $savingsAccountId = null) {
    $sql = "SELECT qa.*, ec.name as category_name 
            FROM quick_actions qa 
            JOIN expense_categories ec ON qa.category_id = ec.id 
            WHERE qa.id = ?";
    $quickAction = fetchOne($sql, [$quickActionId]);
    
    if (!$quickAction) {
        return false;
    }
    
    // Get current month and week
    $currentMonth = getCurrentMonth();
    $currentWeek = getCurrentWeek();
    
    // Find or create expense for this quick action
    $expenseSql = "SELECT id FROM expenses WHERE name = ? AND category_id = ? LIMIT 1";
    $expense = fetchOne($expenseSql, [$quickAction['name'], $quickAction['category_id']]);
    
    if (!$expense) {
        // Create a new expense for this quick action
        $expenseData = [
            'category_id' => $quickAction['category_id'],
            'name' => $quickAction['name'],
            'budgeted_amount' => $amount,
            'schedule_type' => 'monthly',
            'schedule_day' => null,
            'description' => 'Quick expense from ' . $quickAction['name']
        ];
        addExpense($expenseData);
        $expenseId = getLastInsertId();
    } else {
        $expenseId = $expense['id'];
    }
    
    // Add actual expense
    $actualExpenseData = [
        'expense_id' => $expenseId,
        'month_id' => $currentMonth['id'],
        'week_id' => $currentWeek['id'],
        'actual_amount' => $amount,
        'date_paid' => $date,
        'notes' => $notes,
        'payment_method_id' => $paymentMethodId,
        'savings_account_id' => $savingsAccountId
    ];
    
    return addActualExpense($actualExpenseData);
}

/**
 * Update income source
 * @param int $id
 * @param array $data
 * @return bool
 */
function updateIncomeSource($id, $data) {
    $sql = "UPDATE income_sources SET name = ?, amount = ?, schedule_type = ?, schedule_day = ?, description = ? 
            WHERE id = ?";
    return executeQuery($sql, [
        $data['name'],
        $data['amount'],
        $data['schedule_type'],
        $data['schedule_day'],
        $data['description'] ?? '',
        $id
    ]);
}

/**
 * Update expense
 * @param int $id
 * @param array $data
 * @return bool
 */
function updateExpense($id, $data) {
    $userId = getCurrentUserId();
    $sql = "UPDATE expenses SET category_id = ?, name = ?, budgeted_amount = ?, schedule_type = ?, schedule_day = ?, description = ?, due_date = ?, is_bill = ?, bill_type = ? 
            WHERE id = ? AND user_id = ?";
    return executeQuery($sql, [
        $data['category_id'],
        $data['name'],
        $data['budgeted_amount'],
        $data['schedule_type'],
        $data['schedule_day'],
        $data['description'] ?? '',
        $data['due_date'] ?? null,
        $data['is_bill'] ?? 0,
        $data['bill_type'] ?? 'other',
        $id,
        $userId
    ]);
}

/**
 * Delete income source
 * @param int $id
 * @return bool
 */
function deleteIncomeSource($id) {
    $sql = "UPDATE income_sources SET is_active = 0 WHERE id = ?";
    return executeQuery($sql, [$id]);
}

/**
 * Delete expense
 * @param int $id
 * @return bool
 */
function deleteExpense($id) {
    $sql = "UPDATE expenses SET is_active = 0 WHERE id = ?";
    return executeQuery($sql, [$id]);
}

/**
 * Get expense by ID
 * @param int $id
 * @return array|false
 */
function getExpenseById($id) {
    $sql = "SELECT e.*, ec.name as category_name 
            FROM expenses e 
            JOIN expense_categories ec ON e.category_id = ec.id 
            WHERE e.id = ? AND e.is_active = 1";
    return fetchOne($sql, [$id]);
}

/**
 * Get income source by ID
 * @param int $id
 * @return array|false
 */
function getIncomeSourceById($id) {
    $sql = "SELECT * FROM income_sources WHERE id = ? AND is_active = 1";
    return fetchOne($sql, [$id]);
}

/**
 * Validate and sanitize input
 * @param mixed $input
 * @param string $type
 * @return mixed
 */
function sanitizeInput($input, $type = 'string') {
    switch ($type) {
        case 'float':
            return filter_var($input, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
        case 'int':
            return filter_var($input, FILTER_SANITIZE_NUMBER_INT);
        case 'email':
            return filter_var($input, FILTER_SANITIZE_EMAIL);
        case 'url':
            return filter_var($input, FILTER_SANITIZE_URL);
        default:
            return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
    }
}

/**
 * Redirect to a page
 * @param string $page
 */
function redirect($page) {
    header("Location: $page");
    exit();
}

/**
 * Display success message
 * @param string $message
 */
function showSuccess($message) {
    $_SESSION['success'] = $message;
}

/**
 * Display error message
 * @param string $message
 */
function showError($message) {
    $_SESSION['error'] = $message;
}

/**
 * Get and clear session messages
 * @return array
 */
function getMessages() {
    $messages = [
        'success' => $_SESSION['success'] ?? null,
        'error' => $_SESSION['error'] ?? null
    ];
    
    unset($_SESSION['success'], $_SESSION['error']);
    return $messages;
}

/**
 * Get quick action by ID
 * @param int $id
 * @return array|false
 */
function getQuickActionById($id) {
    $sql = "SELECT qa.*, ec.name as category_name 
            FROM quick_actions qa 
            JOIN expense_categories ec ON qa.category_id = ec.id 
            WHERE qa.id = ? AND qa.is_active = 1";
    return fetchOne($sql, [$id]);
}

/**
 * Add new quick action
 * @param array $data
 * @return bool
 */
function addQuickAction($data) {
    $sql = "INSERT INTO quick_actions (user_id, name, category_id, amount, icon, color, is_active) 
            VALUES (?, ?, ?, ?, ?, ?, 1)";
    
    $params = [
        $data['user_id'] ?? 1,
        sanitizeInput($data['name']),
        sanitizeInput($data['category_id'], 'int'),
        sanitizeInput($data['amount'], 'float'),
        sanitizeInput($data['icon']),
        sanitizeInput($data['color'])
    ];
    
    return executeQuery($sql, $params);
}

/**
 * Update quick action
 * @param int $id
 * @param array $data
 * @return bool
 */
function updateQuickAction($id, $data) {
    $sql = "UPDATE quick_actions 
            SET name = ?, category_id = ?, amount = ?, icon = ?, color = ? 
            WHERE id = ? AND is_active = 1";
    
    $params = [
        sanitizeInput($data['name']),
        sanitizeInput($data['category_id'], 'int'),
        sanitizeInput($data['amount'], 'float'),
        sanitizeInput($data['icon']),
        sanitizeInput($data['color']),
        $id
    ];
    
    return executeQuery($sql, $params);
}

/**
 * Delete quick action
 * @param int $id
 * @return bool
 */
function deleteQuickAction($id) {
    $sql = "UPDATE quick_actions SET is_active = 0 WHERE id = ?";
    return executeQuery($sql, [$id]);
}

/**
 * Toggle quick action status
 * @param int $id
 * @return bool
 */
function toggleQuickAction($id) {
    $sql = "UPDATE quick_actions SET is_active = NOT is_active WHERE id = ?";
    return executeQuery($sql, [$id]);
}

/**
 * Get actual expense by ID
 * @param int $id
 * @return array|false
 */
function getActualExpenseById($id) {
    $sql = "SELECT ae.*, e.name as expense_name, ec.name as category_name, m.name as month_name, w.name as week_name,
                   pm.name as payment_method_name, pm.icon as payment_method_icon, pm.color as payment_method_color,
                   sa.name as savings_account_name, sa.icon as savings_account_icon, sa.color as savings_account_color
            FROM actual_expenses ae 
            JOIN expenses e ON ae.expense_id = e.id 
            JOIN expense_categories ec ON e.category_id = ec.id
            LEFT JOIN months m ON ae.month_id = m.id
            LEFT JOIN weeks w ON ae.week_id = w.id
            LEFT JOIN payment_methods pm ON ae.payment_method_id = pm.id
            LEFT JOIN savings_accounts sa ON ae.savings_account_id = sa.id
            WHERE ae.id = ?";
    return fetchOne($sql, [$id]);
}

/**
 * Update actual expense
 * @param int $id
 * @param array $data
 * @return bool
 */
function updateActualExpense($id, $data) {
    $sql = "UPDATE actual_expenses 
            SET expense_id = ?, month_id = ?, week_id = ?, actual_amount = ?, date_paid = ?, notes = ?, payment_method_id = ?, savings_account_id = ? 
            WHERE id = ?";
    
    // Handle empty values for optional fields
    $paymentMethodId = !empty($data['payment_method_id']) ? sanitizeInput($data['payment_method_id'], 'int') : null;
    $savingsAccountId = !empty($data['savings_account_id']) ? sanitizeInput($data['savings_account_id'], 'int') : null;
    
    $params = [
        sanitizeInput($data['expense_id'], 'int'),
        sanitizeInput($data['month_id'], 'int'),
        sanitizeInput($data['week_id'], 'int'),
        sanitizeInput($data['actual_amount'], 'float'),
        sanitizeInput($data['date_paid']),
        sanitizeInput($data['notes']),
        $paymentMethodId,
        $savingsAccountId,
        $id
    ];
    
    return executeQuery($sql, $params);
}

/**
 * Delete actual expense
 * @param int $id
 * @return bool
 */
function deleteActualExpense($id) {
    $sql = "DELETE FROM actual_expenses WHERE id = ?";
    return executeQuery($sql, [$id]);
}

/**
 * Get all payment methods
 * @return array
 */
function getPaymentMethods() {
    $userId = getCurrentUserId();
    $sql = "SELECT * FROM payment_methods WHERE is_active = 1 AND user_id = ? ORDER BY type, name";
    return fetchAll($sql, [$userId]);
}

/**
 * Get payment method by ID
 * @param int $id
 * @return array|false
 */
function getPaymentMethodById($id) {
    $sql = "SELECT * FROM payment_methods WHERE id = ? AND is_active = 1";
    return fetchOne($sql, [$id]);
}

/**
 * Add payment method
 * @param array $data
 * @return bool
 */
function addPaymentMethod($data) {
    $sql = "INSERT INTO payment_methods (name, type, bank_name, icon, color, is_active) 
            VALUES (?, ?, ?, ?, ?, 1)";
    
    $params = [
        sanitizeInput($data['name']),
        sanitizeInput($data['type']),
        sanitizeInput($data['bank_name']),
        sanitizeInput($data['icon']),
        sanitizeInput($data['color'])
    ];
    
    return executeQuery($sql, $params);
}

/**
 * Update payment method
 * @param int $id
 * @param array $data
 * @return bool
 */
function updatePaymentMethod($id, $data) {
    $sql = "UPDATE payment_methods 
            SET name = ?, type = ?, bank_name = ?, icon = ?, color = ? 
            WHERE id = ? AND is_active = 1";
    
    $params = [
        sanitizeInput($data['name']),
        sanitizeInput($data['type']),
        sanitizeInput($data['bank_name']),
        sanitizeInput($data['icon']),
        sanitizeInput($data['color']),
        $id
    ];
    
    return executeQuery($sql, $params);
}

/**
 * Delete payment method
 * @param int $id
 * @return bool
 */
function deletePaymentMethod($id) {
    $sql = "UPDATE payment_methods SET is_active = 0 WHERE id = ?";
    return executeQuery($sql, [$id]);
}

/**
 * Get all savings accounts
 * @return array
 */
function getSavingsAccounts() {
    $userId = getCurrentUserId();
    $sql = "SELECT * FROM savings_accounts WHERE is_active = 1 AND user_id = ? ORDER BY type, name";
    return fetchAll($sql, [$userId]);
}

/**
 * Get savings account by ID
 * @param int $id
 * @return array|false
 */
function getSavingsAccountById($id) {
    $sql = "SELECT * FROM savings_accounts WHERE id = ? AND is_active = 1";
    return fetchOne($sql, [$id]);
}

/**
 * Add savings account
 * @param array $data
 * @return bool
 */
function addSavingsAccount($data) {
    $sql = "INSERT INTO savings_accounts (name, type, bank_name, account_number, current_balance, target_balance, icon, color, is_active) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, 1)";
    
    $params = [
        sanitizeInput($data['name']),
        sanitizeInput($data['type']),
        sanitizeInput($data['bank_name']),
        sanitizeInput($data['account_number']),
        sanitizeInput($data['current_balance'], 'float'),
        sanitizeInput($data['target_balance'], 'float'),
        sanitizeInput($data['icon']),
        sanitizeInput($data['color'])
    ];
    
    return executeQuery($sql, $params);
}

/**
 * Update savings account
 * @param int $id
 * @param array $data
 * @return bool
 */
function updateSavingsAccount($id, $data) {
    $sql = "UPDATE savings_accounts 
            SET name = ?, type = ?, bank_name = ?, account_number = ?, current_balance = ?, target_balance = ?, icon = ?, color = ? 
            WHERE id = ? AND is_active = 1";
    
    $params = [
        sanitizeInput($data['name']),
        sanitizeInput($data['type']),
        sanitizeInput($data['bank_name']),
        sanitizeInput($data['account_number']),
        sanitizeInput($data['current_balance'], 'float'),
        sanitizeInput($data['target_balance'], 'float'),
        sanitizeInput($data['icon']),
        sanitizeInput($data['color']),
        $id
    ];
    
    return executeQuery($sql, $params);
}

/**
 * Delete savings account
 * @param int $id
 * @return bool
 */
function deleteSavingsAccount($id) {
    $sql = "UPDATE savings_accounts SET is_active = 0 WHERE id = ?";
    return executeQuery($sql, [$id]);
}

/**
 * Get total savings across all accounts
 * @return float
 */
function getTotalSavings() {
    $sql = "SELECT SUM(current_balance) as total FROM savings_accounts WHERE is_active = 1";
    $result = fetchOne($sql);
    return $result ? $result['total'] : 0;
}

/**
 * Get savings progress percentage
 * @return float
 */
function getSavingsProgress() {
    $sql = "SELECT SUM(current_balance) as current, SUM(target_balance) as target FROM savings_accounts WHERE is_active = 1";
    $result = fetchOne($sql);
    
    if ($result && $result['target'] > 0) {
        return ($result['current'] / $result['target']) * 100;
    }
    
    return 0;
}
?>
