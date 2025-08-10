<?php
/**
 * User Approvals Page
 * Budget Planner Application
 */

session_start();

// Handle path for router vs direct access
if (defined('USING_ROUTER')) {
    require_once __DIR__ . '/../includes/functions.php';
} else {
    require_once '../includes/functions.php';
}

// Require authentication and admin privileges
requireAuth();
if (!isAdmin()) {
    redirect('../index.php');
}

// Define constants for header inclusion
define('INCLUDED_FROM_INDEX', true);

// Handle actions
$action = $_GET['action'] ?? 'list';
$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($action === 'approve') {
        $userId = sanitizeInput($_POST['user_id'], 'int');
        
        if (approveUser($userId)) {
            // Notify the user that their account has been approved
            $user = fetchOne("SELECT username, email FROM users WHERE id = ?", [$userId]);
            if ($user) {
                createNotification(
                    $userId,
                    'approval',
                    'Account Approved',
                    'Your account has been approved! You can now login to NR BUDGET Planner.'
                );
            }
            $success = 'User approved successfully!';
        } else {
            $error = 'Failed to approve user.';
        }
    } elseif ($action === 'deny') {
        $userId = sanitizeInput($_POST['user_id'], 'int');
        
        if (denyUser($userId)) {
            $success = 'User registration denied and removed.';
        } else {
            $error = 'Failed to deny user.';
        }
    }
}

// Get pending registrations
$pendingRegistrations = getPendingRegistrations();

// Set page title
$pageTitle = 'User Approvals - NR BUDGET Planner';

// Include header component
if (defined('USING_ROUTER')) {
    include __DIR__ . '/../includes/header.php';
} else {
    include '../includes/header.php';
}
?>

<div class="row mb-4">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center">
            <h1 class="h3 mb-0">
                <i class="fas fa-users-cog me-2"></i>User Approvals
            </h1>
        </div>
    </div>
</div>

<?php if (empty($pendingRegistrations)): ?>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body text-center py-5">
                    <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                    <h5>No Pending Approvals</h5>
                    <p class="text-muted">All user registrations have been processed.</p>
                </div>
            </div>
        </div>
    </div>
<?php else: ?>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-clock me-2"></i>Pending User Registrations
                        <span class="badge bg-warning text-dark ms-2"><?php echo count($pendingRegistrations); ?></span>
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Username</th>
                                    <th>Email</th>
                                    <th>Registration Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($pendingRegistrations as $user): ?>
                                <tr>
                                    <td>
                                        <strong><?php echo htmlspecialchars($user['username']); ?></strong>
                                    </td>
                                    <td><?php echo htmlspecialchars($user['email']); ?></td>
                                    <td>
                                        <i class="fas fa-calendar me-1"></i>
                                        <?php echo date('M j, Y g:i A', strtotime($user['created_at'])); ?>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <button class="btn btn-success" onclick="approveUser(<?php echo $user['id']; ?>, '<?php echo htmlspecialchars($user['username']); ?>')">
                                                <i class="fas fa-check me-1"></i>Approve
                                            </button>
                                            <button class="btn btn-danger" onclick="denyUser(<?php echo $user['id']; ?>, '<?php echo htmlspecialchars($user['username']); ?>')">
                                                <i class="fas fa-times me-1"></i>Deny
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
    </div>
<?php endif; ?>

<!-- Approve User Modal -->
<div class="modal fade" id="approveUserModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Approve User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="?action=approve">
                <div class="modal-body">
                    <input type="hidden" id="approve_user_id" name="user_id">
                    <p>Are you sure you want to approve <strong id="approve_username"></strong>?</p>
                    <p class="text-muted">This will allow the user to login to NR BUDGET Planner.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-check me-1"></i>Approve User
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Deny User Modal -->
<div class="modal fade" id="denyUserModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Deny User Registration</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="?action=deny">
                <div class="modal-body">
                    <input type="hidden" id="deny_user_id" name="user_id">
                    <p>Are you sure you want to deny <strong id="deny_username"></strong>?</p>
                    <p class="text-danger">This action will permanently delete the user's registration and cannot be undone.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-times me-1"></i>Deny Registration
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function approveUser(userId, username) {
    document.getElementById('approve_user_id').value = userId;
    document.getElementById('approve_username').textContent = username;
    new bootstrap.Modal(document.getElementById('approveUserModal')).show();
}

function denyUser(userId, username) {
    document.getElementById('deny_user_id').value = userId;
    document.getElementById('deny_username').textContent = username;
    new bootstrap.Modal(document.getElementById('denyUserModal')).show();
}
</script>

<?php
// Include footer component
if (defined('USING_ROUTER')) {
    include __DIR__ . '/../includes/footer.php';
} else {
    include '../includes/footer.php';
}
?>
