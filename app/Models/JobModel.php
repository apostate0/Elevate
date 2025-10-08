<?php
/**
 * JobModel - Job Management Model
 * Elevate Portal - Job Portal Application
 * 
 * Handles job posting, updating, deletion, and job listing with pagination
 * Implements role-based access control for job management
 */

require_once __DIR__ . '/DatabaseModel.php';

class JobModel extends DatabaseModel {
    protected $table = 'jobs';
    
    /**
     * Create a new job posting
     * @param int $company_id Company ID
     * @param array $jobData Job posting data
     * @return array Result with success status and message
     */
    public function createJob($company_id, $jobData) {
        $jobData = $this->sanitize_input($jobData);
        
        // Validate required fields
        $required_fields = ['title', 'description', 'location', 'job_type'];
        $errors = $this->validate_required_fields($jobData, $required_fields);
        
        // Validate job type
        $valid_job_types = ['full-time', 'part-time', 'contract', 'internship'];
        if (!empty($jobData['job_type']) && !in_array($jobData['job_type'], $valid_job_types)) {
            $errors[] = "Invalid job type selected.";
        }
        
        // Validate deadline if provided
        if (!empty($jobData['deadline'])) {
            $deadline = DateTime::createFromFormat('Y-m-d', $jobData['deadline']);
            if (!$deadline || $deadline < new DateTime()) {
                $errors[] = "Deadline must be a future date.";
            }
        }
        
        if (!empty($errors)) {
            return ['success' => false, 'errors' => $errors];
        }
        
        try {
            $sql = "INSERT INTO jobs (company_id, title, description, requirements, location, 
                                    job_type, salary_range, experience_level, deadline, status) 
                    VALUES (:company_id, :title, :description, :requirements, :location, 
                            :job_type, :salary_range, :experience_level, :deadline, :status)";
            
            $params = [
                ':company_id' => $company_id,
                ':title' => $jobData['title'],
                ':description' => $jobData['description'],
                ':requirements' => $jobData['requirements'] ?? null,
                ':location' => $jobData['location'],
                ':job_type' => $jobData['job_type'],
                ':salary_range' => $jobData['salary_range'] ?? null,
                ':experience_level' => $jobData['experience_level'] ?? null,
                ':deadline' => $jobData['deadline'] ?? null,
                ':status' => $jobData['status'] ?? 'active'
            ];
            
            $this->execute_query($sql, $params);
            $job_id = $this->get_last_insert_id();
            
            return [
                'success' => true, 
                'message' => 'Job posted successfully!',
                'job_id' => $job_id
            ];
            
        } catch (Exception $e) {
            error_log("Job Creation Error: " . $e->getMessage());
            return ['success' => false, 'errors' => ['Job posting failed. Please try again.']];
        }
    }
    
    /**
     * Update existing job posting
     * @param int $job_id Job ID
     * @param int $company_id Company ID (for authorization)
     * @param array $jobData Updated job data
     * @return array Result with success status and message
     */
    public function updateJob($job_id, $company_id, $jobData) {
        // Verify job ownership
        if (!$this->verifyJobOwnership($job_id, $company_id)) {
            return ['success' => false, 'errors' => ['Unauthorized access to this job.']];
        }
        
        $jobData = $this->sanitize_input($jobData);
        
        // Validate required fields
        $required_fields = ['title', 'description', 'location', 'job_type'];
        $errors = $this->validate_required_fields($jobData, $required_fields);
        
        // Validate job type
        $valid_job_types = ['full-time', 'part-time', 'contract', 'internship'];
        if (!empty($jobData['job_type']) && !in_array($jobData['job_type'], $valid_job_types)) {
            $errors[] = "Invalid job type selected.";
        }
        
        if (!empty($errors)) {
            return ['success' => false, 'errors' => $errors];
        }
        
        try {
            $sql = "UPDATE jobs 
                    SET title = :title, description = :description, requirements = :requirements,
                        location = :location, job_type = :job_type, salary_range = :salary_range,
                        experience_level = :experience_level, deadline = :deadline, status = :status,
                        updated_at = CURRENT_TIMESTAMP
                    WHERE job_id = :job_id AND company_id = :company_id";
            
            $params = [
                ':title' => $jobData['title'],
                ':description' => $jobData['description'],
                ':requirements' => $jobData['requirements'] ?? null,
                ':location' => $jobData['location'],
                ':job_type' => $jobData['job_type'],
                ':salary_range' => $jobData['salary_range'] ?? null,
                ':experience_level' => $jobData['experience_level'] ?? null,
                ':deadline' => $jobData['deadline'] ?? null,
                ':status' => $jobData['status'] ?? 'active',
                ':job_id' => $job_id,
                ':company_id' => $company_id
            ];
            
            $this->execute_query($sql, $params);
            
            return ['success' => true, 'message' => 'Job updated successfully!'];
            
        } catch (Exception $e) {
            error_log("Job Update Error: " . $e->getMessage());
            return ['success' => false, 'errors' => ['Job update failed. Please try again.']];
        }
    }
    
    /**
     * Delete job posting
     * @param int $job_id Job ID
     * @param int $company_id Company ID (for authorization)
     * @return array Result with success status and message
     */
    public function deleteJob($job_id, $company_id) {
        // Verify job ownership
        if (!$this->verifyJobOwnership($job_id, $company_id)) {
            return ['success' => false, 'errors' => ['Unauthorized access to this job.']];
        }
        
        try {
            $sql = "DELETE FROM jobs WHERE job_id = :job_id AND company_id = :company_id";
            $this->execute_query($sql, [':job_id' => $job_id, ':company_id' => $company_id]);
            
            return ['success' => true, 'message' => 'Job deleted successfully!'];
            
        } catch (Exception $e) {
            error_log("Job Deletion Error: " . $e->getMessage());
            return ['success' => false, 'errors' => ['Job deletion failed. Please try again.']];
        }
    }
    
    /**
     * Get all jobs with pagination and filtering
     * @param int $page Page number
     * @param int $limit Records per page
     * @param array $filters Optional filters (location, job_type, etc.)
     * @return array Jobs data with pagination info
     */
    public function getAllJobs($page = 1, $limit = JOBS_PER_PAGE, $filters = []) {
        $offset = ($page - 1) * $limit;
        
        // Build WHERE clause based on filters
        $where_conditions = ["j.status = 'active'"];
        $params = [];
        
        if (!empty($filters['location'])) {
            $where_conditions[] = "j.location LIKE :location";
            $params[':location'] = '%' . $filters['location'] . '%';
        }
        
        if (!empty($filters['job_type'])) {
            $where_conditions[] = "j.job_type = :job_type";
            $params[':job_type'] = $filters['job_type'];
        }
        
        if (!empty($filters['experience_level'])) {
            $where_conditions[] = "j.experience_level = :experience_level";
            $params[':experience_level'] = $filters['experience_level'];
        }
        
        if (!empty($filters['search'])) {
            $where_conditions[] = "(j.title LIKE :search OR j.description LIKE :search OR c.company_name LIKE :search)";
            $params[':search'] = '%' . $filters['search'] . '%';
        }
        
        $where_clause = implode(' AND ', $where_conditions);
        
        // Get total count
        $count_sql = "SELECT COUNT(*) FROM jobs j 
                      JOIN companies c ON j.company_id = c.company_id 
                      WHERE " . $where_clause;
        $total_jobs = $this->get_count($count_sql, $params);
        
        // Get jobs with pagination
        $sql = "SELECT j.job_id, j.title, j.description, j.location, j.job_type, 
                       j.salary_range, j.experience_level, j.posted_date, j.deadline,
                       c.company_name, c.company_location,
                       (SELECT COUNT(*) FROM applications a WHERE a.job_id = j.job_id) as application_count
                FROM jobs j
                JOIN companies c ON j.company_id = c.company_id
                WHERE " . $where_clause . "
                ORDER BY j.posted_date DESC
                LIMIT :limit OFFSET :offset";
        
        $params[':limit'] = $limit;
        $params[':offset'] = $offset;
        
        $jobs = $this->fetch_all($sql, $params);
        
        return [
            'jobs' => $jobs,
            'filters' => $filters,
            'pagination' => [
                'current_page' => $page,
                'total_pages' => ceil($total_jobs / $limit),
                'total_jobs' => $total_jobs,
                'per_page' => $limit
            ]
        ];
    }
    
    /**
     * Get jobs by company ID
     * @param int $company_id Company ID
     * @param int $page Page number
     * @param int $limit Records per page
     * @return array Company jobs with pagination
     */
    public function getJobsByCompany($company_id, $page = 1, $limit = 10) {
        $offset = ($page - 1) * $limit;
        
        // Get total count
        $count_sql = "SELECT COUNT(*) FROM jobs WHERE company_id = :company_id";
        $total_jobs = $this->get_count($count_sql, [':company_id' => $company_id]);
        
        // Get jobs with pagination
        $sql = "SELECT j.job_id, j.title, j.description, j.location, j.job_type, 
                       j.salary_range, j.experience_level, j.status, j.posted_date, j.deadline,
                       (SELECT COUNT(*) FROM applications a WHERE a.job_id = j.job_id) as application_count
                FROM jobs j
                WHERE j.company_id = :company_id
                ORDER BY j.posted_date DESC
                LIMIT :limit OFFSET :offset";
        
        $jobs = $this->fetch_all($sql, [
            ':company_id' => $company_id,
            ':limit' => $limit,
            ':offset' => $offset
        ]);
        
        return [
            'jobs' => $jobs,
            'pagination' => [
                'current_page' => $page,
                'total_pages' => ceil($total_jobs / $limit),
                'total_jobs' => $total_jobs,
                'per_page' => $limit
            ]
        ];
    }
    
    /**
     * Get job details by ID
     * @param int $job_id Job ID
     * @return array|false Job details if found, false otherwise
     */
    public function getJobById($job_id) {
        $sql = "SELECT j.*, c.company_name, c.company_description, c.company_website, 
                       c.company_location as company_location, c.company_size, c.industry,
                       u.full_name as contact_person, u.email as contact_email
                FROM jobs j
                JOIN companies c ON j.company_id = c.company_id
                JOIN users u ON c.user_id = u.user_id
                WHERE j.job_id = :job_id";
        
        return $this->fetch_single($sql, [':job_id' => $job_id]);
    }
    
    /**
     * Get recent jobs (for homepage)
     * @param int $limit Number of jobs to fetch
     * @return array Recent jobs
     */
    public function getRecentJobs($limit = 6) {
        $sql = "SELECT j.job_id, j.title, j.location, j.job_type, j.salary_range, 
                       j.posted_date, c.company_name
                FROM jobs j
                JOIN companies c ON j.company_id = c.company_id
                WHERE j.status = 'active'
                ORDER BY j.posted_date DESC
                LIMIT :limit";
        
        return $this->fetch_all($sql, [':limit' => $limit]);
    }
    
    /**
     * Get job statistics
     * @return array Job statistics
     */
    public function getJobStats() {
        $stats = [];
        
        // Total active jobs
        $sql = "SELECT COUNT(*) FROM jobs WHERE status = 'active'";
        $stats['total_active_jobs'] = $this->get_count($sql);
        
        // Total companies with jobs
        $sql = "SELECT COUNT(DISTINCT company_id) FROM jobs WHERE status = 'active'";
        $stats['companies_with_jobs'] = $this->get_count($sql);
        
        // Jobs by type
        $sql = "SELECT job_type, COUNT(*) as count FROM jobs WHERE status = 'active' GROUP BY job_type";
        $job_types = $this->fetch_all($sql);
        $stats['jobs_by_type'] = [];
        foreach ($job_types as $type) {
            $stats['jobs_by_type'][$type['job_type']] = $type['count'];
        }
        
        return $stats;
    }
    
    /**
     * Verify job ownership by company
     * @param int $job_id Job ID
     * @param int $company_id Company ID
     * @return bool True if company owns the job, false otherwise
     */
    private function verifyJobOwnership($job_id, $company_id) {
        $sql = "SELECT COUNT(*) FROM jobs WHERE job_id = :job_id AND company_id = :company_id";
        return $this->get_count($sql, [':job_id' => $job_id, ':company_id' => $company_id]) > 0;
    }
    
    /**
     * Update job status
     * @param int $job_id Job ID
     * @param int $company_id Company ID
     * @param string $status New status
     * @return array Result
     */
    public function updateJobStatus($job_id, $company_id, $status) {
        if (!$this->verifyJobOwnership($job_id, $company_id)) {
            return ['success' => false, 'errors' => ['Unauthorized access to this job.']];
        }
        
        $valid_statuses = ['active', 'closed', 'draft'];
        if (!in_array($status, $valid_statuses)) {
            return ['success' => false, 'errors' => ['Invalid status.']];
        }
        
        try {
            $sql = "UPDATE jobs SET status = :status, updated_at = CURRENT_TIMESTAMP 
                    WHERE job_id = :job_id AND company_id = :company_id";
            
            $this->execute_query($sql, [
                ':status' => $status,
                ':job_id' => $job_id,
                ':company_id' => $company_id
            ]);
            
            return ['success' => true, 'message' => 'Job status updated successfully!'];
            
        } catch (Exception $e) {
            error_log("Job Status Update Error: " . $e->getMessage());
            return ['success' => false, 'errors' => ['Status update failed. Please try again.']];
        }
    }
    
    /**
     * Get jobs that are about to expire (deadline within 7 days)
     * @param int $company_id Company ID
     * @return array Jobs about to expire
     */
    public function getExpiringJobs($company_id) {
        $sql = "SELECT job_id, title, deadline, 
                       DATEDIFF(deadline, CURDATE()) as days_remaining
                FROM jobs 
                WHERE company_id = :company_id 
                AND status = 'active' 
                AND deadline IS NOT NULL 
                AND deadline BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 7 DAY)
                ORDER BY deadline ASC";
        
        return $this->fetch_all($sql, [':company_id' => $company_id]);
    }
}
?>
