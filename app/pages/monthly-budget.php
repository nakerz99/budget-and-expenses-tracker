<?php
/**
 * Monthly Budget Planning Page
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
            case 'create_month':
                $monthName = sanitizeInput($_POST['month_name']);
                $year = sanitizeInput($_POST['year'], 'int');
                
                // Check if month already exists
                $existingMonth = fetchOne("SELECT id FROM months WHERE name = ? AND year = ?", [$monthName, $year]);
                if ($existingMonth) {
                    showError('Budget for this month already exists!');
                } else {
                    $sql = "INSERT INTO months (name, year, is_active) VALUES (?, ?, 1)";
                    if (executeQuery($sql, [$monthName, $year])) {
                        showSuccess('Monthly budget created successfully!');
                    } else {
                        showError('Failed to create monthly budget.');
                    }
                }
                redirect('monthly-budget.php');
                break;
                
            case 'set_active':
                $monthId = sanitizeInput($_POST['month_id'], 'int');
                // Deactivate all months first
                executeQuery("UPDATE months SET is_active = 0");
                // Activate selected month
                if (executeQuery("UPDATE months SET is_active = 1 WHERE id = ?", [$monthId])) {
                    showSuccess('Active month updated successfully!');
                } else {
                    showError('Failed to update active month.');
                }
                redirect('monthly-budget.php');
                break;
                
            case 'copy_template':
                $sourceMonthId = sanitizeInput($_POST['source_month_id'], 'int');
                $targetMonthId = sanitizeInput($_POST['target_month_id'], 'int');
                
                // Copy income sources
                $incomeSources = fetchAll("SELECT * FROM income_sources WHERE month_id = ?", [$sourceMonthId]);
                foreach ($incomeSources as $income) {
                    $sql = "INSERT INTO income_sources (name, amount, schedule_type, schedule_day, description, month_id) 
                            VALUES (?, ?, ?, ?, ?, ?)";
                    executeQuery($sql, [
                        $income['name'],
                        $income['amount'],
                        $income['schedule_type'],
                        $income['schedule_day'],
                        $income['description'],
                        $targetMonthId
                    ]);
                }
                
                // Copy expenses
                $expenses = fetchAll("SELECT * FROM expenses WHERE month_id = ?", [$sourceMonthId]);
                foreach ($expenses as $expense) {
                    $sql = "INSERT INTO expenses (category_id, name, budgeted_amount, schedule_type, schedule_day, description, month_id) 
                            VALUES (?, ?, ?, ?, ?, ?, ?)";
                    executeQuery($sql, [
                        $expense['category_id'],
                        $expense['name'],
                        $expense['budgeted_amount'],
                        $expense['schedule_type'],
                        $expense['schedule_day'],
                        $expense['description'],
                        $targetMonthId
                    ]);
                }
                
                showSuccess('Template copied successfully!');
                redirect('monthly-budget.php');
                break;
        }
    }
}

// Get all months
$allMonths = getAllMonths();
$currentMonth = getCurrentMonth();
// Messages are handled by header.php
?>
<?php
$pageTitle = 'Monthly Budget Planning - NR BUDGET Planner';

// Handle path for router vs direct access
if (defined('USING_ROUTER')) {
    include __DIR__ . '/../includes/header.php';
} else {
    include '../includes/header.php';
}
?>

<style>
    .month-card {
        transition: transform 0.2s;
    }
    .month-card:hover {
        transform: translateY(-2px);
    }
    .active-month {
        border: 2px solid #007bff;
        background-color: #f8f9fa;
    }
    .budget-summary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-radius: 15px;
        padding: 30px;
    }
</style>

    <div class="container mt-4">
        <!-- Header -->
        <div class="row mb-4">
            <div class="col">
                <h1 class="h3 mb-0">
                    <i class="fas fa-calendar-alt me-2"></i>Monthly Budget Planning
                </h1>
                <p class="text-muted">Plan and manage your monthly budgets</p>
            </div>
            <div class="col-auto">
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createMonthModal">
                    <i class="fas fa-plus me-2"></i>Create New Month
                </button>
            </div>
        </div>

        <!-- Current Month Status -->
        <div class="row mb-4">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-calendar-check me-2"></i>Current Active Month</h5>
                    </div>
                    <div class="card-body">
                        <?php if ($currentMonth): ?>
                            <h4 class="text-primary"><?php echo $currentMonth['name'] . ' ' . $currentMonth['year']; ?></h4>
                            <p class="text-muted mb-0">This is your active budget month</p>
                        <?php else: ?>
                            <h4 class="text-muted">No Active Month</h4>
                            <p class="text-muted mb-0">Please create a monthly budget first</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-chart-line me-2"></i>Quick Stats</h5>
                    </div>
                    <div class="card-body">
                        <div class="row text-center">
                            <div class="col-4">
                                <h6 class="text-muted">Income</h6>
                                <h5 class="text-success"><?php echo formatCurrency(getTotalIncome()); ?></h5>
                            </div>
                            <div class="col-4">
                                <h6 class="text-muted">Budgeted</h6>
                                <h5 class="text-danger"><?php echo formatCurrency(getTotalBudgetedExpenses()); ?></h5>
                            </div>
                            <div class="col-4">
                                <h6 class="text-muted">Savings</h6>
                                <h5 class="text-primary"><?php echo formatCurrency(getSavings()); ?></h5>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Monthly Budgets Table -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-list me-2"></i>All Monthly Budgets</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Month</th>
                                <th>Year</th>
                                <th>Status</th>
                                <th>Income</th>
                                <th>Budgeted Expenses</th>
                                <th>Actual Expenses</th>
                                <th>Savings</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($allMonths as $month): ?>
                                <tr>
                                    <td>
                                        <strong><?php echo htmlspecialchars($month['name']); ?></strong>
                                    </td>
                                    <td><?php echo htmlspecialchars($month['year']); ?></td>
                                    <td>
                                        <?php if ($month['is_active']): ?>
                                            <span class="badge bg-success">Active</span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary">Inactive</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-success">
                                        <?php echo formatCurrency(getTotalIncome($month['id'])); ?>
                                    </td>
                                    <td class="text-danger">
                                        <?php echo formatCurrency(getTotalBudgetedExpenses($month['id'])); ?>
                                    </td>
                                    <td class="text-warning">
                                        <?php echo formatCurrency(getTotalActualExpenses($month['id'])); ?>
                                    </td>
                                    <td class="text-primary">
                                        <?php echo formatCurrency(getSavings($month['id'])); ?>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm" role="group">
                                            <?php if (!$month['is_active']): ?>
                                                <form method="POST" style="display: inline;">
                                                    <input type="hidden" name="action" value="set_active">
                                                    <input type="hidden" name="month_id" value="<?php echo $month['id']; ?>">
                                                    <button type="submit" class="btn btn-outline-success" title="Set as Active">
                                                        <i class="fas fa-check"></i>
                                                    </button>
                                                </form>
                                            <?php endif; ?>
                                            <a href="income.php?month=<?php echo $month['id']; ?>" class="btn btn-outline-primary" title="Manage Income">
                                                <i class="fas fa-dollar-sign"></i>
                                            </a>
                                            <a href="expenses.php?month=<?php echo $month['id']; ?>" class="btn btn-outline-warning" title="Manage Expenses">
                                                <i class="fas fa-receipt"></i>
                                            </a>
                                            <button class="btn btn-outline-info" data-bs-toggle="modal" data-bs-target="#copyTemplateModal" 
                                                    data-source-month="<?php echo $month['id']; ?>" data-source-name="<?php echo htmlspecialchars($month['name'] . ' ' . $month['year']); ?>" title="Copy as Template">
                                                <i class="fas fa-copy"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Create Month Modal -->
    <div class="modal fade" id="createMonthModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Create New Monthly Budget</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <input type="hidden" name="action" value="create_month">
                        
                        <div class="mb-3">
                            <label for="month_name" class="form-label">Month</label>
                            <select class="form-select" id="month_name" name="month_name" required>
                                <option value="">Select Month</option>
                                <option value="January">January</option>
                                <option value="February">February</option>
                                <option value="March">March</option>
                                <option value="April">April</option>
                                <option value="May">May</option>
                                <option value="June">June</option>
                                <option value="July">July</option>
                                <option value="August">August</option>
                                <option value="September">September</option>
                                <option value="October">October</option>
                                <option value="November">November</option>
                                <option value="December">December</option>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label for="year" class="form-label">Year</label>
                            <input type="number" class="form-control" id="year" name="year" 
                                   value="<?php echo date('Y'); ?>" min="2020" max="2030" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Create Budget</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Copy Template Modal -->
    <div class="modal fade" id="copyTemplateModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Copy Budget Template</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <input type="hidden" name="action" value="copy_template">
                        <input type="hidden" name="source_month_id" id="source_month_id">
                        
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            Copying budget from: <strong id="source_month_name"></strong>
                        </div>
                        
                        <div class="mb-3">
                            <label for="target_month_id" class="form-label">Copy to Month</label>
                            <select class="form-select" id="target_month_id" name="target_month_id" required>
                                <option value="">Select Target Month</option>
                                <?php foreach ($allMonths as $month): ?>
                                    <option value="<?php echo $month['id']; ?>">
                                        <?php echo htmlspecialchars($month['name'] . ' ' . $month['year']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            This will copy all income sources and expenses from the source month to the target month.
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Copy Template</button>
                    </div>
                </form>
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
    // Handle copy template modal
    document.getElementById('copyTemplateModal').addEventListener('show.bs.modal', function (event) {
        var button = event.relatedTarget;
        var sourceMonthId = button.getAttribute('data-source-month');
        var sourceMonthName = button.getAttribute('data-source-name');
        
        document.getElementById('source_month_id').value = sourceMonthId;
        document.getElementById('source_month_name').textContent = sourceMonthName;
    });
</script>
