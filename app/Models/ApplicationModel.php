<?php
/**
 * ApplicationModel - Job Application Management Model
 * Elevate Portal - Job Portal Application
 * 
 * Handles job applications, resume uploads, and application status management
 */

require_once __DIR__ . '/DatabaseModel.php';

class ApplicationModel extends DatabaseModel {
    protected $table = 'applications';
    
    /**
     * Submit a job application
     * @param int $job_id Job ID
     * @param int $seeker_id Seeker user ID
     * @param array $applicationData Application data
     * @return array Result with success status and message
     */
    public function submitApplication($job_id, $seeker_id, $applicationData) {
        // Check if user has already applied for this job
        if ($this->hasApplied($job_id, $seeker_id)) {
            return ['success' => false, 'errors' => ['You have already applied for this job.']];
        }
        
        // Verify job exists and is active
        if (!$this->isJobActive($job_id)) {
            return ['success' => false, 'errors' => ['This job is no longer available.']];
        }
        
        $applicationData = $this->sanitize_input($applicationData);
        
        try {
            $sql = "INSERT INTO applications (job_id, seeker_id, cover_letter, resume_path, status) 
                    VALUES (:job_id, :seeker_id, :cover_letter, :resume_path, 'pending')";
            
            $params = [
                ':job_id' => $job_id,
                ':seeker_id' => $seeker_id,
                ':cover_letter' => $applicationData['cover_letter'] ?? null,
                ':resume_path' => $applicationData['resume_path'] ?? null
            ];
            
            $this->execute_query($sql, $params);
            $application_id = $this->get_last_insert_id();
            
            return [
                'success' => true, 
                'message' => 'Application submitted successfully!',
                'application_id' => $application_id
            ];
            
        } catch (Exception $e) {
            error_log("Application Submission Error: " . $e->getMessage());
            return ['success' => false, 'errors' => ['Application submission failed. Please try again.']];
        }
    }
    
    /**
     * Get applications for a specific job (for employers)
     * @param int $job_id Job ID
     * @param int $company_id Company ID (for authorization)
     * @param int $page Page number
     * @param int $limit Records per page
     * @return array Applications with pagination
     */
    public function getJobApplications($job_id, $company_id, $page = 1, $limit = APPLICATIONS_PER_PAGE) {
        // Verify job ownership
        if (!$this->verifyJobOwnership($job_id, $company_id)) {
            return ['success' => false, 'errors' => ['Unauthorized access to this job.']];
        }
        
        $offset = ($page - 1) * $limit;
        
        // Get total count
        $count_sql = "SELECT COUNT(*) FROM applications a 
                      JOIN jobs j ON a.job_id = j.job_id 
                      WHERE a.job_id = :job_id AND j.company_id = :company_id";
        $total_applications = $this->get_count($count_sql, [':job_id' => $job_id, ':company_id' => $company_id]);
        
        // Get applications with pagination
        $sql = "SELECT a.application_id, a.cover_letter, a.resume_path, a.status, a.applied_date,
                       u.user_id, u.username, u.email, u.full_name, u.phone,
                       j.title as job_title
                FROM applications a
                JOIN users u ON a.seeker_id = u.user_id
                JOIN jobs j ON a.job_id = j.job_id
                WHERE a.job_id = :job_id AND j.company_id = :company_id
                ORDER BY a.applied_date DESC
                LIMIT :limit OFFSET :offset";
        
        $applications = $this->fetch_all($sql, [
            ':job_id' => $job_id,
            ':company_id' => $company_id,
            ':limit' => $limit,
            ':offset' => $offset
        ]);
        
        return [
            'success' => true,
            'applications' => $applications,
            'pagination' => [
                'current_page' => $page,
                'total_pages' => ceil($total_applications / $limit),
                'total_applications' => $total_applications,
                'per_page' => $limit
            ]
        ];
    }
    
    /**
     * Get all applications for a company (employer dashboard)
     * @param int $company_id Company ID
     * @param int $page Page number
     * @param int $limit Records per page
     * @param string $status Filter by status
     * @return array Applications with pagination
     */
    public function getCompanyApplications($company_id, $page = 1, $limit = APPLICATIONS_PER_PAGE, $status = null) {
        $offset = ($page - 1) * $limit;
        
        // Build WHERE clause
        $where_conditions = ["j.company_id = :company_id"];
        $params = [':company_id' => $company_id];
        
        if ($status && in_array($status, ['pending', 'reviewed', 'accepted', 'rejected'])) {
            $where_conditions[] = "a.status = :status";
            $params[':status'] = $status;
        }
        
        $where_clause = implode(' AND ', $where_conditions);
        
        // Get total count
        $count_sql = "SELECT COUNT(*) FROM applications a 
                      JOIN jobs j ON a.job_id = j.job_id 
                      WHERE " . $where_clause;
        $total_applications = $this->get_count($count_sql, $params);
        
        // Get applications with pagination
        $sql = "SELECT a.application_id, a.cover_letter, a.resume_path, a.status, a.applied_date,
                       u.user_id, u.username, u.email, u.full_name, u.phone,
                       j.job_id, j.title as job_title
                FROM applications a
                JOIN users u ON a.seeker_id = u.user_id
                JOIN jobs j ON a.job_id = j.job_id
                WHERE " . $where_clause . "
                ORDER BY a.applied_date DESC
                LIMIT :limit OFFSET :offset";
        
        $params[':limit'] = $limit;
        $params[':offset'] = $offset;
        
        $applications = $this->fetch_all($sql, $params);
        
        return [
            'applications' => $applications,
            'pagination' => [
                'current_page' => $page,
                'total_pages' => ceil($total_applications / $limit),
                'total_applications' => $total_applications,
                'per_page' => $limit
            ]
        ];
    }
    
    /**
     * Get applications by seeker (job seeker dashboard)
     * @param int $seeker_id Seeker user ID
     * @param int $page Page number
     * @param int $limit Records per page
     * @return array Seeker's applications with pagination
     */
    public function getSeekerApplications($seeker_id, $page = 1, $limit = 10) {
        $offset = ($page - 1) * $limit;
        
        // Get total count
        $count_sql = "SELECT COUNT(*) FROM applications WHERE seeker_id = :seeker_id";
        $total_applications = $this->get_count($count_sql, [':seeker_id' => $seeker_id]);
        
        // Get applications with pagination
        $sql = "SELECT a.application_id, a.cover_letter, a.status, a.applied_date,
                       j.job_id, j.title, j.location, j.job_type, j.salary_range,
                       c.company_name, c.company_location
                FROM applications a
                JOIN jobs j ON a.job_id = j.job_id
                JOIN companies c ON j.company_id = c.company_id
                WHERE a.seeker_id = :seeker_id
                ORDER BY a.applied_date DESC
                LIMIT :limit OFFSET :offset";
        
        $applications = $this->fetch_all($sql, [
            ':seeker_id' => $seeker_id,
            ':limit' => $limit,
            ':offset' => $offset
        ]);
        
        return [
            'applications' => $applications,
            'pagination' => [
                'current_page' => $page,
                'total_pages' => ceil($total_applications / $limit),
                'total_applications' => $total_applications,
                'per_page' => $limit
            ]
        ];
    }
    
    /**
     * Update application status (for employers)
     * @param int $application_id Application ID
     * @param int $company_id Company ID (for authorization)
     * @param string $status New status
     * @return array Result
     */
    public function updateApplicationStatus($application_id, $company_id, $status) {
        $valid_statuses = ['pending', 'reviewed', 'accepted', 'rejected'];
        if (!in_array($status, $valid_statuses)) {
            return ['success' => false, 'errors' => ['Invalid status.']];
        }
        
        // Verify application belongs to company
        $sql = "SELECT COUNT(*) FROM applications a 
                JOIN jobs j ON a.job_id = j.job_id 
                WHERE a.application_id = :application_id AND j.company_id = :company_id";
        
        if ($this->get_count($sql, [':application_id' => $application_id, ':company_id' => $company_id]) == 0) {
            return ['success' => false, 'errors' => ['Unauthorized access to this application.']];
        }
        
        try {
            $sql = "UPDATE applications 
                    SET status = :status, updated_at = CURRENT_TIMESTAMP 
                    WHERE application_id = :application_id";
            
            $this->execute_query($sql, [
                ':status' => $status,
                ':application_id' => $application_id
            ]);
            
            return ['success' => true, 'message' => 'Application status updated successfully!'];
            
        } catch (Exception $e) {
            error_log("Application Status Update Error: " . $e->getMessage());
            return ['success' => false, 'errors' => ['Status update failed. Please try again.']];
        }
    }
    
    /**
     * Get application details by ID
     * @param int $application_id Application ID
     * @param int $company_id Company ID (for authorization)
     * @return array|false Application details if found and authorized, false otherwise
     */
    public function getApplicationById($application_id, $company_id = null) {
        $sql = "SELECT a.*, u.username, u.email, u.full_name, u.phone,
                       j.title as job_title, j.job_id, c.company_name
                FROM applications a
                JOIN users u ON a.seeker_id = u.user_id
                JOIN jobs j ON a.job_id = j.job_id
                JOIN companies c ON j.company_id = c.company_id
                WHERE a.application_id = :application_id";
        
        $params = [':application_id' => $application_id];
        
        if ($company_id) {
            $sql .= " AND c.company_id = :company_id";
            $params[':company_id'] = $company_id;
        }
        
        return $this->fetch_single($sql, $params);
    }
    
    /**
     * Handle resume file upload
     * @param array $file $_FILES array element
     * @param int $seeker_id Seeker user ID
     * @return array Result with file path or error
     */
    public function handleResumeUpload($file, $seeker_id) {
        // Validate file
        if (!isset($file['tmp_name']) || $file['error'] !== UPLOAD_ERR_OK) {
            return ['success' => false, 'errors' => ['File upload failed.']];
        }
        
        // Check file size
        if ($file['size'] > UPLOAD_MAX_SIZE) {
            return ['success' => false, 'errors' => ['File size exceeds maximum limit (5MB).']];
        }
        
        // Check file type
        $file_extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($file_extension, ALLOWED_UPLOAD_TYPES)) {
            return ['success' => false, 'errors' => ['Invalid file type. Only PDF, DOC, and DOCX files are allowed.']];
        }
        
        try {
            // Generate unique filename
            $filename = 'resume_' . $seeker_id . '_' . time() . '.' . $file_extension;
            $file_path = RESUME_UPLOAD_DIR . $filename;
            
            // Move uploaded file
            if (move_uploaded_file($file['tmp_name'], $file_path)) {
                return [
                    'success' => true, 
                    'file_path' => 'uploads/resumes/' . $filename,
                    'filename' => $filename
                ];
            } else {
                return ['success' => false, 'errors' => ['Failed to save uploaded file.']];
            }
            
        } catch (Exception $e) {
            error_log("Resume Upload Error: " . $e->getMessage());
            return ['success' => false, 'errors' => ['File upload failed. Please try again.']];
        }
    }
    
    /**
     * Get application statistics for a company
     * @param int $company_id Company ID
     * @return array Application statistics
     */
    public function getApplicationStats($company_id) {
        $stats = [];
        
        // Total applications
        $sql = "SELECT COUNT(*) FROM applications a 
                JOIN jobs j ON a.job_id = j.job_id 
                WHERE j.company_id = :company_id";
        $stats['total_applications'] = $this->get_count($sql, [':company_id' => $company_id]);
        
        // Applications by status
        $sql = "SELECT a.status, COUNT(*) as count 
                FROM applications a 
                JOIN jobs j ON a.job_id = j.job_id 
                WHERE j.company_id = :company_id 
                GROUP BY a.status";
        $status_counts = $this->fetch_all($sql, [':company_id' => $company_id]);
        
        $stats['by_status'] = [];
        foreach ($status_counts as $status) {
            $stats['by_status'][$status['status']] = $status['count'];
        }
        
        // Recent applications (last 7 days)
        $sql = "SELECT COUNT(*) FROM applications a 
                JOIN jobs j ON a.job_id = j.job_id 
                WHERE j.company_id = :company_id 
                AND a.applied_date >= DATE_SUB(NOW(), INTERVAL 7 DAY)";
        $stats['recent_applications'] = $this->get_count($sql, [':company_id' => $company_id]);
        
        return $stats;
    }
    
    /**
     * Check if user has already applied for a job
     * @param int $job_id Job ID
     * @param int $seeker_id Seeker user ID
     * @return bool
     */
    private function hasApplied($job_id, $seeker_id) {
        $sql = "SELECT COUNT(*) FROM applications WHERE job_id = :job_id AND seeker_id = :seeker_id";
        return $this->get_count($sql, [':job_id' => $job_id, ':seeker_id' => $seeker_id]) > 0;
    }
    
    /**
     * Check if job is active
     * @param int $job_id Job ID
     * @return bool
     */
    private function isJobActive($job_id) {
        $sql = "SELECT COUNT(*) FROM jobs WHERE job_id = :job_id AND status = 'active'";
        return $this->get_count($sql, [':job_id' => $job_id]) > 0;
    }
    
    /**
     * Verify job ownership by company
     * @param int $job_id Job ID
     * @param int $company_id Company ID
     * @return bool
     */
    private function verifyJobOwnership($job_id, $company_id) {
        $sql = "SELECT COUNT(*) FROM jobs WHERE job_id = :job_id AND company_id = :company_id";
        return $this->get_count($sql, [':job_id' => $job_id, ':company_id' => $company_id]) > 0;
    }
    
    /**
     * Delete old resume files when new one is uploaded
     * @param int $seeker_id Seeker user ID
     * @return void
     */
    public function cleanupOldResumes($seeker_id) {
        try {
            $pattern = RESUME_UPLOAD_DIR . 'resume_' . $seeker_id . '_*';
            $files = glob($pattern);
            
            // Keep only the most recent file, delete others
            if (count($files) > 1) {
                // Sort by modification time, newest first
                usort($files, function($a, $b) {
                    return filemtime($b) - filemtime($a);
                });
                
                // Delete all but the newest
                for ($i = 1; $i < count($files); $i++) {
                    unlink($files[$i]);
                }
            }
        } catch (Exception $e) {
            error_log("Resume Cleanup Error: " . $e->getMessage());
        }
    }
}
?>
