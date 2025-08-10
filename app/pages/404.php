<?php
/**
 * 404 Error Page
 * Page not found handler
 */

$pageTitle = 'Page Not Found - NR BUDGET Planner';
include APP_ROOT . '/includes/header.php';
?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6 text-center">
            <div class="card">
                <div class="card-body">
                    <h1 class="display-1 text-muted">404</h1>
                    <h2 class="mb-4">Page Not Found</h2>
                    <p class="text-muted mb-4">The page you're looking for doesn't exist or has been moved.</p>
                    <a href="/" class="btn btn-primary">
                        <i class="fas fa-home me-2"></i>Go to Dashboard
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include APP_ROOT . '/includes/footer.php'; ?>
