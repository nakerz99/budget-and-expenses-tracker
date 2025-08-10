<?php
/**
 * Dashboard Page
 * Main dashboard for the budget tracking application
 */

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
include APP_ROOT . '/includes/header.php';
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
                        <h6 class="card-title text-muted mb-1">Savings</h6>
                        <h3 class="mb-0 text-info"><?php echo formatCurrency($savings); ?></h3>
                    </div>
                    <div class="text-info">
                        <i class="fas fa-piggy-bank fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-bolt me-2"></i>Quick Actions</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <?php foreach ($quickActions as $action): ?>
                    <div class="col-md-3 mb-2">
                        <a href="<?php echo $action['url']; ?>" class="btn btn-outline-primary btn-sm w-100">
                            <i class="<?php echo $action['icon']; ?> me-2"></i><?php echo $action['name']; ?>
                        </a>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Recent Activity and Bills -->
<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-chart-line me-2"></i>Budget vs Actual</h5>
            </div>
            <div class="card-body">
                <canvas id="budgetChart" width="400" height="200"></canvas>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-calendar-alt me-2"></i>Upcoming Bills</h5>
            </div>
            <div class="card-body">
                <?php if (empty($upcomingBills)): ?>
                    <p class="text-muted">No upcoming bills</p>
                <?php else: ?>
                    <?php foreach (array_slice($upcomingBills, 0, 5) as $bill): ?>
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div>
                            <strong><?php echo htmlspecialchars($bill['name']); ?></strong>
                            <br><small class="text-muted">Due: <?php echo formatDate($bill['due_date']); ?></small>
                        </div>
                        <span class="badge bg-primary"><?php echo formatCurrency($bill['amount']); ?></span>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php include APP_ROOT . '/includes/footer.php'; ?>

<script>
// Budget vs Actual Chart
const ctx = document.getElementById('budgetChart').getContext('2d');
const budgetData = <?php echo json_encode($budgetVsActual); ?>;

new Chart(ctx, {
    type: 'bar',
    data: {
        labels: budgetData.map(item => item.category),
        datasets: [{
            label: 'Budgeted',
            data: budgetData.map(item => item.budgeted),
            backgroundColor: 'rgba(102, 126, 234, 0.8)',
            borderColor: 'rgba(102, 126, 234, 1)',
            borderWidth: 1
        }, {
            label: 'Actual',
            data: budgetData.map(item => item.actual),
            backgroundColor: 'rgba(255, 193, 7, 0.8)',
            borderColor: 'rgba(255, 193, 7, 1)',
            borderWidth: 1
        }]
    },
    options: {
        responsive: true,
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    callback: function(value) {
                        return '$' + value.toLocaleString();
                    }
                }
            }
        }
    }
});
</script>
