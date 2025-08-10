<?php
/**
 * PIN Login Page
 * Budget Planner Application
 */

session_start();

// Force clear session if requested or if there are authentication issues
if (isset($_GET['clear']) || isset($_GET['reset'])) {
    session_destroy();
    session_start();
    // Redirect to clean login page
    header("Location: login.php");
    exit();
}

// Prevent redirect loops by checking redirect count
if (isset($_SESSION['redirect_count']) && $_SESSION['redirect_count'] > 5) {
    session_destroy();
    session_start();
    $_SESSION['redirect_count'] = 0;
}

require_once '../app/includes/functions.php';

// Check if user is already authenticated
if (isset($_SESSION['authenticated']) && $_SESSION['authenticated'] === true && 
    isset($_SESSION['user_id']) && !empty($_SESSION['user_id'])) {
    // User is authenticated, redirect to dashboard
    header("Location: index.php");
    exit();
}

// Check for session timeout (30 minutes)
if (isset($_SESSION['login_time']) && (time() - $_SESSION['login_time']) > 1800) {
    session_destroy();
    session_start();
}

// Handle login submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = sanitizeInput($_POST['username']);
    $pin = sanitizeInput($_POST['pin']);
    
    if (empty($username) || empty($pin)) {
        $error = 'Username and PIN are required.';
    } elseif (verifyUserPIN($username, $pin)) {
        $_SESSION['authenticated'] = true;
        $_SESSION['user_id'] = getUserIdByUsername($username);
        $_SESSION['username'] = $username;
        $_SESSION['login_time'] = time();
        redirect('index.php');
    } else {
        $error = 'Invalid username or PIN. Please try again.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - NR BUDGET Planner</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        .pin-input {
            letter-spacing: 0.5em;
            text-align: center;
            font-size: 1.5rem;
            font-weight: bold;
        }
        .pin-input:focus {
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
            border-color: #667eea;
        }
        .login-btn {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            border-radius: 50px;
            padding: 12px 40px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        .login-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(102, 126, 234, 0.3);
        }
        .app-icon {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            box-shadow: 0 10px 25px rgba(102, 126, 234, 0.3);
        }
        
        /* PIN Display */
        .pin-display {
            text-align: center;
        }
        
        .pin-dots {
            display: flex;
            justify-content: center;
            gap: 15px;
            margin-bottom: 10px;
        }
        
        .pin-dot {
            width: 20px;
            height: 20px;
            border-radius: 50%;
            border: 2px solid #dee2e6;
            background: transparent;
            transition: all 0.3s ease;
        }
        
        .pin-dot.filled {
            background: #667eea;
            border-color: #667eea;
            box-shadow: 0 0 10px rgba(102, 126, 234, 0.3);
        }
        
        /* Keypad */
        .keypad {
            max-width: 300px;
            margin: 0 auto;
        }
        
        .keypad-row {
            display: flex;
            justify-content: center;
            gap: 10px;
            margin-bottom: 10px;
        }
        
        .keypad-btn {
            width: 60px;
            height: 60px;
            border: none;
            border-radius: 50%;
            background: #f8f9fa;
            color: #495057;
            font-size: 1.2rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }
        
        .keypad-btn:hover {
            background: #e9ecef;
            transform: translateY(-2px);
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15);
        }
        
        .keypad-btn:active {
            transform: translateY(0);
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }
        
        .keypad-btn.clear-btn {
            background: #ffc107;
            color: #212529;
        }
        
        .keypad-btn.clear-btn:hover {
            background: #ffca2c;
        }
        
        .keypad-btn.delete-btn {
            background: #dc3545;
            color: white;
        }
        
        .keypad-btn.delete-btn:hover {
            background: #c82333;
        }
        
        .keypad-btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
            transform: none;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-4">
                <div class="card login-card">
                    <div class="card-body p-5">
                        <div class="text-center mb-4">
                            <div class="app-icon">
                                <i class="fas fa-chart-pie fa-2x text-white"></i>
                            </div>
                            <h2 class="mb-2">NR BUDGET Planner</h2>
                            <p class="text-muted">Enter your 6-digit PIN to continue</p>
                        </div>

                        <?php if (isset($error)): ?>
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <i class="fas fa-exclamation-circle me-2"></i><?php echo $error; ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>

                        <form method="POST" id="pinForm">
                            <div class="mb-3">
                                <label for="username" class="form-label">Username</label>
                                <input type="text" class="form-control" id="username" name="username" 
                                       value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>" 
                                       required>
                            </div>
                            
                            <div class="mb-4">
                                <label class="form-label">Security PIN</label>
                                <div class="pin-display mb-3">
                                    <div class="pin-dots">
                                        <div class="pin-dot" id="dot1"></div>
                                        <div class="pin-dot" id="dot2"></div>
                                        <div class="pin-dot" id="dot3"></div>
                                        <div class="pin-dot" id="dot4"></div>
                                        <div class="pin-dot" id="dot5"></div>
                                        <div class="pin-dot" id="dot6"></div>
                                    </div>
                                    <input type="hidden" id="pin" name="pin" required>
                                </div>
                                
                                <div class="keypad">
                                    <div class="keypad-row">
                                        <button type="button" class="keypad-btn" data-number="1">1</button>
                                        <button type="button" class="keypad-btn" data-number="2">2</button>
                                        <button type="button" class="keypad-btn" data-number="3">3</button>
                                    </div>
                                    <div class="keypad-row">
                                        <button type="button" class="keypad-btn" data-number="4">4</button>
                                        <button type="button" class="keypad-btn" data-number="5">5</button>
                                        <button type="button" class="keypad-btn" data-number="6">6</button>
                                    </div>
                                    <div class="keypad-row">
                                        <button type="button" class="keypad-btn" data-number="7">7</button>
                                        <button type="button" class="keypad-btn" data-number="8">8</button>
                                        <button type="button" class="keypad-btn" data-number="9">9</button>
                                    </div>
                                    <div class="keypad-row">
                                        <button type="button" class="keypad-btn clear-btn" id="clearBtn">
                                            <i class="fas fa-undo"></i>
                                        </button>
                                        <button type="button" class="keypad-btn" data-number="0">0</button>
                                        <button type="button" class="keypad-btn delete-btn" id="deleteBtn">
                                            <i class="fas fa-backspace"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary login-btn" id="submitBtn" disabled>
                                    <i class="fas fa-unlock me-2"></i>Unlock NR BUDGET Planner
                                </button>
                            </div>
                        </form>

                        <div class="text-center mt-4">
                            <small class="text-muted">
                                <i class="fas fa-shield-alt me-1"></i>
                                Your financial data is protected with PIN security
                            </small>
                            <div class="mt-3">
                                                            <a href="./register" class="btn btn-outline-primary btn-sm">
                                <i class="fas fa-user-plus me-1"></i>Create New Account
                            </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        let currentPin = '';
        const maxPinLength = 6;
        
        // Keypad functionality
        document.querySelectorAll('.keypad-btn[data-number]').forEach(function(btn) {
            btn.addEventListener('click', function() {
                const number = this.getAttribute('data-number');
                addToPin(number);
            });
        });
        
        // Clear button
        document.getElementById('clearBtn').addEventListener('click', function() {
            clearPin();
        });
        
        // Delete button
        document.getElementById('deleteBtn').addEventListener('click', function() {
            deleteLastDigit();
        });
        
        // Add number to PIN
        function addToPin(number) {
            if (currentPin.length < maxPinLength) {
                currentPin += number;
                updatePinDisplay();
                updateSubmitButton();
                
                // Auto-submit when 6 digits are entered
                if (currentPin.length === maxPinLength) {
                    setTimeout(() => {
                        document.getElementById('pinForm').submit();
                    }, 300);
                }
            }
        }
        
        // Clear PIN
        function clearPin() {
            currentPin = '';
            updatePinDisplay();
            updateSubmitButton();
        }
        
        // Delete last digit
        function deleteLastDigit() {
            if (currentPin.length > 0) {
                currentPin = currentPin.slice(0, -1);
                updatePinDisplay();
                updateSubmitButton();
            }
        }
        
        // Update PIN display dots
        function updatePinDisplay() {
            for (let i = 1; i <= maxPinLength; i++) {
                const dot = document.getElementById(`dot${i}`);
                if (i <= currentPin.length) {
                    dot.classList.add('filled');
                } else {
                    dot.classList.remove('filled');
                }
            }
            
            // Update hidden input
            document.getElementById('pin').value = currentPin;
        }
        
        // Update submit button state
        function updateSubmitButton() {
            const submitBtn = document.getElementById('submitBtn');
            if (currentPin.length === maxPinLength) {
                submitBtn.disabled = false;
                submitBtn.classList.remove('btn-secondary');
                submitBtn.classList.add('btn-primary');
            } else {
                submitBtn.disabled = true;
                submitBtn.classList.remove('btn-primary');
                submitBtn.classList.add('btn-secondary');
            }
        }
        
        // Keyboard support
        document.addEventListener('keydown', function(e) {
            if (e.key >= '0' && e.key <= '9') {
                addToPin(e.key);
            } else if (e.key === 'Backspace' || e.key === 'Delete') {
                deleteLastDigit();
            } else if (e.key === 'Escape') {
                clearPin();
            }
        });
        
        // Initialize
        updateSubmitButton();
    </script>
</body>
</html>
