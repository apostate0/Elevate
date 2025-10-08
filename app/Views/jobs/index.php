<?php include __DIR__ . '/../layout/header.php'; ?>

<div class="container py-5">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-lg-8">
            <h1 class="fw-bold mb-2">
                <i class="fas fa-briefcase me-2"></i>Job Opportunities
            </h1>
            <p class="text-muted">
                Discover amazing career opportunities from top companies
                <?php if (isset($pagination['total_jobs'])): ?>
                    - <?php echo number_format($pagination['total_jobs']); ?> jobs found
                <?php endif; ?>
            </p>
        </div>
        <div class="col-lg-4 text-lg-end">
            <?php if (isset($current_user) && $current_user['user_type'] === 'employer'): ?>
                <a href="<?php echo url('jobs/create'); ?>" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>Post New Job
                </a>
            <?php endif; ?>
        </div>
    </div>

    <!-- Search and Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <form action="<?php echo url('jobs'); ?>" method="GET" class="row g-3">
                <div class="col-md-4">
                    <label for="search" class="form-label">
                        <i class="fas fa-search me-1"></i>Keywords
                    </label>
                    <input type="text" class="form-control" id="search" name="search" 
                           value="<?php echo htmlspecialchars($filters['search'] ?? ''); ?>"
                           placeholder="Job title, company, skills...">
                </div>
                
                <div class="col-md-3">
                    <label for="location" class="form-label">
                        <i class="fas fa-map-marker-alt me-1"></i>Location
                    </label>
                    <input type="text" class="form-control" id="location" name="location" 
                           value="<?php echo htmlspecialchars($filters['location'] ?? ''); ?>"
                           placeholder="City, state, country...">
                </div>
                
                <div class="col-md-2">
                    <label for="job_type" class="form-label">Job Type</label>
                    <select class="form-select" id="job_type" name="job_type">
                        <option value="">All Types</option>
                        <option value="full-time" <?php echo ($filters['job_type'] ?? '') === 'full-time' ? 'selected' : ''; ?>>
                            Full Time
                        </option>
                        <option value="part-time" <?php echo ($filters['job_type'] ?? '') === 'part-time' ? 'selected' : ''; ?>>
                            Part Time
                        </option>
                        <option value="contract" <?php echo ($filters['job_type'] ?? '') === 'contract' ? 'selected' : ''; ?>>
                            Contract
                        </option>
                        <option value="internship" <?php echo ($filters['job_type'] ?? '') === 'internship' ? 'selected' : ''; ?>>
                            Internship
                        </option>
                    </select>
                </div>
                
                <div class="col-md-2">
                    <label for="experience_level" class="form-label">Experience</label>
                    <select class="form-select" id="experience_level" name="experience_level">
                        <option value="">All Levels</option>
                        <option value="Entry-level" <?php echo ($filters['experience_level'] ?? '') === 'Entry-level' ? 'selected' : ''; ?>>
                            Entry Level
                        </option>
                        <option value="Mid-level" <?php echo ($filters['experience_level'] ?? '') === 'Mid-level' ? 'selected' : ''; ?>>
                            Mid Level
                        </option>
                        <option value="Senior" <?php echo ($filters['experience_level'] ?? '') === 'Senior' ? 'selected' : ''; ?>>
                            Senior Level
                        </option>
                    </select>
                </div>
                
                <div class="col-md-1">
                    <label class="form-label">&nbsp;</label>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </div>
            </form>
            
            <?php if (!empty($filters)): ?>
            <div class="mt-3">
                <span class="text-muted me-2">Active filters:</span>
                <?php foreach ($filters as $key => $value): ?>
                    <?php if (!empty($value)): ?>
                        <span class="badge bg-secondary me-1">
                            <?php echo ucfirst(str_replace('_', ' ', $key)); ?>: <?php echo htmlspecialchars($value); ?>
                            <a href="?<?php echo http_build_query(array_merge($_GET, [$key => ''])); ?>" 
                               class="text-white text-decoration-none ms-1">×</a>
                        </span>
                    <?php endif; ?>
                <?php endforeach; ?>
                <a href="<?php echo url('jobs'); ?>" class="btn btn-sm btn-outline-secondary ms-2">Clear All</a>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="row">
        <!-- Job Listings -->
        <div class="col-lg-8">
            <?php if (!empty($jobs)): ?>
                <?php foreach ($jobs as $job): ?>
                <div class="card job-card mb-4">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-8">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <h5 class="card-title mb-0">
                                        <a href="<?php echo url('jobs/show?id=' . $job['job_id']); ?>" 
                                           class="text-decoration-none text-dark">
                                            <?php echo htmlspecialchars($job['title']); ?>
                                        </a>
                                    </h5>
                                    <span class="badge bg-primary ms-2">
                                        <?php echo ucfirst(str_replace('-', ' ', $job['job_type'])); ?>
                                    </span>
                                </div>
                                
                                <h6 class="text-primary mb-2">
                                    <i class="fas fa-building me-1"></i>
                                    <?php echo htmlspecialchars($job['company_name']); ?>
                                </h6>
                                
                                <div class="mb-2">
                                    <small class="text-muted me-3">
                                        <i class="fas fa-map-marker-alt me-1"></i>
                                        <?php echo htmlspecialchars($job['location']); ?>
                                    </small>
                                    
                                    <?php if (!empty($job['experience_level'])): ?>
                                    <small class="text-muted me-3">
                                        <i class="fas fa-user-tie me-1"></i>
                                        <?php echo htmlspecialchars($job['experience_level']); ?>
                                    </small>
                                    <?php endif; ?>
                                    
                                    <?php if (!empty($job['salary_range'])): ?>
                                    <small class="text-success fw-bold">
                                        <i class="fas fa-money-bill-wave me-1"></i>
                                        <?php echo htmlspecialchars($job['salary_range']); ?>
                                    </small>
                                    <?php endif; ?>
                                </div>
                                
                                <p class="card-text text-muted">
                                    <?php echo htmlspecialchars(substr($job['description'], 0, 150)); ?>
                                    <?php if (strlen($job['description']) > 150): ?>...<?php endif; ?>
                                </p>
                            </div>
                            
                            <div class="col-md-4 text-md-end">
                                <div class="mb-2">
                                    <small class="text-muted">
                                        <i class="fas fa-clock me-1"></i>
                                        Posted <?php echo date('M j, Y', strtotime($job['posted_date'])); ?>
                                    </small>
                                </div>
                                
                                <?php if (isset($job['application_count']) && $job['application_count'] > 0): ?>
                                <div class="mb-2">
                                    <small class="text-info">
                                        <i class="fas fa-users me-1"></i>
                                        <?php echo $job['application_count']; ?> applicant<?php echo $job['application_count'] > 1 ? 's' : ''; ?>
                                    </small>
                                </div>
                                <?php endif; ?>
                                
                                <?php if (!empty($job['deadline'])): ?>
                                <div class="mb-2">
                                    <small class="text-warning">
                                        <i class="fas fa-calendar-times me-1"></i>
                                        Deadline: <?php echo date('M j, Y', strtotime($job['deadline'])); ?>
                                    </small>
                                </div>
                                <?php endif; ?>
                                
                                <a href="<?php echo url('jobs/show?id=' . $job['job_id']); ?>" 
                                   class="btn btn-outline-primary">
                                    View Details
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
                
                <!-- Pagination -->
                <?php if (isset($pagination) && $pagination['total_pages'] > 1): ?>
                <nav aria-label="Job listings pagination">
                    <ul class="pagination justify-content-center">
                        <?php if ($pagination['current_page'] > 1): ?>
                            <li class="page-item">
                                <a class="page-link" href="?<?php echo http_build_query(array_merge($_GET, ['page' => $pagination['current_page'] - 1])); ?>">
                                    <i class="fas fa-chevron-left"></i> Previous
                                </a>
                            </li>
                        <?php endif; ?>
                        
                        <?php
                        $start_page = max(1, $pagination['current_page'] - 2);
                        $end_page = min($pagination['total_pages'], $pagination['current_page'] + 2);
                        ?>
                        
                        <?php if ($start_page > 1): ?>
                            <li class="page-item">
                                <a class="page-link" href="?<?php echo http_build_query(array_merge($_GET, ['page' => 1])); ?>">1</a>
                            </li>
                            <?php if ($start_page > 2): ?>
                                <li class="page-item disabled"><span class="page-link">...</span></li>
                            <?php endif; ?>
                        <?php endif; ?>
                        
                        <?php for ($i = $start_page; $i <= $end_page; $i++): ?>
                            <li class="page-item <?php echo $i === $pagination['current_page'] ? 'active' : ''; ?>">
                                <a class="page-link" href="?<?php echo http_build_query(array_merge($_GET, ['page' => $i])); ?>">
                                    <?php echo $i; ?>
                                </a>
                            </li>
                        <?php endfor; ?>
                        
                        <?php if ($end_page < $pagination['total_pages']): ?>
                            <?php if ($end_page < $pagination['total_pages'] - 1): ?>
                                <li class="page-item disabled"><span class="page-link">...</span></li>
                            <?php endif; ?>
                            <li class="page-item">
                                <a class="page-link" href="?<?php echo http_build_query(array_merge($_GET, ['page' => $pagination['total_pages']])); ?>">
                                    <?php echo $pagination['total_pages']; ?>
                                </a>
                            </li>
                        <?php endif; ?>
                        
                        <?php if ($pagination['current_page'] < $pagination['total_pages']): ?>
                            <li class="page-item">
                                <a class="page-link" href="?<?php echo http_build_query(array_merge($_GET, ['page' => $pagination['current_page'] + 1])); ?>">
                                    Next <i class="fas fa-chevron-right"></i>
                                </a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </nav>
                <?php endif; ?>
                
            <?php else: ?>
                <div class="text-center py-5">
                    <i class="fas fa-search fa-4x text-muted mb-3"></i>
                    <h4 class="text-muted">No jobs found</h4>
                    <p class="text-muted">
                        Try adjusting your search criteria or 
                        <a href="<?php echo url('jobs'); ?>" class="text-primary">browse all jobs</a>
                    </p>
                </div>
            <?php endif; ?>
        </div>
        
        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Quick Stats -->
            <div class="card mb-4">
                <div class="card-body">
                    <h6 class="card-title fw-bold mb-3">
                        <i class="fas fa-chart-bar me-1"></i>Job Market Stats
                    </h6>
                    
                    <?php if (isset($pagination)): ?>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Total Jobs:</span>
                        <span class="fw-bold text-primary"><?php echo number_format($pagination['total_jobs']); ?></span>
                    </div>
                    <?php endif; ?>
                    
                    <div class="d-flex justify-content-between mb-2">
                        <span>New This Week:</span>
                        <span class="fw-bold text-success">25+</span>
                    </div>
                    
                    <div class="d-flex justify-content-between">
                        <span>Active Companies:</span>
                        <span class="fw-bold text-info">150+</span>
                    </div>
                </div>
            </div>
            
            <!-- Job Categories -->
            <div class="card mb-4">
                <div class="card-body">
                    <h6 class="card-title fw-bold mb-3">
                        <i class="fas fa-tags me-1"></i>Popular Categories
                    </h6>
                    
                    <div class="d-grid gap-2">
                        <a href="<?php echo url('jobs?search=software'); ?>" class="btn btn-outline-secondary btn-sm text-start">
                            <i class="fas fa-code me-2"></i>Software Development
                        </a>
                        <a href="<?php echo url('jobs?search=marketing'); ?>" class="btn btn-outline-secondary btn-sm text-start">
                            <i class="fas fa-bullhorn me-2"></i>Marketing & Sales
                        </a>
                        <a href="<?php echo url('jobs?search=design'); ?>" class="btn btn-outline-secondary btn-sm text-start">
                            <i class="fas fa-paint-brush me-2"></i>Design & Creative
                        </a>
                        <a href="<?php echo url('jobs?search=finance'); ?>" class="btn btn-outline-secondary btn-sm text-start">
                            <i class="fas fa-chart-line me-2"></i>Finance & Accounting
                        </a>
                        <a href="<?php echo url('jobs?search=hr'); ?>" class="btn btn-outline-secondary btn-sm text-start">
                            <i class="fas fa-users me-2"></i>Human Resources
                        </a>
                    </div>
                </div>
            </div>
            
            <!-- Job Alerts -->
            <?php if (isset($current_user) && $current_user['user_type'] === 'seeker'): ?>
            <div class="card">
                <div class="card-body">
                    <h6 class="card-title fw-bold mb-3">
                        <i class="fas fa-bell me-1"></i>Job Alerts
                    </h6>
                    <p class="card-text small text-muted">
                        Get notified when new jobs matching your criteria are posted.
                    </p>
                    <button class="btn btn-primary btn-sm w-100">
                        <i class="fas fa-plus me-1"></i>Create Job Alert
                    </button>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../layout/footer.php'; ?>
