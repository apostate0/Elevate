<?php include __DIR__ . '/../layout/header.php'; ?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-6 text-center">
            <div class="mb-4">
                <i class="fas fa-exclamation-triangle fa-5x text-warning mb-3"></i>
                <h1 class="display-1 fw-bold text-primary">404</h1>
                <h2 class="fw-bold mb-3">Page Not Found</h2>
                <p class="text-muted mb-4">
                    Sorry, the page you are looking for doesn't exist or has been moved.
                </p>
            </div>
            
            <div class="d-grid gap-2 d-md-flex justify-content-md-center">
                <a href="/" class="btn btn-primary">
                    <i class="fas fa-home me-2"></i>Go Home
                </a>
                <a href="/jobs" class="btn btn-outline-primary">
                    <i class="fas fa-search me-2"></i>Browse Jobs
                </a>
                <button onclick="history.back()" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Go Back
                </button>
            </div>
            
            <div class="mt-5">
                <h5 class="fw-bold mb-3">Popular Pages</h5>
                <div class="row">
                    <div class="col-md-6">
                        <ul class="list-unstyled">
                            <li><a href="/" class="text-decoration-none">Home</a></li>
                            <li><a href="/jobs" class="text-decoration-none">Browse Jobs</a></li>
                            <li><a href="/about" class="text-decoration-none">About Us</a></li>
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <ul class="list-unstyled">
                            <li><a href="/contact" class="text-decoration-none">Contact</a></li>
                            <li><a href="/login" class="text-decoration-none">Login</a></li>
                            <li><a href="/register" class="text-decoration-none">Register</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../layout/footer.php'; ?>
