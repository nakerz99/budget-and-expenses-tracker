<?php
/**
 * Reports Page
 * Budget Planner Application
 */

session_start();

// Handle path for router vs direct access
if (defined('USING_ROUTER')) {
    require_once __DIR__ . '/../includes/functions.php';
} else {
    require_once '../includes/functions.php';
}

// Get current date
$currentDate = new DateTime();
$currentMonthNum = $currentDate->format('n'); // 8 for August
$currentYear = $currentDate->format('Y'); // 2025

// Get previous, current, and next month data
$previousMonth = $currentMonthNum - 1;
$previousYear = $currentYear;
if ($previousMonth < 1) {
    $previousMonth = 12;
    $previousYear = $currentYear - 1;
}

$nextMonth = $currentMonthNum + 1;
$nextYear = $currentYear;
if ($nextMonth > 12) {
    $nextMonth = 1;
    $nextYear = $currentYear + 1;
}

// Get month names
$monthNames = [
    1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April',
    5 => 'May', 6 => 'June', 7 => 'July', 8 => 'August',
    9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December'
];

// Get data for each month
$previousMonthData = getMonthData($previousMonth, $previousYear);
$currentMonthData = getMonthData($currentMonthNum, $currentYear);
$nextMonthData = getForecastData($nextMonth, $nextYear);

// Get data
$currentMonth = getCurrentMonth();
$allMonths = getAllMonths();
$budgetVsActual = getBudgetVsActual();
$incomeSources = getIncomeSources();
$budgetedExpenses = getBudgetedExpenses();
$actualExpenses = getActualExpenses();

// Calculate totals
$totalIncome = getTotalIncome();
$totalBudgetedExpenses = getTotalBudgetedExpenses();
$totalActualExpenses = getTotalActualExpenses();
$savings = getSavings();

// Group expenses by category for analysis
$expensesByCategory = [];
foreach ($budgetedExpenses as $expense) {
    $categoryName = $expense['category_name'];
    if (!isset($expensesByCategory[$categoryName])) {
        $expensesByCategory[$categoryName] = [
            'budgeted' => 0,
            'actual' => 0,
            'expenses' => []
        ];
    }
    $expensesByCategory[$categoryName]['budgeted'] += $expense['budgeted_amount'];
    $expensesByCategory[$categoryName]['expenses'][] = $expense;
}

// Add actual amounts to categories
foreach ($actualExpenses as $actual) {
    $categoryName = $actual['category_name'];
    if (isset($expensesByCategory[$categoryName])) {
        $expensesByCategory[$categoryName]['actual'] += $actual['actual_amount'];
    }
}

$messages = getMessages();
?>
<?php
$pageTitle = 'Reports - NR BUDGET Planner';

// Handle path for router vs direct access
if (defined('USING_ROUTER')) {
    include __DIR__ . '/../includes/header.php';
} else {
    include '../includes/header.php';
}
?>
    <style>
        .report-card {
            transition: transform 0.2s;
        }
        .report-card:hover {
            transform: translateY(-2px);
        }
        .chart-container {
            position: relative;
            height: 300px;
            margin: 20px 0;
        }
    </style>
</head>
<body class="bg-light">
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="../index.php">
                <i class="fas fa-chart-pie me-2"></i>Budget Planner
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="../index.php">Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="income.php">Income</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="expenses.php">Expenses</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="actual-expenses.php">Actual Expenses</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="quick-actions.php">Quick Actions</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="reports.php">Reports</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="savings.php">Savings</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="pin-settings.php">Security</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../logout.php">Logout</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

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
                    <i class="fas fa-chart-line me-2"></i>Financial Reports
                </h1>
                <p class="text-muted">Detailed analysis for <?php echo $currentMonth ? $currentMonth['name'] : 'current month'; ?></p>
            </div>
        </div>

        <!-- Summary Cards -->
        <div class="row mb-4">
            <div class="col-md-3 mb-3">
                <div class="card report-card border-success">
                    <div class="card-body text-center">
                        <i class="fas fa-arrow-up fa-2x text-success mb-2"></i>
                        <h5 class="card-title">Total Income</h5>
                        <h3 class="text-success"><?php echo formatCurrency($totalIncome); ?></h3>
                    </div>
                </div>
            </div>
            
            <div class="col-md-3 mb-3">
                <div class="card report-card border-danger">
                    <div class="card-body text-center">
                        <i class="fas fa-arrow-down fa-2x text-danger mb-2"></i>
                        <h5 class="card-title">Budgeted Expenses</h5>
                        <h3 class="text-danger"><?php echo formatCurrency($totalBudgetedExpenses); ?></h3>
                    </div>
                </div>
            </div>
            
            <div class="col-md-3 mb-3">
                <div class="card report-card border-warning">
                    <div class="card-body text-center">
                        <i class="fas fa-receipt fa-2x text-warning mb-2"></i>
                        <h5 class="card-title">Actual Expenses</h5>
                        <h3 class="text-warning"><?php echo formatCurrency($totalActualExpenses); ?></h3>
                    </div>
                </div>
            </div>
            
            <div class="col-md-3 mb-3">
                <div class="card report-card border-primary">
                    <div class="card-body text-center">
                        <i class="fas fa-piggy-bank fa-2x text-primary mb-2"></i>
                        <h5 class="card-title">Net Savings</h5>
                        <h3 class="<?php echo $savings >= 0 ? 'text-primary' : 'text-danger'; ?>">
                            <?php echo formatCurrency($savings); ?>
                        </h3>
                    </div>
                </div>
            </div>
        </div>

        <!-- Monthly Analysis Section -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-calendar-alt me-2"></i>Monthly Analysis & Forecast
                        </h5>
                        <small class="text-muted">Previous Month • Current Month • Next Month Forecast</small>
                        <?php if ($previousMonthData['income'] == 0 && $previousMonthData['expenses'] == 0): ?>
                            <div class="alert alert-info mt-2 mb-0 py-2">
                                <i class="fas fa-info-circle me-2"></i>
                                <small>Note: <?php echo $monthNames[$previousMonth] . ' ' . $previousYear; ?> shows zero values because we just started tracking today (<?php echo date('F j, Y'); ?>).</small>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <!-- Previous Month -->
                            <div class="col-md-4 mb-3">
                                <div class="card h-100 border-secondary">
                                    <div class="card-header bg-secondary text-white">
                                        <h6 class="mb-0">
                                            <i class="fas fa-history me-2"></i><?php echo $monthNames[$previousMonth] . ' ' . $previousYear; ?>
                                        </h6>
                                        <small>Previous Month</small>
                                    </div>
                                    <div class="card-body">
                                        <div class="row text-center">
                                            <div class="col-6">
                                                <h6 class="text-muted">Income</h6>
                                                <h5 class="text-success"><?php echo formatCurrency($previousMonthData['income']); ?></h5>
                                            </div>
                                            <div class="col-6">
                                                <h6 class="text-muted">Expenses</h6>
                                                <h5 class="text-danger"><?php echo formatCurrency($previousMonthData['expenses']); ?></h5>
                                            </div>
                                        </div>
                                        <hr>
                                        <div class="text-center">
                                            <h6 class="text-muted">Savings</h6>
                                            <h4 class="<?php echo $previousMonthData['savings'] >= 0 ? 'text-success' : 'text-danger'; ?>">
                                                <?php echo formatCurrency($previousMonthData['savings']); ?>
                                            </h4>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Current Month -->
                            <div class="col-md-4 mb-3">
                                <div class="card h-100 border-primary">
                                    <div class="card-header bg-primary text-white">
                                        <h6 class="mb-0">
                                            <i class="fas fa-calendar-check me-2"></i><?php echo $monthNames[$currentMonthNum] . ' ' . $currentYear; ?>
                                        </h6>
                                        <small>Current Month</small>
                                    </div>
                                    <div class="card-body">
                                        <div class="row text-center">
                                            <div class="col-6">
                                                <h6 class="text-muted">Income</h6>
                                                <h5 class="text-success"><?php echo formatCurrency($currentMonthData['income']); ?></h5>
                                            </div>
                                            <div class="col-6">
                                                <h6 class="text-muted">Expenses</h6>
                                                <h5 class="text-danger"><?php echo formatCurrency($currentMonthData['expenses']); ?></h5>
                                            </div>
                                        </div>
                                        <hr>
                                        <div class="text-center">
                                            <h6 class="text-muted">Savings</h6>
                                            <h4 class="<?php echo $currentMonthData['savings'] >= 0 ? 'text-success' : 'text-danger'; ?>">
                                                <?php echo formatCurrency($currentMonthData['savings']); ?>
                                            </h4>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Next Month Forecast -->
                            <div class="col-md-4 mb-3">
                                <div class="card h-100 border-info">
                                    <div class="card-header bg-info text-white">
                                        <h6 class="mb-0">
                                            <i class="fas fa-crystal-ball me-2"></i><?php echo $monthNames[$nextMonth] . ' ' . $nextYear; ?>
                                        </h6>
                                        <small>Forecast</small>
                                    </div>
                                    <div class="card-body">
                                        <div class="row text-center">
                                            <div class="col-6">
                                                <h6 class="text-muted">Income</h6>
                                                <h5 class="text-success"><?php echo formatCurrency($nextMonthData['income']); ?></h5>
                                            </div>
                                            <div class="col-6">
                                                <h6 class="text-muted">Expenses</h6>
                                                <h5 class="text-danger"><?php echo formatCurrency($nextMonthData['expenses']); ?></h5>
                                            </div>
                                        </div>
                                        <hr>
                                        <div class="text-center">
                                            <h6 class="text-muted">Projected Savings</h6>
                                            <h4 class="<?php echo $nextMonthData['savings'] >= 0 ? 'text-success' : 'text-danger'; ?>">
                                                <?php echo formatCurrency($nextMonthData['savings']); ?>
                                            </h4>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Monthly Comparison Table -->
                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h6 class="mb-0">
                                            <i class="fas fa-table me-2"></i>Monthly Comparison Table
                                        </h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="table table-sm table-hover">
                                                <thead>
                                                    <tr>
                                                        <th>Category</th>
                                                        <th><?php echo $monthNames[$previousMonth]; ?></th>
                                                        <th><?php echo $monthNames[$currentMonthNum]; ?></th>
                                                        <th><?php echo $monthNames[$nextMonth]; ?></th>
                                                        <th>Trend</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr class="table-success">
                                                        <td><strong>Income</strong></td>
                                                        <td><?php echo formatCurrency($previousMonthData['income']); ?></td>
                                                        <td><?php echo formatCurrency($currentMonthData['income']); ?></td>
                                                        <td><?php echo formatCurrency($nextMonthData['income']); ?></td>
                                                        <td>
                                                            <?php 
                                                            $incomeTrend = $currentMonthData['income'] - $previousMonthData['income'];
                                                            if ($incomeTrend > 0): ?>
                                                                <i class="fas fa-arrow-up text-success"></i> +<?php echo formatCurrency($incomeTrend); ?>
                                                            <?php elseif ($incomeTrend < 0): ?>
                                                                <i class="fas fa-arrow-down text-danger"></i> <?php echo formatCurrency($incomeTrend); ?>
                                                            <?php else: ?>
                                                                <i class="fas fa-minus text-muted"></i> No change
                                                            <?php endif; ?>
                                                        </td>
                                                    </tr>
                                                    <tr class="table-danger">
                                                        <td><strong>Expenses</strong></td>
                                                        <td><?php echo formatCurrency($previousMonthData['expenses']); ?></td>
                                                        <td><?php echo formatCurrency($currentMonthData['expenses']); ?></td>
                                                        <td><?php echo formatCurrency($nextMonthData['expenses']); ?></td>
                                                        <td>
                                                            <?php 
                                                            $expenseTrend = $currentMonthData['expenses'] - $previousMonthData['expenses'];
                                                            if ($expenseTrend > 0): ?>
                                                                <i class="fas fa-arrow-up text-danger"></i> +<?php echo formatCurrency($expenseTrend); ?>
                                                            <?php elseif ($expenseTrend < 0): ?>
                                                                <i class="fas fa-arrow-down text-success"></i> <?php echo formatCurrency($expenseTrend); ?>
                                                            <?php else: ?>
                                                                <i class="fas fa-minus text-muted"></i> No change
                                                            <?php endif; ?>
                                                        </td>
                                                    </tr>
                                                    <tr class="table-primary">
                                                        <td><strong>Savings</strong></td>
                                                        <td class="<?php echo $previousMonthData['savings'] >= 0 ? 'text-success' : 'text-danger'; ?>">
                                                            <?php echo formatCurrency($previousMonthData['savings']); ?>
                                                        </td>
                                                        <td class="<?php echo $currentMonthData['savings'] >= 0 ? 'text-success' : 'text-danger'; ?>">
                                                            <?php echo formatCurrency($currentMonthData['savings']); ?>
                                                        </td>
                                                        <td class="<?php echo $nextMonthData['savings'] >= 0 ? 'text-success' : 'text-danger'; ?>">
                                                            <?php echo formatCurrency($nextMonthData['savings']); ?>
                                                        </td>
                                                        <td>
                                                            <?php 
                                                            $savingsTrend = $currentMonthData['savings'] - $previousMonthData['savings'];
                                                            if ($savingsTrend > 0): ?>
                                                                <i class="fas fa-arrow-up text-success"></i> +<?php echo formatCurrency($savingsTrend); ?>
                                                            <?php elseif ($savingsTrend < 0): ?>
                                                                <i class="fas fa-arrow-down text-danger"></i> <?php echo formatCurrency($savingsTrend); ?>
                                                            <?php else: ?>
                                                                <i class="fas fa-minus text-muted"></i> No change
                                                            <?php endif; ?>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                        
                                        <!-- Monthly Comparison Bar Chart -->
                                        <div class="mt-4">
                                            <h6 class="mb-3">
                                                <i class="fas fa-chart-bar me-2"></i>Monthly Comparison Chart
                                            </h6>
                                            <div class="chart-container" style="height: 300px;">
                                                <canvas id="monthlyComparisonChart"></canvas>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Row -->
        <div class="row mb-4">
            <!-- Income vs Expenses Chart -->
            <div class="col-md-6 mb-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-chart-bar me-2"></i>Income vs Expenses Overview
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="chart-container">
                            <canvas id="incomeExpensesChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Expense Categories Chart -->
            <div class="col-md-6 mb-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-chart-pie me-2"></i>Expense Categories Distribution
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="chart-container">
                            <canvas id="expenseCategoriesChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Budget vs Actual Analysis -->
        <div class="row mb-4">
            <div class="col">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-balance-scale me-2"></i>Budget vs Actual Analysis
                        </h5>
                    </div>
                    <div class="card-body">
                        <?php if (empty($budgetVsActual)): ?>
                            <div class="text-center py-4">
                                <i class="fas fa-chart-line fa-3x text-muted mb-3"></i>
                                <p class="text-muted">No budget vs actual data available.</p>
                                <p class="text-muted">Add some expenses and record actual spending to see analysis.</p>
                            </div>
                        <?php else: ?>
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
                                            <th>Utilization</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($budgetVsActual as $comparison): ?>
                                            <?php 
                                            $difference = $comparison['difference'];
                                            $statusClass = $difference >= 0 ? 'text-success' : 'text-danger';
                                            $statusIcon = $difference >= 0 ? 'fas fa-check' : 'fas fa-exclamation-triangle';
                                            $utilization = $comparison['budgeted_amount'] > 0 ? 
                                                ($comparison['actual_amount'] / $comparison['budgeted_amount']) * 100 : 0;
                                            $utilizationClass = $utilization > 100 ? 'text-danger' : 
                                                ($utilization > 80 ? 'text-warning' : 'text-success');
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
                                                <td class="<?php echo $utilizationClass; ?>">
                                                    <?php echo number_format($utilization, 1); ?>%
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

        <!-- Category Analysis -->
        <div class="row mb-4">
            <div class="col">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-tags me-2"></i>Category Analysis
                        </h5>
                    </div>
                    <div class="card-body">
                        <?php if (empty($expensesByCategory)): ?>
                            <div class="text-center py-4">
                                <i class="fas fa-tags fa-3x text-muted mb-3"></i>
                                <p class="text-muted">No expense categories found.</p>
                            </div>
                        <?php else: ?>
                            <div class="row">
                                <?php foreach ($expensesByCategory as $categoryName => $categoryData): ?>
                                    <?php 
                                    $budgeted = $categoryData['budgeted'];
                                    $actual = $categoryData['actual'];
                                    $difference = $budgeted - $actual;
                                    $utilization = $budgeted > 0 ? ($actual / $budgeted) * 100 : 0;
                                    $statusClass = $difference >= 0 ? 'text-success' : 'text-danger';
                                    $progressClass = $utilization > 100 ? 'bg-danger' : 
                                        ($utilization > 80 ? 'bg-warning' : 'bg-success');
                                    ?>
                                    <div class="col-md-6 col-lg-4 mb-3">
                                        <div class="card border-0 shadow-sm">
                                            <div class="card-body">
                                                <h6 class="card-title"><?php echo htmlspecialchars($categoryName); ?></h6>
                                                <div class="row text-center mb-2">
                                                    <div class="col-6">
                                                        <small class="text-muted">Budgeted</small>
                                                        <div class="fw-bold text-danger"><?php echo formatCurrency($budgeted); ?></div>
                                                    </div>
                                                    <div class="col-6">
                                                        <small class="text-muted">Actual</small>
                                                        <div class="fw-bold text-warning"><?php echo formatCurrency($actual); ?></div>
                                                    </div>
                                                </div>
                                                <div class="progress mb-2" style="height: 8px;">
                                                    <div class="progress-bar <?php echo $progressClass; ?>" 
                                                         style="width: <?php echo min($utilization, 100); ?>%"></div>
                                                </div>
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <small class="text-muted"><?php echo number_format($utilization, 1); ?>% used</small>
                                                    <small class="<?php echo $statusClass; ?>">
                                                        <?php echo formatCurrency(abs($difference)); ?>
                                                        <?php if ($difference < 0): ?>
                                                            <i class="fas fa-arrow-up"></i>
                                                        <?php else: ?>
                                                            <i class="fas fa-arrow-down"></i>
                                                        <?php endif; ?>
                                                    </small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Income Sources Breakdown -->
        <div class="row mb-4">
            <div class="col">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-arrow-up me-2"></i>Income Sources Breakdown
                        </h5>
                    </div>
                    <div class="card-body">
                        <?php if (empty($incomeSources)): ?>
                            <div class="text-center py-4">
                                <i class="fas fa-arrow-up fa-3x text-muted mb-3"></i>
                                <p class="text-muted">No income sources found.</p>
                            </div>
                        <?php else: ?>
                            <div class="row">
                                <?php foreach ($incomeSources as $income): ?>
                                    <?php 
                                    $percentage = $totalIncome > 0 ? ($income['amount'] / $totalIncome) * 100 : 0;
                                    ?>
                                    <div class="col-md-6 col-lg-4 mb-3">
                                        <div class="card border-0 shadow-sm">
                                            <div class="card-body text-center">
                                                <h6 class="card-title"><?php echo htmlspecialchars($income['name']); ?></h6>
                                                <h4 class="text-success mb-2"><?php echo formatCurrency($income['amount']); ?></h4>
                                                <div class="progress mb-2" style="height: 8px;">
                                                    <div class="progress-bar bg-success" style="width: <?php echo $percentage; ?>%"></div>
                                                </div>
                                                <small class="text-muted"><?php echo number_format($percentage, 1); ?>% of total income</small>
                                                <br>
                                                <small class="text-muted">
                                                    <?php if ($income['schedule_type'] === 'monthly'): ?>
                                                        <i class="fas fa-calendar me-1"></i>Monthly
                                                    <?php else: ?>
                                                        <i class="fas fa-calendar-day me-1"></i><?php echo $income['schedule_day']; ?><?php echo getOrdinalSuffix($income['schedule_day']); ?>
                                                    <?php endif; ?>
                                                </small>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Income vs Expenses Chart
        const incomeExpensesCtx = document.getElementById('incomeExpensesChart').getContext('2d');
        new Chart(incomeExpensesCtx, {
            type: 'bar',
            data: {
                labels: ['Income', 'Budgeted Expenses', 'Actual Expenses'],
                datasets: [{
                    label: 'Amount (₱)',
                    data: [
                        <?php echo $totalIncome; ?>,
                        <?php echo $totalBudgetedExpenses; ?>,
                        <?php echo $totalActualExpenses; ?>
                    ],
                    backgroundColor: [
                        'rgba(40, 167, 69, 0.8)',
                        'rgba(220, 53, 69, 0.8)',
                        'rgba(255, 193, 7, 0.8)'
                    ],
                    borderColor: [
                        'rgba(40, 167, 69, 1)',
                        'rgba(220, 53, 69, 1)',
                        'rgba(255, 193, 7, 1)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return '₱' + value.toLocaleString();
                            }
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    }
                }
            }
        });

        // Expense Categories Chart
        const expenseCategoriesCtx = document.getElementById('expenseCategoriesChart').getContext('2d');
        new Chart(expenseCategoriesCtx, {
            type: 'doughnut',
            data: {
                labels: [
                    <?php 
                    $labels = [];
                    $data = [];
                    $colors = [];
                    foreach ($expensesByCategory as $categoryName => $categoryData) {
                        $labels[] = "'" . addslashes($categoryName) . "'";
                        $data[] = $categoryData['budgeted'];
                        $colors[] = "'" . sprintf('#%06X', mt_rand(0, 0xFFFFFF)) . "'";
                    }
                    echo implode(', ', $labels);
                    ?>
                ],
                datasets: [{
                    data: [<?php echo implode(', ', $data); ?>],
                    backgroundColor: [<?php echo implode(', ', $colors); ?>],
                    borderWidth: 2,
                    borderColor: '#fff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 20,
                            usePointStyle: true
                        }
                    }
                }
            }
        });

        // Monthly Comparison Bar Chart
        const monthlyComparisonCtx = document.getElementById('monthlyComparisonChart').getContext('2d');
        new Chart(monthlyComparisonCtx, {
            type: 'bar',
            data: {
                labels: ['<?php echo $monthNames[$previousMonth] . ' ' . $previousYear; ?>', 
                         '<?php echo $monthNames[$currentMonthNum] . ' ' . $currentYear; ?>', 
                         '<?php echo $monthNames[$nextMonth] . ' ' . $nextYear; ?>'],
                datasets: [{
                    label: 'Income',
                    data: [<?php echo $previousMonthData['income']; ?>, 
                           <?php echo $currentMonthData['income']; ?>, 
                           <?php echo $nextMonthData['income']; ?>],
                    backgroundColor: 'rgba(40, 167, 69, 0.8)',
                    borderColor: 'rgba(40, 167, 69, 1)',
                    borderWidth: 1,
                    yAxisID: 'y'
                }, {
                    label: 'Expenses',
                    data: [<?php echo $previousMonthData['expenses']; ?>, 
                           <?php echo $currentMonthData['expenses']; ?>, 
                           <?php echo $nextMonthData['expenses']; ?>],
                    backgroundColor: 'rgba(220, 53, 69, 0.8)',
                    borderColor: 'rgba(220, 53, 69, 1)',
                    borderWidth: 1,
                    yAxisID: 'y'
                }, {
                    label: 'Savings',
                    data: [<?php echo $previousMonthData['savings']; ?>, 
                           <?php echo $currentMonthData['savings']; ?>, 
                           <?php echo $nextMonthData['savings']; ?>],
                    backgroundColor: 'rgba(0, 123, 255, 0.8)',
                    borderColor: 'rgba(0, 123, 255, 1)',
                    borderWidth: 1,
                    yAxisID: 'y'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    mode: 'index',
                    intersect: false,
                },
                scales: {
                    x: {
                        display: true,
                        title: {
                            display: true,
                            text: 'Month'
                        }
                    },
                    y: {
                        type: 'linear',
                        display: true,
                        position: 'left',
                        title: {
                            display: true,
                            text: 'Amount (₱)'
                        },
                        ticks: {
                            callback: function(value) {
                                return '₱' + value.toLocaleString();
                            }
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: true,
                        position: 'top'
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return context.dataset.label + ': ₱' + context.parsed.y.toLocaleString();
                            }
                        }
                    }
                }
            }
        });
    </script>

<?php 
// Handle path for router vs direct access
if (defined('USING_ROUTER')) {
    include __DIR__ . '/../includes/footer.php';
} else {
    include '../includes/footer.php';
}
?>

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

/**
 * Get data for a specific month
 */
function getMonthData($month, $year) {
    // Get month ID
    $monthId = fetchOne("SELECT id FROM months WHERE month = ? AND year = ?", [$month, $year]);
    
    // If no month exists in database, return zero values (for months like July when we just started)
    if (!$monthId) {
        return [
            'income' => 0,
            'expenses' => 0,
            'savings' => 0,
            'categories' => []
        ];
    }
    
    $monthId = $monthId['id'];
    
    // Get income
    $income = fetchOne("SELECT COALESCE(SUM(amount), 0) as total FROM income_sources WHERE month_id = ?", [$monthId]);
    $income = $income['total'] ?? 0;
    
    // Get budgeted expenses
    $budgetedExpenses = fetchOne("SELECT COALESCE(SUM(budgeted_amount), 0) as total FROM expenses WHERE month_id = ?", [$monthId]);
    $budgetedExpenses = $budgetedExpenses['total'] ?? 0;
    
    // Get actual expenses
    $actualExpenses = fetchOne("SELECT COALESCE(SUM(actual_amount), 0) as total FROM actual_expenses WHERE month_id = ?", [$monthId]);
    $actualExpenses = $actualExpenses['total'] ?? 0;
    
    // Use actual expenses if available, otherwise use budgeted
    $expenses = $actualExpenses > 0 ? $actualExpenses : $budgetedExpenses;
    
    return [
        'income' => $income,
        'expenses' => $expenses,
        'savings' => $income - $expenses,
        'categories' => []
    ];
}

/**
 * Get forecast data for next month
 */
function getForecastData($month, $year) {
    // For forecasting, we'll use the current month's data as a base
    $currentMonth = date('n');
    $currentYear = date('Y');
    
    $currentData = getMonthData($currentMonth, $currentYear);
    
    // Simple forecast: use current month data with small variations
    $incomeVariation = 1.0; // No change
    $expenseVariation = 1.05; // 5% increase
    
    return [
        'income' => $currentData['income'] * $incomeVariation,
        'expenses' => $currentData['expenses'] * $expenseVariation,
        'savings' => ($currentData['income'] * $incomeVariation) - ($currentData['expenses'] * $expenseVariation),
        'categories' => $currentData['categories']
    ];
}
?>
