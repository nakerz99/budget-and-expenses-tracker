<?php
/**
 * Income Management Page
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
                    'name' => sanitizeInput($_POST['name']),
                    'amount' => sanitizeInput($_POST['amount'], 'float'),
                    'schedule_type' => sanitizeInput($_POST['schedule_type']),
                    'schedule_day' => $_POST['schedule_type'] === 'specific_date' ? sanitizeInput($_POST['schedule_day'], 'int') : null,
                    'description' => sanitizeInput($_POST['description'])
                ];
                
                if (addIncomeSource($data)) {
                    showSuccess('Income source added successfully!');
                } else {
                    showError('Failed to add income source.');
                }
                redirect('income.php');
                break;
                
            case 'edit':
                $id = sanitizeInput($_POST['id'], 'int');
                $data = [
                    'name' => sanitizeInput($_POST['name']),
                    'amount' => sanitizeInput($_POST['amount'], 'float'),
                    'schedule_type' => sanitizeInput($_POST['schedule_type']),
                    'schedule_day' => $_POST['schedule_type'] === 'specific_date' ? sanitizeInput($_POST['schedule_day'], 'int') : null,
                    'description' => sanitizeInput($_POST['description'])
                ];
                
                if (updateIncomeSource($id, $data)) {
                    showSuccess('Income source updated successfully!');
                } else {
                    showError('Failed to update income source.');
                }
                redirect('income.php');
                break;
                
            case 'delete':
                $id = sanitizeInput($_POST['id'], 'int');
                if (deleteIncomeSource($id)) {
                    showSuccess('Income source deleted successfully!');
                } else {
                    showError('Failed to delete income source.');
                }
                redirect('income.php');
                break;
        }
    }
}

// Get income sources
$selectedMonthId = isset($_GET['month']) ? (int)$_GET['month'] : null;
$incomeSources = getIncomeSources($selectedMonthId);
$messages = getMessages();
$allMonths = getAllMonths();
$currentMonth = getCurrentMonth();

// Get income for editing (if edit mode)
$editIncome = null;
if (isset($_GET['edit']) && is_numeric($_GET['edit'])) {
    $editIncome = getIncomeSourceById($_GET['edit']);
}
?>

<?php
$pageTitle = 'Income Management - NR BUDGET Planner';

// Handle path for router vs direct access
if (defined('USING_ROUTER')) {
    include __DIR__ . '/../includes/header.php';
} else {
    include '../includes/header.php';
}
?>

    <div class="container mt-4">
        <!-- Messages -->
        <?php if ($messages['success']): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i><?php echo $messages['success']; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        
        <?php if ($messages['error']): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i><?php echo $messages['error']; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <!-- Header -->
        <div class="row mb-4">
            <div class="col">
                <h1 class="h3 mb-0">
                    <i class="fas fa-arrow-up me-2"></i>Income Management
                </h1>
                <p class="text-muted">Manage your income sources and schedules</p>
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
                            <?php echo $editIncome ? 'Edit Income Source' : 'Add Income Source'; ?>
                        </h5>
                    </div>
                    <div class="card-body">
                        <form method="POST">
                            <input type="hidden" name="action" value="<?php echo $editIncome ? 'edit' : 'add'; ?>">
                            <?php if ($editIncome): ?>
                                <input type="hidden" name="id" value="<?php echo $editIncome['id']; ?>">
                            <?php endif; ?>
                            
                            <div class="mb-3">
                                <label for="name" class="form-label">Income Source Name</label>
                                <input type="text" class="form-control" id="name" name="name" 
                                       value="<?php echo $editIncome ? htmlspecialchars($editIncome['name']) : ''; ?>" 
                                       required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="amount" class="form-label">Amount</label>
                                <div class="input-group">
                                    <span class="input-group-text">₱</span>
                                    <input type="number" class="form-control" id="amount" name="amount" 
                                           step="0.01" min="0" 
                                           value="<?php echo $editIncome ? $editIncome['amount'] : ''; ?>" 
                                           required>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="schedule_type" class="form-label">Schedule Type</label>
                                <select class="form-select" id="schedule_type" name="schedule_type" required>
                                    <option value="">Select Schedule</option>
                                    <option value="monthly" <?php echo ($editIncome && $editIncome['schedule_type'] === 'monthly') ? 'selected' : ''; ?>>Monthly</option>
                                    <option value="specific_date" <?php echo ($editIncome && $editIncome['schedule_type'] === 'specific_date') ? 'selected' : ''; ?>>Specific Date</option>
                                </select>
                            </div>
                            
                            <div class="mb-3" id="schedule_day_group" style="display: none;">
                                <label for="schedule_day" class="form-label">Day of Month</label>
                                <select class="form-select" id="schedule_day" name="schedule_day">
                                    <?php for ($i = 1; $i <= 31; $i++): ?>
                                        <option value="<?php echo $i; ?>" <?php echo ($editIncome && $editIncome['schedule_day'] == $i) ? 'selected' : ''; ?>>
                                            <?php echo $i; ?><?php echo getOrdinalSuffix($i); ?>
                                        </option>
                                    <?php endfor; ?>
                                </select>
                            </div>
                            
                            <div class="mb-3">
                                <label for="description" class="form-label">Description</label>
                                <textarea class="form-control" id="description" name="description" rows="3"><?php echo $editIncome ? htmlspecialchars($editIncome['description']) : ''; ?></textarea>
                            </div>
                            
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-2"></i>
                                    <?php echo $editIncome ? 'Update Income Source' : 'Add Income Source'; ?>
                                </button>
                                <?php if ($editIncome): ?>
                                    <a href="income.php" class="btn btn-secondary">
                                        <i class="fas fa-times me-2"></i>Cancel Edit
                                    </a>
                                <?php endif; ?>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Income Sources List -->
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-list me-2"></i>Income Sources
                        </h5>
                    </div>
                    <div class="card-body">
                        <?php if (empty($incomeSources)): ?>
                            <div class="text-center py-4">
                                <i class="fas fa-arrow-up fa-3x text-muted mb-3"></i>
                                <p class="text-muted">No income sources added yet.</p>
                                <p class="text-muted">Add your first income source using the form on the left.</p>
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Source</th>
                                            <th>Amount</th>
                                            <th>Schedule</th>
                                            <th>Description</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($incomeSources as $income): ?>
                                        <tr>
                                            <td><strong><?php echo htmlspecialchars($income['name']); ?></strong></td>
                                            <td class="text-success"><?php echo formatCurrency($income['amount']); ?></td>
                                            <td>
                                                <?php if ($income['schedule_type'] === 'monthly'): ?>
                                                    <span class="badge bg-info">Monthly</span>
                                                <?php else: ?>
                                                    <span class="badge bg-warning"><?php echo $income['schedule_day']; ?><?php echo getOrdinalSuffix($income['schedule_day']); ?></span>
                                                <?php endif; ?>
                                            </td>
                                            <td><?php echo htmlspecialchars($income['description']); ?></td>
                                            <td>
                                                <div class="btn-group btn-group-sm" role="group">
                                                    <a href="?edit=<?php echo $income['id']; ?>" class="btn btn-outline-primary">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <button type="button" class="btn btn-outline-danger" 
                                                            onclick="confirmDelete(<?php echo $income['id']; ?>, '<?php echo htmlspecialchars($income['name']); ?>')">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                    <tfoot>
                                        <tr class="table-success">
                                            <td><strong>Total Income</strong></td>
                                            <td><strong><?php echo formatCurrency(getTotalIncome()); ?></strong></td>
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
                    <p>Are you sure you want to delete the income source "<span id="deleteName"></span>"?</p>
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
