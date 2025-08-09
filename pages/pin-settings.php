<?php
/**
 * PIN Settings Page
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

// Handle PIN update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action']) && $_POST['action'] === 'update_pin') {
        $currentPin = sanitizeInput($_POST['current_pin']);
        $newPin = sanitizeInput($_POST['new_pin']);
        $confirmPin = sanitizeInput($_POST['confirm_pin']);
        
        // Validate current PIN
        if (!verifyPIN($currentPin)) {
            showError('Current PIN is incorrect.');
        }
        // Validate new PIN format
        elseif (!preg_match('/^[0-9]{6}$/', $newPin)) {
            showError('New PIN must be exactly 6 digits.');
        }
        // Validate PIN confirmation
        elseif ($newPin !== $confirmPin) {
            showError('New PIN and confirmation PIN do not match.');
        }
        // Validate PIN is not the same as current
        elseif ($currentPin === $newPin) {
            showError('New PIN must be different from current PIN.');
        }
        // Update PIN
        elseif (updatePIN($newPin)) {
            showSuccess('PIN updated successfully!');
        } else {
            showError('Failed to update PIN. Please try again.');
        }
        
        redirect('pin-settings.php');
    }
}

// Messages are handled by header.php
?>
<?php
$pageTitle = 'PIN Settings - NR BUDGET Planner';

// Handle path for router vs direct access
if (defined('USING_ROUTER')) {
    include __DIR__ . '/../includes/header.php';
} else {
    include '../includes/header.php';
}
?>
    <style>
        .pin-input {
            letter-spacing: 0.3em;
            text-align: center;
            font-size: 1.2rem;
            font-weight: bold;
        }
        .security-card {
            border-left: 4px solid #dc3545;
        }
        
        /* PIN Display */
        .pin-display {
            text-align: center;
        }
        
        .pin-dots {
            display: flex;
            justify-content: center;
            gap: 10px;
            margin-bottom: 10px;
        }
        
        .pin-dot {
            width: 16px;
            height: 16px;
            border-radius: 50%;
            border: 2px solid #dee2e6;
            background: transparent;
            transition: all 0.3s ease;
        }
        
        .pin-dot.filled {
            background: #dc3545;
            border-color: #dc3545;
            box-shadow: 0 0 8px rgba(220, 53, 69, 0.3);
        }
        
        /* Keypad */
        .keypad {
            max-width: 280px;
            margin: 0 auto;
        }
        
        .keypad-row {
            display: flex;
            justify-content: center;
            gap: 8px;
            margin-bottom: 8px;
        }
        
        .keypad-btn {
            width: 50px;
            height: 50px;
            border: none;
            border-radius: 50%;
            background: #f8f9fa;
            color: #495057;
            font-size: 1rem;
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
                        <a class="nav-link" href="reports.php">Reports</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="pin-settings.php">Security</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <!-- Header -->
        <div class="row mb-4">
            <div class="col">
                <h1 class="h3 mb-0">
                    <i class="fas fa-shield-alt me-2"></i>Security Settings
                </h1>
                <p class="text-muted">Manage your 6-digit security PIN</p>
            </div>
        </div>

        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">
                <div class="card security-card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-key me-2"></i>Update Security PIN
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>Security Note:</strong> Your PIN is used to protect access to your financial data. 
                            Choose a PIN that you can remember but others cannot easily guess.
                        </div>

                        <form method="POST" id="pinForm">
                            <input type="hidden" name="action" value="update_pin">
                            
                            <div class="mb-3">
                                <label class="form-label">Current PIN</label>
                                <div class="pin-display mb-2">
                                    <div class="pin-dots">
                                        <div class="pin-dot" id="current-dot1"></div>
                                        <div class="pin-dot" id="current-dot2"></div>
                                        <div class="pin-dot" id="current-dot3"></div>
                                        <div class="pin-dot" id="current-dot4"></div>
                                        <div class="pin-dot" id="current-dot5"></div>
                                        <div class="pin-dot" id="current-dot6"></div>
                                    </div>
                                    <input type="hidden" id="current_pin" name="current_pin" required>
                                </div>
                                <div class="form-text">Enter your current 6-digit PIN</div>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">New PIN</label>
                                <div class="pin-display mb-2">
                                    <div class="pin-dots">
                                        <div class="pin-dot" id="new-dot1"></div>
                                        <div class="pin-dot" id="new-dot2"></div>
                                        <div class="pin-dot" id="new-dot3"></div>
                                        <div class="pin-dot" id="new-dot4"></div>
                                        <div class="pin-dot" id="new-dot5"></div>
                                        <div class="pin-dot" id="new-dot6"></div>
                                    </div>
                                    <input type="hidden" id="new_pin" name="new_pin" required>
                                </div>
                                <div class="form-text">Enter your new 6-digit PIN</div>
                            </div>
                            
                            <div class="mb-4">
                                <label class="form-label">Confirm New PIN</label>
                                <div class="pin-display mb-2">
                                    <div class="pin-dots">
                                        <div class="pin-dot" id="confirm-dot1"></div>
                                        <div class="pin-dot" id="confirm-dot2"></div>
                                        <div class="pin-dot" id="confirm-dot3"></div>
                                        <div class="pin-dot" id="confirm-dot4"></div>
                                        <div class="pin-dot" id="confirm-dot5"></div>
                                        <div class="pin-dot" id="confirm-dot6"></div>
                                    </div>
                                    <input type="hidden" id="confirm_pin" name="confirm_pin" required>
                                </div>
                                <div class="form-text">Re-enter your new 6-digit PIN to confirm</div>
                            </div>
                            
                            <div class="keypad mt-3">
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

                            <div class="d-grid gap-2 mt-3">
                                <button type="submit" class="btn btn-primary" id="submitBtn" disabled>
                                    <i class="fas fa-save me-2"></i>Update PIN
                                </button>
                                <a href="../index.php" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left me-2"></i>Back to Dashboard
                                </a>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Security Tips -->
                <div class="card mt-4">
                    <div class="card-header">
                        <h6 class="mb-0">
                            <i class="fas fa-lightbulb me-2"></i>Security Tips
                        </h6>
                    </div>
                    <div class="card-body">
                        <ul class="list-unstyled mb-0">
                            <li class="mb-2">
                                <i class="fas fa-check text-success me-2"></i>
                                Use a PIN that's easy for you to remember but hard for others to guess
                            </li>
                            <li class="mb-2">
                                <i class="fas fa-check text-success me-2"></i>
                                Avoid using obvious patterns like 123456 or 000000
                            </li>
                            <li class="mb-2">
                                <i class="fas fa-check text-success me-2"></i>
                                Don't share your PIN with anyone
                            </li>
                            <li class="mb-2">
                                <i class="fas fa-check text-success me-2"></i>
                                Change your PIN regularly for better security
                            </li>
                            <li>
                                <i class="fas fa-check text-success me-2"></i>
                                Keep your PIN in a secure place if you need to write it down
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Only allow numeric input for all PIN fields
        document.querySelectorAll('.pin-input').forEach(function(input) {
            input.addEventListener('input', function(e) {
                this.value = this.value.replace(/[^0-9]/g, '');
            });

            input.addEventListener('keydown', function(e) {
                if (e.key === 'Backspace' || e.key === 'Delete') {
                    return;
                }
                
                if (!/[0-9]/.test(e.key) && !['Tab', 'Enter', 'ArrowLeft', 'ArrowRight', 'ArrowUp', 'ArrowDown'].includes(e.key)) {
                    e.preventDefault();
                }
            });
        });

        let currentPinInput = '';
        let newPinInput = '';
        let confirmPinInput = '';
        let activeField = 'current'; // current, new, confirm
        const maxPinLength = 6;
        
        // Keypad functionality
        document.querySelectorAll('.keypad-btn[data-number]').forEach(function(btn) {
            btn.addEventListener('click', function() {
                const number = this.getAttribute('data-number');
                addToActivePin(number);
            });
        });
        
        // Clear button
        document.getElementById('clearBtn').addEventListener('click', function() {
            clearActivePin();
        });
        
        // Delete button
        document.getElementById('deleteBtn').addEventListener('click', function() {
            deleteLastDigit();
        });
        
        // Add number to active PIN field
        function addToActivePin(number) {
            let currentInput = getCurrentInput();
            if (currentInput.length < maxPinLength) {
                setCurrentInput(currentInput + number);
                updatePinDisplay();
                updateSubmitButton();
                
                // Auto-advance to next field when current is full
                if (currentInput.length + 1 === maxPinLength) {
                    if (activeField === 'current') {
                        activeField = 'new';
                    } else if (activeField === 'new') {
                        activeField = 'confirm';
                    }
                    updateActiveFieldIndicator();
                }
            }
        }
        
        // Clear active PIN field
        function clearActivePin() {
            setCurrentInput('');
            updatePinDisplay();
            updateSubmitButton();
        }
        
        // Delete last digit from active field
        function deleteLastDigit() {
            let currentInput = getCurrentInput();
            if (currentInput.length > 0) {
                setCurrentInput(currentInput.slice(0, -1));
                updatePinDisplay();
                updateSubmitButton();
            }
        }
        
        // Get current input based on active field
        function getCurrentInput() {
            switch (activeField) {
                case 'current': return currentPinInput;
                case 'new': return newPinInput;
                case 'confirm': return confirmPinInput;
                default: return currentPinInput;
            }
        }
        
        // Set current input based on active field
        function setCurrentInput(value) {
            switch (activeField) {
                case 'current': 
                    currentPinInput = value;
                    document.getElementById('current_pin').value = value;
                    break;
                case 'new': 
                    newPinInput = value;
                    document.getElementById('new_pin').value = value;
                    break;
                case 'confirm': 
                    confirmPinInput = value;
                    document.getElementById('confirm_pin').value = value;
                    break;
            }
        }
        
        // Update PIN display dots
        function updatePinDisplay() {
            // Update current PIN dots
            updateDots('current', currentPinInput);
            // Update new PIN dots
            updateDots('new', newPinInput);
            // Update confirm PIN dots
            updateDots('confirm', confirmPinInput);
        }
        
        // Update dots for specific field
        function updateDots(field, value) {
            for (let i = 1; i <= maxPinLength; i++) {
                const dot = document.getElementById(`${field}-dot${i}`);
                if (i <= value.length) {
                    dot.classList.add('filled');
                } else {
                    dot.classList.remove('filled');
                }
            }
        }
        
        // Update active field indicator
        function updateActiveFieldIndicator() {
            // Remove active class from all labels
            document.querySelectorAll('.form-label').forEach(label => {
                label.classList.remove('text-primary', 'fw-bold');
            });
            
            // Add active class to current field label
            const activeLabels = document.querySelectorAll('.form-label');
            switch (activeField) {
                case 'current':
                    activeLabels[0].classList.add('text-primary', 'fw-bold');
                    break;
                case 'new':
                    activeLabels[1].classList.add('text-primary', 'fw-bold');
                    break;
                case 'confirm':
                    activeLabels[2].classList.add('text-primary', 'fw-bold');
                    break;
            }
        }
        
        // Update submit button state
        function updateSubmitButton() {
            const submitBtn = document.getElementById('submitBtn');
            if (currentPinInput.length === maxPinLength && 
                newPinInput.length === maxPinLength && 
                confirmPinInput.length === maxPinLength) {
                submitBtn.disabled = false;
                submitBtn.classList.remove('btn-secondary');
                submitBtn.classList.add('btn-primary');
            } else {
                submitBtn.disabled = true;
                submitBtn.classList.remove('btn-primary');
                submitBtn.classList.add('btn-secondary');
            }
        }
        
        // Form validation
        document.getElementById('pinForm').addEventListener('submit', function(e) {
            if (currentPinInput.length !== 6 || newPinInput.length !== 6 || confirmPinInput.length !== 6) {
                e.preventDefault();
                alert('All PINs must be exactly 6 digits.');
                return;
            }

            if (newPinInput !== confirmPinInput) {
                e.preventDefault();
                alert('New PIN and confirmation PIN do not match.');
                return;
            }

            if (currentPinInput === newPinInput) {
                e.preventDefault();
                alert('New PIN must be different from current PIN.');
                return;
            }
        });
        
        // Keyboard support
        document.addEventListener('keydown', function(e) {
            if (e.key >= '0' && e.key <= '9') {
                addToActivePin(e.key);
            } else if (e.key === 'Backspace' || e.key === 'Delete') {
                deleteLastDigit();
            } else if (e.key === 'Escape') {
                clearActivePin();
            } else if (e.key === 'Tab') {
                // Cycle through fields
                if (activeField === 'current') {
                    activeField = 'new';
                } else if (activeField === 'new') {
                    activeField = 'confirm';
                } else {
                    activeField = 'current';
                }
                updateActiveFieldIndicator();
            }
        });
        
        // Initialize
        updateActiveFieldIndicator();
        updateSubmitButton();
    </script>

<?php 
// Handle path for router vs direct access
if (defined('USING_ROUTER')) {
    include __DIR__ . '/../includes/footer.php';
} else {
    include '../includes/footer.php';
}
?>
