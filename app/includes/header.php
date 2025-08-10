<?php
/**
 * Header Component
 * Budget Planner Application
 * 
 * This component follows SOLID principles:
 * - Single Responsibility: Only handles header/navigation display
 * - Open/Closed: Can be extended without modification
 * - Dependency Inversion: Depends on abstractions (functions) not concrete implementations
 */

$currentUser = getCurrentUsername();
$currentPage = basename($_SERVER['PHP_SELF'], '.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle ?? 'NR BUDGET Planner'; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.min.css" rel="stylesheet">
    <style>
        body {
            padding-top: 80px; /* Prevent content from being hidden behind fixed navbar */
        }
        
        .navbar-brand {
            font-weight: bold;
            color: #667eea !important;
        }
        
        .nav-link {
            transition: all 0.3s ease;
            padding: 0.5rem 1rem !important;
        }
        
        .nav-link:hover {
            transform: translateY(-2px);
        }
        
        .nav-link.active {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white !important;
            border-radius: 8px;
        }
        
        .user-info {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 0.9rem;
        }
        
        .dropdown-menu {
            border: none;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            border-radius: 15px;
        }
        
        .dropdown-item:hover {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        
        /* Mobile-friendly styles */
        @media (max-width: 991.98px) {
            .navbar-nav {
                text-align: center;
                padding: 1rem 0;
            }
            
            .nav-link {
                padding: 0.75rem 1rem !important;
                border-bottom: 1px solid #f8f9fa;
            }
            
            .nav-link:last-child {
                border-bottom: none;
            }
            
            .navbar-collapse {
                background: white;
                border-radius: 0 0 15px 15px;
                box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
                margin-top: 0.5rem;
            }
            
            .dropdown-menu {
                border: none;
                box-shadow: none;
                background: #f8f9fa;
                margin-top: 0;
            }
            
            .dropdown-item {
                padding: 0.75rem 1rem;
                border-bottom: 1px solid #e9ecef;
            }
            
            .dropdown-item:last-child {
                border-bottom: none;
            }
            
            .navbar-nav .dropdown-menu {
                position: static !important;
                float: none;
                width: 100%;
                margin-top: 0;
                box-shadow: none;
                border: none;
                background: #f8f9fa;
            }
            
            .user-info {
                margin: 0.5rem 0;
                text-align: center;
                display: block;
            }
        }
        
        /* Notification styles */
        .notification-badge {
            position: absolute;
            top: -5px;
            right: -5px;
            background: #dc3545;
            color: white;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            font-size: 0.75rem;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .notification-item {
            cursor: pointer;
            transition: background-color 0.2s;
        }
        
        .notification-item:hover {
            background-color: #f8f9fa;
        }
        
        .notification-item.read {
            opacity: 0.6;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm fixed-top">
        <div class="container">
            <a class="navbar-brand" href="<?php echo isInPagesDirectory() ? '../' : './'; ?>">
                <i class="fas fa-chart-pie me-2"></i>NR BUDGET Planner
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link <?php echo $currentPage === 'index' ? 'active' : ''; ?>" href="<?php echo isInPagesDirectory() ? '../' : './'; ?>">
                            <i class="fas fa-tachometer-alt me-1"></i><span class="d-none d-lg-inline">Dashboard</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo $currentPage === 'income' ? 'active' : ''; ?>" href="<?php echo isInPagesDirectory() ? '../income' : './income'; ?>">
                            <i class="fas fa-money-bill-wave me-1"></i><span class="d-none d-lg-inline">Income</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo $currentPage === 'expenses' ? 'active' : ''; ?>" href="<?php echo isInPagesDirectory() ? '../expenses' : './expenses'; ?>">
                            <i class="fas fa-receipt me-1"></i><span class="d-none d-lg-inline">Expenses</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo $currentPage === 'bills' ? 'active' : ''; ?>" href="<?php echo isInPagesDirectory() ? '../bills' : './bills'; ?>">
                            <i class="fas fa-file-invoice-dollar me-1"></i><span class="d-none d-lg-inline">Bills</span>
                        </a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-chart-line me-1"></i><span class="d-none d-lg-inline">More</span>
                        </a>
                        <ul class="dropdown-menu">
                            <li>
                                <a class="dropdown-item <?php echo $currentPage === 'actual-expenses' ? 'active' : ''; ?>" href="<?php echo isInPagesDirectory() ? '../actual-expenses' : './actual-expenses'; ?>">
                                    <i class="fas fa-list-alt me-2"></i>Actual Expenses
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item <?php echo $currentPage === 'quick-actions' ? 'active' : ''; ?>" href="<?php echo isInPagesDirectory() ? '../quick-actions' : './quick-actions'; ?>">
                                    <i class="fas fa-bolt me-2"></i>Quick Actions
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item <?php echo $currentPage === 'savings' ? 'active' : ''; ?>" href="<?php echo isInPagesDirectory() ? '../savings' : './savings'; ?>">
                                    <i class="fas fa-piggy-bank me-2"></i>Savings
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item <?php echo $currentPage === 'monthly-budget' ? 'active' : ''; ?>" href="<?php echo isInPagesDirectory() ? '../monthly-budget' : './monthly-budget'; ?>">
                                    <i class="fas fa-calendar-alt me-2"></i>Monthly Budget
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item <?php echo $currentPage === 'analytics' ? 'active' : ''; ?>" href="<?php echo isInPagesDirectory() ? '../analytics' : './analytics'; ?>">
                                    <i class="fas fa-chart-bar me-2"></i>Analytics
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item <?php echo $currentPage === 'expense-categories' ? 'active' : ''; ?>" href="<?php echo isInPagesDirectory() ? '../expense-categories' : './expense-categories'; ?>">
                                    <i class="fas fa-tags me-2"></i>Expense Categories
                                </a>
                            </li>
                            <?php if (isAdmin()): ?>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <a class="dropdown-item <?php echo $currentPage === 'user-approvals' ? 'active' : ''; ?>" href="<?php echo isInPagesDirectory() ? '../user-approvals' : './user-approvals'; ?>">
                                    <i class="fas fa-user-check me-2"></i>User Approvals
                                </a>
                            </li>
                            <?php endif; ?>
                        </ul>
                    </li>

                </ul>
                
                <ul class="navbar-nav">
                    <!-- Notifications -->
                    <li class="nav-item dropdown me-3">
                        <a class="nav-link position-relative" href="#" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-bell"></i>
                            <?php 
                            $unreadCount = getUnreadNotificationCount();
                            if ($unreadCount > 0): 
                            ?>
                                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                    <?php echo $unreadCount; ?>
                                </span>
                            <?php endif; ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end notification-dropdown" style="width: 350px; max-height: 400px; overflow-y: auto;">
                            <li class="dropdown-header d-flex justify-content-between align-items-center">
                                <span>Notifications</span>
                                <?php if ($unreadCount > 0): ?>
                                    <button class="btn btn-sm btn-outline-primary" onclick="markAllAsRead()">
                                        Mark all read
                                    </button>
                                <?php endif; ?>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            <?php 
                            $notifications = getNotifications(true); // Get unread notifications
                            if (empty($notifications)): 
                            ?>
                                <li class="dropdown-item text-muted">No new notifications</li>
                            <?php else: ?>
                                <?php foreach ($notifications as $notification): ?>
                                    <li class="dropdown-item notification-item" data-id="<?php echo $notification['id']; ?>">
                                        <div class="d-flex">
                                            <div class="flex-shrink-0">
                                                <i class="fas fa-<?php echo $notification['type'] === 'registration' ? 'user-plus' : 'info-circle'; ?> text-primary"></i>
                                            </div>
                                            <div class="flex-grow-1 ms-3">
                                                <div class="fw-bold"><?php echo htmlspecialchars($notification['title']); ?></div>
                                                <div class="small text-muted"><?php echo htmlspecialchars($notification['message']); ?></div>
                                                <div class="small text-muted"><?php echo date('M j, Y g:i A', strtotime($notification['created_at'])); ?></div>
                                            </div>
                                        </div>
                                    </li>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </ul>
                    </li>
                    
                    <!-- User Menu -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle user-info" href="#" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-user me-1"></i><?php echo htmlspecialchars($currentUser); ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <?php if (isAdmin()): ?>
                                <li>
                                    <a class="dropdown-item" href="<?php echo isInPagesDirectory() ? '../user-approvals' : './user-approvals'; ?>">
                                        <i class="fas fa-users-cog me-2"></i>User Approvals
                                        <?php 
                                        $pendingCount = count(getPendingRegistrations());
                                        if ($pendingCount > 0): 
                                        ?>
                                            <span class="badge bg-warning text-dark ms-2"><?php echo $pendingCount; ?></span>
                                        <?php endif; ?>
                                    </a>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                            <?php endif; ?>
                            <li>
                                <a class="dropdown-item" href="<?php echo isInPagesDirectory() ? '../pin-settings' : './pin-settings'; ?>">
                                    <i class="fas fa-key me-2"></i>Security Settings
                                </a>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <a class="dropdown-item text-danger" href="<?php echo isInPagesDirectory() ? '../logout' : './logout'; ?>">
                                    <i class="fas fa-sign-out-alt me-2"></i>Logout
                                </a>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <?php
        // Display success/error messages
        $messages = getMessages();
        if (!empty($messages['success'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i><?php echo $messages['success']; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        
        <?php if (!empty($messages['error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i><?php echo $messages['error']; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <script>
        function markAllAsRead() {
            fetch('<?php echo isInPagesDirectory() ? '../ajax/mark-notifications-read.php' : './ajax/mark-notifications-read.php'; ?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                }
            });
        }

        // Mark notification as read when clicked
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.notification-item').forEach(function(item) {
                item.addEventListener('click', function() {
                    const notificationId = this.getAttribute('data-id');
                    fetch('<?php echo isInPagesDirectory() ? '../ajax/mark-notification-read.php' : './ajax/mark-notification-read.php'; ?>', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({ notification_id: notificationId })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            this.style.opacity = '0.5';
                        }
                    });
                });
            });
        });
        </script>
