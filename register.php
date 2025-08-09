<?php
/**
 * User Registration Page
 * Budget Planner Application
 */

session_start();
require_once 'includes/functions.php';

// Redirect if already logged in
if (isAuthenticated()) {
    redirect('index.php');
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = sanitizeInput($_POST['username']);
    $email = sanitizeInput($_POST['email']);
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirm_password'];
    $pin = $_POST['pin'];
    $confirmPin = $_POST['confirm_pin'];
    
    // Validation
    if (empty($username) || empty($email) || empty($password) || empty($pin)) {
        $error = 'All fields are required.';
    } elseif (strlen($username) < 3) {
        $error = 'Username must be at least 3 characters long.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address.';
    } elseif (strlen($password) < 6) {
        $error = 'Password must be at least 6 characters long.';
    } elseif ($password !== $confirmPassword) {
        $error = 'Passwords do not match.';
    } elseif (strlen($pin) !== 6 || !is_numeric($pin)) {
        $error = 'PIN must be exactly 6 digits.';
    } elseif ($pin !== $confirmPin) {
        $error = 'PINs do not match.';
    } else {
        // Check if username or email already exists
        $existingUser = fetchOne("SELECT id FROM users WHERE username = ? OR email = ?", [$username, $email]);
        
        if ($existingUser) {
            $error = 'Username or email already exists.';
        } else {
            // Create user
            $passwordHash = password_hash($password, PASSWORD_DEFAULT);
            $pinHash = password_hash($pin, PASSWORD_DEFAULT);
            
            $sql = "INSERT INTO users (username, email, password_hash) VALUES (?, ?, ?)";
            if (executeQuery($sql, [$username, $email, $passwordHash])) {
                $userId = getLastInsertId();
                
                // Create security PIN for user
                $pinSql = "INSERT INTO security_pin (user_id, pin_hash) VALUES (?, ?)";
                executeQuery($pinSql, [$userId, $pinHash]);
                
                // Create default expense categories for user
                $defaultCategories = [
                    ['Food & Dining', '#FF6B6B'],
                    ['Transportation', '#4ECDC4'],
                    ['Housing', '#45B7D1'],
                    ['Utilities', '#96CEB4'],
                    ['Entertainment', '#FFEAA7'],
                    ['Healthcare', '#DDA0DD'],
                    ['Shopping', '#98D8C8'],
                    ['Education', '#F7DC6F'],
                    ['Insurance', '#BB8FCE'],
                    ['Savings', '#85C1E9']
                ];
                
                foreach ($defaultCategories as $category) {
                    $catSql = "INSERT INTO expense_categories (name, color, user_id) VALUES (?, ?, ?)";
                    executeQuery($catSql, [$category[0], $category[1], $userId]);
                }
                
                // Create default payment methods for user
                $defaultPaymentMethods = [
                    ['Cash', 'cash', null, 'fas fa-money-bill-wave', '#28a745'],
                    ['BPI Credit Card', 'credit_card', 'BPI', 'fas fa-credit-card', '#007bff'],
                    ['Unionbank Credit Card', 'credit_card', 'Unionbank', 'fas fa-credit-card', '#6f42c1'],
                    ['BDO Credit Card', 'credit_card', 'BDO', 'fas fa-credit-card', '#dc3545'],
                    ['Security Bank Credit Card', 'credit_card', 'Security Bank', 'fas fa-credit-card', '#fd7e14'],
                    ['GCash', 'online', 'GCash', 'fas fa-mobile-alt', '#20c997'],
                    ['BPI Online', 'online', 'BPI', 'fas fa-university', '#007bff'],
                    ['Unionbank Online', 'online', 'Unionbank', 'fas fa-university', '#6f42c1'],
                    ['UNO Digital Bank', 'online', 'UNO', 'fas fa-university', '#e83e8c']
                ];
                
                foreach ($defaultPaymentMethods as $method) {
                    $methodSql = "INSERT INTO payment_methods (name, type, bank_name, icon, color, user_id) VALUES (?, ?, ?, ?, ?, ?)";
                    executeQuery($methodSql, [$method[0], $method[1], $method[2], $method[3], $method[4], $userId]);
                }
                
                // Create default savings accounts for user
                $defaultSavingsAccounts = [
                    ['Cash Savings', 'cash', null, null, 0, 0, 'fas fa-piggy-bank', '#28a745'],
                    ['BPI Savings', 'bank', 'BPI', null, 0, 0, 'fas fa-university', '#007bff'],
                    ['Unionbank Savings', 'bank', 'Unionbank', null, 0, 0, 'fas fa-university', '#6f42c1'],
                    ['GCash Wallet', 'digital', 'GCash', null, 0, 0, 'fas fa-mobile-alt', '#20c997']
                ];
                
                foreach ($defaultSavingsAccounts as $account) {
                    $accountSql = "INSERT INTO savings_accounts (name, type, bank_name, account_number, current_balance, target_balance, icon, color, user_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
                    executeQuery($accountSql, [$account[0], $account[1], $account[2], $account[3], $account[4], $account[5], $account[6], $account[7], $userId]);
                }
                
                $success = 'Account created successfully! Please wait for admin approval before you can login.';
                
                // Notify all approved users (admins) about new registration
                $adminUsers = fetchAll("SELECT id FROM users WHERE is_approved = 1");
                foreach ($adminUsers as $admin) {
                    createNotification(
                        $admin['id'],
                        'registration',
                        'New User Registration',
                        "User '$username' has registered and is waiting for approval."
                    );
                }
            } else {
                $error = 'Failed to create account. Please try again.';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - NR BUDGET Planner</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
        }
        .register-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
        }
        .keypad {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 10px;
            margin-top: 10px;
        }
        .keypad button {
            padding: 15px;
            font-size: 18px;
            border: 1px solid #ddd;
            background: #f8f9fa;
            border-radius: 10px;
            cursor: pointer;
            transition: all 0.3s;
        }
        .keypad button:hover {
            background: #e9ecef;
            transform: translateY(-2px);
        }
        .keypad button:active {
            transform: translateY(0);
        }
        .pin-display {
            background: #f8f9fa;
            border: 2px solid #dee2e6;
            border-radius: 10px;
            padding: 15px;
            text-align: center;
            font-size: 24px;
            font-weight: bold;
            letter-spacing: 5px;
            margin-bottom: 15px;
        }
        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">
                <div class="register-card p-5">
                    <div class="text-center mb-4">
                        <h1 class="h3 mb-3">
                            <i class="fas fa-user-plus text-primary me-2"></i>Create Account
                        </h1>
                        <p class="text-muted">Join NR BUDGET Planner to start tracking your finances</p>
                    </div>

                    <?php if ($error): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-circle me-2"></i><?php echo $error; ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <?php if ($success): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle me-2"></i><?php echo $success; ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                        <div class="text-center">
                            <a href="./login" class="btn btn-primary">
                                <i class="fas fa-sign-in-alt me-2"></i>Go to Login
                            </a>
                        </div>
                    <?php else: ?>
                        <form method="POST" id="registerForm">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="username" class="form-label">Username</label>
                                        <input type="text" class="form-control" id="username" name="username" 
                                               value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>" 
                                               required>
                                        <small class="text-muted">At least 3 characters</small>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="email" class="form-label">Email</label>
                                        <input type="email" class="form-control" id="email" name="email" 
                                               value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>" 
                                               required>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="password" class="form-label">Password</label>
                                        <input type="password" class="form-control" id="password" name="password" required>
                                        <small class="text-muted">At least 6 characters</small>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="confirm_password" class="form-label">Confirm Password</label>
                                        <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">6-Digit Security PIN</label>
                                <div class="pin-display" id="pinDisplay">••••••</div>
                                <input type="hidden" id="pin" name="pin" required>
                                <input type="hidden" id="confirm_pin" name="confirm_pin" required>
                                <small class="text-muted">This PIN will be used to access your budget data</small>
                            </div>

                            <div class="keypad">
                                <button type="button" class="keypad-btn" data-value="1">1</button>
                                <button type="button" class="keypad-btn" data-value="2">2</button>
                                <button type="button" class="keypad-btn" data-value="3">3</button>
                                <button type="button" class="keypad-btn" data-value="4">4</button>
                                <button type="button" class="keypad-btn" data-value="5">5</button>
                                <button type="button" class="keypad-btn" data-value="6">6</button>
                                <button type="button" class="keypad-btn" data-value="7">7</button>
                                <button type="button" class="keypad-btn" data-value="8">8</button>
                                <button type="button" class="keypad-btn" data-value="9">9</button>
                                <button type="button" class="keypad-btn" data-value="0">0</button>
                                <button type="button" id="clearPin" class="btn btn-outline-secondary">Clear</button>
                                <button type="button" id="confirmPin" class="btn btn-outline-primary">Confirm</button>
                            </div>

                            <div class="d-grid gap-2 mt-4">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="fas fa-user-plus me-2"></i>Create Account
                                </button>
                            </div>
                        </form>

                        <div class="text-center mt-4">
                            <p class="text-muted">Already have an account?</p>
                            <a href="./login" class="btn btn-outline-primary">
                                <i class="fas fa-sign-in-alt me-2"></i>Login
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        let pin = '';
        let confirmPinMode = false;
        let confirmPinValue = '';

        // PIN keypad functionality
        document.querySelectorAll('.keypad-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const value = this.getAttribute('data-value');
                if (pin.length < 6) {
                    pin += value;
                    updatePinDisplay();
                }
            });
        });

        document.getElementById('clearPin').addEventListener('click', function() {
            pin = '';
            confirmPinMode = false;
            confirmPinValue = '';
            updatePinDisplay();
        });

        document.getElementById('confirmPin').addEventListener('click', function() {
            if (pin.length === 6) {
                if (!confirmPinMode) {
                    confirmPinMode = true;
                    confirmPinValue = pin;
                    pin = '';
                    updatePinDisplay();
                    this.textContent = 'Set PIN';
                    this.classList.remove('btn-outline-primary');
                    this.classList.add('btn-success');
                } else {
                    if (pin === confirmPinValue) {
                        document.getElementById('pin').value = pin;
                        document.getElementById('confirm_pin').value = pin;
                        this.textContent = 'PIN Set ✓';
                        this.classList.remove('btn-success');
                        this.classList.add('btn-outline-success');
                        this.disabled = true;
                    } else {
                        alert('PINs do not match. Please try again.');
                        pin = '';
                        confirmPinMode = false;
                        confirmPinValue = '';
                        updatePinDisplay();
                        this.textContent = 'Confirm';
                        this.classList.remove('btn-success');
                        this.classList.add('btn-outline-primary');
                    }
                }
            } else {
                alert('Please enter a 6-digit PIN.');
            }
        });

        function updatePinDisplay() {
            const display = document.getElementById('pinDisplay');
            if (confirmPinMode) {
                display.textContent = 'Confirm PIN';
            } else {
                display.textContent = pin.padEnd(6, '•');
            }
        }

        // Form validation
        document.getElementById('registerForm').addEventListener('submit', function(e) {
            const pin = document.getElementById('pin').value;
            const confirmPin = document.getElementById('confirm_pin').value;
            
            if (!pin || pin.length !== 6) {
                e.preventDefault();
                alert('Please set a 6-digit PIN.');
                return false;
            }
            
            if (pin !== confirmPin) {
                e.preventDefault();
                alert('PINs do not match.');
                return false;
            }
        });
    </script>
</body>
</html>
