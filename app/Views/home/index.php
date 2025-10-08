<?php include __DIR__ . '/../layout/header.php'; ?>

<!-- Hero Section -->
<section class="hero-section">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <h1 class="display-4 fw-bold mb-4">
                    Find Your Dream Job Today
                </h1>
                <p class="lead mb-4">
                    Connect with top employers and discover amazing career opportunities. 
                    Your next adventure starts here.
                </p>
                <div class="d-flex gap-3 mb-4">
                    <a href="<?php echo url('jobs'); ?>" class="btn btn-light btn-lg">
                        <i class="fas fa-search me-2"></i>Browse Jobs
                    </a>
                    <a href="<?php echo url('register'); ?>" class="btn btn-outline-light btn-lg">
                        <i class="fas fa-user-plus me-2"></i>Get Started
                    </a>
                </div>
                
                <!-- Quick Stats -->
                <?php if (isset($job_stats)): ?>
                <div class="row mt-5">
                    <div class="col-4">
                        <div class="text-center">
                            <h3 class="fw-bold mb-1"><?php echo number_format($job_stats['total_active_jobs'] ?? 0); ?></h3>
                            <small class="opacity-75">Active Jobs</small>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="text-center">
                            <h3 class="fw-bold mb-1"><?php echo number_format($job_stats['companies_with_jobs'] ?? 0); ?></h3>
                            <small class="opacity-75">Companies</small>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="text-center">
                            <h3 class="fw-bold mb-1">1000+</h3>
                            <small class="opacity-75">Success Stories</small>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
            </div>
            <div class="col-lg-6">
                <div class="text-center">
                    <i class="fas fa-rocket fa-10x opacity-75"></i>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Job Search Form -->
<div class="container">
    <div class="search-form">
        <form action="<?php echo url('jobs'); ?>" method="GET" id="job-search-form">
            <div class="row g-3">
                <div class="col-md-4">
                    <label for="search" class="form-label">
                        <i class="fas fa-search me-1"></i>Job Title or Keywords
                    </label>
                    <input type="text" class="form-control" id="search" name="search" 
                           placeholder="e.g. Software Developer">
                </div>
                <div class="col-md-4">
                    <label for="location" class="form-label">
                        <i class="fas fa-map-marker-alt me-1"></i>Location
                    </label>
                    <input type="text" class="form-control" id="location" name="location" 
                           placeholder="e.g. Kathmandu">
                </div>
                <div class="col-md-4">
                    <label for="job_type" class="form-label">
                        <i class="fas fa-briefcase me-1"></i>Job Type
                    </label>
                    <select class="form-select" id="job_type" name="job_type">
                        <option value="">All Types</option>
                        <option value="full-time">Full Time</option>
                        <option value="part-time">Part Time</option>
                        <option value="contract">Contract</option>
                        <option value="internship">Internship</option>
                    </select>
                </div>
                <div class="col-12 text-center">
                    <button type="submit" class="btn btn-primary btn-lg px-5">
                        <i class="fas fa-search me-2"></i>Search Jobs
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Recent Jobs Section -->
<?php if (isset($recent_jobs) && !empty($recent_jobs)): ?>
<section class="py-5">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="fw-bold">Latest Job Opportunities</h2>
            <p class="text-muted">Discover the newest job postings from top companies</p>
        </div>
        
        <div class="row">
            <?php foreach ($recent_jobs as $job): ?>
            <div class="col-lg-6 mb-4">
                <div class="card job-card h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <h5 class="card-title mb-0">
                                <a href="<?php echo url('jobs/show?id=' . $job['job_id']); ?>" 
                                   class="text-decoration-none text-dark">
                                    <?php echo htmlspecialchars($job['title']); ?>
                                </a>
                            </h5>
                            <span class="badge bg-primary"><?php echo ucfirst(str_replace('-', ' ', $job['job_type'])); ?></span>
                        </div>
                        
                        <div class="mb-3">
                            <h6 class="text-primary mb-1">
                                <i class="fas fa-building me-1"></i>
                                <?php echo htmlspecialchars($job['company_name']); ?>
                            </h6>
                            <small class="text-muted">
                                <i class="fas fa-map-marker-alt me-1"></i>
                                <?php echo htmlspecialchars($job['location']); ?>
                            </small>
                        </div>
                        
                        <?php if (!empty($job['salary_range'])): ?>
                        <div class="mb-3">
                            <small class="text-success fw-bold">
                                <i class="fas fa-money-bill-wave me-1"></i>
                                <?php echo htmlspecialchars($job['salary_range']); ?>
                            </small>
                        </div>
                        <?php endif; ?>
                        
                        <div class="d-flex justify-content-between align-items-center">
                            <small class="text-muted">
                                <i class="fas fa-clock me-1"></i>
                                Posted <?php echo date('M j, Y', strtotime($job['posted_date'])); ?>
                            </small>
                            <a href="<?php echo url('jobs/show?id=' . $job['job_id']); ?>" 
                               class="btn btn-outline-primary btn-sm">
                                View Details
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        
        <div class="text-center mt-4">
            <a href="<?php echo url('jobs'); ?>" class="btn btn-primary btn-lg">
                <i class="fas fa-eye me-2"></i>View All Jobs
            </a>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Features Section -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="fw-bold">Why Choose Elevate Portal?</h2>
            <p class="text-muted">We make job searching and hiring easier than ever</p>
        </div>
        
        <div class="row">
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="card h-100 text-center">
                    <div class="card-body">
                        <div class="mb-3">
                            <i class="fas fa-search fa-3x text-primary"></i>
                        </div>
                        <h5 class="card-title">Smart Job Search</h5>
                        <p class="card-text">
                            Find the perfect job with our advanced search filters and personalized recommendations.
                        </p>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="card h-100 text-center">
                    <div class="card-body">
                        <div class="mb-3">
                            <i class="fas fa-users fa-3x text-primary"></i>
                        </div>
                        <h5 class="card-title">Top Companies</h5>
                        <p class="card-text">
                            Connect with leading employers and startups looking for talented professionals like you.
                        </p>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="card h-100 text-center">
                    <div class="card-body">
                        <div class="mb-3">
                            <i class="fas fa-rocket fa-3x text-primary"></i>
                        </div>
                        <h5 class="card-title">Fast Applications</h5>
                        <p class="card-text">
                            Apply to multiple jobs quickly with our streamlined application process and resume builder.
                        </p>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="card h-100 text-center">
                    <div class="card-body">
                        <div class="mb-3">
                            <i class="fas fa-shield-alt fa-3x text-primary"></i>
                        </div>
                        <h5 class="card-title">Secure & Private</h5>
                        <p class="card-text">
                            Your personal information is protected with industry-standard security measures.
                        </p>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="card h-100 text-center">
                    <div class="card-body">
                        <div class="mb-3">
                            <i class="fas fa-mobile-alt fa-3x text-primary"></i>
                        </div>
                        <h5 class="card-title">Mobile Friendly</h5>
                        <p class="card-text">
                            Search and apply for jobs on the go with our responsive mobile-friendly design.
                        </p>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="card h-100 text-center">
                    <div class="card-body">
                        <div class="mb-3">
                            <i class="fas fa-headset fa-3x text-primary"></i>
                        </div>
                        <h5 class="card-title">24/7 Support</h5>
                        <p class="card-text">
                            Get help whenever you need it with our dedicated customer support team.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Call to Action Section -->
<section class="py-5">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <h3 class="fw-bold mb-2">Ready to Take the Next Step in Your Career?</h3>
                <p class="text-muted mb-0">
                    Join thousands of professionals who have found their dream jobs through Elevate Portal.
                </p>
            </div>
            <div class="col-lg-4 text-lg-end">
                <?php if (!isset($current_user) || !$current_user): ?>
                <a href="/register" class="btn btn-primary btn-lg">
                    <i class="fas fa-user-plus me-2"></i>Get Started Today
                </a>
                <?php else: ?>
                <a href="/jobs" class="btn btn-primary btn-lg">
                    <i class="fas fa-search me-2"></i>Browse Jobs
                </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<?php include __DIR__ . '/../layout/footer.php'; ?>
