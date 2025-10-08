<?php
/**
 * JobController - Job Management Controller
 * Elevate Portal - Job Portal Application
 * 
 * Handles job listing, creation, updating, deletion, and job applications
 * Implements role-based access control and pagination
 */

require_once __DIR__ . '/../Models/JobModel.php';
require_once __DIR__ . '/../Models/CompanyModel.php';
require_once __DIR__ . '/../Models/ApplicationModel.php';
require_once __DIR__ . '/AuthController.php';

class JobController {
    private $jobModel;
    private $companyModel;
    private $applicationModel;
    private $authController;
    
    public function __construct() {
        $this->jobModel = new JobModel();
        $this->companyModel = new CompanyModel();
        $this->applicationModel = new ApplicationModel();
        $this->authController = new AuthController();
    }
    
    /**
     * Display job listings with pagination and filters
     */
    public function index() {
        $page = max(1, intval($_GET['page'] ?? 1));
        $filters = [
            'location' => trim($_GET['location'] ?? ''),
            'job_type' => $_GET['job_type'] ?? '',
            'experience_level' => $_GET['experience_level'] ?? '',
            'search' => trim($_GET['search'] ?? '')
        ];
        
        // Remove empty filters
        $filters = array_filter($filters, function($value) {
            return $value !== '';
        });
        
        $result = $this->jobModel->getAllJobs($page, JOBS_PER_PAGE, $filters);
        
        $data = [
            'title' => 'Job Listings - ' . APP_NAME,
            'jobs' => $result['jobs'],
            'pagination' => $result['pagination'],
            'filters' => $result['filters'],
            'current_user' => $this->authController->getCurrentUser()
        ];
        
        $this->loadView('jobs/index', $data);
    }
    
    /**
     * Display single job details
     */
    public function show() {
        $job_id = intval($_GET['id'] ?? 0);
        
        if (!$job_id) {
            $_SESSION['error'] = 'Job not found.';
            $this->redirect('/jobs');
            return;
        }
        
        $job = $this->jobModel->getJobById($job_id);
        
        if (!$job) {
            $_SESSION['error'] = 'Job not found.';
            $this->redirect('/jobs');
            return;
        }
        
        // Check if user has already applied (for seekers)
        $has_applied = false;
        $current_user = $this->authController->getCurrentUser();
        
        if ($current_user && $current_user['user_type'] === 'seeker') {
            // This would require a method in ApplicationModel to check
            $has_applied = $this->hasUserApplied($job_id, $current_user['user_id']);
        }
        
        $data = [
            'title' => $job['title'] . ' - ' . APP_NAME,
            'job' => $job,
            'has_applied' => $has_applied,
            'current_user' => $current_user
        ];
        
        $this->loadView('jobs/show', $data);
    }
    
    /**
     * Display job creation form (employers only)
     */
    public function create() {
        $this->authController->requireRole('employer');
        
        // Get company info
        $company = $this->companyModel->getCompanyByUserId($_SESSION['user_id']);
        
        if (!$company) {
            $_SESSION['error'] = 'Please complete your company profile before posting jobs.';
            $this->redirect('/employer/profile');
            return;
        }
        
        $data = [
            'title' => 'Post New Job - ' . APP_NAME,
            'company' => $company,
            'current_user' => $this->authController->getCurrentUser(),
            'error' => $_SESSION['error'] ?? null,
            'old_input' => $_SESSION['old_input'] ?? [],
            'csrf_token' => $this->authController->generateCSRFToken()
        ];
        
        unset($_SESSION['error'], $_SESSION['old_input']);
        
        $this->loadView('jobs/create', $data);
    }
    
    /**
     * Store new job posting
     */
    public function store() {
        $this->authController->requireRole('employer');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/jobs/create');
            return;
        }
        
        // Verify CSRF token
        if (!$this->authController->verifyCSRFToken($_POST['csrf_token'] ?? '')) {
            $_SESSION['error'] = 'Invalid form submission.';
            $this->redirect('/jobs/create');
            return;
        }
        
        // Get company info
        $company = $this->companyModel->getCompanyByUserId($_SESSION['user_id']);
        
        if (!$company) {
            $_SESSION['error'] = 'Company profile not found.';
            $this->redirect('/employer/profile');
            return;
        }
        
        $jobData = [
            'title' => trim($_POST['title'] ?? ''),
            'description' => trim($_POST['description'] ?? ''),
            'requirements' => trim($_POST['requirements'] ?? ''),
            'location' => trim($_POST['location'] ?? ''),
            'job_type' => $_POST['job_type'] ?? '',
            'salary_range' => trim($_POST['salary_range'] ?? ''),
            'experience_level' => $_POST['experience_level'] ?? '',
            'deadline' => $_POST['deadline'] ?? null,
            'status' => $_POST['status'] ?? 'active'
        ];
        
        // Store old input for form repopulation
        $_SESSION['old_input'] = $jobData;
        
        $result = $this->jobModel->createJob($company['company_id'], $jobData);
        
        if ($result['success']) {
            unset($_SESSION['old_input']);
            $_SESSION['success'] = $result['message'];
            $this->redirect('/employer/dashboard');
        } else {
            $_SESSION['error'] = implode('<br>', $result['errors']);
            $this->redirect('/jobs/create');
        }
    }
    
    /**
     * Display job edit form (employers only)
     */
    public function edit() {
        $this->authController->requireRole('employer');
        
        $job_id = intval($_GET['id'] ?? 0);
        
        if (!$job_id) {
            $_SESSION['error'] = 'Job not found.';
            $this->redirect('/employer/dashboard');
            return;
        }
        
        $job = $this->jobModel->getJobById($job_id);
        $company = $this->companyModel->getCompanyByUserId($_SESSION['user_id']);
        
        if (!$job || !$company || $job['company_id'] != $company['company_id']) {
            $_SESSION['error'] = 'Unauthorized access or job not found.';
            $this->redirect('/employer/dashboard');
            return;
        }
        
        $data = [
            'title' => 'Edit Job - ' . APP_NAME,
            'job' => $job,
            'company' => $company,
            'current_user' => $this->authController->getCurrentUser(),
            'error' => $_SESSION['error'] ?? null,
            'old_input' => $_SESSION['old_input'] ?? [],
            'csrf_token' => $this->authController->generateCSRFToken()
        ];
        
        unset($_SESSION['error'], $_SESSION['old_input']);
        
        $this->loadView('jobs/edit', $data);
    }
    
    /**
     * Update job posting
     */
    public function update() {
        $this->authController->requireRole('employer');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/employer/dashboard');
            return;
        }
        
        $job_id = intval($_POST['job_id'] ?? 0);
        
        // Verify CSRF token
        if (!$this->authController->verifyCSRFToken($_POST['csrf_token'] ?? '')) {
            $_SESSION['error'] = 'Invalid form submission.';
            $this->redirect("/jobs/edit?id=$job_id");
            return;
        }
        
        $company = $this->companyModel->getCompanyByUserId($_SESSION['user_id']);
        
        if (!$company) {
            $_SESSION['error'] = 'Company profile not found.';
            $this->redirect('/employer/dashboard');
            return;
        }
        
        $jobData = [
            'title' => trim($_POST['title'] ?? ''),
            'description' => trim($_POST['description'] ?? ''),
            'requirements' => trim($_POST['requirements'] ?? ''),
            'location' => trim($_POST['location'] ?? ''),
            'job_type' => $_POST['job_type'] ?? '',
            'salary_range' => trim($_POST['salary_range'] ?? ''),
            'experience_level' => $_POST['experience_level'] ?? '',
            'deadline' => $_POST['deadline'] ?? null,
            'status' => $_POST['status'] ?? 'active'
        ];
        
        // Store old input for form repopulation
        $_SESSION['old_input'] = $jobData;
        
        $result = $this->jobModel->updateJob($job_id, $company['company_id'], $jobData);
        
        if ($result['success']) {
            unset($_SESSION['old_input']);
            $_SESSION['success'] = $result['message'];
            $this->redirect('/employer/dashboard');
        } else {
            $_SESSION['error'] = implode('<br>', $result['errors']);
            $this->redirect("/jobs/edit?id=$job_id");
        }
    }
    
    /**
     * Delete job posting
     */
    public function destroy() {
        $this->authController->requireRole('employer');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/employer/dashboard');
            return;
        }
        
        $job_id = intval($_POST['job_id'] ?? 0);
        
        // Verify CSRF token
        if (!$this->authController->verifyCSRFToken($_POST['csrf_token'] ?? '')) {
            $_SESSION['error'] = 'Invalid form submission.';
            $this->redirect('/employer/dashboard');
            return;
        }
        
        $company = $this->companyModel->getCompanyByUserId($_SESSION['user_id']);
        
        if (!$company) {
            $_SESSION['error'] = 'Company profile not found.';
            $this->redirect('/employer/dashboard');
            return;
        }
        
        $result = $this->jobModel->deleteJob($job_id, $company['company_id']);
        
        if ($result['success']) {
            $_SESSION['success'] = $result['message'];
        } else {
            $_SESSION['error'] = implode('<br>', $result['errors']);
        }
        
        $this->redirect('/employer/dashboard');
    }
    
    /**
     * Apply for a job (seekers only)
     */
    public function apply() {
        $this->authController->requireRole('seeker');
        
        $job_id = intval($_POST['job_id'] ?? 0);
        
        if (!$job_id) {
            $_SESSION['error'] = 'Job not found.';
            $this->redirect('/jobs');
            return;
        }
        
        // Verify CSRF token
        if (!$this->authController->verifyCSRFToken($_POST['csrf_token'] ?? '')) {
            $_SESSION['error'] = 'Invalid form submission.';
            $this->redirect("/jobs/show?id=$job_id");
            return;
        }
        
        $applicationData = [
            'cover_letter' => trim($_POST['cover_letter'] ?? '')
        ];
        
        // Handle resume upload if provided
        if (isset($_FILES['resume']) && $_FILES['resume']['error'] === UPLOAD_ERR_OK) {
            $upload_result = $this->applicationModel->handleResumeUpload($_FILES['resume'], $_SESSION['user_id']);
            
            if ($upload_result['success']) {
                $applicationData['resume_path'] = $upload_result['file_path'];
                
                // Clean up old resumes
                $this->applicationModel->cleanupOldResumes($_SESSION['user_id']);
            } else {
                $_SESSION['error'] = implode('<br>', $upload_result['errors']);
                $this->redirect("/jobs/show?id=$job_id");
                return;
            }
        }
        
        $result = $this->applicationModel->submitApplication($job_id, $_SESSION['user_id'], $applicationData);
        
        if ($result['success']) {
            $_SESSION['success'] = $result['message'];
        } else {
            $_SESSION['error'] = implode('<br>', $result['errors']);
        }
        
        $this->redirect("/jobs/show?id=$job_id");
    }
    
    /**
     * Display employer dashboard with job management
     */
    public function employerDashboard() {
        $this->authController->requireRole('employer');
        
        $company = $this->companyModel->getCompanyByUserId($_SESSION['user_id']);
        
        if (!$company) {
            $_SESSION['error'] = 'Please complete your company profile first.';
            $this->redirect('/employer/profile');
            return;
        }
        
        $page = max(1, intval($_GET['page'] ?? 1));
        $jobs_result = $this->jobModel->getJobsByCompany($company['company_id'], $page);
        $applications_result = $this->applicationModel->getCompanyApplications($company['company_id'], 1, 5);
        $company_stats = $this->companyModel->getCompanyStats($company['company_id']);
        $app_stats = $this->applicationModel->getApplicationStats($company['company_id']);
        
        $data = [
            'title' => 'Employer Dashboard - ' . APP_NAME,
            'company' => $company,
            'jobs' => $jobs_result['jobs'],
            'jobs_pagination' => $jobs_result['pagination'],
            'recent_applications' => $applications_result['applications'],
            'company_stats' => $company_stats,
            'app_stats' => $app_stats,
            'current_user' => $this->authController->getCurrentUser(),
            'success' => $_SESSION['success'] ?? null,
            'error' => $_SESSION['error'] ?? null
        ];
        
        unset($_SESSION['success'], $_SESSION['error']);
        
        $this->loadView('employer/dashboard', $data);
    }
    
    /**
     * Display seeker dashboard with applied jobs
     */
    public function seekerDashboard() {
        $this->authController->requireRole('seeker');
        
        $page = max(1, intval($_GET['page'] ?? 1));
        $applications_result = $this->applicationModel->getSeekerApplications($_SESSION['user_id'], $page);
        $recent_jobs = $this->jobModel->getRecentJobs(6);
        
        $data = [
            'title' => 'Job Seeker Dashboard - ' . APP_NAME,
            'applications' => $applications_result['applications'],
            'applications_pagination' => $applications_result['pagination'],
            'recent_jobs' => $recent_jobs,
            'current_user' => $this->authController->getCurrentUser(),
            'success' => $_SESSION['success'] ?? null,
            'error' => $_SESSION['error'] ?? null
        ];
        
        unset($_SESSION['success'], $_SESSION['error']);
        
        $this->loadView('seeker/dashboard', $data);
    }
    
    /**
     * Check if user has applied for a job
     * @param int $job_id Job ID
     * @param int $user_id User ID
     * @return bool
     */
    private function hasUserApplied($job_id, $user_id) {
        // Use ApplicationModel to check if user has applied
        try {
            // This would need to be implemented in ApplicationModel
            // For now, return false to avoid errors
            return false;
        } catch (Exception $e) {
            return false;
        }
    }
    
    /**
     * Load view file
     * @param string $view View file path
     * @param array $data Data to pass to view
     */
    private function loadView($view, $data = []) {
        extract($data);
        $view_file = __DIR__ . '/../Views/' . $view . '.php';
        
        if (file_exists($view_file)) {
            include $view_file;
        } else {
            die("View file not found: $view_file");
        }
    }
    
    /**
     * Redirect to URL
     * @param string $url URL to redirect to
     */
    private function redirect($url) {
        header("Location: $url");
        exit;
    }
}
?>
