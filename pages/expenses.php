<?php
/**
 * Expenses Management Page
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
                $data = [
                    'category_id' => sanitizeInput($_POST['category_id'], 'int'),
                    'name' => sanitizeInput($_POST['name']),
                    'budgeted_amount' => sanitizeInput($_POST['budgeted_amount'], 'float'),
                    'schedule_type' => sanitizeInput($_POST['schedule_type']),
                    'schedule_day' => $_POST['schedule_type'] === 'specific_date' ? sanitizeInput($_POST['schedule_day'], 'int') : null,
                    'description' => sanitizeInput($_POST['description']),
                    'due_date' => !empty($_POST['due_date']) ? sanitizeInput($_POST['due_date']) : null,
                    'is_bill' => isset($_POST['is_bill']) ? 1 : 0,
                    'bill_type' => sanitizeInput($_POST['bill_type'] ?? 'other')
                ];
                
                if (addExpense($data)) {
                    showSuccess('Expense added successfully!');
                } else {
                    showError('Failed to add expense.');
                }
                redirect('expenses.php');
                break;
                
            case 'edit':
                $id = sanitizeInput($_POST['id'], 'int');
                $data = [
                    'category_id' => sanitizeInput($_POST['category_id'], 'int'),
                    'name' => sanitizeInput($_POST['name']),
                    'budgeted_amount' => sanitizeInput($_POST['budgeted_amount'], 'float'),
                    'schedule_type' => sanitizeInput($_POST['schedule_type']),
                    'schedule_day' => $_POST['schedule_type'] === 'specific_date' ? sanitizeInput($_POST['schedule_day'], 'int') : null,
                    'description' => sanitizeInput($_POST['description']),
                    'due_date' => !empty($_POST['due_date']) ? sanitizeInput($_POST['due_date']) : null,
                    'is_bill' => isset($_POST['is_bill']) ? 1 : 0,
                    'bill_type' => sanitizeInput($_POST['bill_type'] ?? 'other')
                ];
                
                if (updateExpense($id, $data)) {
                    showSuccess('Expense updated successfully!');
                } else {
                    showError('Failed to update expense.');
                }
                redirect('expenses.php');
                break;
                
            case 'delete':
                $id = sanitizeInput($_POST['id'], 'int');
                if (deleteExpense($id)) {
                    showSuccess('Expense deleted successfully!');
                } else {
                    showError('Failed to delete expense.');
                }
                redirect('expenses.php');
                break;
        }
    }
}

// Get data
$selectedMonthId = isset($_GET['month']) ? (int)$_GET['month'] : null;
$expenseCategories = getExpenseCategories();
$budgetedExpenses = getBudgetedExpenses($selectedMonthId);
// Messages are handled by header.php
$allMonths = getAllMonths();
$currentMonth = getCurrentMonth();

// Get expense for editing (if edit mode)
$editExpense = null;
if (isset($_GET['edit']) && is_numeric($_GET['edit'])) {
    $editExpense = getExpenseById($_GET['edit']);
}
?>

<?php
$pageTitle = 'Expenses Management - NR BUDGET Planner';

// Handle path for router vs direct access
if (defined('USING_ROUTER')) {
    include __DIR__ . '/../includes/header.php';
} else {
    include '../includes/header.php';
}
?>

    <div class="container mt-4">
        <!-- Header -->
        <div class="row mb-4">
            <div class="col">
                <h1 class="h3 mb-0">
                    <i class="fas fa-arrow-down me-2"></i>Expenses Management
                </h1>
                <p class="text-muted">Manage your budgeted expenses and categories</p>
            </div>
            <div class="col-auto">
                <form method="GET" class="d-flex">
                    <select name="month" class="form-select me-2" onchange="this.form.submit()">
                        <option value="">All Months</option>
                        <?php foreach ($allMonths as $month): ?>
                            <option value="<?php echo $month['id']; ?>" <?php echo ($selectedMonthId == $month['id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($month['name'] . ' ' . $month['year']); ?>
                                <?php if ($month['is_active']): ?> (Active)<?php endif; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </form>
            </div>
        </div>

        <div class="row">
            <!-- Add/Edit Form -->
            <div class="col-md-4 mb-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-plus me-2"></i>
                            <?php echo $editExpense ? 'Edit Expense' : 'Add Expense'; ?>
                        </h5>
                    </div>
                    <div class="card-body">
                        <form method="POST">
                            <input type="hidden" name="action" value="<?php echo $editExpense ? 'edit' : 'add'; ?>">
                            <?php if ($editExpense): ?>
                                <input type="hidden" name="id" value="<?php echo $editExpense['id']; ?>">
                            <?php endif; ?>
                            
                            <div class="mb-3">
                                <label for="category_id" class="form-label">Category</label>
                                <select class="form-select" id="category_id" name="category_id" required>
                                    <option value="">Select Category</option>
                                    <?php foreach ($expenseCategories as $category): ?>
                                        <option value="<?php echo $category['id']; ?>" 
                                                <?php echo ($editExpense && $editExpense['category_id'] == $category['id']) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($category['name']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            
                            <div class="mb-3">
                                <label for="name" class="form-label">Expense Name</label>
                                <input type="text" class="form-control" id="name" name="name" 
                                       value="<?php echo $editExpense ? htmlspecialchars($editExpense['name']) : ''; ?>" 
                                       required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="budgeted_amount" class="form-label">Budgeted Amount</label>
                                <div class="input-group">
                                    <span class="input-group-text">₱</span>
                                    <input type="number" class="form-control" id="budgeted_amount" name="budgeted_amount" 
                                           step="0.01" min="0" 
                                           value="<?php echo $editExpense ? $editExpense['budgeted_amount'] : ''; ?>" 
                                           required>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="schedule_type" class="form-label">Schedule Type</label>
                                <select class="form-select" id="schedule_type" name="schedule_type" required>
                                    <option value="">Select Schedule</option>
                                    <option value="monthly" <?php echo ($editExpense && $editExpense['schedule_type'] === 'monthly') ? 'selected' : ''; ?>>Monthly</option>
                                    <option value="specific_date" <?php echo ($editExpense && $editExpense['schedule_type'] === 'specific_date') ? 'selected' : ''; ?>>Specific Date</option>
                                </select>
                            </div>
                            
                            <div class="mb-3" id="schedule_day_group" style="display: none;">
                                <label for="schedule_day" class="form-label">Day of Month</label>
                                <select class="form-select" id="schedule_day" name="schedule_day">
                                    <?php for ($i = 1; $i <= 31; $i++): ?>
                                        <option value="<?php echo $i; ?>" <?php echo ($editExpense && $editExpense['schedule_day'] == $i) ? 'selected' : ''; ?>>
                                            <?php echo $i; ?><?php echo getOrdinalSuffix($i); ?>
                                        </option>
                                    <?php endfor; ?>
                                </select>
                            </div>
                            
                            <div class="mb-3">
                                <label for="description" class="form-label">Description</label>
                                <textarea class="form-control" id="description" name="description" rows="3"><?php echo $editExpense ? htmlspecialchars($editExpense['description']) : ''; ?></textarea>
                            </div>
                            
                            <!-- Bill Settings -->
                            <div class="mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="is_bill" name="is_bill" 
                                           <?php echo ($editExpense && $editExpense['is_bill']) ? 'checked' : ''; ?>>
                                    <label class="form-check-label" for="is_bill">
                                        <i class="fas fa-file-invoice-dollar me-1"></i>This is a bill or subscription
                                    </label>
                                </div>
                            </div>
                            
                            <div class="mb-3" id="bill_fields" style="display: none;">
                                <label for="bill_type" class="form-label">Bill Type</label>
                                <select class="form-select" id="bill_type" name="bill_type">
                                    <option value="utility" <?php echo ($editExpense && $editExpense['bill_type'] === 'utility') ? 'selected' : ''; ?>>Utility (Electricity, Water, etc.)</option>
                                    <option value="subscription" <?php echo ($editExpense && $editExpense['bill_type'] === 'subscription') ? 'selected' : ''; ?>>Subscription (Netflix, Spotify, etc.)</option>
                                    <option value="credit_card" <?php echo ($editExpense && $editExpense['bill_type'] === 'credit_card') ? 'selected' : ''; ?>>Credit Card</option>
                                    <option value="loan" <?php echo ($editExpense && $editExpense['bill_type'] === 'loan') ? 'selected' : ''; ?>>Loan</option>
                                    <option value="insurance" <?php echo ($editExpense && $editExpense['bill_type'] === 'insurance') ? 'selected' : ''; ?>>Insurance</option>
                                    <option value="other" <?php echo ($editExpense && $editExpense['bill_type'] === 'other') ? 'selected' : ''; ?>>Other</option>
                                </select>
                            </div>
                            
                            <div class="mb-3" id="due_date_group" style="display: none;">
                                <label for="due_date" class="form-label">Due Date</label>
                                <input type="date" class="form-control" id="due_date" name="due_date" 
                                       value="<?php echo $editExpense ? $editExpense['due_date'] : ''; ?>">
                                <div class="form-text">Set the due date for this bill</div>
                            </div>
                            
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-2"></i>
                                    <?php echo $editExpense ? 'Update Expense' : 'Add Expense'; ?>
                                </button>
                                <?php if ($editExpense): ?>
                                    <a href="expenses.php" class="btn btn-secondary">
                                        <i class="fas fa-times me-2"></i>Cancel Edit
                                    </a>
                                <?php endif; ?>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Expenses List -->
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-list me-2"></i>Budgeted Expenses
                        </h5>
                    </div>
                    <div class="card-body">
                        <?php if (empty($budgetedExpenses)): ?>
                            <div class="text-center py-4">
                                <i class="fas fa-arrow-down fa-3x text-muted mb-3"></i>
                                <p class="text-muted">No budgeted expenses added yet.</p>
                                <p class="text-muted">Add your first expense using the form on the left.</p>
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Category</th>
                                            <th>Expense</th>
                                            <th>Budgeted Amount</th>
                                            <th>Schedule</th>
                                            <th>Bill Info</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php 
                                        $currentCategory = '';
                                        $categoryTotal = 0;
                                        $grandTotal = 0;
                                        ?>
                                        <?php foreach ($budgetedExpenses as $expense): ?>
                                            <?php 
                                            // Show category total when category changes
                                            if ($currentCategory !== $expense['category_name'] && $currentCategory !== ''): ?>
                                                <tr class="table-secondary">
                                                    <td colspan="2"><strong><?php echo htmlspecialchars($currentCategory); ?> Total</strong></td>
                                                    <td><strong><?php echo formatCurrency($categoryTotal); ?></strong></td>
                                                    <td colspan="2"></td>
                                                </tr>
                                            <?php endif; ?>
                                            
                                            <?php 
                                            if ($currentCategory !== $expense['category_name']):
                                                $currentCategory = $expense['category_name'];
                                                $categoryTotal = 0;
                                            endif;
                                            $categoryTotal += $expense['budgeted_amount'];
                                            $grandTotal += $expense['budgeted_amount'];
                                            ?>
                                            
                                            <tr>
                                                <td>
                                                    <span class="badge" style="background-color: <?php echo $expense['category_color']; ?>">
                                                        <?php echo htmlspecialchars($expense['category_name']); ?>
                                                    </span>
                                                </td>
                                                <td><strong><?php echo htmlspecialchars($expense['name']); ?></strong></td>
                                                <td class="text-danger"><?php echo formatCurrency($expense['budgeted_amount']); ?></td>
                                                <td>
                                                    <?php if ($expense['schedule_type'] === 'monthly'): ?>
                                                        <span class="badge bg-info">Monthly</span>
                                                    <?php else: ?>
                                                        <span class="badge bg-warning"><?php echo $expense['schedule_day']; ?><?php echo getOrdinalSuffix($expense['schedule_day']); ?></span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <?php if ($expense['is_bill']): ?>
                                                        <div class="d-flex flex-column">
                                                            <span class="badge bg-warning text-dark">
                                                                <i class="fas fa-file-invoice-dollar me-1"></i><?php echo ucfirst($expense['bill_type']); ?>
                                                            </span>
                                                            <?php if ($expense['due_date']): ?>
                                                                <small class="text-muted mt-1">
                                                                    Due: <?php echo date('M j', strtotime($expense['due_date'])); ?>
                                                                </small>
                                                            <?php endif; ?>
                                                        </div>
                                                    <?php else: ?>
                                                        <span class="text-muted">-</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <div class="btn-group btn-group-sm" role="group">
                                                        <a href="?edit=<?php echo $expense['id']; ?>" class="btn btn-outline-primary">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                        <button type="button" class="btn btn-outline-danger" 
                                                                onclick="confirmDelete(<?php echo $expense['id']; ?>, '<?php echo htmlspecialchars($expense['name']); ?>')">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                        
                                        <!-- Show last category total -->
                                        <?php if ($currentCategory !== ''): ?>
                                            <tr class="table-secondary">
                                                <td colspan="2"><strong><?php echo htmlspecialchars($currentCategory); ?> Total</strong></td>
                                                <td><strong><?php echo formatCurrency($categoryTotal); ?></strong></td>
                                                <td colspan="3"></td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                    <tfoot>
                                        <tr class="table-danger">
                                            <td colspan="2"><strong>Total Budgeted Expenses</strong></td>
                                            <td><strong><?php echo formatCurrency($grandTotal); ?></strong></td>
                                            <td colspan="3"></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Confirm Delete</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to delete the expense "<span id="deleteName"></span>"?</p>
                    <p class="text-danger"><small>This action cannot be undone.</small></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <form method="POST" style="display: inline;">
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" name="id" id="deleteId">
                        <button type="submit" class="btn btn-danger">Delete</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

<?php 
// Handle path for router vs direct access
if (defined('USING_ROUTER')) {
    include __DIR__ . '/../includes/footer.php';
} else {
    include '../includes/footer.php';
}
?>

<script>
    // Show/hide schedule day based on schedule type
    document.getElementById('schedule_type').addEventListener('change', function() {
        const scheduleDayGroup = document.getElementById('schedule_day_group');
        if (this.value === 'specific_date') {
            scheduleDayGroup.style.display = 'block';
            document.getElementById('schedule_day').required = true;
        } else {
            scheduleDayGroup.style.display = 'none';
            document.getElementById('schedule_day').required = false;
        }
    });

    // Initialize on page load
    document.addEventListener('DOMContentLoaded', function() {
        const scheduleType = document.getElementById('schedule_type');
        if (scheduleType.value === 'specific_date') {
            document.getElementById('schedule_day_group').style.display = 'block';
            document.getElementById('schedule_day').required = true;
        }
        
        // Initialize bill fields
        const isBillCheckbox = document.getElementById('is_bill');
        if (isBillCheckbox.checked) {
            document.getElementById('bill_fields').style.display = 'block';
            document.getElementById('due_date_group').style.display = 'block';
        }
    });
    
    // Bill fields toggle
    document.getElementById('is_bill').addEventListener('change', function() {
        const billFields = document.getElementById('bill_fields');
        const dueDateGroup = document.getElementById('due_date_group');
        
        if (this.checked) {
            billFields.style.display = 'block';
            dueDateGroup.style.display = 'block';
        } else {
            billFields.style.display = 'none';
            dueDateGroup.style.display = 'none';
        }
    });

    // Delete confirmation
    function confirmDelete(id, name) {
        document.getElementById('deleteId').value = id;
        document.getElementById('deleteName').textContent = name;
        new bootstrap.Modal(document.getElementById('deleteModal')).show();
    }
</script>

<?php
/**
 * Get ordinal suffix for numbers
 * @param int $number
 * @return string
 */
function getOrdinalSuffix($number) {
    if ($number % 100 >= 11 && $number % 100 <= 13) {
        return 'th';
    }
    
    switch ($number % 10) {
        case 1: return 'st';
        case 2: return 'nd';
        case 3: return 'rd';
        default: return 'th';
    }
}
?>
