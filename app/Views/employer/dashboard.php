<?php include __DIR__ . '/../layout/header.php'; ?>

<div class="container py-5">
    <!-- Dashboard Header -->
    <div class="row mb-4">
        <div class="col-lg-8">
            <h1 class="fw-bold mb-2">
                <i class="fas fa-tachometer-alt me-2"></i>Employer Dashboard
            </h1>
            <p class="text-muted">
                Welcome back, <?php echo htmlspecialchars($company['company_name']); ?>!
                Manage your job postings and applications.
            </p>
        </div>
        <div class="col-lg-4 text-lg-end">
            <a href="/jobs/create" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>Post New Job
            </a>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card stats-card text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h3 class="fw-bold mb-0"><?php echo $company_stats['total_jobs'] ?? 0; ?></h3>
                            <small class="opacity-75">Total Jobs Posted</small>
                        </div>
                        <i class="fas fa-briefcase fa-2x opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h3 class="fw-bold mb-0"><?php echo $company_stats['active_jobs'] ?? 0; ?></h3>
                            <small class="opacity-75">Active Jobs</small>
                        </div>
                        <i class="fas fa-check-circle fa-2x opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h3 class="fw-bold mb-0"><?php echo $app_stats['total_applications'] ?? 0; ?></h3>
                            <small class="opacity-75">Total Applications</small>
                        </div>
                        <i class="fas fa-file-alt fa-2x opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h3 class="fw-bold mb-0"><?php echo $app_stats['by_status']['pending'] ?? 0; ?></h3>
                            <small class="opacity-75">Pending Review</small>
                        </div>
                        <i class="fas fa-clock fa-2x opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Job Listings -->
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-briefcase me-2"></i>Your Job Postings
                    </h5>
                    <a href="/jobs/create" class="btn btn-sm btn-primary">
                        <i class="fas fa-plus me-1"></i>Add New
                    </a>
                </div>
                <div class="card-body p-0">
                    <?php if (!empty($jobs)): ?>
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Job Title</th>
                                        <th>Status</th>
                                        <th>Applications</th>
                                        <th>Posted Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($jobs as $job): ?>
                                    <tr>
                                        <td>
                                            <div>
                                                <h6 class="mb-1">
                                                    <a href="/jobs/show?id=<?php echo $job['job_id']; ?>" 
                                                       class="text-decoration-none">
                                                        <?php echo htmlspecialchars($job['title']); ?>
                                                    </a>
                                                </h6>
                                                <small class="text-muted">
                                                    <i class="fas fa-map-marker-alt me-1"></i>
                                                    <?php echo htmlspecialchars($job['location']); ?>
                                                </small>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-<?php 
                                                echo $job['status'] === 'active' ? 'success' : 
                                                    ($job['status'] === 'closed' ? 'secondary' : 'warning'); 
                                            ?>">
                                                <?php echo ucfirst($job['status']); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge bg-info">
                                                <?php echo $job['application_count']; ?> 
                                                application<?php echo $job['application_count'] != 1 ? 's' : ''; ?>
                                            </span>
                                        </td>
                                        <td>
                                            <small class="text-muted">
                                                <?php echo date('M j, Y', strtotime($job['posted_date'])); ?>
                                            </small>
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <a href="/jobs/show?id=<?php echo $job['job_id']; ?>" 
                                                   class="btn btn-outline-primary" title="View">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="/jobs/edit?id=<?php echo $job['job_id']; ?>" 
                                                   class="btn btn-outline-secondary" title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <button type="button" class="btn btn-outline-danger" 
                                                        title="Delete" onclick="deleteJob(<?php echo $job['job_id']; ?>)">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        
                        <!-- Pagination -->
                        <?php if (isset($jobs_pagination) && $jobs_pagination['total_pages'] > 1): ?>
                        <div class="card-footer">
                            <nav aria-label="Jobs pagination">
                                <ul class="pagination pagination-sm justify-content-center mb-0">
                                    <?php if ($jobs_pagination['current_page'] > 1): ?>
                                        <li class="page-item">
                                            <a class="page-link" href="?page=<?php echo $jobs_pagination['current_page'] - 1; ?>">
                                                Previous
                                            </a>
                                        </li>
                                    <?php endif; ?>
                                    
                                    <?php for ($i = 1; $i <= $jobs_pagination['total_pages']; $i++): ?>
                                        <li class="page-item <?php echo $i === $jobs_pagination['current_page'] ? 'active' : ''; ?>">
                                            <a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                                        </li>
                                    <?php endfor; ?>
                                    
                                    <?php if ($jobs_pagination['current_page'] < $jobs_pagination['total_pages']): ?>
                                        <li class="page-item">
                                            <a class="page-link" href="?page=<?php echo $jobs_pagination['current_page'] + 1; ?>">
                                                Next
                                            </a>
                                        </li>
                                    <?php endif; ?>
                                </ul>
                            </nav>
                        </div>
                        <?php endif; ?>
                        
                    <?php else: ?>
                        <div class="text-center py-5">
                            <i class="fas fa-briefcase fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">No jobs posted yet</h5>
                            <p class="text-muted">Start by posting your first job to attract talented candidates.</p>
                            <a href="/jobs/create" class="btn btn-primary">
                                <i class="fas fa-plus me-2"></i>Post Your First Job
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Recent Applications -->
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">
                        <i class="fas fa-file-alt me-2"></i>Recent Applications
                    </h6>
                    <a href="/employer/applications" class="btn btn-sm btn-outline-primary">View All</a>
                </div>
                <div class="card-body p-0">
                    <?php if (!empty($recent_applications)): ?>
                        <div class="list-group list-group-flush">
                            <?php foreach ($recent_applications as $application): ?>
                            <div class="list-group-item">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div class="flex-grow-1">
                                        <h6 class="mb-1"><?php echo htmlspecialchars($application['full_name']); ?></h6>
                                        <p class="mb-1 small text-muted">
                                            Applied for: <?php echo htmlspecialchars($application['job_title']); ?>
                                        </p>
                                        <small class="text-muted">
                                            <?php echo date('M j, Y', strtotime($application['applied_date'])); ?>
                                        </small>
                                    </div>
                                    <span class="badge bg-<?php 
                                        echo $application['status'] === 'pending' ? 'warning' : 
                                            ($application['status'] === 'accepted' ? 'success' : 
                                            ($application['status'] === 'rejected' ? 'danger' : 'info')); 
                                    ?>">
                                        <?php echo ucfirst($application['status']); ?>
                                    </span>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-4">
                            <i class="fas fa-inbox fa-2x text-muted mb-2"></i>
                            <p class="text-muted mb-0">No applications yet</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Company Profile -->
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="fas fa-building me-2"></i>Company Profile
                    </h6>
                </div>
                <div class="card-body">
                    <div class="text-center mb-3">
                        <i class="fas fa-building fa-3x text-primary mb-2"></i>
                        <h6 class="fw-bold"><?php echo htmlspecialchars($company['company_name']); ?></h6>
                        <small class="text-muted"><?php echo htmlspecialchars($company['company_location']); ?></small>
                    </div>
                    
                    <?php if (!empty($company['industry'])): ?>
                    <div class="mb-2">
                        <strong>Industry:</strong><br>
                        <small class="text-muted"><?php echo htmlspecialchars($company['industry']); ?></small>
                    </div>
                    <?php endif; ?>
                    
                    <?php if (!empty($company['company_size'])): ?>
                    <div class="mb-3">
                        <strong>Company Size:</strong><br>
                        <small class="text-muted"><?php echo htmlspecialchars($company['company_size']); ?> employees</small>
                    </div>
                    <?php endif; ?>
                    
                    <div class="d-grid">
                        <a href="/employer/profile" class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-edit me-1"></i>Edit Profile
                        </a>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="fas fa-bolt me-2"></i>Quick Actions
                    </h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="/jobs/create" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus me-2"></i>Post New Job
                        </a>
                        <a href="/employer/applications" class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-file-alt me-2"></i>Manage Applications
                        </a>
                        <a href="/employer/profile" class="btn btn-outline-secondary btn-sm">
                            <i class="fas fa-building me-2"></i>Company Profile
                        </a>
                        <a href="/jobs" class="btn btn-outline-info btn-sm">
                            <i class="fas fa-search me-2"></i>Browse All Jobs
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Job Modal -->
<div class="modal fade" id="deleteJobModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-exclamation-triangle text-danger me-2"></i>
                    Confirm Delete
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this job posting?</p>
                <p class="text-muted small">
                    <strong>Warning:</strong> This action cannot be undone. All applications for this job will also be deleted.
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form id="deleteJobForm" method="POST" action="<?php echo url('jobs/delete'); ?>" style="display: inline;">
                    <input type="hidden" name="job_id" id="deleteJobId">
                    <input type="hidden" name="csrf_token" value="<?php echo $authController->generateCSRFToken(); ?>">
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash me-1"></i>Delete Job
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function deleteJob(jobId) {
    document.getElementById('deleteJobId').value = jobId;
    const modal = new bootstrap.Modal(document.getElementById('deleteJobModal'));
    modal.show();
}

// Auto-refresh stats every 30 seconds
setInterval(function() {
    // You could implement AJAX refresh of stats here
}, 30000);
</script>

<?php include __DIR__ . '/../layout/footer.php'; ?>
