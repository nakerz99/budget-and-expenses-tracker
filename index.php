<?php
/**
 * Budget Planner - Main Dashboard
 * Entry point for the budget tracking application
 */

session_start();
require_once 'includes/functions.php';

// Require authentication
requireAuth();

// Define constants for header inclusion
define('INCLUDED_FROM_INDEX', true);

// Get current data
$currentMonth = getCurrentMonth();
$currentWeek = getCurrentWeek();
$totalIncome = getTotalIncome();
$totalBudgetedExpenses = getTotalBudgetedExpenses();
$totalActualExpenses = getTotalActualExpenses();
$totalActualExpensesWeek = getTotalActualExpensesWeek();
$savings = getSavings();
$totalActualSavings = getTotalSavings();

// Get recent data for display
$incomeSources = getIncomeSources();
$budgetedExpenses = getBudgetedExpenses();
$actualExpenses = getActualExpenses();
$actualExpensesWeek = getActualExpensesWeek();
$budgetVsActual = getBudgetVsActual();
$quickActions = getQuickActions();
$upcomingBills = getUpcomingBills();
$overdueBills = getOverdueBills();

// Set page title
$pageTitle = 'Dashboard - NR BUDGET Planner';

// Include header component
include 'includes/header.php';
?>

    <div class="container mt-4">
        <!-- Header -->
        <div class="row mb-4">
            <div class="col">
                <h1 class="h3 mb-0">
                    <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                </h1>
                <p class="text-muted"><?php echo $currentMonth['name']; ?> Overview</p>
            </div>
        </div>

        <!-- Summary Cards -->
        <div class="row mb-4">
            <div class="col-md-3 mb-3">
                <div class="card card-dashboard income-card h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="card-title text-muted mb-1">Total Income</h6>
                                <h3 class="mb-0 text-success"><?php echo formatCurrency($totalIncome); ?></h3>
                            </div>
                            <div class="text-success">
                                <i class="fas fa-arrow-up fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-3 mb-3">
                <div class="card card-dashboard expense-card h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="card-title text-muted mb-1">Budgeted Expenses</h6>
                                <h3 class="mb-0 text-danger"><?php echo formatCurrency($totalBudgetedExpenses); ?></h3>
                            </div>
                            <div class="text-danger">
                                <i class="fas fa-arrow-down fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-3 mb-3">
                <div class="card card-dashboard budget-card h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="card-title text-muted mb-1">Actual Expenses</h6>
                                <h3 class="mb-0 text-warning"><?php echo formatCurrency($totalActualExpenses); ?></h3>
                            </div>
                            <div class="text-warning">
                                <i class="fas fa-receipt fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-3 mb-3">
                <div class="card card-dashboard savings-card h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="card-title text-muted mb-1">Total Savings</h6>
                                <h3 class="mb-0 text-primary"><?php echo formatCurrency($totalActualSavings); ?></h3>
                            </div>
                            <div class="text-primary">
                                <i class="fas fa-piggy-bank fa-2x"></i>
                            </div>
                        </div>
                        <div class="mt-3">
                            <a href="pages/savings.php" class="btn btn-outline-primary btn-sm w-100">
                                <i class="fas fa-eye me-1"></i>View Details
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bills Alerts -->
        <?php if (!empty($overdueBills) || !empty($upcomingBills)): ?>
        <div class="row mb-4">
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white">
                        <h5 class="mb-0">
                            <i class="fas fa-file-invoice-dollar me-2"></i>Bills & Due Dates
                        </h5>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($overdueBills)): ?>
                        <div class="alert alert-danger mb-3">
                            <h6 class="alert-heading">
                                <i class="fas fa-exclamation-triangle me-2"></i>Overdue Bills
                            </h6>
                            <div class="row">
                                <?php foreach (array_slice($overdueBills, 0, 3) as $bill): ?>
                                <div class="col-md-4 mb-2">
                                    <div class="d-flex justify-content-between">
                                        <span><?php echo htmlspecialchars($bill['name']); ?></span>
                                        <span class="fw-bold"><?php echo formatCurrency($bill['budgeted_amount']); ?></span>
                                    </div>
                                    <small class="text-muted">
                                        Due: <?php echo date('M j', strtotime($bill['due_date'])); ?> 
                                        (<?php echo $bill['days_overdue']; ?> days overdue)
                                    </small>
                                </div>
                                <?php endforeach; ?>
                            </div>
                            <?php if (count($overdueBills) > 3): ?>
                            <div class="mt-2">
                                <small class="text-muted">+<?php echo count($overdueBills) - 3; ?> more overdue bills</small>
                            </div>
                            <?php endif; ?>
                        </div>
                        <?php endif; ?>

                        <?php if (!empty($upcomingBills)): ?>
                        <div class="alert alert-warning">
                            <h6 class="alert-heading">
                                <i class="fas fa-clock me-2"></i>Upcoming Bills (Next 7 Days)
                            </h6>
                            <div class="row">
                                <?php foreach (array_slice($upcomingBills, 0, 3) as $bill): ?>
                                <div class="col-md-4 mb-2">
                                    <div class="d-flex justify-content-between">
                                        <span><?php echo htmlspecialchars($bill['name']); ?></span>
                                        <span class="fw-bold"><?php echo formatCurrency($bill['budgeted_amount']); ?></span>
                                    </div>
                                    <small class="text-muted">
                                        Due: <?php echo date('M j', strtotime($bill['due_date'])); ?> 
                                        (in <?php echo $bill['days_until_due']; ?> days)
                                    </small>
                                </div>
                                <?php endforeach; ?>
                            </div>
                            <?php if (count($upcomingBills) > 3): ?>
                            <div class="mt-2">
                                <small class="text-muted">+<?php echo count($upcomingBills) - 3; ?> more upcoming bills</small>
                            </div>
                            <?php endif; ?>
                        </div>
                        <?php endif; ?>

                        <div class="text-center mt-3">
                            <a href="./bills" class="btn btn-primary">
                                <i class="fas fa-file-invoice-dollar me-2"></i>Manage Bills
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Quick Actions -->
        <div class="row mb-4">
            <div class="col">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-bolt me-2"></i>Quick Actions</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <?php foreach ($quickActions as $action): ?>
                            <div class="col-md-3 col-sm-6 mb-3">
                                <button class="btn btn-outline-primary w-100 h-100 p-3 quick-action-btn" 
                                        data-action-id="<?php echo $action['id']; ?>"
                                        data-action-name="<?php echo htmlspecialchars($action['name']); ?>"
                                        data-action-amount="<?php echo $action['amount']; ?>"
                                        data-action-category="<?php echo htmlspecialchars($action['category_name']); ?>">
                                    <div class="d-flex flex-column align-items-center">
                                        <i class="<?php echo $action['icon']; ?> fa-2x mb-2" style="color: <?php echo $action['color']; ?>"></i>
                                        <strong><?php echo htmlspecialchars($action['name']); ?></strong>
                                        <small class="text-muted"><?php echo formatCurrency($action['amount']); ?></small>
                                    </div>
                                </button>
                            </div>
                            <?php endforeach; ?>
                        </div>

                    </div>
                </div>
            </div>
        </div>

        <!-- Budget vs Actual Progress -->
        <div class="row mb-4">
            <div class="col-md-6 mb-3">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-chart-bar me-2"></i>Monthly Progress</h5>
                    </div>
                    <div class="card-body">
                        <?php if ($totalBudgetedExpenses > 0): ?>
                            <?php 
                            $progressPercentage = ($totalActualExpenses / $totalBudgetedExpenses) * 100;
                            $progressClass = $progressPercentage > 100 ? 'bg-danger' : 'bg-success';
                            ?>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Progress: <?php echo number_format($progressPercentage, 1); ?>%</span>
                                <span><?php echo formatCurrency($totalActualExpenses); ?> / <?php echo formatCurrency($totalBudgetedExpenses); ?></span>
                            </div>
                            <div class="progress progress-bar-custom">
                                <div class="progress-bar <?php echo $progressClass; ?>" 
                                     style="width: <?php echo min($progressPercentage, 100); ?>%"></div>
                            </div>
                            <?php if ($progressPercentage > 100): ?>
                                <small class="text-danger mt-2">
                                    <i class="fas fa-exclamation-triangle me-1"></i>
                                    Over budget by <?php echo formatCurrency($totalActualExpenses - $totalBudgetedExpenses); ?>
                                </small>
                            <?php endif; ?>
                        <?php else: ?>
                            <p class="text-muted mb-0">No budgeted expenses set.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6 mb-3">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-calendar-week me-2"></i>Weekly Progress</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-2">
                            <span>This Week: <?php echo $currentWeek['name']; ?></span>
                            <span><?php echo formatCurrency($totalActualExpensesWeek); ?></span>
                        </div>
                        <div class="progress progress-bar-custom">
                            <div class="progress-bar bg-info" 
                                 style="width: <?php echo min(($totalActualExpensesWeek / 5000) * 100, 100); ?>%"></div>
                        </div>
                        <small class="text-muted mt-2">
                            Weekly spending: <?php echo formatCurrency($totalActualExpensesWeek); ?>
                        </small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Content Tabs -->
        <div class="row">
            <div class="col">
                <div class="card">
                    <div class="card-header">
                        <ul class="nav nav-tabs card-header-tabs" id="dashboardTabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="income-tab" data-bs-toggle="tab" data-bs-target="#income" type="button" role="tab" aria-controls="income" aria-selected="true">
                                    <i class="fas fa-arrow-up me-2"></i>Income Sources
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="expenses-tab" data-bs-toggle="tab" data-bs-target="#expenses" type="button" role="tab" aria-controls="expenses" aria-selected="false">
                                    <i class="fas fa-arrow-down me-2"></i>Budgeted Expenses
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="actual-tab" data-bs-toggle="tab" data-bs-target="#actual" type="button" role="tab" aria-controls="actual" aria-selected="false">
                                    <i class="fas fa-receipt me-2"></i>Actual Expenses
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="comparison-tab" data-bs-toggle="tab" data-bs-target="#comparison" type="button" role="tab" aria-controls="comparison" aria-selected="false">
                                    <i class="fas fa-balance-scale me-2"></i>Budget vs Actual
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="weekly-tab" data-bs-toggle="tab" data-bs-target="#weekly" type="button" role="tab" aria-controls="weekly" aria-selected="false">
                                    <i class="fas fa-calendar-week me-2"></i>Weekly Tracking
                                </button>
                            </li>
                        </ul>
                    </div>
                    <div class="card-body">
                        <div class="tab-content" id="dashboardTabsContent">
                            <!-- Income Sources Tab -->
                            <div class="tab-pane fade show active" id="income" role="tabpanel" aria-labelledby="income-tab">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h5 class="mb-0">Income Sources</h5>
                                    <a href="pages/income.php" class="btn btn-primary btn-sm">
                                        <i class="fas fa-plus me-1"></i>Add Income
                                    </a>
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>Source</th>
                                                <th>Amount</th>
                                                <th>Schedule</th>
                                                <th>Description</th>
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
                                                        <span class="badge bg-warning"><?php echo $income['schedule_day']; ?>th</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td><?php echo htmlspecialchars($income['description']); ?></td>
                                            </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <!-- Budgeted Expenses Tab -->
                            <div class="tab-pane fade" id="expenses" role="tabpanel">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h5 class="mb-0">Budgeted Expenses</h5>
                                    <a href="pages/expenses.php" class="btn btn-primary btn-sm">
                                        <i class="fas fa-plus me-1"></i>Add Expense
                                    </a>
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>Category</th>
                                                <th>Expense</th>
                                                <th>Budgeted Amount</th>
                                                <th>Schedule</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($budgetedExpenses as $expense): ?>
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
                                                        <span class="badge bg-warning"><?php echo $expense['schedule_day']; ?>th</span>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <!-- Actual Expenses Tab -->
                            <div class="tab-pane fade" id="actual" role="tabpanel">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h5 class="mb-0">Actual Expenses</h5>
                                    <a href="pages/actual-expenses.php" class="btn btn-primary btn-sm">
                                        <i class="fas fa-plus me-1"></i>Add Actual Expense
                                    </a>
                                </div>
                                <?php if (empty($actualExpenses)): ?>
                                    <div class="text-center py-4">
                                        <i class="fas fa-receipt fa-3x text-muted mb-3"></i>
                                        <p class="text-muted">No actual expenses recorded yet.</p>
                                        <a href="pages/actual-expenses.php" class="btn btn-primary">
                                            <i class="fas fa-plus me-1"></i>Record First Expense
                                        </a>
                                    </div>
                                <?php else: ?>
                                    <div class="table-responsive">
                                        <table class="table table-hover">
                                            <thead>
                                                <tr>
                                                    <th>Date</th>
                                                    <th>Category</th>
                                                    <th>Expense</th>
                                                    <th>Actual Amount</th>
                                                    <th>Notes</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($actualExpenses as $actual): ?>
                                                <tr>
                                                    <td><?php echo date('M d, Y', strtotime($actual['date_paid'])); ?></td>
                                                    <td>
                                                        <span class="badge" style="background-color: <?php echo $actual['category_color']; ?>">
                                                            <?php echo htmlspecialchars($actual['category_name']); ?>
                                                        </span>
                                                    </td>
                                                    <td><strong><?php echo htmlspecialchars($actual['expense_name']); ?></strong></td>
                                                    <td class="text-danger"><?php echo formatCurrency($actual['actual_amount']); ?></td>
                                                    <td><?php echo htmlspecialchars($actual['notes']); ?></td>
                                                </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <!-- Budget vs Actual Tab -->
                            <div class="tab-pane fade" id="comparison" role="tabpanel">
                                <h5 class="mb-3">Budget vs Actual Comparison</h5>
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>Category</th>
                                                <th>Expense</th>
                                                <th>Budgeted</th>
                                                <th>Actual</th>
                                                <th>Difference</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($budgetVsActual as $comparison): ?>
                                            <?php 
                                            $difference = $comparison['difference'];
                                            $statusClass = $difference >= 0 ? 'text-success' : 'text-danger';
                                            $statusIcon = $difference >= 0 ? 'fas fa-check' : 'fas fa-exclamation-triangle';
                                            ?>
                                            <tr>
                                                <td>
                                                    <span class="badge bg-secondary">
                                                        <?php echo htmlspecialchars($comparison['category_name']); ?>
                                                    </span>
                                                </td>
                                                <td><strong><?php echo htmlspecialchars($comparison['name']); ?></strong></td>
                                                <td><?php echo formatCurrency($comparison['budgeted_amount']); ?></td>
                                                <td><?php echo formatCurrency($comparison['actual_amount']); ?></td>
                                                <td class="<?php echo $statusClass; ?>">
                                                    <?php echo formatCurrency(abs($difference)); ?>
                                                    <?php if ($difference < 0): ?>
                                                        <i class="fas fa-arrow-up ms-1"></i>
                                                    <?php else: ?>
                                                        <i class="fas fa-arrow-down ms-1"></i>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <i class="<?php echo $statusIcon; ?> <?php echo $statusClass; ?>"></i>
                                                </td>
                                            </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <!-- Weekly Tracking Tab -->
                            <div class="tab-pane fade" id="weekly" role="tabpanel">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h5 class="mb-0">Weekly Expenses Tracking</h5>
                                    <span class="badge bg-info"><?php echo $currentWeek['name']; ?></span>
                                </div>
                                <?php if (empty($actualExpensesWeek)): ?>
                                    <div class="text-center py-4">
                                        <i class="fas fa-calendar-week fa-3x text-muted mb-3"></i>
                                        <p class="text-muted">No expenses recorded for this week yet.</p>
                                        <p class="text-muted">Use quick actions above to add expenses.</p>
                                    </div>
                                <?php else: ?>
                                    <div class="table-responsive">
                                        <table class="table table-hover">
                                            <thead>
                                                <tr>
                                                    <th>Date</th>
                                                    <th>Category</th>
                                                    <th>Expense</th>
                                                    <th>Amount</th>
                                                    <th>Notes</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($actualExpensesWeek as $expense): ?>
                                                <tr>
                                                    <td><?php echo date('M d, Y', strtotime($expense['date_paid'])); ?></td>
                                                    <td>
                                                        <span class="badge" style="background-color: <?php echo $expense['category_color']; ?>">
                                                            <?php echo htmlspecialchars($expense['category_name']); ?>
                                                        </span>
                                                    </td>
                                                    <td><strong><?php echo htmlspecialchars($expense['expense_name']); ?></strong></td>
                                                    <td class="text-danger"><?php echo formatCurrency($expense['actual_amount']); ?></td>
                                                    <td><?php echo htmlspecialchars($expense['notes']); ?></td>
                                                </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                            <tfoot>
                                                <tr class="table-info">
                                                    <td colspan="3"><strong>Weekly Total</strong></td>
                                                    <td><strong><?php echo formatCurrency($totalActualExpensesWeek); ?></strong></td>
                                                    <td></td>
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
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Quick Action Modal -->
    <div class="modal fade" id="quickActionModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Quick Expense</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="quickExpenseForm" method="POST" action="pages/quick-expense.php">
                        <input type="hidden" name="action" value="add_quick_expense">
                        <input type="hidden" name="quick_action_id" id="quickActionId">
                        
                        <div class="mb-3">
                            <label class="form-label">Expense Type</label>
                            <div class="form-control-plaintext" id="quickActionName"></div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="quickAmount" class="form-label">Amount</label>
                            <div class="input-group">
                                <span class="input-group-text">₱</span>
                                <input type="number" class="form-control" id="quickAmount" name="amount" 
                                       step="0.01" min="0" required>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="quickDate" class="form-label">Date</label>
                            <input type="date" class="form-control" id="quickDate" name="date_paid" 
                                   value="<?php echo date('Y-m-d'); ?>" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="quickPaymentMethod" class="form-label">Payment Method</label>
                            <select class="form-select" id="quickPaymentMethod" name="payment_method_id">
                                <option value="">Select Payment Method (Optional)</option>
                                <optgroup label="Cash">
                                    <option value="1" selected>Cash</option>
                                </optgroup>
                                <optgroup label="Credit Cards">
                                    <option value="2">BPI Credit Card</option>
                                    <option value="3">Unionbank Credit Card</option>
                                    <option value="4">BDO Credit Card</option>
                                    <option value="5">Security Bank Credit Card</option>
                                </optgroup>
                                <optgroup label="Online">
                                    <option value="6">GCash</option>
                                    <option value="7">BPI Online</option>
                                    <option value="8">Unionbank Online</option>
                                    <option value="9">UNO Digital Bank</option>
                                </optgroup>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label for="quickNotes" class="form-label">Notes (Optional)</label>
                            <textarea class="form-control" id="quickNotes" name="notes" rows="2" 
                                      placeholder="Add any notes about this expense..."></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" form="quickExpenseForm" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>Save
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Initialize Bootstrap tabs
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize all tabs
            var triggerTabList = [].slice.call(document.querySelectorAll('#dashboardTabs button'))
            triggerTabList.forEach(function (triggerEl) {
                var tabTrigger = new bootstrap.Tab(triggerEl)
                
                triggerEl.addEventListener('click', function (event) {
                    event.preventDefault()
                    tabTrigger.show()
                })
            })
            
            // Quick Actions functionality
            document.querySelectorAll('.quick-action-btn').forEach(function(btn) {
                btn.addEventListener('click', function() {
                    const actionId = this.getAttribute('data-action-id');
                    const actionName = this.getAttribute('data-action-name');
                    const actionAmount = this.getAttribute('data-action-amount');
                    
                    document.getElementById('quickActionId').value = actionId;
                    document.getElementById('quickActionName').textContent = actionName;
                    document.getElementById('quickAmount').value = actionAmount;
                    
                    new bootstrap.Modal(document.getElementById('quickActionModal')).show();
                });
            });
        });
    </script>
</body>
</html>
