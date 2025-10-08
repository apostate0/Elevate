<?php include __DIR__ . '/../layout/header.php'; ?>

<div class="container py-5">
    <!-- Dashboard Header -->
    <div class="row mb-4">
        <div class="col-lg-8">
            <h1 class="fw-bold mb-2">
                <i class="fas fa-tachometer-alt me-2"></i>Job Seeker Dashboard
            </h1>
            <p class="text-muted">
                Welcome back! Track your applications and discover new opportunities.
            </p>
        </div>
        <div class="col-lg-4 text-lg-end">
            <a href="/jobs" class="btn btn-primary">
                <i class="fas fa-search me-2"></i>Browse Jobs
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
                            <h3 class="fw-bold mb-0"><?php echo count($applications ?? []); ?></h3>
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
                            <h3 class="fw-bold mb-0">
                                <?php 
                                $pending = array_filter($applications ?? [], function($app) { 
                                    return $app['status'] === 'pending'; 
                                });
                                echo count($pending);
                                ?>
                            </h3>
                            <small class="opacity-75">Pending Review</small>
                        </div>
                        <i class="fas fa-clock fa-2x opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h3 class="fw-bold mb-0">
                                <?php 
                                $reviewed = array_filter($applications ?? [], function($app) { 
                                    return $app['status'] === 'reviewed'; 
                                });
                                echo count($reviewed);
                                ?>
                            </h3>
                            <small class="opacity-75">Under Review</small>
                        </div>
                        <i class="fas fa-eye fa-2x opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h3 class="fw-bold mb-0">
                                <?php 
                                $accepted = array_filter($applications ?? [], function($app) { 
                                    return $app['status'] === 'accepted'; 
                                });
                                echo count($accepted);
                                ?>
                            </h3>
                            <small class="opacity-75">Accepted</small>
                        </div>
                        <i class="fas fa-check-circle fa-2x opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Applications List -->
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-file-alt me-2"></i>My Applications
                    </h5>
                    <div class="btn-group btn-group-sm">
                        <button type="button" class="btn btn-outline-secondary active" data-filter="all">
                            All
                        </button>
                        <button type="button" class="btn btn-outline-secondary" data-filter="pending">
                            Pending
                        </button>
                        <button type="button" class="btn btn-outline-secondary" data-filter="reviewed">
                            Reviewed
                        </button>
                        <button type="button" class="btn btn-outline-secondary" data-filter="accepted">
                            Accepted
                        </button>
                    </div>
                </div>
                <div class="card-body p-0">
                    <?php if (!empty($applications)): ?>
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Job Title</th>
                                        <th>Company</th>
                                        <th>Status</th>
                                        <th>Applied Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($applications as $application): ?>
                                    <tr data-status="<?php echo $application['status']; ?>">
                                        <td>
                                            <div>
                                                <h6 class="mb-1">
                                                    <a href="/jobs/show?id=<?php echo $application['job_id']; ?>" 
                                                       class="text-decoration-none">
                                                        <?php echo htmlspecialchars($application['title']); ?>
                                                    </a>
                                                </h6>
                                                <small class="text-muted">
                                                    <i class="fas fa-map-marker-alt me-1"></i>
                                                    <?php echo htmlspecialchars($application['location']); ?>
                                                </small>
                                                <br>
                                                <small class="text-muted">
                                                    <i class="fas fa-briefcase me-1"></i>
                                                    <?php echo ucfirst(str_replace('-', ' ', $application['job_type'])); ?>
                                                </small>
                                            </div>
                                        </td>
                                        <td>
                                            <div>
                                                <h6 class="mb-1 text-primary">
                                                    <?php echo htmlspecialchars($application['company_name']); ?>
                                                </h6>
                                                <small class="text-muted">
                                                    <?php echo htmlspecialchars($application['company_location']); ?>
                                                </small>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-<?php 
                                                echo $application['status'] === 'pending' ? 'warning' : 
                                                    ($application['status'] === 'accepted' ? 'success' : 
                                                    ($application['status'] === 'rejected' ? 'danger' : 'info')); 
                                            ?>">
                                                <?php echo ucfirst($application['status']); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <small class="text-muted">
                                                <?php echo date('M j, Y', strtotime($application['applied_date'])); ?>
                                            </small>
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <a href="/jobs/show?id=<?php echo $application['job_id']; ?>" 
                                                   class="btn btn-outline-primary" title="View Job">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <?php if (!empty($application['cover_letter'])): ?>
                                                <button type="button" class="btn btn-outline-secondary" 
                                                        title="View Cover Letter" 
                                                        onclick="showCoverLetter('<?php echo htmlspecialchars($application['cover_letter'], ENT_QUOTES); ?>')">
                                                    <i class="fas fa-file-text"></i>
                                                </button>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        
                        <!-- Pagination -->
                        <?php if (isset($applications_pagination) && $applications_pagination['total_pages'] > 1): ?>
                        <div class="card-footer">
                            <nav aria-label="Applications pagination">
                                <ul class="pagination pagination-sm justify-content-center mb-0">
                                    <?php if ($applications_pagination['current_page'] > 1): ?>
                                        <li class="page-item">
                                            <a class="page-link" href="?page=<?php echo $applications_pagination['current_page'] - 1; ?>">
                                                Previous
                                            </a>
                                        </li>
                                    <?php endif; ?>
                                    
                                    <?php for ($i = 1; $i <= $applications_pagination['total_pages']; $i++): ?>
                                        <li class="page-item <?php echo $i === $applications_pagination['current_page'] ? 'active' : ''; ?>">
                                            <a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                                        </li>
                                    <?php endfor; ?>
                                    
                                    <?php if ($applications_pagination['current_page'] < $applications_pagination['total_pages']): ?>
                                        <li class="page-item">
                                            <a class="page-link" href="?page=<?php echo $applications_pagination['current_page'] + 1; ?>">
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
                            <i class="fas fa-file-alt fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">No applications yet</h5>
                            <p class="text-muted">Start applying to jobs to see your applications here.</p>
                            <a href="/jobs" class="btn btn-primary">
                                <i class="fas fa-search me-2"></i>Browse Jobs
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Profile Completion -->
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="fas fa-user me-2"></i>Profile Completion
                    </h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <small>Profile Strength</small>
                            <small>75%</small>
                        </div>
                        <div class="progress" style="height: 8px;">
                            <div class="progress-bar bg-success" style="width: 75%"></div>
                        </div>
                    </div>
                    
                    <div class="mb-2">
                        <small class="text-success">
                            <i class="fas fa-check me-1"></i>Basic information completed
                        </small>
                    </div>
                    <div class="mb-2">
                        <small class="text-warning">
                            <i class="fas fa-exclamation-triangle me-1"></i>Add a profile photo
                        </small>
                    </div>
                    <div class="mb-3">
                        <small class="text-warning">
                            <i class="fas fa-exclamation-triangle me-1"></i>Upload your resume
                        </small>
                    </div>
                    
                    <div class="d-grid">
                        <button class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-edit me-1"></i>Complete Profile
                        </button>
                    </div>
                </div>
            </div>

            <!-- Recent Jobs -->
            <?php if (!empty($recent_jobs)): ?>
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">
                        <i class="fas fa-fire me-2"></i>Recommended Jobs
                    </h6>
                    <a href="/jobs" class="btn btn-sm btn-outline-primary">View All</a>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        <?php foreach (array_slice($recent_jobs, 0, 4) as $job): ?>
                        <div class="list-group-item">
                            <div class="d-flex justify-content-between align-items-start">
                                <div class="flex-grow-1">
                                    <h6 class="mb-1">
                                        <a href="/jobs/show?id=<?php echo $job['job_id']; ?>" 
                                           class="text-decoration-none">
                                            <?php echo htmlspecialchars($job['title']); ?>
                                        </a>
                                    </h6>
                                    <p class="mb-1 small text-primary">
                                        <?php echo htmlspecialchars($job['company_name']); ?>
                                    </p>
                                    <small class="text-muted">
                                        <i class="fas fa-map-marker-alt me-1"></i>
                                        <?php echo htmlspecialchars($job['location']); ?>
                                    </small>
                                </div>
                                <span class="badge bg-primary">
                                    <?php echo ucfirst(str_replace('-', ' ', $job['job_type'])); ?>
                                </span>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <!-- Job Alerts -->
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="fas fa-bell me-2"></i>Job Alerts
                    </h6>
                </div>
                <div class="card-body">
                    <p class="card-text small text-muted">
                        Get notified when new jobs matching your preferences are posted.
                    </p>
                    <div class="d-grid gap-2">
                        <button class="btn btn-primary btn-sm">
                            <i class="fas fa-plus me-1"></i>Create Job Alert
                        </button>
                        <button class="btn btn-outline-secondary btn-sm">
                            <i class="fas fa-cog me-1"></i>Manage Alerts
                        </button>
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
                        <a href="/jobs" class="btn btn-primary btn-sm">
                            <i class="fas fa-search me-2"></i>Browse Jobs
                        </a>
                        <button class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-file-upload me-2"></i>Upload Resume
                        </button>
                        <button class="btn btn-outline-secondary btn-sm">
                            <i class="fas fa-user-edit me-2"></i>Edit Profile
                        </button>
                        <button class="btn btn-outline-info btn-sm">
                            <i class="fas fa-chart-line me-2"></i>Career Tips
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Cover Letter Modal -->
<div class="modal fade" id="coverLetterModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-file-text me-2"></i>Cover Letter
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="coverLetterContent" class="p-3 bg-light rounded">
                    <!-- Cover letter content will be inserted here -->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Filter applications
    const filterButtons = document.querySelectorAll('[data-filter]');
    const applicationRows = document.querySelectorAll('tbody tr[data-status]');
    
    filterButtons.forEach(button => {
        button.addEventListener('click', function() {
            const filter = this.getAttribute('data-filter');
            
            // Update active button
            filterButtons.forEach(btn => btn.classList.remove('active'));
            this.classList.add('active');
            
            // Filter rows
            applicationRows.forEach(row => {
                const status = row.getAttribute('data-status');
                if (filter === 'all' || status === filter) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
    });
});

// Show cover letter in modal
function showCoverLetter(coverLetter) {
    document.getElementById('coverLetterContent').innerHTML = coverLetter.replace(/\n/g, '<br>');
    const modal = new bootstrap.Modal(document.getElementById('coverLetterModal'));
    modal.show();
}

// Auto-refresh applications every 60 seconds
setInterval(function() {
    // You could implement AJAX refresh of applications here
}, 60000);
</script>

<?php include __DIR__ . '/../layout/footer.php'; ?>
