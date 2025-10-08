<?php include __DIR__ . '/../layout/header.php'; ?>

<div class="container py-5">
    <!-- Page Header -->
    <div class="row mb-5">
        <div class="col-lg-8 mx-auto text-center">
            <h1 class="fw-bold mb-3">Get in Touch</h1>
            <p class="text-muted lead">
                Have questions, feedback, or need support? We'd love to hear from you. 
                Our team is here to help you succeed.
            </p>
        </div>
    </div>

    <div class="row">
        <!-- Contact Form -->
        <div class="col-lg-8 mb-5">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">
                        <i class="fas fa-envelope me-2"></i>Send us a Message
                    </h4>
                </div>
                <div class="card-body p-4">
                    <form id="contactForm">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="name" class="form-label">
                                    Full Name <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control" id="name" name="name" 
                                       placeholder="Enter your full name" required>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label">
                                    Email Address <span class="text-danger">*</span>
                                </label>
                                <input type="email" class="form-control" id="email" name="email" 
                                       placeholder="Enter your email address" required>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="phone" class="form-label">Phone Number</label>
                                <input type="tel" class="form-control" id="phone" name="phone" 
                                       placeholder="Enter your phone number">
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="subject" class="form-label">
                                    Subject <span class="text-danger">*</span>
                                </label>
                                <select class="form-select" id="subject" name="subject" required>
                                    <option value="">Select a subject</option>
                                    <option value="general">General Inquiry</option>
                                    <option value="support">Technical Support</option>
                                    <option value="billing">Billing Question</option>
                                    <option value="feature">Feature Request</option>
                                    <option value="bug">Report a Bug</option>
                                    <option value="partnership">Partnership Opportunity</option>
                                    <option value="other">Other</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="user_type" class="form-label">I am a</label>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="user_type" 
                                               id="job_seeker" value="job_seeker">
                                        <label class="form-check-label" for="job_seeker">
                                            Job Seeker
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="user_type" 
                                               id="employer" value="employer">
                                        <label class="form-check-label" for="employer">
                                            Employer
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="user_type" 
                                               id="visitor" value="visitor" checked>
                                        <label class="form-check-label" for="visitor">
                                            Visitor
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-4">
                            <label for="message" class="form-label">
                                Message <span class="text-danger">*</span>
                            </label>
                            <textarea class="form-control" id="message" name="message" rows="6" 
                                      placeholder="Tell us how we can help you..." required></textarea>
                            <small class="text-muted">
                                <span id="messageCount">0</span>/1000 characters
                            </small>
                        </div>
                        
                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="newsletter" name="newsletter">
                                <label class="form-check-label" for="newsletter">
                                    Subscribe to our newsletter for job market insights and updates
                                </label>
                            </div>
                        </div>
                        
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-paper-plane me-2"></i>Send Message
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Contact Information -->
        <div class="col-lg-4">
            <!-- Contact Details -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-info-circle me-2"></i>Contact Information
                    </h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <div class="d-flex align-items-center mb-2">
                            <i class="fas fa-map-marker-alt text-primary me-3"></i>
                            <div>
                                <strong>Address</strong><br>
                                <small class="text-muted">
                                    Kathmandu, Nepal<br>
                                    Thamel, Ward No. 26
                                </small>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <div class="d-flex align-items-center mb-2">
                            <i class="fas fa-phone text-primary me-3"></i>
                            <div>
                                <strong>Phone</strong><br>
                                <small class="text-muted">+977-1-4567890</small>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <div class="d-flex align-items-center mb-2">
                            <i class="fas fa-envelope text-primary me-3"></i>
                            <div>
                                <strong>Email</strong><br>
                                <small class="text-muted">support@elevateportal.com</small>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <div class="d-flex align-items-center mb-2">
                            <i class="fas fa-clock text-primary me-3"></i>
                            <div>
                                <strong>Business Hours</strong><br>
                                <small class="text-muted">
                                    Mon - Fri: 9:00 AM - 6:00 PM<br>
                                    Sat: 10:00 AM - 4:00 PM<br>
                                    Sun: Closed
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- FAQ -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-question-circle me-2"></i>Quick Help
                    </h5>
                </div>
                <div class="card-body">
                    <div class="accordion" id="faqAccordion">
                        <div class="accordion-item">
                            <h6 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" 
                                        data-bs-toggle="collapse" data-bs-target="#faq1">
                                    How do I post a job?
                                </button>
                            </h6>
                            <div id="faq1" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body small">
                                    Register as an employer, complete your company profile, 
                                    and click "Post New Job" from your dashboard.
                                </div>
                            </div>
                        </div>
                        
                        <div class="accordion-item">
                            <h6 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" 
                                        data-bs-toggle="collapse" data-bs-target="#faq2">
                                    How do I apply for jobs?
                                </button>
                            </h6>
                            <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body small">
                                    Create a job seeker account, browse jobs, and click 
                                    "Apply" on any job that interests you.
                                </div>
                            </div>
                        </div>
                        
                        <div class="accordion-item">
                            <h6 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" 
                                        data-bs-toggle="collapse" data-bs-target="#faq3">
                                    Is Elevate Portal free?
                                </button>
                            </h6>
                            <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body small">
                                    Yes! Job seekers can use all features for free. 
                                    Employers can post jobs and manage applications at no cost.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Social Media -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-share-alt me-2"></i>Follow Us
                    </h5>
                </div>
                <div class="card-body">
                    <p class="small text-muted mb-3">
                        Stay connected with us on social media for the latest updates, 
                        job market insights, and career tips.
                    </p>
                    
                    <div class="d-grid gap-2">
                        <a href="#" class="btn btn-outline-primary btn-sm">
                            <i class="fab fa-facebook-f me-2"></i>Facebook
                        </a>
                        <a href="#" class="btn btn-outline-info btn-sm">
                            <i class="fab fa-twitter me-2"></i>Twitter
                        </a>
                        <a href="#" class="btn btn-outline-primary btn-sm">
                            <i class="fab fa-linkedin-in me-2"></i>LinkedIn
                        </a>
                        <a href="#" class="btn btn-outline-danger btn-sm">
                            <i class="fab fa-instagram me-2"></i>Instagram
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Success Message (Hidden by default) -->
    <div id="successMessage" class="alert alert-success d-none mt-4">
        <i class="fas fa-check-circle me-2"></i>
        <strong>Thank you!</strong> Your message has been sent successfully. 
        We'll get back to you within 24 hours.
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Character counter for message
    const messageTextarea = document.getElementById('message');
    const messageCount = document.getElementById('messageCount');
    
    function updateMessageCount() {
        const count = messageTextarea.value.length;
        messageCount.textContent = count;
        
        if (count > 1000) {
            messageCount.className = 'text-danger';
            messageTextarea.classList.add('is-invalid');
        } else if (count > 800) {
            messageCount.className = 'text-warning';
            messageTextarea.classList.remove('is-invalid');
        } else {
            messageCount.className = 'text-muted';
            messageTextarea.classList.remove('is-invalid');
        }
    }
    
    messageTextarea.addEventListener('input', updateMessageCount);
    updateMessageCount(); // Initial count

    // Form submission
    const contactForm = document.getElementById('contactForm');
    contactForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Basic validation
        const requiredFields = ['name', 'email', 'subject', 'message'];
        let isValid = true;
        
        // Clear previous validation states
        document.querySelectorAll('.is-invalid').forEach(field => {
            field.classList.remove('is-invalid');
        });
        
        // Check required fields
        requiredFields.forEach(fieldName => {
            const field = document.getElementById(fieldName);
            if (!field.value.trim()) {
                field.classList.add('is-invalid');
                isValid = false;
            }
        });
        
        // Validate email format
        const email = document.getElementById('email').value;
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(email)) {
            document.getElementById('email').classList.add('is-invalid');
            isValid = false;
        }
        
        // Validate message length
        const message = document.getElementById('message').value;
        if (message.length > 1000) {
            document.getElementById('message').classList.add('is-invalid');
            isValid = false;
        }
        
        if (!isValid) {
            alert('Please correct the highlighted errors and try again.');
            return false;
        }
        
        // Show loading state
        const submitBtn = contactForm.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Sending...';
        submitBtn.disabled = true;
        
        // Simulate form submission (replace with actual AJAX call)
        setTimeout(function() {
            // Show success message
            document.getElementById('successMessage').classList.remove('d-none');
            
            // Reset form
            contactForm.reset();
            updateMessageCount();
            
            // Reset button
            submitBtn.innerHTML = originalText;
            submitBtn.disabled = false;
            
            // Scroll to success message
            document.getElementById('successMessage').scrollIntoView({
                behavior: 'smooth',
                block: 'center'
            });
        }, 2000);
    });

    // Auto-fill form if user is logged in
    <?php if (isset($current_user) && $current_user): ?>
    document.getElementById('name').value = '<?php echo addslashes($current_user['full_name']); ?>';
    document.getElementById('email').value = '<?php echo addslashes($current_user['email']); ?>';
    
    // Set user type based on current user
    const userType = '<?php echo $current_user['user_type']; ?>';
    if (userType === 'seeker') {
        document.getElementById('job_seeker').checked = true;
    } else if (userType === 'employer') {
        document.getElementById('employer').checked = true;
    }
    <?php endif; ?>
});
</script>

<?php include __DIR__ . '/../layout/footer.php'; ?>
