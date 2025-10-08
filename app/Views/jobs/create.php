<?php include __DIR__ . '/../layout/header.php'; ?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">
                        <i class="fas fa-plus me-2"></i>Post a New Job
                    </h4>
                    <p class="text-muted mb-0">Fill out the details below to attract the best candidates</p>
                </div>
                <div class="card-body p-4">
                    <form action="<?php echo url('jobs/create'); ?>" method="POST" id="jobForm">
                        <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                        
                        <!-- Basic Information -->
                        <div class="mb-4">
                            <h5 class="fw-bold mb-3">
                                <i class="fas fa-info-circle me-2"></i>Basic Information
                            </h5>
                            
                            <div class="row">
                                <div class="col-md-8 mb-3">
                                    <label for="title" class="form-label">
                                        Job Title <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" class="form-control" id="title" name="title" 
                                           value="<?php echo htmlspecialchars($old_input['title'] ?? ''); ?>"
                                           placeholder="e.g. Senior Software Developer" required>
                                    <small class="text-muted">Be specific and use keywords candidates might search for</small>
                                </div>
                                
                                <div class="col-md-4 mb-3">
                                    <label for="job_type" class="form-label">
                                        Job Type <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-select" id="job_type" name="job_type" required>
                                        <option value="">Select job type</option>
                                        <option value="full-time" <?php echo ($old_input['job_type'] ?? '') === 'full-time' ? 'selected' : ''; ?>>
                                            Full Time
                                        </option>
                                        <option value="part-time" <?php echo ($old_input['job_type'] ?? '') === 'part-time' ? 'selected' : ''; ?>>
                                            Part Time
                                        </option>
                                        <option value="contract" <?php echo ($old_input['job_type'] ?? '') === 'contract' ? 'selected' : ''; ?>>
                                            Contract
                                        </option>
                                        <option value="internship" <?php echo ($old_input['job_type'] ?? '') === 'internship' ? 'selected' : ''; ?>>
                                            Internship
                                        </option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="location" class="form-label">
                                        Location <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" class="form-control" id="location" name="location" 
                                           value="<?php echo htmlspecialchars($old_input['location'] ?? ''); ?>"
                                           placeholder="e.g. Kathmandu, Nepal or Remote" required>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="experience_level" class="form-label">Experience Level</label>
                                    <select class="form-select" id="experience_level" name="experience_level">
                                        <option value="">Select experience level</option>
                                        <option value="Entry-level" <?php echo ($old_input['experience_level'] ?? '') === 'Entry-level' ? 'selected' : ''; ?>>
                                            Entry Level (0-2 years)
                                        </option>
                                        <option value="Mid-level" <?php echo ($old_input['experience_level'] ?? '') === 'Mid-level' ? 'selected' : ''; ?>>
                                            Mid Level (2-5 years)
                                        </option>
                                        <option value="Senior" <?php echo ($old_input['experience_level'] ?? '') === 'Senior' ? 'selected' : ''; ?>>
                                            Senior Level (5+ years)
                                        </option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="salary_range" class="form-label">Salary Range</label>
                                    <input type="text" class="form-control" id="salary_range" name="salary_range" 
                                           value="<?php echo htmlspecialchars($old_input['salary_range'] ?? ''); ?>"
                                           placeholder="e.g. Rs. 50,000 - Rs. 80,000">
                                    <small class="text-muted">Optional but recommended to attract more candidates</small>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="deadline" class="form-label">Application Deadline</label>
                                    <input type="date" class="form-control" id="deadline" name="deadline" 
                                           value="<?php echo htmlspecialchars($old_input['deadline'] ?? ''); ?>"
                                           min="<?php echo date('Y-m-d', strtotime('+1 day')); ?>">
                                    <small class="text-muted">Leave blank for no deadline</small>
                                </div>
                            </div>
                        </div>

                        <!-- Job Description -->
                        <div class="mb-4">
                            <h5 class="fw-bold mb-3">
                                <i class="fas fa-file-alt me-2"></i>Job Description
                            </h5>
                            
                            <div class="mb-3">
                                <label for="description" class="form-label">
                                    Job Description <span class="text-danger">*</span>
                                </label>
                                <textarea class="form-control" id="description" name="description" 
                                          rows="8" required placeholder="Describe the role, responsibilities, and what makes this opportunity exciting..."><?php echo htmlspecialchars($old_input['description'] ?? ''); ?></textarea>
                                <small class="text-muted">
                                    <span id="descriptionCount">0</span> characters. 
                                    Aim for 300-1000 characters for best results.
                                </small>
                            </div>
                            
                            <div class="mb-3">
                                <label for="requirements" class="form-label">Requirements & Qualifications</label>
                                <textarea class="form-control" id="requirements" name="requirements" 
                                          rows="6" placeholder="List the required skills, qualifications, and experience..."><?php echo htmlspecialchars($old_input['requirements'] ?? ''); ?></textarea>
                                <small class="text-muted">
                                    Include required skills, education, experience, and any specific qualifications
                                </small>
                            </div>
                        </div>

                        <!-- Job Settings -->
                        <div class="mb-4">
                            <h5 class="fw-bold mb-3">
                                <i class="fas fa-cog me-2"></i>Job Settings
                            </h5>
                            
                            <div class="mb-3">
                                <label for="status" class="form-label">Job Status</label>
                                <select class="form-select" id="status" name="status">
                                    <option value="active" <?php echo ($old_input['status'] ?? 'active') === 'active' ? 'selected' : ''; ?>>
                                        Active - Visible to job seekers
                                    </option>
                                    <option value="draft" <?php echo ($old_input['status'] ?? '') === 'draft' ? 'selected' : ''; ?>>
                                        Draft - Save for later
                                    </option>
                                </select>
                            </div>
                        </div>

                        <!-- Company Preview -->
                        <div class="mb-4">
                            <h5 class="fw-bold mb-3">
                                <i class="fas fa-building me-2"></i>Company Information
                            </h5>
                            <div class="alert alert-info">
                                <div class="row">
                                    <div class="col-md-8">
                                        <h6 class="alert-heading"><?php echo htmlspecialchars($company['company_name']); ?></h6>
                                        <p class="mb-1"><?php echo htmlspecialchars($company['company_location']); ?></p>
                                        <?php if (!empty($company['industry'])): ?>
                                        <small class="text-muted"><?php echo htmlspecialchars($company['industry']); ?></small>
                                        <?php endif; ?>
                                    </div>
                                    <div class="col-md-4 text-md-end">
                                        <a href="/employer/profile" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-edit me-1"></i>Edit Company Profile
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Form Actions -->
                        <div class="d-flex justify-content-between">
                            <a href="/employer/dashboard" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left me-2"></i>Cancel
                            </a>
                            <div>
                                <button type="submit" name="action" value="draft" class="btn btn-outline-primary me-2">
                                    <i class="fas fa-save me-2"></i>Save as Draft
                                </button>
                                <button type="submit" name="action" value="publish" class="btn btn-primary">
                                    <i class="fas fa-paper-plane me-2"></i>Publish Job
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Tips Card -->
            <div class="card mt-4">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="fas fa-lightbulb me-2"></i>Tips for Writing Great Job Posts
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <ul class="list-unstyled mb-0">
                                <li class="mb-2">
                                    <i class="fas fa-check text-success me-2"></i>
                                    <small>Use clear, specific job titles</small>
                                </li>
                                <li class="mb-2">
                                    <i class="fas fa-check text-success me-2"></i>
                                    <small>Include salary range to attract more candidates</small>
                                </li>
                                <li class="mb-2">
                                    <i class="fas fa-check text-success me-2"></i>
                                    <small>Describe your company culture</small>
                                </li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <ul class="list-unstyled mb-0">
                                <li class="mb-2">
                                    <i class="fas fa-check text-success me-2"></i>
                                    <small>List required vs. preferred qualifications</small>
                                </li>
                                <li class="mb-2">
                                    <i class="fas fa-check text-success me-2"></i>
                                    <small>Mention growth opportunities</small>
                                </li>
                                <li class="mb-2">
                                    <i class="fas fa-check text-success me-2"></i>
                                    <small>Keep it concise but informative</small>
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
    // Character counter for description
    const descriptionTextarea = document.getElementById('description');
    const descriptionCount = document.getElementById('descriptionCount');
    
    function updateDescriptionCount() {
        const count = descriptionTextarea.value.length;
        descriptionCount.textContent = count;
        
        if (count < 300) {
            descriptionCount.className = 'text-warning';
        } else if (count > 1000) {
            descriptionCount.className = 'text-danger';
        } else {
            descriptionCount.className = 'text-success';
        }
    }
    
    descriptionTextarea.addEventListener('input', updateDescriptionCount);
    updateDescriptionCount(); // Initial count

    // Form validation
    const jobForm = document.getElementById('jobForm');
    jobForm.addEventListener('submit', function(e) {
        const requiredFields = ['title', 'job_type', 'location', 'description'];
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
        
        // Validate description length
        const description = document.getElementById('description').value;
        if (description.length < 100) {
            document.getElementById('description').classList.add('is-invalid');
            alert('Job description should be at least 100 characters long.');
            isValid = false;
        }
        
        // Validate deadline
        const deadline = document.getElementById('deadline').value;
        if (deadline) {
            const deadlineDate = new Date(deadline);
            const today = new Date();
            today.setHours(0, 0, 0, 0);
            
            if (deadlineDate <= today) {
                document.getElementById('deadline').classList.add('is-invalid');
                alert('Application deadline must be in the future.');
                isValid = false;
            }
        }
        
        if (!isValid) {
            e.preventDefault();
            alert('Please correct the highlighted errors and try again.');
            return false;
        }
        
        // Set status based on button clicked
        const clickedButton = e.submitter;
        const statusField = document.getElementById('status');
        
        if (clickedButton.value === 'draft') {
            statusField.value = 'draft';
        } else {
            statusField.value = 'active';
        }
        
        // Show loading state
        clickedButton.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Processing...';
        clickedButton.disabled = true;
    });

    // Auto-save draft functionality (optional)
    let autoSaveTimer;
    const formInputs = jobForm.querySelectorAll('input, textarea, select');
    
    formInputs.forEach(input => {
        input.addEventListener('input', function() {
            clearTimeout(autoSaveTimer);
            autoSaveTimer = setTimeout(function() {
                // You could implement auto-save functionality here
                console.log('Auto-save triggered');
            }, 30000); // Auto-save after 30 seconds of inactivity
        });
    });

    // Preview functionality
    function updatePreview() {
        const title = document.getElementById('title').value;
        const company = '<?php echo addslashes($company['company_name']); ?>';
        const location = document.getElementById('location').value;
        const jobType = document.getElementById('job_type').value;
        const salary = document.getElementById('salary_range').value;
        
        // You could show a live preview of the job posting here
    }
    
    // Update preview on input changes
    formInputs.forEach(input => {
        input.addEventListener('input', updatePreview);
    });
});
</script>

<?php include __DIR__ . '/../layout/footer.php'; ?>
