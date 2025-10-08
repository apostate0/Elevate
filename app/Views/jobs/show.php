<?php include __DIR__ . '/../layout/header.php'; ?>

<div class="container py-5">
    <div class="row">
        <!-- Job Details -->
        <div class="col-lg-8">
            <div class="card">
                <div class="card-body p-4">
                    <!-- Job Header -->
                    <div class="d-flex justify-content-between align-items-start mb-4">
                        <div>
                            <h1 class="fw-bold mb-2"><?php echo htmlspecialchars($job['title']); ?></h1>
                            <h5 class="text-primary mb-2">
                                <i class="fas fa-building me-2"></i>
                                <?php echo htmlspecialchars($job['company_name']); ?>
                            </h5>
                            <div class="mb-3">
                                <span class="badge bg-primary me-2">
                                    <?php echo ucfirst(str_replace('-', ' ', $job['job_type'])); ?>
                                </span>
                                <?php if (!empty($job['experience_level'])): ?>
                                <span class="badge bg-secondary me-2">
                                    <?php echo htmlspecialchars($job['experience_level']); ?>
                                </span>
                                <?php endif; ?>
                                <?php if ($job['status'] !== 'active'): ?>
                                <span class="badge bg-warning">
                                    <?php echo ucfirst($job['status']); ?>
                                </span>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="text-end">
                            <?php if (!empty($job['salary_range'])): ?>
                            <div class="mb-2">
                                <h5 class="text-success mb-0">
                                    <i class="fas fa-money-bill-wave me-1"></i>
                                    <?php echo htmlspecialchars($job['salary_range']); ?>
                                </h5>
                            </div>
                            <?php endif; ?>
                            <small class="text-muted">
                                <i class="fas fa-clock me-1"></i>
                                Posted <?php echo date('M j, Y', strtotime($job['posted_date'])); ?>
                            </small>
                        </div>
                    </div>

                    <!-- Job Info -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="d-flex align-items-center mb-2">
                                <i class="fas fa-map-marker-alt text-muted me-2"></i>
                                <span><?php echo htmlspecialchars($job['location']); ?></span>
                            </div>
                            <?php if (!empty($job['company_website'])): ?>
                            <div class="d-flex align-items-center mb-2">
                                <i class="fas fa-globe text-muted me-2"></i>
                                <a href="<?php echo htmlspecialchars($job['company_website']); ?>" 
                                   target="_blank" class="text-decoration-none">
                                    Company Website
                                </a>
                            </div>
                            <?php endif; ?>
                        </div>
                        <div class="col-md-6">
                            <?php if (!empty($job['deadline'])): ?>
                            <div class="d-flex align-items-center mb-2">
                                <i class="fas fa-calendar-times text-muted me-2"></i>
                                <span>
                                    Apply by <?php echo date('M j, Y', strtotime($job['deadline'])); ?>
                                    <?php
                                    $days_left = ceil((strtotime($job['deadline']) - time()) / (60 * 60 * 24));
                                    if ($days_left > 0):
                                    ?>
                                    <small class="text-warning ms-1">(<?php echo $days_left; ?> days left)</small>
                                    <?php elseif ($days_left === 0): ?>
                                    <small class="text-danger ms-1">(Last day!)</small>
                                    <?php else: ?>
                                    <small class="text-danger ms-1">(Expired)</small>
                                    <?php endif; ?>
                                </span>
                            </div>
                            <?php endif; ?>
                            
                            <?php if (!empty($job['contact_email'])): ?>
                            <div class="d-flex align-items-center mb-2">
                                <i class="fas fa-envelope text-muted me-2"></i>
                                <span><?php echo htmlspecialchars($job['contact_person']); ?></span>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Job Description -->
                    <div class="mb-4">
                        <h5 class="fw-bold mb-3">
                            <i class="fas fa-file-alt me-2"></i>Job Description
                        </h5>
                        <div class="job-description">
                            <?php echo nl2br(htmlspecialchars($job['description'])); ?>
                        </div>
                    </div>

                    <!-- Requirements -->
                    <?php if (!empty($job['requirements'])): ?>
                    <div class="mb-4">
                        <h5 class="fw-bold mb-3">
                            <i class="fas fa-list-check me-2"></i>Requirements
                        </h5>
                        <div class="job-requirements">
                            <?php echo nl2br(htmlspecialchars($job['requirements'])); ?>
                        </div>
                    </div>
                    <?php endif; ?>

                    <!-- Company Info -->
                    <div class="mb-4">
                        <h5 class="fw-bold mb-3">
                            <i class="fas fa-building me-2"></i>About <?php echo htmlspecialchars($job['company_name']); ?>
                        </h5>
                        <div class="row">
                            <div class="col-md-8">
                                <?php if (!empty($job['company_description'])): ?>
                                <p><?php echo nl2br(htmlspecialchars($job['company_description'])); ?></p>
                                <?php endif; ?>
                            </div>
                            <div class="col-md-4">
                                <?php if (!empty($job['company_size'])): ?>
                                <div class="mb-2">
                                    <strong>Company Size:</strong><br>
                                    <span class="text-muted"><?php echo htmlspecialchars($job['company_size']); ?> employees</span>
                                </div>
                                <?php endif; ?>
                                
                                <?php if (!empty($job['industry'])): ?>
                                <div class="mb-2">
                                    <strong>Industry:</strong><br>
                                    <span class="text-muted"><?php echo htmlspecialchars($job['industry']); ?></span>
                                </div>
                                <?php endif; ?>
                                
                                <?php if (!empty($job['company_location'])): ?>
                                <div class="mb-2">
                                    <strong>Headquarters:</strong><br>
                                    <span class="text-muted"><?php echo htmlspecialchars($job['company_location']); ?></span>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Application Card -->
            <?php if (isset($current_user) && $current_user['user_type'] === 'seeker'): ?>
            <div class="card mb-4">
                <div class="card-body">
                    <h6 class="card-title fw-bold mb-3">
                        <i class="fas fa-paper-plane me-1"></i>Apply for this Job
                    </h6>
                    
                    <?php if ($has_applied): ?>
                        <div class="alert alert-info">
                            <i class="fas fa-check-circle me-2"></i>
                            You have already applied for this job.
                        </div>
                        <a href="/seeker/dashboard" class="btn btn-outline-primary w-100">
                            <i class="fas fa-eye me-2"></i>View My Applications
                        </a>
                    <?php elseif ($job['status'] !== 'active'): ?>
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            This job is no longer accepting applications.
                        </div>
                    <?php elseif (!empty($job['deadline']) && strtotime($job['deadline']) < time()): ?>
                        <div class="alert alert-danger">
                            <i class="fas fa-calendar-times me-2"></i>
                            The application deadline has passed.
                        </div>
                    <?php else: ?>
                        <form action="<?php echo url('jobs/apply'); ?>" method="POST" enctype="multipart/form-data" id="applicationForm">
                            <input type="hidden" name="job_id" value="<?php echo $job['job_id']; ?>">
                            <input type="hidden" name="csrf_token" value="<?php echo $authController->generateCSRFToken(); ?>">
                            
                            <div class="mb-3">
                                <label for="cover_letter" class="form-label">Cover Letter</label>
                                <textarea class="form-control" id="cover_letter" name="cover_letter" 
                                          rows="4" placeholder="Tell the employer why you're perfect for this role..."></textarea>
                            </div>
                            
                            <div class="mb-3">
                                <label for="resume" class="form-label">Resume/CV</label>
                                <input type="file" class="form-control" id="resume" name="resume" 
                                       accept=".pdf,.doc,.docx" onchange="validateFileUpload(this)">
                                <small class="text-muted">PDF, DOC, or DOCX files only. Max 5MB.</small>
                            </div>
                            
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-paper-plane me-2"></i>Submit Application
                            </button>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
            <?php elseif (isset($current_user) && $current_user['user_type'] === 'employer'): ?>
            <div class="card mb-4">
                <div class="card-body">
                    <h6 class="card-title fw-bold mb-3">
                        <i class="fas fa-user-tie me-1"></i>Employer Actions
                    </h6>
                    <div class="d-grid gap-2">
                        <a href="/jobs" class="btn btn-outline-primary">
                            <i class="fas fa-search me-2"></i>Browse More Jobs
                        </a>
                        <a href="/jobs/create" class="btn btn-primary">
                            <i class="fas fa-plus me-2"></i>Post New Job
                        </a>
                    </div>
                </div>
            </div>
            <?php else: ?>
            <div class="card mb-4">
                <div class="card-body">
                    <h6 class="card-title fw-bold mb-3">
                        <i class="fas fa-sign-in-alt me-1"></i>Apply for this Job
                    </h6>
                    <p class="card-text text-muted">
                        You need to be logged in as a job seeker to apply for this position.
                    </p>
                    <div class="d-grid gap-2">
                        <a href="/login" class="btn btn-primary">
                            <i class="fas fa-sign-in-alt me-2"></i>Login
                        </a>
                        <a href="/register" class="btn btn-outline-primary">
                            <i class="fas fa-user-plus me-2"></i>Create Account
                        </a>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <!-- Job Actions -->
            <div class="card mb-4">
                <div class="card-body">
                    <h6 class="card-title fw-bold mb-3">
                        <i class="fas fa-share me-1"></i>Share this Job
                    </h6>
                    <div class="d-grid gap-2">
                        <button class="btn btn-outline-primary btn-sm" onclick="shareJob('facebook')">
                            <i class="fab fa-facebook-f me-2"></i>Share on Facebook
                        </button>
                        <button class="btn btn-outline-info btn-sm" onclick="shareJob('twitter')">
                            <i class="fab fa-twitter me-2"></i>Share on Twitter
                        </button>
                        <button class="btn btn-outline-primary btn-sm" onclick="shareJob('linkedin')">
                            <i class="fab fa-linkedin-in me-2"></i>Share on LinkedIn
                        </button>
                        <button class="btn btn-outline-secondary btn-sm" onclick="copyJobLink()">
                            <i class="fas fa-link me-2"></i>Copy Link
                        </button>
                    </div>
                </div>
            </div>

            <!-- Similar Jobs -->
            <div class="card">
                <div class="card-body">
                    <h6 class="card-title fw-bold mb-3">
                        <i class="fas fa-lightbulb me-1"></i>Similar Jobs
                    </h6>
                    <div class="d-grid gap-2">
                        <a href="/jobs?search=<?php echo urlencode($job['title']); ?>" 
                           class="btn btn-outline-secondary btn-sm text-start">
                            <i class="fas fa-search me-2"></i>More <?php echo htmlspecialchars($job['title']); ?> jobs
                        </a>
                        <a href="/jobs?location=<?php echo urlencode($job['location']); ?>" 
                           class="btn btn-outline-secondary btn-sm text-start">
                            <i class="fas fa-map-marker-alt me-2"></i>Jobs in <?php echo htmlspecialchars($job['location']); ?>
                        </a>
                        <a href="/jobs?job_type=<?php echo $job['job_type']; ?>" 
                           class="btn btn-outline-secondary btn-sm text-start">
                            <i class="fas fa-briefcase me-2"></i><?php echo ucfirst(str_replace('-', ' ', $job['job_type'])); ?> jobs
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Back to Jobs -->
    <div class="row mt-4">
        <div class="col-12">
            <a href="/jobs" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>Back to Job Listings
            </a>
        </div>
    </div>
</div>

<script>
// Share job functionality
function shareJob(platform) {
    const url = encodeURIComponent(window.location.href);
    const title = encodeURIComponent('<?php echo addslashes($job['title']); ?> at <?php echo addslashes($job['company_name']); ?>');
    
    let shareUrl = '';
    
    switch(platform) {
        case 'facebook':
            shareUrl = `https://www.facebook.com/sharer/sharer.php?u=${url}`;
            break;
        case 'twitter':
            shareUrl = `https://twitter.com/intent/tweet?url=${url}&text=${title}`;
            break;
        case 'linkedin':
            shareUrl = `https://www.linkedin.com/sharing/share-offsite/?url=${url}`;
            break;
    }
    
    if (shareUrl) {
        window.open(shareUrl, '_blank', 'width=600,height=400');
    }
}

// Copy job link
function copyJobLink() {
    navigator.clipboard.writeText(window.location.href).then(function() {
        alert('Job link copied to clipboard!');
    }).catch(function(err) {
        console.error('Could not copy text: ', err);
        // Fallback for older browsers
        const textArea = document.createElement('textarea');
        textArea.value = window.location.href;
        document.body.appendChild(textArea);
        textArea.select();
        document.execCommand('copy');
        document.body.removeChild(textArea);
        alert('Job link copied to clipboard!');
    });
}

// Application form validation
document.addEventListener('DOMContentLoaded', function() {
    const applicationForm = document.getElementById('applicationForm');
    if (applicationForm) {
        applicationForm.addEventListener('submit', function(e) {
            const coverLetter = document.getElementById('cover_letter').value.trim();
            const resume = document.getElementById('resume').files[0];
            
            if (!coverLetter && !resume) {
                e.preventDefault();
                alert('Please provide either a cover letter or upload your resume.');
                return false;
            }
            
            if (confirm('Are you sure you want to submit your application?')) {
                // Show loading state
                const submitBtn = this.querySelector('button[type="submit"]');
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Submitting...';
                submitBtn.disabled = true;
            } else {
                e.preventDefault();
            }
        });
    }
});
</script>

<?php include __DIR__ . '/../layout/footer.php'; ?>
