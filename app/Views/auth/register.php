<?php include __DIR__ . '/../layout/header.php'; ?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-6 col-md-8">
            <div class="card shadow-lg border-0">
                <div class="card-body p-5">
                    <div class="text-center mb-4">
                        <i class="fas fa-user-plus fa-3x text-primary mb-3"></i>
                        <h2 class="fw-bold">Create Your Account</h2>
                        <p class="text-muted">Join thousands of professionals and employers</p>
                    </div>

                    <form action="<?php echo url('register'); ?>" method="POST" id="registerForm">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="full_name" class="form-label">
                                    <i class="fas fa-user me-1"></i>Full Name *
                                </label>
                                <input type="text" class="form-control" id="full_name" name="full_name" 
                                       value="<?php echo htmlspecialchars($old_input['full_name'] ?? ''); ?>"
                                       placeholder="Enter your full name" required>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="username" class="form-label">
                                    <i class="fas fa-at me-1"></i>Username *
                                </label>
                                <input type="text" class="form-control" id="username" name="username" 
                                       value="<?php echo htmlspecialchars($old_input['username'] ?? ''); ?>"
                                       placeholder="Choose a username" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">
                                <i class="fas fa-envelope me-1"></i>Email Address *
                            </label>
                            <input type="email" class="form-control" id="email" name="email" 
                                   value="<?php echo htmlspecialchars($old_input['email'] ?? ''); ?>"
                                   placeholder="Enter your email address" required>
                        </div>

                        <div class="mb-3">
                            <label for="phone" class="form-label">
                                <i class="fas fa-phone me-1"></i>Phone Number
                            </label>
                            <input type="tel" class="form-control" id="phone" name="phone" 
                                   value="<?php echo htmlspecialchars($old_input['phone'] ?? ''); ?>"
                                   placeholder="Enter your phone number">
                        </div>

                        <div class="mb-3">
                            <label for="user_type" class="form-label">
                                <i class="fas fa-user-tag me-1"></i>I am a *
                            </label>
                            <select class="form-select" id="user_type" name="user_type" required>
                                <option value="">Select your role</option>
                                <option value="seeker" <?php echo ($old_input['user_type'] ?? '') === 'seeker' ? 'selected' : ''; ?>>
                                    Job Seeker - Looking for opportunities
                                </option>
                                <option value="employer" <?php echo ($old_input['user_type'] ?? '') === 'employer' ? 'selected' : ''; ?>>
                                    Employer - Hiring talent
                                </option>
                            </select>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="password" class="form-label">
                                    <i class="fas fa-lock me-1"></i>Password *
                                </label>
                                <div class="input-group">
                                    <input type="password" class="form-control" id="password" name="password" 
                                           placeholder="Create a password" required>
                                    <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                                <small class="text-muted">Minimum 6 characters</small>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="confirm_password" class="form-label">
                                    <i class="fas fa-lock me-1"></i>Confirm Password *
                                </label>
                                <div class="input-group">
                                    <input type="password" class="form-control" id="confirm_password" name="confirm_password" 
                                           placeholder="Confirm your password" required>
                                    <button class="btn btn-outline-secondary" type="button" id="toggleConfirmPassword">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="mb-4">
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" id="terms" required>
                                <label class="form-check-label" for="terms">
                                    I agree to the <a href="#" class="text-primary">Terms of Service</a> 
                                    and <a href="#" class="text-primary">Privacy Policy</a>
                                </label>
                            </div>
                        </div>

                        <div class="d-grid mb-3">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-user-plus me-2"></i>Create Account
                            </button>
                        </div>

                        <div class="text-center">
                            <p class="mb-0">
                                Already have an account? 
                                <a href="<?php echo url('login'); ?>" class="text-primary text-decoration-none fw-bold">
                                    Sign in here
                                </a>
                            </p>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Registration Benefits -->
            <div class="card mt-4 border-0 bg-light">
                <div class="card-body p-4">
                    <h6 class="fw-bold mb-3">
                        <i class="fas fa-star me-1"></i>Why Join Elevate Portal?
                    </h6>
                    <div class="row">
                        <div class="col-md-6">
                            <ul class="list-unstyled mb-0">
                                <li class="mb-2">
                                    <i class="fas fa-check text-success me-2"></i>
                                    <small>Free to join and use</small>
                                </li>
                                <li class="mb-2">
                                    <i class="fas fa-check text-success me-2"></i>
                                    <small>Access to premium job listings</small>
                                </li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <ul class="list-unstyled mb-0">
                                <li class="mb-2">
                                    <i class="fas fa-check text-success me-2"></i>
                                    <small>Direct contact with employers</small>
                                </li>
                                <li class="mb-2">
                                    <i class="fas fa-check text-success me-2"></i>
                                    <small>Career guidance and tips</small>
                                </li>
                            </ul>
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
    function setupPasswordToggle(toggleId, passwordId) {
        const toggle = document.getElementById(toggleId);
        const password = document.getElementById(passwordId);
        
        toggle.addEventListener('click', function() {
            const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
            password.setAttribute('type', type);
            
            const icon = this.querySelector('i');
            icon.classList.toggle('fa-eye');
            icon.classList.toggle('fa-eye-slash');
        });
    }

    setupPasswordToggle('togglePassword', 'password');
    setupPasswordToggle('toggleConfirmPassword', 'confirm_password');

    // Form validation
    const registerForm = document.getElementById('registerForm');
    const passwordInput = document.getElementById('password');
    const confirmPasswordInput = document.getElementById('confirm_password');

    // Real-time password confirmation check
    confirmPasswordInput.addEventListener('input', function() {
        if (this.value !== passwordInput.value) {
            this.setCustomValidity('Passwords do not match');
            this.classList.add('is-invalid');
        } else {
            this.setCustomValidity('');
            this.classList.remove('is-invalid');
            this.classList.add('is-valid');
        }
    });

    // Password strength indicator
    passwordInput.addEventListener('input', function() {
        const password = this.value;
        const strength = calculatePasswordStrength(password);
        
        // Remove existing strength indicators
        const existingIndicator = this.parentNode.parentNode.querySelector('.password-strength');
        if (existingIndicator) {
            existingIndicator.remove();
        }

        if (password.length > 0) {
            const indicator = document.createElement('div');
            indicator.className = 'password-strength mt-1';
            
            let strengthText = '';
            let strengthClass = '';
            
            if (strength < 2) {
                strengthText = 'Weak';
                strengthClass = 'text-danger';
            } else if (strength < 4) {
                strengthText = 'Medium';
                strengthClass = 'text-warning';
            } else {
                strengthText = 'Strong';
                strengthClass = 'text-success';
            }
            
            indicator.innerHTML = `<small class="${strengthClass}">Password strength: ${strengthText}</small>`;
            this.parentNode.parentNode.appendChild(indicator);
        }
    });

    // Form submission validation
    registerForm.addEventListener('submit', function(e) {
        const requiredFields = ['full_name', 'username', 'email', 'user_type', 'password', 'confirm_password'];
        let isValid = true;

        // Check required fields
        requiredFields.forEach(function(fieldName) {
            const field = document.getElementById(fieldName);
            if (!field.value.trim()) {
                field.classList.add('is-invalid');
                isValid = false;
            } else {
                field.classList.remove('is-invalid');
            }
        });

        // Validate email format
        const email = document.getElementById('email').value;
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(email)) {
            document.getElementById('email').classList.add('is-invalid');
            isValid = false;
        }

        // Validate password length
        const password = document.getElementById('password').value;
        if (password.length < 6) {
            document.getElementById('password').classList.add('is-invalid');
            isValid = false;
        }

        // Validate password confirmation
        if (password !== document.getElementById('confirm_password').value) {
            document.getElementById('confirm_password').classList.add('is-invalid');
            isValid = false;
        }

        // Check terms agreement
        if (!document.getElementById('terms').checked) {
            alert('Please agree to the Terms of Service and Privacy Policy.');
            isValid = false;
        }

        if (!isValid) {
            e.preventDefault();
            alert('Please correct the highlighted errors and try again.');
        }
    });

    // Password strength calculation
    function calculatePasswordStrength(password) {
        let strength = 0;
        
        if (password.length >= 6) strength++;
        if (password.length >= 10) strength++;
        if (/[a-z]/.test(password)) strength++;
        if (/[A-Z]/.test(password)) strength++;
        if (/[0-9]/.test(password)) strength++;
        if (/[^A-Za-z0-9]/.test(password)) strength++;
        
        return strength;
    }
});
</script>

<?php include __DIR__ . '/../layout/footer.php'; ?>
