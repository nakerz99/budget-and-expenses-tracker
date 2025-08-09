<?php
/**
 * Savings Management Page
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

$messages = getMessages();
$currentMonth = getCurrentMonth();
$savingsAccounts = getSavingsAccounts();

// Calculate monthly savings
$totalIncome = getTotalIncome();
$totalBudgetedExpenses = getTotalBudgetedExpenses();
$totalActualExpenses = getTotalActualExpenses();
$monthlySavings = $totalIncome - $totalActualExpenses;
$budgetedSavings = $totalIncome - $totalBudgetedExpenses;

// Get total actual savings across all accounts
$totalActualSavings = getTotalSavings();
$savingsProgress = getSavingsProgress();

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    switch ($action) {
        case 'add_savings_account':
            $data = [
                'name' => $_POST['name'],
                'type' => $_POST['type'],
                'bank_name' => $_POST['bank_name'],
                'account_number' => $_POST['account_number'],
                'current_balance' => $_POST['current_balance'],
                'target_balance' => $_POST['target_balance'],
                'icon' => $_POST['icon'],
                'color' => $_POST['color']
            ];
            
            if (addSavingsAccount($data)) {
                showSuccess('Savings account added successfully!');
            } else {
                showError('Failed to add savings account.');
            }
            redirect('savings.php');
            break;
            
        case 'update_savings_account':
            $id = $_POST['id'];
            $data = [
                'name' => $_POST['name'],
                'type' => $_POST['type'],
                'bank_name' => $_POST['bank_name'],
                'account_number' => $_POST['account_number'],
                'current_balance' => $_POST['current_balance'],
                'target_balance' => $_POST['target_balance'],
                'icon' => $_POST['icon'],
                'color' => $_POST['color']
            ];
            
            if (updateSavingsAccount($id, $data)) {
                showSuccess('Savings account updated successfully!');
            } else {
                showError('Failed to update savings account.');
            }
            redirect('savings.php');
            break;
            
        case 'delete_savings_account':
            $id = $_POST['id'];
            if (deleteSavingsAccount($id)) {
                showSuccess('Savings account deleted successfully!');
            } else {
                showError('Failed to delete savings account.');
            }
            redirect('savings.php');
            break;
    }
}

// Get savings account for editing (if edit mode)
$editSavingsAccount = null;
if (isset($_GET['edit'])) {
    $editSavingsAccount = getSavingsAccountById($_GET['edit']);
}
?>

<?php
$pageTitle = 'Savings Management - NR BUDGET Planner';

// Handle path for router vs direct access
if (defined('USING_ROUTER')) {
    include __DIR__ . '/../includes/header.php';
} else {
    include '../includes/header.php';
}
?>

<style>
    .savings-card {
        transition: transform 0.2s;
    }
    .savings-card:hover {
        transform: translateY(-2px);
    }
    .progress-circle {
        width: 120px;
        height: 120px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto;
        position: relative;
    }
    .progress-circle::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        border-radius: 50%;
        background: conic-gradient(var(--progress-color) 0deg, #e9ecef 0deg);
        transform: rotate(-90deg);
    }
    .progress-circle .content {
        background: white;
        width: 80px;
        height: 80px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        position: relative;
        z-index: 1;
    }
</style>

    <div class="container mt-4">
        <!-- Messages -->
        <?php if ($messages['success']): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?= htmlspecialchars($messages['success']) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if ($messages['error']): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?= htmlspecialchars($messages['error']) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <!-- Header -->
        <div class="row mb-4">
            <div class="col">
                <h1 class="h3 mb-0">
                    <i class="fas fa-piggy-bank text-success me-2"></i>Savings Management
                </h1>
                <p class="text-muted"><?php echo $currentMonth['name']; ?> Savings Overview</p>
            </div>
            <div class="col-auto">
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addSavingsAccountModal">
                    <i class="fas fa-plus me-2"></i>Add Savings Account
                </button>
            </div>
        </div>

        <!-- Total Savings -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-body text-center">
                        <h4 class="text-muted mb-3">Total Savings</h4>
                        <div class="display-4 text-success mb-2"><?php echo formatCurrency($totalActualSavings); ?></div>
                        <p class="text-muted">Across all savings accounts</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Savings Accounts -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-university me-2"></i>Savings Accounts
                        </h5>
                    </div>
                    <div class="card-body">
                        <?php if (empty($savingsAccounts)): ?>
                            <div class="text-center py-5">
                                <i class="fas fa-piggy-bank fa-3x text-muted mb-3"></i>
                                <h4 class="text-muted">No Savings Accounts</h4>
                                <p class="text-muted">Create your first savings account to start tracking your savings.</p>
                                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addSavingsAccountModal">
                                    <i class="fas fa-plus me-2"></i>Add Savings Account
                                </button>
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Account</th>
                                            <th>Type</th>
                                            <th>Current Balance</th>
                                            <th>Target Balance</th>
                                            <th>Progress</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($savingsAccounts as $account): ?>
                                            <?php 
                                            $accountProgress = $account['target_balance'] > 0 ? ($account['current_balance'] / $account['target_balance']) * 100 : 0;
                                            $progressClass = $accountProgress >= 100 ? 'bg-success' : 'bg-primary';
                                            ?>
                                            <tr>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="me-2" style="color: <?php echo $account['color']; ?>;">
                                                            <i class="<?php echo $account['icon']; ?>"></i>
                                                        </div>
                                                        <div>
                                                            <strong><?php echo htmlspecialchars($account['name']); ?></strong><br>
                                                            <small class="text-muted"><?php echo htmlspecialchars($account['bank_name']); ?></small>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <span class="badge bg-secondary"><?php echo ucfirst($account['type']); ?></span>
                                                </td>
                                                <td>
                                                    <strong class="text-primary"><?php echo formatCurrency($account['current_balance']); ?></strong>
                                                </td>
                                                <td>
                                                    <span class="text-muted"><?php echo formatCurrency($account['target_balance']); ?></span>
                                                </td>
                                                <td>
                                                    <div class="progress" style="height: 6px; width: 100px;">
                                                        <div class="progress-bar <?php echo $progressClass; ?>" 
                                                             style="width: <?php echo min($accountProgress, 100); ?>%"></div>
                                                    </div>
                                                    <small class="text-muted"><?php echo number_format($accountProgress, 1); ?>%</small>
                                                </td>
                                                <td>
                                                    <div class="btn-group btn-group-sm">
                                                        <a href="?edit=<?php echo $account['id']; ?>" class="btn btn-outline-primary btn-sm">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                        <form method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this savings account?')">
                                                            <input type="hidden" name="action" value="delete_savings_account">
                                                            <input type="hidden" name="id" value="<?php echo $account['id']; ?>">
                                                            <button type="submit" class="btn btn-outline-danger btn-sm">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        </form>
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
    </div>

    <!-- Add Savings Account Modal -->
    <div class="modal fade" id="addSavingsAccountModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-plus text-primary me-2"></i>Add Savings Account
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <input type="hidden" name="action" value="add_savings_account">
                        
                        <div class="mb-3">
                            <label for="name" class="form-label">Account Name</label>
                            <input type="text" class="form-control" id="name" name="name" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="type" class="form-label">Account Type</label>
                            <select class="form-select" id="type" name="type" required>
                                <option value="">Select Type</option>
                                <option value="cash">Cash</option>
                                <option value="bank">Bank</option>
                                <option value="digital">Digital (GCash, etc.)</option>
                                <option value="investment">Investment</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label for="bank_name" class="form-label">Bank/Provider Name</label>
                            <input type="text" class="form-control" id="bank_name" name="bank_name" 
                                   placeholder="e.g., BPI, Unionbank, GCash, etc.">
                        </div>
                        
                        <div class="mb-3">
                            <label for="account_number" class="form-label">Account Number/ID</label>
                            <input type="text" class="form-control" id="account_number" name="account_number" 
                                   placeholder="Optional">
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="current_balance" class="form-label">Current Balance</label>
                                    <div class="input-group">
                                        <span class="input-group-text">₱</span>
                                        <input type="number" class="form-control" id="current_balance" name="current_balance" 
                                               step="0.01" min="0" value="0.00" required>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="target_balance" class="form-label">Target Balance</label>
                                    <div class="input-group">
                                        <span class="input-group-text">₱</span>
                                        <input type="number" class="form-control" id="target_balance" name="target_balance" 
                                               step="0.01" min="0" value="0.00" required>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="icon" class="form-label">Icon</label>
                            <input type="text" class="form-control" id="icon" name="icon" value="fas fa-university" required>
                            <small class="form-text text-muted">Font Awesome icon class</small>
                        </div>
                        
                        <div class="mb-3">
                            <label for="color" class="form-label">Color</label>
                            <input type="color" class="form-control form-control-color" id="color" name="color" value="#007bff">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Add Account</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Savings Account Modal -->
    <?php if ($editSavingsAccount): ?>
    <div class="modal fade show" id="editSavingsAccountModal" tabindex="-1" style="display: block;">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-edit text-primary me-2"></i>Edit Savings Account
                    </h5>
                    <a href="savings.php" class="btn-close"></a>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <input type="hidden" name="action" value="update_savings_account">
                        <input type="hidden" name="id" value="<?php echo $editSavingsAccount['id']; ?>">
                        
                        <div class="mb-3">
                            <label for="edit_name" class="form-label">Account Name</label>
                            <input type="text" class="form-control" id="edit_name" name="name" 
                                   value="<?php echo htmlspecialchars($editSavingsAccount['name']); ?>" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="edit_type" class="form-label">Account Type</label>
                            <select class="form-select" id="edit_type" name="type" required>
                                <option value="">Select Type</option>
                                <option value="cash" <?php echo $editSavingsAccount['type'] === 'cash' ? 'selected' : ''; ?>>Cash</option>
                                <option value="bank" <?php echo $editSavingsAccount['type'] === 'bank' ? 'selected' : ''; ?>>Bank</option>
                                <option value="digital" <?php echo $editSavingsAccount['type'] === 'digital' ? 'selected' : ''; ?>>Digital (GCash, etc.)</option>
                                <option value="investment" <?php echo $editSavingsAccount['type'] === 'investment' ? 'selected' : ''; ?>>Investment</option>
                                <option value="other" <?php echo $editSavingsAccount['type'] === 'other' ? 'selected' : ''; ?>>Other</option>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label for="edit_bank_name" class="form-label">Bank/Provider Name</label>
                            <input type="text" class="form-control" id="edit_bank_name" name="bank_name" 
                                   value="<?php echo htmlspecialchars($editSavingsAccount['bank_name']); ?>" 
                                   placeholder="e.g., BPI, Unionbank, GCash, etc.">
                        </div>
                        
                        <div class="mb-3">
                            <label for="edit_account_number" class="form-label">Account Number/ID</label>
                            <input type="text" class="form-control" id="edit_account_number" name="account_number" 
                                   value="<?php echo htmlspecialchars($editSavingsAccount['account_number']); ?>" 
                                   placeholder="Optional">
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="edit_current_balance" class="form-label">Current Balance</label>
                                    <div class="input-group">
                                        <span class="input-group-text">₱</span>
                                        <input type="number" class="form-control" id="edit_current_balance" name="current_balance" 
                                               step="0.01" min="0" value="<?php echo $editSavingsAccount['current_balance']; ?>" required>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="edit_target_balance" class="form-label">Target Balance</label>
                                    <div class="input-group">
                                        <span class="input-group-text">₱</span>
                                        <input type="number" class="form-control" id="edit_target_balance" name="target_balance" 
                                               step="0.01" min="0" value="<?php echo $editSavingsAccount['target_balance']; ?>" required>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="edit_icon" class="form-label">Icon</label>
                            <input type="text" class="form-control" id="edit_icon" name="icon" 
                                   value="<?php echo htmlspecialchars($editSavingsAccount['icon']); ?>" required>
                            <small class="form-text text-muted">Font Awesome icon class</small>
                        </div>
                        
                        <div class="mb-3">
                            <label for="edit_color" class="form-label">Color</label>
                            <input type="color" class="form-control form-control-color" id="edit_color" name="color" 
                                   value="<?php echo htmlspecialchars($editSavingsAccount['color']); ?>">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <a href="savings.php" class="btn btn-secondary">Cancel</a>
                        <button type="submit" class="btn btn-primary">Update Account</button>
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
