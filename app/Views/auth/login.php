<?php include __DIR__ . '/../layout/header.php'; ?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-5 col-md-7">
            <div class="card shadow-lg border-0">
                <div class="card-body p-5">
                    <div class="text-center mb-4">
                        <i class="fas fa-sign-in-alt fa-3x text-primary mb-3"></i>
                        <h2 class="fw-bold">Welcome Back</h2>
                        <p class="text-muted">Sign in to your account to continue</p>
                    </div>

                    <form action="<?php echo url('login'); ?>" method="POST" id="loginForm">
                        <div class="mb-3">
                            <label for="username" class="form-label">
                                <i class="fas fa-user me-1"></i>Username or Email
                            </label>
                            <input type="text" class="form-control" id="username" name="username" 
                                   placeholder="Enter your username or email" required>
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">
                                <i class="fas fa-lock me-1"></i>Password
                            </label>
                            <div class="input-group">
                                <input type="password" class="form-control" id="password" name="password" 
                                       placeholder="Enter your password" required>
                                <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                        </div>

                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="remember_me" name="remember_me">
                            <label class="form-check-label" for="remember_me">
                                Remember me for 30 days
                            </label>
                        </div>

                        <div class="d-grid mb-3">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-sign-in-alt me-2"></i>Sign In
                            </button>
                        </div>

                        <div class="text-center">
                            <p class="mb-0">
                                Don't have an account? 
                                <a href="<?php echo url('register'); ?>" class="text-primary text-decoration-none fw-bold">
                                    Create one here
                                </a>
                            </p>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Quick Demo Accounts -->
            <div class="card mt-4 border-0 bg-light">
                <div class="card-body p-4">
                    <h6 class="fw-bold mb-3">
                        <i class="fas fa-info-circle me-1"></i>Demo Accounts
                    </h6>
                    <div class="row">
                        <div class="col-6">
                            <small class="text-muted d-block">Employer Account:</small>
                            <small class="fw-bold">admin_user / password</small>
                        </div>
                        <div class="col-6">
                            <small class="text-muted d-block">Job Seeker Account:</small>
                            <small class="fw-bold">john_seeker / password</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Toggle password visibility
    const togglePassword = document.getElementById('togglePassword');
    const passwordInput = document.getElementById('password');
    
    togglePassword.addEventListener('click', function() {
        const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
        passwordInput.setAttribute('type', type);
        
        const icon = this.querySelector('i');
        icon.classList.toggle('fa-eye');
        icon.classList.toggle('fa-eye-slash');
    });

    // Form validation
    const loginForm = document.getElementById('loginForm');
    loginForm.addEventListener('submit', function(e) {
        const username = document.getElementById('username').value.trim();
        const password = document.getElementById('password').value;

        if (!username || !password) {
            e.preventDefault();
            alert('Please fill in all required fields.');
            return false;
        }

        if (password.length < 6) {
            e.preventDefault();
            alert('Password must be at least 6 characters long.');
            return false;
        }
    });
});
</script>

<?php include __DIR__ . '/../layout/footer.php'; ?>
