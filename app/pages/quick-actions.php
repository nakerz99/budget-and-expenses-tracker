<?php
session_start();

// Handle path for router vs direct access
if (defined('USING_ROUTER')) {
    require_once __DIR__ . '/../includes/functions.php';
} else {
    require_once '../includes/functions.php';
}

// Require authentication
requireAuth();

// Messages are handled by header.php
$quickActions = getQuickActions();
$expenseCategories = getExpenseCategories();

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    switch ($action) {
        case 'add':
            $data = [
                'user_id' => 1,
                'name' => $_POST['name'],
                'category_id' => $_POST['category_id'],
                'amount' => $_POST['amount'],
                'icon' => $_POST['icon'],
                'color' => $_POST['color']
            ];
            
            if (addQuickAction($data)) {
                showSuccess('Quick action added successfully!');
            } else {
                showError('Failed to add quick action.');
            }
            redirect('quick-actions.php');
            break;
            
        case 'update':
            $id = $_POST['id'];
            $data = [
                'name' => $_POST['name'],
                'category_id' => $_POST['category_id'],
                'amount' => $_POST['amount'],
                'icon' => $_POST['icon'],
                'color' => $_POST['color']
            ];
            
            if (updateQuickAction($id, $data)) {
                showSuccess('Quick action updated successfully!');
            } else {
                showError('Failed to update quick action.');
            }
            redirect('quick-actions.php');
            break;
            
        case 'delete':
            $id = $_POST['id'];
            if (deleteQuickAction($id)) {
                showSuccess('Quick action deleted successfully!');
            } else {
                showError('Failed to delete quick action.');
            }
            redirect('quick-actions.php');
            break;
            
        case 'toggle':
            $id = $_POST['id'];
            if (toggleQuickAction($id)) {
                showSuccess('Quick action status updated!');
            } else {
                showError('Failed to update quick action status.');
            }
            redirect('quick-actions.php');
            break;
    }
}

// Get quick action for editing
$editQuickAction = null;
if (isset($_GET['edit'])) {
    $editQuickAction = getQuickActionById($_GET['edit']);
}
?>

<?php
$pageTitle = 'Quick Actions - NR BUDGET Planner';

// Handle path for router vs direct access
if (defined('USING_ROUTER')) {
    include __DIR__ . '/../includes/header.php';
} else {
    include '../includes/header.php';
}
?>

<style>
    .quick-action-card {
        transition: transform 0.2s;
    }
    .quick-action-card:hover {
        transform: translateY(-2px);
    }
    .icon-preview {
        width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 8px;
        margin-right: 10px;
    }
    .status-badge {
        position: absolute;
        top: 10px;
        right: 10px;
    }
</style>

    <div class="container mt-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">
                        <i class="fas fa-bolt text-warning me-2"></i>Quick Actions
                    </h1>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addQuickActionModal">
                        <i class="fas fa-plus me-2"></i>Add Quick Action
                    </button>
                </div>

                <!-- Quick Actions Grid -->
                <div class="row">
                    <?php foreach ($quickActions as $action): ?>
                        <div class="col-md-6 col-lg-4 mb-4">
                            <div class="card quick-action-card h-100 position-relative">
                                <div class="card-body">
                                    <div class="d-flex align-items-center mb-3">
                                        <div class="icon-preview" style="background-color: <?= htmlspecialchars($action['color']) ?>; color: white;">
                                            <i class="<?= htmlspecialchars($action['icon']) ?>"></i>
                                        </div>
                                        <div>
                                            <h6 class="card-title mb-1"><?= htmlspecialchars($action['name']) ?></h6>
                                            <small class="text-muted"><?= htmlspecialchars($action['category_name']) ?></small>
                                        </div>
                                    </div>
                                    
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="h5 text-primary mb-0"><?= formatCurrency($action['amount']) ?></span>
                                        <div class="btn-group btn-group-sm">
                                            <a href="?edit=<?= $action['id'] ?>" class="btn btn-outline-primary btn-sm">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this quick action?')">
                                                <input type="hidden" name="action" value="delete">
                                                <input type="hidden" name="id" value="<?= $action['id'] ?>">
                                                <button type="submit" class="btn btn-outline-danger btn-sm">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Status Badge -->
                                <div class="status-badge">
                                    <form method="POST" class="d-inline">
                                        <input type="hidden" name="action" value="toggle">
                                        <input type="hidden" name="id" value="<?= $action['id'] ?>">
                                        <button type="submit" class="btn btn-sm <?= $action['is_active'] ? 'btn-success' : 'btn-secondary' ?>">
                                            <i class="fas <?= $action['is_active'] ? 'fa-check' : 'fa-times' ?>"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <?php if (empty($quickActions)): ?>
                    <div class="text-center py-5">
                        <i class="fas fa-bolt fa-3x text-muted mb-3"></i>
                        <h4 class="text-muted">No Quick Actions</h4>
                        <p class="text-muted">Create your first quick action to speed up expense recording.</p>
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addQuickActionModal">
                            <i class="fas fa-plus me-2"></i>Add Quick Action
                        </button>
                    </div>
                <?php endif; ?>
            </main>
        </div>
    </div>

    <!-- Add Quick Action Modal -->
    <div class="modal fade" id="addQuickActionModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-plus text-primary me-2"></i>Add Quick Action
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <input type="hidden" name="action" value="add">
                        
                        <div class="mb-3">
                            <label for="name" class="form-label">Name</label>
                            <input type="text" class="form-control" id="name" name="name" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="category_id" class="form-label">Category</label>
                            <select class="form-select" id="category_id" name="category_id" required>
                                <option value="">Select Category</option>
                                <?php foreach ($expenseCategories as $category): ?>
                                    <option value="<?= $category['id'] ?>"><?= htmlspecialchars($category['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label for="amount" class="form-label">Amount</label>
                            <div class="input-group">
                                <span class="input-group-text">₱</span>
                                <input type="number" class="form-control" id="amount" name="amount" step="0.01" min="0" required>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="icon" class="form-label">Icon</label>
                            <input type="text" class="form-control" id="icon" name="icon" value="fas fa-receipt" required>
                            <small class="form-text text-muted">Font Awesome icon class (e.g., fas fa-utensils)</small>
                        </div>
                        
                        <div class="mb-3">
                            <label for="color" class="form-label">Color</label>
                            <input type="color" class="form-control form-control-color" id="color" name="color" value="#007bff">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Add Quick Action</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Quick Action Modal -->
    <?php if ($editQuickAction): ?>
    <div class="modal fade show" id="editQuickActionModal" tabindex="-1" style="display: block;">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-edit text-primary me-2"></i>Edit Quick Action
                    </h5>
                    <a href="quick-actions.php" class="btn-close"></a>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <input type="hidden" name="action" value="update">
                        <input type="hidden" name="id" value="<?= $editQuickAction['id'] ?>">
                        
                        <div class="mb-3">
                            <label for="edit_name" class="form-label">Name</label>
                            <input type="text" class="form-control" id="edit_name" name="name" value="<?= htmlspecialchars($editQuickAction['name']) ?>" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="edit_category_id" class="form-label">Category</label>
                            <select class="form-select" id="edit_category_id" name="category_id" required>
                                <option value="">Select Category</option>
                                <?php foreach ($expenseCategories as $category): ?>
                                    <option value="<?= $category['id'] ?>" <?= $category['id'] == $editQuickAction['category_id'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($category['name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label for="edit_amount" class="form-label">Amount</label>
                            <div class="input-group">
                                <span class="input-group-text">₱</span>
                                <input type="number" class="form-control" id="edit_amount" name="amount" step="0.01" min="0" value="<?= $editQuickAction['amount'] ?>" required>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="edit_icon" class="form-label">Icon</label>
                            <input type="text" class="form-control" id="edit_icon" name="icon" value="<?= htmlspecialchars($editQuickAction['icon']) ?>" required>
                            <small class="form-text text-muted">Font Awesome icon class (e.g., fas fa-utensils)</small>
                        </div>
                        
                        <div class="mb-3">
                            <label for="edit_color" class="form-label">Color</label>
                            <input type="color" class="form-control form-control-color" id="edit_color" name="color" value="<?= htmlspecialchars($editQuickAction['color']) ?>">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <a href="quick-actions.php" class="btn btn-secondary">Cancel</a>
                        <button type="submit" class="btn btn-primary">Update Quick Action</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="modal-backdrop fade show"></div>
    <?php endif; ?>
    </div>

<?php 
// Handle path for router vs direct access
if (defined('USING_ROUTER')) {
    include __DIR__ . '/../includes/footer.php';
} else {
    include '../includes/footer.php';
}
?>
