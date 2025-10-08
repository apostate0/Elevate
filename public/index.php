<?php
/**
 * Front Controller / Router
 * Elevate Portal - Job Portal Application
 * 
 * Entry point for all requests - handles routing and initialization
 */

// Start output buffering
ob_start();

// Set error reporting for development
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Set timezone
date_default_timezone_set('Asia/Kathmandu');

// Include configuration
require_once __DIR__ . '/../config/database.php';

// Include controllers
require_once __DIR__ . '/../app/Controllers/AuthController.php';
require_once __DIR__ . '/../app/Controllers/JobController.php';

// Initialize controllers
$authController = new AuthController();
$jobController = new JobController();

// Get the request URI and method
$request_uri = $_SERVER['REQUEST_URI'];
$request_method = $_SERVER['REQUEST_METHOD'];

// Remove query string from URI
$uri = strtok($request_uri, '?');

// Remove base path if running in subdirectory
$base_path = '/Elevate/public';
if (strpos($uri, $base_path) === 0) {
    $uri = substr($uri, strlen($base_path));
}

// Ensure URI starts with /
if (empty($uri) || $uri[0] !== '/') {
    $uri = '/' . $uri;
}

// Route handling
try {
    switch ($uri) {
        // Home page
        case '/':
            $recent_jobs = (new JobModel())->getRecentJobs(6);
            $job_stats = (new JobModel())->getJobStats();
            
            $data = [
                'title' => 'Welcome to ' . APP_NAME,
                'recent_jobs' => $recent_jobs,
                'job_stats' => $job_stats,
                'current_user' => $authController->getCurrentUser()
            ];
            
            loadView('home/index', $data);
            break;
            
        // Authentication routes
        case '/login':
            if ($request_method === 'GET') {
                $authController->showLogin();
            } else {
                $authController->login();
            }
            break;
            
        case '/register':
            if ($request_method === 'GET') {
                $authController->showRegister();
            } else {
                $authController->register();
            }
            break;
            
        case '/logout':
            $authController->logout();
            break;
            
        // Job routes
        case '/jobs':
            $jobController->index();
            break;
            
        case '/jobs/show':
            $jobController->show();
            break;
            
        case '/jobs/create':
            if ($request_method === 'GET') {
                $jobController->create();
            } else {
                $jobController->store();
            }
            break;
            
        case '/jobs/edit':
            if ($request_method === 'GET') {
                $jobController->edit();
            } else {
                $jobController->update();
            }
            break;
            
        case '/jobs/delete':
            $jobController->destroy();
            break;
            
        case '/jobs/apply':
            $jobController->apply();
            break;
            
        // Dashboard routes
        case '/dashboard':
            $current_user = $authController->getCurrentUser();
            if ($current_user) {
                if ($current_user['user_type'] === 'employer') {
                    $jobController->employerDashboard();
                } else {
                    $jobController->seekerDashboard();
                }
            } else {
                header('Location: /login');
            }
            break;
            
        case '/employer/dashboard':
            $jobController->employerDashboard();
            break;
            
        case '/seeker/dashboard':
            $jobController->seekerDashboard();
            break;
            
        // Company profile routes
        case '/employer/profile':
            handleCompanyProfile($authController);
            break;
            
        // Application management routes
        case '/employer/applications':
            handleApplicationManagement($authController);
            break;
            
        // About and Contact pages
        case '/about':
            $data = ['title' => 'About Us - ' . APP_NAME];
            loadView('pages/about', $data);
            break;
            
        case '/contact':
            $data = ['title' => 'Contact Us - ' . APP_NAME];
            loadView('pages/contact', $data);
            break;
            
        // API routes for AJAX requests
        case '/api/jobs/search':
            handleJobSearch();
            break;
            
        case '/api/applications/status':
            handleApplicationStatusUpdate($authController);
            break;
            
        // 404 - Page not found
        default:
            http_response_code(404);
            $data = ['title' => 'Page Not Found - ' . APP_NAME];
            loadView('errors/404', $data);
            break;
    }
    
} catch (Exception $e) {
    // Log error
    error_log("Application Error: " . $e->getMessage());
    
    // Show error page
    http_response_code(500);
    $data = [
        'title' => 'Server Error - ' . APP_NAME,
        'error_message' => 'An unexpected error occurred. Please try again later.'
    ];
    loadView('errors/500', $data);
}

/**
 * Handle company profile management
 */
function handleCompanyProfile($authController) {
    $authController->requireRole('employer');
    
    $companyModel = new CompanyModel();
    $company = $companyModel->getCompanyByUserId($_SESSION['user_id']);
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Handle profile update
        $companyData = [
            'company_name' => trim($_POST['company_name'] ?? ''),
            'company_description' => trim($_POST['company_description'] ?? ''),
            'company_website' => trim($_POST['company_website'] ?? ''),
            'company_location' => trim($_POST['company_location'] ?? ''),
            'company_size' => $_POST['company_size'] ?? '',
            'industry' => $_POST['industry'] ?? ''
        ];
        
        $result = $companyModel->createOrUpdateProfile($_SESSION['user_id'], $companyData);
        
        if ($result['success']) {
            $_SESSION['success'] = $result['message'];
        } else {
            $_SESSION['error'] = implode('<br>', $result['errors']);
        }
        
        header('Location: /employer/profile');
        exit;
    }
    
    $data = [
        'title' => 'Company Profile - ' . APP_NAME,
        'company' => $company,
        'success' => $_SESSION['success'] ?? null,
        'error' => $_SESSION['error'] ?? null,
        'csrf_token' => $authController->generateCSRFToken()
    ];
    
    unset($_SESSION['success'], $_SESSION['error']);
    
    loadView('employer/profile', $data);
}

/**
 * Handle application management
 */
function handleApplicationManagement($authController) {
    $authController->requireRole('employer');
    
    $companyModel = new CompanyModel();
    $applicationModel = new ApplicationModel();
    
    $company = $companyModel->getCompanyByUserId($_SESSION['user_id']);
    
    if (!$company) {
        $_SESSION['error'] = 'Company profile not found.';
        header('Location: /employer/profile');
        exit;
    }
    
    $page = max(1, intval($_GET['page'] ?? 1));
    $status_filter = $_GET['status'] ?? null;
    
    $result = $applicationModel->getCompanyApplications($company['company_id'], $page, APPLICATIONS_PER_PAGE, $status_filter);
    
    $data = [
        'title' => 'Manage Applications - ' . APP_NAME,
        'applications' => $result['applications'],
        'pagination' => $result['pagination'],
        'status_filter' => $status_filter,
        'company' => $company
    ];
    
    loadView('employer/applications', $data);
}

/**
 * Handle job search API
 */
function handleJobSearch() {
    header('Content-Type: application/json');
    
    $search_term = trim($_GET['q'] ?? '');
    $location = trim($_GET['location'] ?? '');
    
    if (empty($search_term) && empty($location)) {
        echo json_encode(['jobs' => []]);
        return;
    }
    
    $jobModel = new JobModel();
    $filters = [];
    
    if (!empty($search_term)) {
        $filters['search'] = $search_term;
    }
    
    if (!empty($location)) {
        $filters['location'] = $location;
    }
    
    $result = $jobModel->getAllJobs(1, 10, $filters);
    
    echo json_encode([
        'jobs' => $result['jobs'],
        'total' => $result['pagination']['total_jobs']
    ]);
}

/**
 * Handle application status update API
 */
function handleApplicationStatusUpdate($authController) {
    header('Content-Type: application/json');
    
    $authController->requireRole('employer');
    
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        echo json_encode(['success' => false, 'message' => 'Method not allowed']);
        return;
    }
    
    $input = json_decode(file_get_contents('php://input'), true);
    $application_id = intval($input['application_id'] ?? 0);
    $status = $input['status'] ?? '';
    
    if (!$application_id || !$status) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Invalid parameters']);
        return;
    }
    
    $companyModel = new CompanyModel();
    $applicationModel = new ApplicationModel();
    
    $company = $companyModel->getCompanyByUserId($_SESSION['user_id']);
    
    if (!$company) {
        http_response_code(403);
        echo json_encode(['success' => false, 'message' => 'Company not found']);
        return;
    }
    
    $result = $applicationModel->updateApplicationStatus($application_id, $company['company_id'], $status);
    
    echo json_encode($result);
}

/**
 * Generate URL with proper base path
 * @param string $path URL path
 * @return string Full URL
 */
function url($path = '') {
    $base_path = '/Elevate/public';
    $path = ltrim($path, '/');
    return $base_path . ($path ? '/' . $path : '');
}

/**
 * Load view file
 * @param string $view View file path
 * @param array $data Data to pass to view
 */
function loadView($view, $data = []) {
    extract($data);
    $view_file = __DIR__ . '/../app/Views/' . $view . '.php';
    
    if (file_exists($view_file)) {
        include $view_file;
    } else {
        // Try to load a basic error view
        http_response_code(500);
        echo "<h1>Error</h1><p>View file not found: $view</p>";
    }
}

// Flush output buffer
ob_end_flush();
?>
