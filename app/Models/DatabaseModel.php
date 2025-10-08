<?php
/**
 * DatabaseModel - Base Model Class
 * Elevate Portal - Job Portal Application
 * 
 * Abstract base class providing database connection and common operations
 * following OOP principles and secure database practices
 */

abstract class DatabaseModel {
    protected $connection;
    protected $table;
    
    /**
     * Constructor - Initialize database connection
     */
    public function __construct() {
        $this->connect();
    }
    
    /**
     * Establish database connection using PDO
     * @return void
     */
    protected function connect() {
        try {
            $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
            $this->connection = new PDO($dsn, DB_USER, DB_PASS, DB_OPTIONS);
        } catch (PDOException $e) {
            error_log("Database Connection Error: " . $e->getMessage());
            throw new Exception("Database connection failed. Please try again later.");
        }
    }
    
    /**
     * Execute a prepared SQL query
     * @param string $sql SQL query with placeholders
     * @param array $params Parameters for prepared statement
     * @return PDOStatement
     */
    protected function execute_query($sql, $params = []) {
        try {
            $stmt = $this->connection->prepare($sql);
            $stmt->execute($params);
            return $stmt;
        } catch (PDOException $e) {
            error_log("Query Execution Error: " . $e->getMessage());
            error_log("SQL: " . $sql);
            error_log("Params: " . print_r($params, true));
            throw new Exception("Database operation failed. Please try again.");
        }
    }
    
    /**
     * Fetch a single record from database
     * @param string $sql SQL query
     * @param array $params Parameters for prepared statement
     * @return array|false
     */
    protected function fetch_single($sql, $params = []) {
        $stmt = $this->execute_query($sql, $params);
        return $stmt->fetch();
    }
    
    /**
     * Fetch all records from database
     * @param string $sql SQL query
     * @param array $params Parameters for prepared statement
     * @return array
     */
    protected function fetch_all($sql, $params = []) {
        $stmt = $this->execute_query($sql, $params);
        return $stmt->fetchAll();
    }
    
    /**
     * Get the last inserted ID
     * @return string
     */
    protected function get_last_insert_id() {
        return $this->connection->lastInsertId();
    }
    
    /**
     * Get total count of records
     * @param string $sql SQL query for counting
     * @param array $params Parameters for prepared statement
     * @return int
     */
    protected function get_count($sql, $params = []) {
        $stmt = $this->execute_query($sql, $params);
        return (int) $stmt->fetchColumn();
    }
    
    /**
     * Begin database transaction
     * @return bool
     */
    protected function begin_transaction() {
        return $this->connection->beginTransaction();
    }
    
    /**
     * Commit database transaction
     * @return bool
     */
    protected function commit_transaction() {
        return $this->connection->commit();
    }
    
    /**
     * Rollback database transaction
     * @return bool
     */
    protected function rollback_transaction() {
        return $this->connection->rollBack();
    }
    
    /**
     * Sanitize input data to prevent XSS
     * @param mixed $data Input data
     * @return mixed Sanitized data
     */
    protected function sanitize_input($data) {
        if (is_array($data)) {
            return array_map([$this, 'sanitize_input'], $data);
        }
        return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
    }
    
    /**
     * Validate required fields
     * @param array $data Input data
     * @param array $required_fields Required field names
     * @return array Validation errors
     */
    protected function validate_required_fields($data, $required_fields) {
        $errors = [];
        foreach ($required_fields as $field) {
            if (empty($data[$field])) {
                $errors[] = ucfirst(str_replace('_', ' ', $field)) . " is required.";
            }
        }
        return $errors;
    }
    
    /**
     * Validate email format
     * @param string $email Email address
     * @return bool
     */
    protected function validate_email($email) {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }
    
    /**
     * Close database connection
     */
    public function __destruct() {
        $this->connection = null;
    }
}
?>
