<?php include __DIR__ . '/../layout/header.php'; ?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-6 text-center">
            <div class="mb-4">
                <i class="fas fa-server fa-5x text-danger mb-3"></i>
                <h1 class="display-1 fw-bold text-primary">500</h1>
                <h2 class="fw-bold mb-3">Server Error</h2>
                <p class="text-muted mb-4">
                    <?php echo htmlspecialchars($error_message ?? 'Something went wrong on our end. Please try again later.'); ?>
                </p>
            </div>
            
            <div class="d-grid gap-2 d-md-flex justify-content-md-center">
                <button onclick="location.reload()" class="btn btn-primary">
                    <i class="fas fa-redo me-2"></i>Try Again
                </button>
                <a href="/" class="btn btn-outline-primary">
                    <i class="fas fa-home me-2"></i>Go Home
                </a>
                <a href="/contact" class="btn btn-outline-secondary">
                    <i class="fas fa-envelope me-2"></i>Contact Support
                </a>
            </div>
            
            <div class="mt-5">
                <div class="alert alert-info">
                    <h6 class="alert-heading">
                        <i class="fas fa-info-circle me-2"></i>What can you do?
                    </h6>
                    <ul class="list-unstyled mb-0 text-start">
                        <li class="mb-1">• Try refreshing the page</li>
                        <li class="mb-1">• Check your internet connection</li>
                        <li class="mb-1">• Contact our support team if the problem persists</li>
                        <li>• Visit our homepage to continue browsing</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../layout/footer.php'; ?>
