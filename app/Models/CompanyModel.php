<?php
/**
 * CompanyModel - Company Management Model
 * Elevate Portal - Job Portal Application
 * 
 * Handles company profile management and company-related operations
 */

require_once __DIR__ . '/DatabaseModel.php';

class CompanyModel extends DatabaseModel {
    protected $table = 'companies';
    
    /**
     * Create or update company profile
     * @param int $user_id User ID (employer)
     * @param array $companyData Company profile data
     * @return array Result with success status and message
     */
    public function createOrUpdateProfile($user_id, $companyData) {
        $companyData = $this->sanitize_input($companyData);
        
        // Validate required fields
        $required_fields = ['company_name', 'company_description', 'company_location'];
        $errors = $this->validate_required_fields($companyData, $required_fields);
        
        // Validate website URL if provided
        if (!empty($companyData['company_website'])) {
            if (!filter_var($companyData['company_website'], FILTER_VALIDATE_URL)) {
                $errors[] = "Please enter a valid website URL.";
            }
        }
        
        if (!empty($errors)) {
            return ['success' => false, 'errors' => $errors];
        }
        
        try {
            // Check if company profile already exists
            $existing_company = $this->getCompanyByUserId($user_id);
            
            if ($existing_company) {
                // Update existing profile
                return $this->updateProfile($user_id, $companyData);
            } else {
                // Create new profile
                return $this->createProfile($user_id, $companyData);
            }
            
        } catch (Exception $e) {
            error_log("Company Profile Error: " . $e->getMessage());
            return ['success' => false, 'errors' => ['Operation failed. Please try again.']];
        }
    }
    
    /**
     * Create new company profile
     * @param int $user_id User ID
     * @param array $companyData Company data
     * @return array Result
     */
    private function createProfile($user_id, $companyData) {
        $sql = "INSERT INTO companies (user_id, company_name, company_description, company_website, 
                                     company_location, company_size, industry) 
                VALUES (:user_id, :company_name, :company_description, :company_website, 
                        :company_location, :company_size, :industry)";
        
        $params = [
            ':user_id' => $user_id,
            ':company_name' => $companyData['company_name'],
            ':company_description' => $companyData['company_description'],
            ':company_website' => $companyData['company_website'] ?? null,
            ':company_location' => $companyData['company_location'],
            ':company_size' => $companyData['company_size'] ?? null,
            ':industry' => $companyData['industry'] ?? null
        ];
        
        $this->execute_query($sql, $params);
        
        return ['success' => true, 'message' => 'Company profile created successfully!'];
    }
    
    /**
     * Update existing company profile
     * @param int $user_id User ID
     * @param array $companyData Company data
     * @return array Result
     */
    private function updateProfile($user_id, $companyData) {
        $sql = "UPDATE companies 
                SET company_name = :company_name,
                    company_description = :company_description,
                    company_website = :company_website,
                    company_location = :company_location,
                    company_size = :company_size,
                    industry = :industry,
                    updated_at = CURRENT_TIMESTAMP
                WHERE user_id = :user_id";
        
        $params = [
            ':company_name' => $companyData['company_name'],
            ':company_description' => $companyData['company_description'],
            ':company_website' => $companyData['company_website'] ?? null,
            ':company_location' => $companyData['company_location'],
            ':company_size' => $companyData['company_size'] ?? null,
            ':industry' => $companyData['industry'] ?? null,
            ':user_id' => $user_id
        ];
        
        $this->execute_query($sql, $params);
        
        return ['success' => true, 'message' => 'Company profile updated successfully!'];
    }
    
    /**
     * Get company profile by user ID
     * @param int $user_id User ID
     * @return array|false Company data if found, false otherwise
     */
    public function getCompanyByUserId($user_id) {
        $sql = "SELECT c.*, u.username, u.email, u.full_name
                FROM companies c
                JOIN users u ON c.user_id = u.user_id
                WHERE c.user_id = :user_id";
        
        return $this->fetch_single($sql, [':user_id' => $user_id]);
    }
    
    /**
     * Get company profile by company ID
     * @param int $company_id Company ID
     * @return array|false Company data if found, false otherwise
     */
    public function getCompanyById($company_id) {
        $sql = "SELECT c.*, u.username, u.email, u.full_name
                FROM companies c
                JOIN users u ON c.user_id = u.user_id
                WHERE c.company_id = :company_id";
        
        return $this->fetch_single($sql, [':company_id' => $company_id]);
    }
    
    /**
     * Get all companies with pagination
     * @param int $page Page number
     * @param int $limit Records per page
     * @return array Companies data with pagination info
     */
    public function getAllCompanies($page = 1, $limit = 10) {
        $offset = ($page - 1) * $limit;
        
        // Get total count
        $count_sql = "SELECT COUNT(*) FROM companies c JOIN users u ON c.user_id = u.user_id";
        $total_companies = $this->get_count($count_sql);
        
        // Get companies with pagination
        $sql = "SELECT c.company_id, c.company_name, c.company_description, c.company_location,
                       c.company_size, c.industry, c.created_at,
                       u.full_name as contact_person, u.email as contact_email,
                       (SELECT COUNT(*) FROM jobs j WHERE j.company_id = c.company_id AND j.status = 'active') as active_jobs
                FROM companies c
                JOIN users u ON c.user_id = u.user_id
                ORDER BY c.created_at DESC
                LIMIT :limit OFFSET :offset";
        
        $companies = $this->fetch_all($sql, [
            ':limit' => $limit,
            ':offset' => $offset
        ]);
        
        return [
            'companies' => $companies,
            'pagination' => [
                'current_page' => $page,
                'total_pages' => ceil($total_companies / $limit),
                'total_companies' => $total_companies,
                'per_page' => $limit
            ]
        ];
    }
    
    /**
     * Search companies by name or location
     * @param string $search_term Search term
     * @param int $page Page number
     * @param int $limit Records per page
     * @return array Search results with pagination
     */
    public function searchCompanies($search_term, $page = 1, $limit = 10) {
        $offset = ($page - 1) * $limit;
        $search_param = '%' . $search_term . '%';
        
        // Get total count
        $count_sql = "SELECT COUNT(*) FROM companies c 
                      JOIN users u ON c.user_id = u.user_id
                      WHERE c.company_name LIKE :search OR c.company_location LIKE :search";
        $total_companies = $this->get_count($count_sql, [':search' => $search_param]);
        
        // Get search results with pagination
        $sql = "SELECT c.company_id, c.company_name, c.company_description, c.company_location,
                       c.company_size, c.industry, c.created_at,
                       u.full_name as contact_person, u.email as contact_email,
                       (SELECT COUNT(*) FROM jobs j WHERE j.company_id = c.company_id AND j.status = 'active') as active_jobs
                FROM companies c
                JOIN users u ON c.user_id = u.user_id
                WHERE c.company_name LIKE :search OR c.company_location LIKE :search
                ORDER BY c.company_name ASC
                LIMIT :limit OFFSET :offset";
        
        $companies = $this->fetch_all($sql, [
            ':search' => $search_param,
            ':limit' => $limit,
            ':offset' => $offset
        ]);
        
        return [
            'companies' => $companies,
            'search_term' => $search_term,
            'pagination' => [
                'current_page' => $page,
                'total_pages' => ceil($total_companies / $limit),
                'total_companies' => $total_companies,
                'per_page' => $limit
            ]
        ];
    }
    
    /**
     * Get company statistics
     * @param int $company_id Company ID
     * @return array Company statistics
     */
    public function getCompanyStats($company_id) {
        $stats = [];
        
        // Total jobs posted
        $sql = "SELECT COUNT(*) FROM jobs WHERE company_id = :company_id";
        $stats['total_jobs'] = $this->get_count($sql, [':company_id' => $company_id]);
        
        // Active jobs
        $sql = "SELECT COUNT(*) FROM jobs WHERE company_id = :company_id AND status = 'active'";
        $stats['active_jobs'] = $this->get_count($sql, [':company_id' => $company_id]);
        
        // Total applications received
        $sql = "SELECT COUNT(*) FROM applications a 
                JOIN jobs j ON a.job_id = j.job_id 
                WHERE j.company_id = :company_id";
        $stats['total_applications'] = $this->get_count($sql, [':company_id' => $company_id]);
        
        // Pending applications
        $sql = "SELECT COUNT(*) FROM applications a 
                JOIN jobs j ON a.job_id = j.job_id 
                WHERE j.company_id = :company_id AND a.status = 'pending'";
        $stats['pending_applications'] = $this->get_count($sql, [':company_id' => $company_id]);
        
        return $stats;
    }
    
    /**
     * Delete company profile (and cascade delete related jobs and applications)
     * @param int $user_id User ID
     * @return array Result
     */
    public function deleteCompany($user_id) {
        try {
            $this->begin_transaction();
            
            // Get company ID first
            $company = $this->getCompanyByUserId($user_id);
            if (!$company) {
                return ['success' => false, 'errors' => ['Company not found.']];
            }
            
            // Delete company (cascade will handle jobs and applications)
            $sql = "DELETE FROM companies WHERE user_id = :user_id";
            $this->execute_query($sql, [':user_id' => $user_id]);
            
            $this->commit_transaction();
            
            return ['success' => true, 'message' => 'Company profile deleted successfully.'];
            
        } catch (Exception $e) {
            $this->rollback_transaction();
            error_log("Company Deletion Error: " . $e->getMessage());
            return ['success' => false, 'errors' => ['Deletion failed. Please try again.']];
        }
    }
}
?>
