<?php
/**
 * Actual Expenses Tracking Page
 * Budget Planner Application
 */

session_start();

// Handle path for router vs direct access
if (defined('USING_ROUTER')) {
    require_once __DIR__ . '/../includes/functions.php';
} else {
    require_once '../includes/functions.php';
}

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'add':
                // Determine month and week from the date
                $datePaid = sanitizeInput($_POST['date_paid']);
                $dateObj = new DateTime($datePaid);
                $year = $dateObj->format('Y');
                $month = $dateObj->format('n');
                $day = $dateObj->format('j');
                
                // Get month ID
                $monthData = fetchOne("SELECT id FROM months WHERE year = ? AND month = ?", [$year, $month]);
                $monthId = $monthData ? $monthData['id'] : getCurrentMonth()['id'];
                
                // Get week ID
                $weekData = fetchOne("SELECT id FROM weeks WHERE year = ? AND month = ? AND ? BETWEEN DAY(start_date) AND DAY(end_date)", [$year, $month, $day]);
                $weekId = $weekData ? $weekData['id'] : getCurrentWeek()['id'];
                
                $data = [
                    'expense_id' => sanitizeInput($_POST['expense_id'], 'int'),
                    'month_id' => $monthId,
                    'week_id' => $weekId,
                    'actual_amount' => sanitizeInput($_POST['actual_amount'], 'float'),
                    'date_paid' => $datePaid,
                    'notes' => sanitizeInput($_POST['notes']),
                    'payment_method_id' => !empty($_POST['payment_method_id']) ? sanitizeInput($_POST['payment_method_id'], 'int') : null,
                    'savings_account_id' => !empty($_POST['savings_account_id']) ? sanitizeInput($_POST['savings_account_id'], 'int') : null
                ];
                
                if (addActualExpense($data)) {
                    showSuccess('Actual expense recorded successfully!');
                } else {
                    showError('Failed to record actual expense.');
                }
                redirect('actual-expenses.php');
                break;
                
            case 'update':
                // Determine month and week from the date
                $datePaid = sanitizeInput($_POST['date_paid']);
                $dateObj = new DateTime($datePaid);
                $year = $dateObj->format('Y');
                $month = $dateObj->format('n');
                $day = $dateObj->format('j');
                
                // Get month ID
                $monthData = fetchOne("SELECT id FROM months WHERE year = ? AND month = ?", [$year, $month]);
                $monthId = $monthData ? $monthData['id'] : null;
                
                // Get week ID
                $weekData = fetchOne("SELECT id FROM weeks WHERE year = ? AND month = ? AND ? BETWEEN DAY(start_date) AND DAY(end_date)", [$year, $month, $day]);
                $weekId = $weekData ? $weekData['id'] : null;
                
                $data = [
                    'expense_id' => sanitizeInput($_POST['expense_id'], 'int'),
                    'month_id' => $monthId,
                    'week_id' => $weekId,
                    'actual_amount' => sanitizeInput($_POST['actual_amount'], 'float'),
                    'date_paid' => $datePaid,
                    'notes' => sanitizeInput($_POST['notes']),
                    'payment_method_id' => !empty($_POST['payment_method_id']) ? sanitizeInput($_POST['payment_method_id'], 'int') : null,
                    'savings_account_id' => !empty($_POST['savings_account_id']) ? sanitizeInput($_POST['savings_account_id'], 'int') : null
                ];
                
                if (updateActualExpense($_POST['id'], $data)) {
                    showSuccess('Actual expense updated successfully!');
                } else {
                    showError('Failed to update actual expense.');
                }
                redirect('actual-expenses.php');
                break;
                
            case 'delete':
                if (deleteActualExpense($_POST['id'])) {
                    showSuccess('Actual expense deleted successfully!');
                } else {
                    showError('Failed to delete actual expense.');
                }
                redirect('actual-expenses.php');
                break;
        }
    }
}

// Get data
$budgetedExpenses = getBudgetedExpenses();
$actualExpenses = getActualExpenses();
$currentMonth = getCurrentMonth();
// Messages are handled by header.php
$allMonths = getAllMonths();
$allWeeks = getAllWeeks();
$paymentMethods = getPaymentMethods();
$savingsAccounts = getSavingsAccounts();

// Get actual expense for editing (if edit mode)
$editActualExpense = null;
if (isset($_GET['edit']) && is_numeric($_GET['edit'])) {
    $editActualExpense = getActualExpenseById($_GET['edit']);
}

// Group expenses by category for better organization
$expensesByCategory = [];
foreach ($budgetedExpenses as $expense) {
    $categoryName = $expense['category_name'];
    if (!isset($expensesByCategory[$categoryName])) {
        $expensesByCategory[$categoryName] = [];
    }
    $expensesByCategory[$categoryName][] = $expense;
}
?>
<?php
$pageTitle = 'Actual Expenses - NR BUDGET Planner';

// Handle path for router vs direct access
if (defined('USING_ROUTER')) {
    include __DIR__ . '/../includes/header.php';
} else {
    include '../includes/header.php';
}
?>

<style>
    .expense-option {
        padding: 8px 12px;
        border-bottom: 1px solid #eee;
    }
    .expense-option:hover {
        background-color: #f8f9fa;
    }
    .expense-option:last-child {
        border-bottom: none;
    }
    .category-header {
        background-color: #f8f9fa;
        padding: 8px 12px;
        font-weight: bold;
        color: #495057;
    }
</style>

    <div class="container mt-4">
        <!-- Header -->
        <div class="row mb-4">
            <div class="col">
                <h1 class="h3 mb-0">
                    <i class="fas fa-receipt me-2"></i>Actual Expenses Tracking
                </h1>
                <p class="text-muted">Record your actual spending for <?php echo $currentMonth ? $currentMonth['name'] : 'current month'; ?></p>
            </div>
        </div>

        <div class="row">
            <!-- Add Actual Expense Form -->
            <div class="col-md-4 mb-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-plus me-2"></i>Record Actual Expense
                        </h5>
                    </div>
                    <div class="card-body">
                        <form method="POST">
                            <input type="hidden" name="action" value="add">
                            
                            <div class="mb-3">
                                <label for="expense_id" class="form-label">Budgeted Expense</label>
                                <select class="form-select" id="expense_id" name="expense_id" required>
                                    <option value="">Select Budgeted Expense</option>
                                    <?php foreach ($expensesByCategory as $categoryName => $expenses): ?>
                                        <optgroup label="<?php echo htmlspecialchars($categoryName); ?>">
                                            <?php foreach ($expenses as $expense): ?>
                                                <option value="<?php echo $expense['id']; ?>">
                                                    <?php echo htmlspecialchars($expense['name']); ?> 
                                                    (<?php echo formatCurrency($expense['budgeted_amount']); ?>)
                                                </option>
                                            <?php endforeach; ?>
                                        </optgroup>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            
                            <div class="mb-3">
                                <label for="actual_amount" class="form-label">Actual Amount Paid</label>
                                <div class="input-group">
                                    <span class="input-group-text">₱</span>
                                    <input type="number" class="form-control" id="actual_amount" name="actual_amount" 
                                           step="0.01" min="0" required>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="date_paid" class="form-label">Date Paid</label>
                                <input type="date" class="form-control" id="date_paid" name="date_paid" 
                                       value="<?php echo date('Y-m-d'); ?>" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="payment_method_id" class="form-label">Payment Method</label>
                                <select class="form-select" id="payment_method_id" name="payment_method_id">
                                    <option value="">Select Payment Method (Optional)</option>
                                    <optgroup label="Cash">
                                        <?php foreach ($paymentMethods as $method): ?>
                                            <?php if ($method['type'] === 'cash'): ?>
                                                <option value="<?php echo $method['id']; ?>" selected>
                                                    <i class="<?php echo $method['icon']; ?>"></i>
                                                    <?php echo htmlspecialchars($method['name']); ?>
                                                </option>
                                            <?php endif; ?>
                                        <?php endforeach; ?>
                                    </optgroup>
                                    <optgroup label="Credit Cards">
                                        <?php foreach ($paymentMethods as $method): ?>
                                            <?php if ($method['type'] === 'credit_card'): ?>
                                                <option value="<?php echo $method['id']; ?>">
                                                    <i class="<?php echo $method['icon']; ?>"></i>
                                                    <?php echo htmlspecialchars($method['name']); ?>
                                                </option>
                                            <?php endif; ?>
                                        <?php endforeach; ?>
                                    </optgroup>
                                    <optgroup label="Online">
                                        <?php foreach ($paymentMethods as $method): ?>
                                            <?php if ($method['type'] === 'online'): ?>
                                                <option value="<?php echo $method['id']; ?>">
                                                    <i class="<?php echo $method['icon']; ?>"></i>
                                                    <?php echo htmlspecialchars($method['name']); ?>
                                                </option>
                                            <?php endif; ?>
                                        <?php endforeach; ?>
                                    </optgroup>
                                </select>
                            </div>
                            

                            
                            <div class="mb-3">
                                <label for="notes" class="form-label">Notes</label>
                                <textarea class="form-control" id="notes" name="notes" rows="3" 
                                          placeholder="Optional notes about this expense..."></textarea>
                            </div>
                            
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-2"></i>Record Expense
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Summary Card -->
                <div class="card mt-3">
                    <div class="card-header">
                        <h6 class="mb-0">
                            <i class="fas fa-chart-bar me-2"></i>Summary
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="row text-center">
                            <div class="col-6">
                                <h6 class="text-muted">Budgeted</h6>
                                <h5 class="text-danger"><?php echo formatCurrency(getTotalBudgetedExpenses()); ?></h5>
                            </div>
                            <div class="col-6">
                                <h6 class="text-muted">Actual</h6>
                                <h5 class="text-warning"><?php echo formatCurrency(getTotalActualExpenses()); ?></h5>
                            </div>
                        </div>
                        <hr>
                        <div class="text-center">
                            <h6 class="text-muted">Remaining</h6>
                            <h5 class="<?php echo (getTotalBudgetedExpenses() - getTotalActualExpenses()) >= 0 ? 'text-success' : 'text-danger'; ?>">
                                <?php echo formatCurrency(getTotalBudgetedExpenses() - getTotalActualExpenses()); ?>
                            </h5>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Actual Expenses List -->
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-list me-2"></i>Recorded Actual Expenses
                        </h5>
                    </div>
                    <div class="card-body">
                        <?php if (empty($actualExpenses)): ?>
                            <div class="text-center py-4">
                                <i class="fas fa-receipt fa-3x text-muted mb-3"></i>
                                <p class="text-muted">No actual expenses recorded yet for <?php echo $currentMonth ? $currentMonth['name'] : 'current month'; ?>.</p>
                                <p class="text-muted">Start recording your expenses using the form on the left.</p>
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>Category</th>
                                            <th>Expense</th>
                                            <th>Budgeted</th>
                                            <th>Actual</th>
                                            <th>Payment Method</th>
                                            <th>Difference</th>
                                            <th>Notes</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php 
                                        $totalBudgeted = 0;
                                        $totalActual = 0;
                                        ?>
                                        <?php foreach ($actualExpenses as $actual): ?>
                                            <?php 
                                            $budgeted = $actual['budgeted_amount'];
                                            $actualAmount = $actual['actual_amount'];
                                            $difference = $budgeted - $actualAmount;
                                            $totalBudgeted += $budgeted;
                                            $totalActual += $actualAmount;
                                            ?>
                                            <tr>
                                                <td><?php echo date('M d, Y', strtotime($actual['date_paid'])); ?></td>
                                                <td>
                                                    <span class="badge" style="background-color: <?php echo $actual['category_color']; ?>">
                                                        <?php echo htmlspecialchars($actual['category_name']); ?>
                                                    </span>
                                                </td>
                                                <td><strong><?php echo htmlspecialchars($actual['expense_name']); ?></strong></td>
                                                <td><?php echo formatCurrency($budgeted); ?></td>
                                                <td class="text-danger"><?php echo formatCurrency($actualAmount); ?></td>
                                                <td>
                                                    <?php if ($actual['payment_method_name']): ?>
                                                        <span class="badge" style="background-color: <?php echo $actual['payment_method_color']; ?>">
                                                            <i class="<?php echo $actual['payment_method_icon']; ?> me-1"></i>
                                                            <?php echo htmlspecialchars($actual['payment_method_name']); ?>
                                                        </span>
                                                    <?php else: ?>
                                                        <span class="text-muted">-</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td class="<?php echo $difference >= 0 ? 'text-success' : 'text-danger'; ?>">
                                                    <?php echo formatCurrency(abs($difference)); ?>
                                                    <?php if ($difference < 0): ?>
                                                        <i class="fas fa-arrow-up ms-1"></i>
                                                    <?php else: ?>
                                                        <i class="fas fa-arrow-down ms-1"></i>
                                                    <?php endif; ?>
                                                </td>
                                                <td><?php echo htmlspecialchars($actual['notes']); ?></td>
                                                <td>
                                                    <div class="btn-group btn-group-sm">
                                                        <a href="?edit=<?php echo $actual['id']; ?>" class="btn btn-outline-primary btn-sm" title="Edit">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                        <form method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this expense?')">
                                                            <input type="hidden" name="action" value="delete">
                                                            <input type="hidden" name="id" value="<?php echo $actual['id']; ?>">
                                                            <button type="submit" class="btn btn-outline-danger btn-sm" title="Delete">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        </form>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                    <tfoot>
                                        <tr class="table-secondary">
                                            <td colspan="3"><strong>Totals</strong></td>
                                            <td><strong><?php echo formatCurrency($totalBudgeted); ?></strong></td>
                                            <td><strong><?php echo formatCurrency($totalActual); ?></strong></td>
                                            <td></td>
                                            <td class="<?php echo ($totalBudgeted - $totalActual) >= 0 ? 'text-success' : 'text-danger'; ?>">
                                                <strong><?php echo formatCurrency(abs($totalBudgeted - $totalActual)); ?></strong>
                                            </td>
                                            <td></td>
                                            <td></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Budget vs Actual Chart -->
                <?php if (!empty($actualExpenses)): ?>
                <div class="card mt-3">
                    <div class="card-header">
                        <h6 class="mb-0">
                            <i class="fas fa-chart-pie me-2"></i>Budget vs Actual Overview
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h6 class="text-muted">Budget Utilization</h6>
                                <?php 
                                $utilizationPercentage = ($totalActual / $totalBudgeted) * 100;
                                $progressClass = $utilizationPercentage > 100 ? 'bg-danger' : 'bg-success';
                                ?>
                                <div class="progress mb-2" style="height: 20px;">
                                    <div class="progress-bar <?php echo $progressClass; ?>" 
                                         style="width: <?php echo min($utilizationPercentage, 100); ?>%">
                                        <?php echo number_format($utilizationPercentage, 1); ?>%
                                    </div>
                                </div>
                                <small class="text-muted">
                                    <?php echo formatCurrency($totalActual); ?> of <?php echo formatCurrency($totalBudgeted); ?> used
                                </small>
                            </div>
                            <div class="col-md-6">
                                <h6 class="text-muted">Status</h6>
                                <?php if ($totalActual <= $totalBudgeted): ?>
                                    <div class="text-success">
                                        <i class="fas fa-check-circle me-2"></i>Within Budget
                                    </div>
                                    <small class="text-muted">
                                        <?php echo formatCurrency($totalBudgeted - $totalActual); ?> remaining
                                    </small>
                                <?php else: ?>
                                    <div class="text-danger">
                                        <i class="fas fa-exclamation-triangle me-2"></i>Over Budget
                                    </div>
                                    <small class="text-muted">
                                        <?php echo formatCurrency($totalActual - $totalBudgeted); ?> over budget
                                    </small>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Edit Actual Expense Modal -->
    <?php if ($editActualExpense): ?>
    <div class="modal fade show" id="editActualExpenseModal" tabindex="-1" style="display: block;">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-edit text-primary me-2"></i>Edit Actual Expense
                    </h5>
                    <a href="actual-expenses.php" class="btn-close"></a>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <input type="hidden" name="action" value="update">
                        <input type="hidden" name="id" value="<?php echo $editActualExpense['id']; ?>">
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="edit_expense_id" class="form-label">Expense</label>
                                    <select class="form-select" id="edit_expense_id" name="expense_id" required>
                                        <option value="">Select Expense</option>
                                        <?php foreach ($budgetedExpenses as $expense): ?>
                                            <option value="<?php echo $expense['id']; ?>" 
                                                    <?php echo $expense['id'] == $editActualExpense['expense_id'] ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($expense['name']); ?> (₱<?php echo number_format($expense['budgeted_amount'], 2); ?>)
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="edit_actual_amount" class="form-label">Actual Amount</label>
                                    <div class="input-group">
                                        <span class="input-group-text">₱</span>
                                        <input type="number" class="form-control" id="edit_actual_amount" name="actual_amount" 
                                               step="0.01" min="0" value="<?php echo $editActualExpense['actual_amount']; ?>" required>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="edit_date_paid" class="form-label">Date Paid</label>
                                    <input type="date" class="form-control" id="edit_date_paid" name="date_paid" 
                                           value="<?php echo $editActualExpense['date_paid']; ?>" required>
                                    <small class="text-muted">Month and week will be automatically determined from this date.</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="edit_notes" class="form-label">Notes</label>
                                    <textarea class="form-control" id="edit_notes" name="notes" rows="3"><?php echo htmlspecialchars($editActualExpense['notes']); ?></textarea>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="edit_payment_method_id" class="form-label">Payment Method</label>
                                    <select class="form-select" id="edit_payment_method_id" name="payment_method_id">
                                        <option value="">Select Payment Method (Optional)</option>
                                        <optgroup label="Cash">
                                            <?php foreach ($paymentMethods as $method): ?>
                                                <?php if ($method['type'] === 'cash'): ?>
                                                    <option value="<?php echo $method['id']; ?>" 
                                                            <?php echo $method['id'] == $editActualExpense['payment_method_id'] ? 'selected' : ''; ?>>
                                                        <?php echo htmlspecialchars($method['name']); ?>
                                                    </option>
                                                <?php endif; ?>
                                            <?php endforeach; ?>
                                        </optgroup>
                                        <optgroup label="Credit Cards">
                                            <?php foreach ($paymentMethods as $method): ?>
                                                <?php if ($method['type'] === 'credit_card'): ?>
                                                    <option value="<?php echo $method['id']; ?>" 
                                                            <?php echo $method['id'] == $editActualExpense['payment_method_id'] ? 'selected' : ''; ?>>
                                                        <?php echo htmlspecialchars($method['name']); ?>
                                                    </option>
                                                <?php endif; ?>
                                            <?php endforeach; ?>
                                        </optgroup>
                                        <optgroup label="Online">
                                            <?php foreach ($paymentMethods as $method): ?>
                                                <?php if ($method['type'] === 'online'): ?>
                                                    <option value="<?php echo $method['id']; ?>" 
                                                            <?php echo $method['id'] == $editActualExpense['payment_method_id'] ? 'selected' : ''; ?>>
                                                        <?php echo htmlspecialchars($method['name']); ?>
                                                    </option>
                                                <?php endif; ?>
                                            <?php endforeach; ?>
                                        </optgroup>
                                    </select>
                                </div>
                            </div>

                        </div>
                        
                        <div class="mb-3">
                            <label for="edit_notes" class="form-label">Notes</label>
                            <textarea class="form-control" id="edit_notes" name="notes" rows="3"><?php echo htmlspecialchars($editActualExpense['notes']); ?></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <a href="actual-expenses.php" class="btn btn-secondary">Cancel</a>
                        <button type="submit" class="btn btn-primary">Update Expense</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="modal-backdrop fade show"></div>
    <?php endif; ?>

<?php 
// Handle path for router vs direct access
if (defined('USING_ROUTER')) {
    include __DIR__ . '/../includes/footer.php';
} else {
    include '../includes/footer.php';
}
?>

<script>
    // Auto-fill budgeted amount when expense is selected
    document.getElementById('expense_id').addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        if (selectedOption.value) {
            // Extract budgeted amount from option text (format: "Name (₱Amount)")
            const text = selectedOption.text;
            const match = text.match(/₱([\d,]+\.?\d*)/);
            if (match) {
                const budgetedAmount = match[1].replace(',', '');
                document.getElementById('actual_amount').placeholder = `Budgeted: ₱${budgetedAmount}`;
            }
        } else {
            document.getElementById('actual_amount').placeholder = '';
        }
    });
</script>
