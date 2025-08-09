<?php
/**
 * Bills Management Page
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

// Define constants for header inclusion
define('INCLUDED_FROM_INDEX', true);

// Handle actions
$action = $_GET['action'] ?? $_POST['action'] ?? 'list';
$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($action === 'update_due_date') {
        $expenseId = sanitizeInput($_POST['expense_id'], 'int');
        $dueDate = sanitizeInput($_POST['due_date']);
        
        if (updateBillDueDate($expenseId, $dueDate)) {
            $success = 'Due date updated successfully!';
        } else {
            $error = 'Failed to update due date.';
        }
    } elseif ($action === 'mark_paid') {
        $expenseId = sanitizeInput($_POST['expense_id'], 'int');
        $amount = sanitizeInput($_POST['amount'], 'float');
        $date = sanitizeInput($_POST['date_paid']);
        $notes = sanitizeInput($_POST['notes']);
        $paymentMethodId = !empty($_POST['payment_method_id']) ? sanitizeInput($_POST['payment_method_id'], 'int') : null;
        
        // Debug information
        error_log("Mark as paid attempt - Expense ID: $expenseId, Amount: $amount, Date: $date");
        
        if (markBillAsPaid($expenseId, $amount, $date, $notes, $paymentMethodId)) {
            $success = 'Bill marked as paid successfully!';
        } else {
            $error = 'Failed to mark bill as paid. Please ensure you have an active monthly budget.';
        }
    }
}

// Get data
$bills = getBills();
$upcomingBills = getUpcomingBills();
$overdueBills = getOverdueBills();
$billStats = getBillStatistics();
$paymentMethods = getPaymentMethods();

// Set page title
$pageTitle = 'Bills & Subscriptions - NR BUDGET Planner';

// Include header component
if (defined('USING_ROUTER')) {
    include __DIR__ . '/../includes/header.php';
} else {
    include '../includes/header.php';
}
?>

<style>
    .bill-card {
        transition: transform 0.2s;
        border-left: 4px solid #007bff;
    }
    .bill-card:hover {
        transform: translateY(-2px);
    }
    .bill-card.overdue {
        border-left-color: #dc3545;
        background-color: #fff5f5;
    }
    .bill-card.upcoming {
        border-left-color: #ffc107;
        background-color: #fffbf0;
    }
    .bill-card.paid {
        border-left-color: #28a745;
        background-color: #f8fff9;
    }
    .due-date {
        font-weight: bold;
    }
    .due-date.overdue {
        color: #dc3545;
    }
    .due-date.upcoming {
        color: #ffc107;
    }
    .due-date.paid {
        color: #28a745;
    }
    .bill-type-badge {
        font-size: 0.75rem;
        padding: 0.25rem 0.5rem;
    }
</style>

<div class="row mb-4">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center">
            <h1 class="h3 mb-0">
                <i class="fas fa-file-invoice-dollar me-2"></i>Bills & Subscriptions
            </h1>
            <a href="<?php echo isInPagesDirectory() ? 'expenses?action=add&bill=1' : '../expenses?action=add&bill=1'; ?>" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>Add New Bill
            </a>
        </div>
    </div>
</div>

<!-- Bill Statistics -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6 class="card-title">Total Bills</h6>
                        <h3 class="mb-0"><?php echo $billStats['total_bills'] ?? 0; ?></h3>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-file-invoice fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-warning text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6 class="card-title">Upcoming</h6>
                        <h3 class="mb-0"><?php echo $billStats['upcoming_bills'] ?? 0; ?></h3>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-clock fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-danger text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6 class="card-title">Overdue</h6>
                        <h3 class="mb-0"><?php echo $billStats['overdue_bills'] ?? 0; ?></h3>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-exclamation-triangle fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-success text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6 class="card-title">Total Amount</h6>
                        <h3 class="mb-0"><?php echo formatCurrency($billStats['total_amount'] ?? 0); ?></h3>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-money-bill-wave fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Overdue Bills -->
<?php if (!empty($overdueBills)): ?>
<div class="row mb-4">
    <div class="col-12">
        <div class="card border-danger">
            <div class="card-header bg-danger text-white">
                <h5 class="mb-0">
                    <i class="fas fa-exclamation-triangle me-2"></i>Overdue Bills
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <?php foreach ($overdueBills as $bill): ?>
                    <div class="col-md-6 col-lg-4 mb-3">
                        <div class="card bill-card overdue">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <h6 class="card-title mb-0"><?php echo htmlspecialchars($bill['name']); ?></h6>
                                    <span class="badge bill-type-badge bg-<?php echo getBillTypeColor($bill['bill_type']); ?>">
                                        <?php echo ucfirst(str_replace('_', ' ', $bill['bill_type'])); ?>
                                    </span>
                                </div>
                                <p class="card-text">
                                    <strong>Amount:</strong> <?php echo formatCurrency($bill['budgeted_amount']); ?><br>
                                    <strong>Due Date:</strong> 
                                    <span class="due-date overdue">
                                        <?php echo date('M j, Y', strtotime($bill['due_date'])); ?>
                                        (<?php echo $bill['days_overdue']; ?> days overdue)
                                    </span><br>
                                    <strong>Category:</strong> <?php echo htmlspecialchars($bill['category_name']); ?>
                                </p>
                                <div class="btn-group btn-group-sm w-100">
                                    <button class="btn btn-outline-primary" onclick="editDueDate(<?php echo $bill['id']; ?>, '<?php echo $bill['due_date']; ?>')">
                                        <i class="fas fa-edit"></i> Update Due Date
                                    </button>
                                    <button class="btn btn-outline-success" onclick="markAsPaid(<?php echo $bill['id']; ?>, '<?php echo $bill['name']; ?>', <?php echo $bill['budgeted_amount']; ?>)">
                                        <i class="fas fa-check"></i> Mark Paid
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- Upcoming Bills -->
<?php if (!empty($upcomingBills)): ?>
<div class="row mb-4">
    <div class="col-12">
        <div class="card border-warning">
            <div class="card-header bg-warning text-white">
                <h5 class="mb-0">
                    <i class="fas fa-clock me-2"></i>Upcoming Bills (Next 7 Days)
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <?php foreach ($upcomingBills as $bill): ?>
                    <div class="col-md-6 col-lg-4 mb-3">
                        <div class="card bill-card upcoming">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <h6 class="card-title mb-0"><?php echo htmlspecialchars($bill['name']); ?></h6>
                                    <span class="badge bill-type-badge bg-<?php echo getBillTypeColor($bill['bill_type']); ?>">
                                        <?php echo ucfirst(str_replace('_', ' ', $bill['bill_type'])); ?>
                                    </span>
                                </div>
                                <p class="card-text">
                                    <strong>Amount:</strong> <?php echo formatCurrency($bill['budgeted_amount']); ?><br>
                                    <strong>Due Date:</strong> 
                                    <span class="due-date upcoming">
                                        <?php echo date('M j, Y', strtotime($bill['due_date'])); ?>
                                        (in <?php echo $bill['days_until_due']; ?> days)
                                    </span><br>
                                    <strong>Category:</strong> <?php echo htmlspecialchars($bill['category_name']); ?>
                                </p>
                                <div class="btn-group btn-group-sm w-100">
                                    <button class="btn btn-outline-primary" onclick="editDueDate(<?php echo $bill['id']; ?>, '<?php echo $bill['due_date']; ?>')">
                                        <i class="fas fa-edit"></i> Update Due Date
                                    </button>
                                    <button class="btn btn-outline-success" onclick="markAsPaid(<?php echo $bill['id']; ?>, '<?php echo $bill['name']; ?>', <?php echo $bill['budgeted_amount']; ?>)">
                                        <i class="fas fa-check"></i> Mark Paid
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- All Bills -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-list me-2"></i>All Bills & Subscriptions
                </h5>
            </div>
            <div class="card-body">
                <?php if (empty($bills)): ?>
                    <div class="text-center py-4">
                        <i class="fas fa-file-invoice fa-3x text-muted mb-3"></i>
                        <h5>No bills found</h5>
                        <p class="text-muted">Add your first bill or subscription to start tracking due dates.</p>
                        <a href="<?php echo isInPagesDirectory() ? 'expenses?action=add&bill=1' : '../expenses?action=add&bill=1'; ?>" class="btn btn-primary">
                            <i class="fas fa-plus me-2"></i>Add New Bill
                        </a>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Bill Name</th>
                                    <th>Type</th>
                                    <th>Category</th>
                                    <th>Amount</th>
                                    <th>Due Date</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($bills as $bill): ?>
                                <tr>
                                    <td>
                                        <strong><?php echo htmlspecialchars($bill['name']); ?></strong>
                                    </td>
                                    <td>
                                        <span class="badge bg-<?php echo getBillTypeColor($bill['bill_type']); ?>">
                                            <?php echo ucfirst(str_replace('_', ' ', $bill['bill_type'])); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge" style="background-color: <?php echo $bill['category_color']; ?>">
                                            <?php echo htmlspecialchars($bill['category_name']); ?>
                                        </span>
                                    </td>
                                    <td><?php echo formatCurrency($bill['budgeted_amount']); ?></td>
                                    <td>
                                        <?php
                                        $dueDate = strtotime($bill['due_date']);
                                        $today = time();
                                        $daysDiff = ($dueDate - $today) / (60 * 60 * 24);
                                        
                                        if ($daysDiff < 0) {
                                            echo '<span class="text-danger"><i class="fas fa-exclamation-triangle me-1"></i>' . date('M j, Y', $dueDate) . '</span>';
                                        } elseif ($daysDiff <= 7) {
                                            echo '<span class="text-warning"><i class="fas fa-clock me-1"></i>' . date('M j, Y', $dueDate) . '</span>';
                                        } else {
                                            echo '<span class="text-success"><i class="fas fa-calendar me-1"></i>' . date('M j, Y', $dueDate) . '</span>';
                                        }
                                        ?>
                                    </td>
                                    <td>
                                        <?php
                                        if ($daysDiff < 0) {
                                            echo '<span class="badge bg-danger">Overdue</span>';
                                        } elseif ($daysDiff <= 7) {
                                            echo '<span class="badge bg-warning">Upcoming</span>';
                                        } else {
                                            echo '<span class="badge bg-success">On Time</span>';
                                        }
                                        ?>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <button class="btn btn-outline-primary" onclick="editDueDate(<?php echo $bill['id']; ?>, '<?php echo $bill['due_date']; ?>')">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button class="btn btn-outline-success" onclick="markAsPaid(<?php echo $bill['id']; ?>, '<?php echo $bill['name']; ?>', <?php echo $bill['budgeted_amount']; ?>)">
                                                <i class="fas fa-check"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Update Due Date Modal -->
<div class="modal fade" id="updateDueDateModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Update Due Date</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <input type="hidden" name="action" value="update_due_date">
                <div class="modal-body">
                    <input type="hidden" id="expense_id" name="expense_id">
                    <div class="mb-3">
                        <label for="due_date" class="form-label">New Due Date</label>
                        <input type="date" class="form-control" id="due_date" name="due_date" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Due Date</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Mark as Paid Modal -->
<div class="modal fade" id="markAsPaidModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Mark Bill as Paid</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <input type="hidden" name="action" value="mark_paid">
                <div class="modal-body">
                    <input type="hidden" id="paid_expense_id" name="expense_id">
                    <div class="mb-3">
                        <label class="form-label">Bill Name</label>
                        <input type="text" class="form-control" id="bill_name" readonly>
                    </div>
                    <div class="mb-3">
                        <label for="paid_amount" class="form-label">Amount Paid</label>
                        <input type="number" step="0.01" class="form-control" id="paid_amount" name="amount" required>
                    </div>
                    <div class="mb-3">
                        <label for="date_paid" class="form-label">Date Paid</label>
                        <input type="date" class="form-control" id="date_paid" name="date_paid" required>
                    </div>
                    <div class="mb-3">
                        <label for="payment_method_id" class="form-label">Payment Method</label>
                        <select class="form-select" id="payment_method_id" name="payment_method_id">
                            <option value="">Select Payment Method</option>
                            <?php foreach ($paymentMethods as $method): ?>
                                <option value="<?php echo $method['id']; ?>">
                                    <?php echo htmlspecialchars($method['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="notes" class="form-label">Notes</label>
                        <textarea class="form-control" id="notes" name="notes" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">Mark as Paid</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function editDueDate(expenseId, currentDueDate) {
    document.getElementById('expense_id').value = expenseId;
    document.getElementById('due_date').value = currentDueDate;
    new bootstrap.Modal(document.getElementById('updateDueDateModal')).show();
}

function markAsPaid(expenseId, billName, amount) {
    document.getElementById('paid_expense_id').value = expenseId;
    document.getElementById('bill_name').value = billName;
    document.getElementById('paid_amount').value = amount;
    document.getElementById('date_paid').value = new Date().toISOString().split('T')[0];
    new bootstrap.Modal(document.getElementById('markAsPaidModal')).show();
}
</script>

<?php
// Helper function for bill type colors
function getBillTypeColor($billType) {
    switch ($billType) {
        case 'utility': return 'info';
        case 'subscription': return 'primary';
        case 'credit_card': return 'warning';
        case 'loan': return 'danger';
        case 'insurance': return 'success';
        default: return 'secondary';
    }
}

// Include footer component
if (defined('USING_ROUTER')) {
    include __DIR__ . '/../includes/footer.php';
} else {
    include '../includes/footer.php';
}
?>
