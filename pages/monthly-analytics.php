<?php
/**
 * Monthly Analytics Page
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
$currentMonth = $currentDate->format('n'); // 8 for August
$currentYear = $currentDate->format('Y'); // 2025

// Get previous, current, and next month data
$previousMonth = $currentMonth - 1;
$previousYear = $currentYear;
if ($previousMonth < 1) {
    $previousMonth = 12;
    $previousYear = $currentYear - 1;
}

$nextMonth = $currentMonth + 1;
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
$currentMonthData = getMonthData($currentMonth, $currentYear);
$nextMonthData = getForecastData($nextMonth, $nextYear);

$messages = getMessages();
?>
<?php
$pageTitle = 'Monthly Analytics - NR BUDGET Planner';

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
                    <i class="fas fa-chart-line me-2"></i>Monthly Analytics
                </h1>
                <p class="text-muted">Track your monthly financial performance and forecasts</p>
                <?php if ($previousMonthData['income'] == 0 && $previousMonthData['expenses'] == 0): ?>
                    <div class="alert alert-info mt-2 mb-0 py-2">
                        <i class="fas fa-info-circle me-2"></i>
                        <small>Note: <?php echo $monthNames[$previousMonth] . ' ' . $previousYear; ?> shows zero values because we just started tracking today (<?php echo date('F j, Y'); ?>).</small>
                    </div>
                <?php endif; ?>
            </div>
            <div class="col-auto">
                <span class="badge bg-primary fs-6"><?php echo date('F j, Y'); ?></span>
            </div>
        </div>

        <!-- Monthly Overview Cards -->
        <div class="row mb-4">
            <!-- Previous Month -->
            <div class="col-md-4 mb-3">
                <div class="card h-100">
                    <div class="card-header bg-secondary text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-history me-2"></i><?php echo $monthNames[$previousMonth] . ' ' . $previousYear; ?>
                        </h5>
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
                        <h5 class="mb-0">
                            <i class="fas fa-calendar-check me-2"></i><?php echo $monthNames[$currentMonth] . ' ' . $currentYear; ?>
                        </h5>
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
                <div class="card h-100">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-crystal-ball me-2"></i><?php echo $monthNames[$nextMonth] . ' ' . $nextYear; ?>
                        </h5>
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

        <!-- Charts Row -->
        <div class="row mb-4">
            <!-- Income vs Expenses Chart -->
            <div class="col-lg-8 mb-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-chart-bar me-2"></i>Income vs Expenses Trend</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="incomeExpensesChart" height="100"></canvas>
                    </div>
                </div>
            </div>

            <!-- Savings Trend Chart -->
            <div class="col-lg-4 mb-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-chart-pie me-2"></i>Savings Trend</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="savingsChart" height="100"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Detailed Analysis -->
        <div class="row">
            <!-- Expense Categories Breakdown -->
            <div class="col-lg-6 mb-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-list me-2"></i>Current Month Expense Breakdown</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="expenseCategoriesChart" height="200"></canvas>
                    </div>
                </div>
            </div>

            <!-- Monthly Comparison Table -->
            <div class="col-lg-6 mb-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-table me-2"></i>Monthly Comparison</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Category</th>
                                        <th><?php echo $monthNames[$previousMonth]; ?></th>
                                        <th><?php echo $monthNames[$currentMonth]; ?></th>
                                        <th><?php echo $monthNames[$nextMonth]; ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr class="table-success">
                                        <td><strong>Income</strong></td>
                                        <td><?php echo formatCurrency($previousMonthData['income']); ?></td>
                                        <td><?php echo formatCurrency($currentMonthData['income']); ?></td>
                                        <td><?php echo formatCurrency($nextMonthData['income']); ?></td>
                                    </tr>
                                    <tr class="table-danger">
                                        <td><strong>Expenses</strong></td>
                                        <td><?php echo formatCurrency($previousMonthData['expenses']); ?></td>
                                        <td><?php echo formatCurrency($currentMonthData['expenses']); ?></td>
                                        <td><?php echo formatCurrency($nextMonthData['expenses']); ?></td>
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
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
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
    // Chart data from PHP
    const chartData = {
        labels: ['<?php echo $monthNames[$previousMonth] . ' ' . $previousYear; ?>', 
                 '<?php echo $monthNames[$currentMonth] . ' ' . $currentYear; ?>', 
                 '<?php echo $monthNames[$nextMonth] . ' ' . $nextYear; ?>'],
        income: [<?php echo $previousMonthData['income']; ?>, 
                 <?php echo $currentMonthData['income']; ?>, 
                 <?php echo $nextMonthData['income']; ?>],
        expenses: [<?php echo $previousMonthData['expenses']; ?>, 
                   <?php echo $currentMonthData['expenses']; ?>, 
                   <?php echo $nextMonthData['expenses']; ?>],
        savings: [<?php echo $previousMonthData['savings']; ?>, 
                  <?php echo $currentMonthData['savings']; ?>, 
                  <?php echo $nextMonthData['savings']; ?>]
    };

    // Income vs Expenses Chart
    const incomeExpensesCtx = document.getElementById('incomeExpensesChart').getContext('2d');
    new Chart(incomeExpensesCtx, {
        type: 'bar',
        data: {
            labels: chartData.labels,
            datasets: [{
                label: 'Income',
                data: chartData.income,
                backgroundColor: 'rgba(40, 167, 69, 0.8)',
                borderColor: 'rgba(40, 167, 69, 1)',
                borderWidth: 1
            }, {
                label: 'Expenses',
                data: chartData.expenses,
                backgroundColor: 'rgba(220, 53, 69, 0.8)',
                borderColor: 'rgba(220, 53, 69, 1)',
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

    // Savings Trend Chart
    const savingsCtx = document.getElementById('savingsChart').getContext('2d');
    new Chart(savingsCtx, {
        type: 'line',
        data: {
            labels: chartData.labels,
            datasets: [{
                label: 'Savings',
                data: chartData.savings,
                borderColor: 'rgba(0, 123, 255, 1)',
                backgroundColor: 'rgba(0, 123, 255, 0.1)',
                borderWidth: 3,
                fill: true,
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    ticks: {
                        callback: function(value) {
                            return '₱' + value.toLocaleString();
                        }
                    }
                }
            },
            plugins: {
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return 'Savings: ₱' + context.parsed.y.toLocaleString();
                        }
                    }
                }
            }
        }
    });

    // Expense Categories Chart (if data available)
    <?php if (!empty($currentMonthData['categories'])): ?>
    const categoriesCtx = document.getElementById('expenseCategoriesChart').getContext('2d');
    new Chart(categoriesCtx, {
        type: 'doughnut',
        data: {
            labels: <?php echo json_encode(array_keys($currentMonthData['categories'])); ?>,
            datasets: [{
                data: <?php echo json_encode(array_values($currentMonthData['categories'])); ?>,
                backgroundColor: [
                    'rgba(255, 99, 132, 0.8)',
                    'rgba(54, 162, 235, 0.8)',
                    'rgba(255, 206, 86, 0.8)',
                    'rgba(75, 192, 192, 0.8)',
                    'rgba(153, 102, 255, 0.8)',
                    'rgba(255, 159, 64, 0.8)'
                ],
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return context.label + ': ₱' + context.parsed.toLocaleString();
                        }
                    }
                }
            }
        }
    });
    <?php endif; ?>
</script>

<?php
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
    
    // Get expense categories breakdown
    $categories = fetchAll("
        SELECT ec.name, COALESCE(SUM(ae.actual_amount), SUM(e.budgeted_amount), 0) as total
        FROM expense_categories ec
        LEFT JOIN expenses e ON ec.id = e.category_id AND e.month_id = ?
        LEFT JOIN actual_expenses ae ON e.id = ae.expense_id AND ae.month_id = ?
        WHERE ec.is_active = 1
        GROUP BY ec.id, ec.name
        HAVING total > 0
        ORDER BY total DESC
    ", [$monthId, $monthId]);
    
    $categoryData = [];
    foreach ($categories as $category) {
        $categoryData[$category['name']] = $category['total'];
    }
    
    return [
        'income' => $income,
        'expenses' => $expenses,
        'savings' => $income - $expenses,
        'categories' => $categoryData
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
