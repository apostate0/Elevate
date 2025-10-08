    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="row">
                <div class="col-lg-4 col-md-6 mb-4">
                    <h5 class="text-white mb-3">
                        <i class="fas fa-rocket me-2"></i>
                        <?php echo APP_NAME; ?>
                    </h5>
                    <p class="mb-3">
                        Your gateway to amazing career opportunities. Connect with top employers and find your dream job today.
                    </p>
                    <div class="social-links">
                        <a href="#" class="text-light me-3"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" class="text-light me-3"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="text-light me-3"><i class="fab fa-linkedin-in"></i></a>
                        <a href="#" class="text-light me-3"><i class="fab fa-instagram"></i></a>
                    </div>
                </div>
                
                <div class="col-lg-2 col-md-6 mb-4">
                    <h6 class="text-white mb-3">Quick Links</h6>
                    <ul class="list-unstyled">
                        <li><a href="/" class="text-light text-decoration-none">Home</a></li>
                        <li><a href="/jobs" class="text-light text-decoration-none">Browse Jobs</a></li>
                        <li><a href="/about" class="text-light text-decoration-none">About Us</a></li>
                        <li><a href="/contact" class="text-light text-decoration-none">Contact</a></li>
                    </ul>
                </div>
                
                <div class="col-lg-2 col-md-6 mb-4">
                    <h6 class="text-white mb-3">For Job Seekers</h6>
                    <ul class="list-unstyled">
                        <li><a href="/register" class="text-light text-decoration-none">Create Account</a></li>
                        <li><a href="/jobs" class="text-light text-decoration-none">Search Jobs</a></li>
                        <li><a href="/seeker/dashboard" class="text-light text-decoration-none">My Applications</a></li>
                        <li><a href="#" class="text-light text-decoration-none">Career Tips</a></li>
                    </ul>
                </div>
                
                <div class="col-lg-2 col-md-6 mb-4">
                    <h6 class="text-white mb-3">For Employers</h6>
                    <ul class="list-unstyled">
                        <li><a href="/register" class="text-light text-decoration-none">Post Jobs</a></li>
                        <li><a href="/employer/dashboard" class="text-light text-decoration-none">Employer Dashboard</a></li>
                        <li><a href="#" class="text-light text-decoration-none">Pricing</a></li>
                        <li><a href="#" class="text-light text-decoration-none">Success Stories</a></li>
                    </ul>
                </div>
                
                <div class="col-lg-2 col-md-6 mb-4">
                    <h6 class="text-white mb-3">Support</h6>
                    <ul class="list-unstyled">
                        <li><a href="#" class="text-light text-decoration-none">Help Center</a></li>
                        <li><a href="#" class="text-light text-decoration-none">Privacy Policy</a></li>
                        <li><a href="#" class="text-light text-decoration-none">Terms of Service</a></li>
                        <li><a href="/contact" class="text-light text-decoration-none">Contact Support</a></li>
                    </ul>
                </div>
            </div>
            
            <hr class="my-4" style="border-color: #475569;">
            
            <div class="row align-items-center">
                <div class="col-md-6">
                    <p class="mb-0">
                        &copy; <?php echo date('Y'); ?> <?php echo APP_NAME; ?>. All rights reserved.
                    </p>
                </div>
                <div class="col-md-6 text-md-end">
                    <p class="mb-0">
                        Made with <i class="fas fa-heart text-danger"></i> in Nepal
                    </p>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Custom JavaScript -->
    <script>
        // Auto-hide alerts after 5 seconds
        document.addEventListener('DOMContentLoaded', function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(function(alert) {
                setTimeout(function() {
                    const bsAlert = new bootstrap.Alert(alert);
                    bsAlert.close();
                }, 5000);
            });
        });

        // Search functionality
        function initializeSearch() {
            const searchForm = document.getElementById('job-search-form');
            if (searchForm) {
                searchForm.addEventListener('submit', function(e) {
                    const searchInput = document.getElementById('search');
                    const locationInput = document.getElementById('location');
                    
                    if (!searchInput.value.trim() && !locationInput.value.trim()) {
                        e.preventDefault();
                        alert('Please enter a search term or location.');
                        return false;
                    }
                });
            }
        }

        // Initialize search on page load
        document.addEventListener('DOMContentLoaded', initializeSearch);

        // Confirm delete actions
        function confirmDelete(message) {
            return confirm(message || 'Are you sure you want to delete this item?');
        }

        // Application status update
        function updateApplicationStatus(applicationId, status) {
            if (!confirm('Are you sure you want to update this application status?')) {
                return;
            }

            fetch('/api/applications/status', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    application_id: applicationId,
                    status: status
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert('Error updating status: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while updating the status.');
            });
        }

        // Form validation
        function validateForm(formId) {
            const form = document.getElementById(formId);
            if (!form) return true;

            const requiredFields = form.querySelectorAll('[required]');
            let isValid = true;

            requiredFields.forEach(function(field) {
                if (!field.value.trim()) {
                    field.classList.add('is-invalid');
                    isValid = false;
                } else {
                    field.classList.remove('is-invalid');
                }
            });

            return isValid;
        }

        // File upload validation
        function validateFileUpload(input, maxSize = 5242880, allowedTypes = ['pdf', 'doc', 'docx']) {
            const file = input.files[0];
            if (!file) return true;

            // Check file size (5MB default)
            if (file.size > maxSize) {
                alert('File size must be less than ' + (maxSize / 1024 / 1024) + 'MB');
                input.value = '';
                return false;
            }

            // Check file type
            const fileExtension = file.name.split('.').pop().toLowerCase();
            if (!allowedTypes.includes(fileExtension)) {
                alert('Only ' + allowedTypes.join(', ').toUpperCase() + ' files are allowed');
                input.value = '';
                return false;
            }

            return true;
        }

        // Initialize tooltips and dropdowns
        document.addEventListener('DOMContentLoaded', function() {
            const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            const tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
            
            // Ensure dropdowns are working
            const dropdownElementList = [].slice.call(document.querySelectorAll('.dropdown-toggle'));
            const dropdownList = dropdownElementList.map(function(dropdownToggleEl) {
                return new bootstrap.Dropdown(dropdownToggleEl);
            });
        });

        // Smooth scrolling for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function(e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });
    </script>
</body>
</html>
