<?php
/**
 * Elevate Portal - Installation Script
 * 
 * This script helps automate the installation process
 * Run this file once to set up the application
 */

// Prevent direct access in production
if (file_exists('INSTALLED.lock')) {
    die('Application already installed. Delete INSTALLED.lock file to reinstall.');
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Elevate Portal - Installation</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white">
                        <h3 class="mb-0">
                            <i class="fas fa-rocket me-2"></i>
                            Elevate Portal Installation
                        </h3>
                    </div>
                    <div class="card-body p-4">
                        
                        <?php if ($_SERVER['REQUEST_METHOD'] === 'POST'): ?>
                            <?php
                            // Process installation
                            $db_host = $_POST['db_host'] ?? 'localhost';
                            $db_name = $_POST['db_name'] ?? 'elevate_portal_db';
                            $db_user = $_POST['db_user'] ?? 'root';
                            $db_pass = $_POST['db_pass'] ?? '';
                            
                            $errors = [];
                            $success = false;
                            
                            // Test database connection
                            try {
                                $dsn = "mysql:host=$db_host;charset=utf8mb4";
                                $pdo = new PDO($dsn, $db_user, $db_pass);
                                $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                                
                                // Create database if it doesn't exist
                                $pdo->exec("CREATE DATABASE IF NOT EXISTS `$db_name`");
                                $pdo->exec("USE `$db_name`");
                                
                                // Read and execute schema
                                $schema = file_get_contents('database/schema.sql');
                                if ($schema) {
                                    $pdo->exec($schema);
                                    
                                    // Update config file
                                    $config_content = file_get_contents('config/database.php');
                                    $config_content = str_replace("define('DB_HOST', 'localhost');", "define('DB_HOST', '$db_host');", $config_content);
                                    $config_content = str_replace("define('DB_NAME', 'elevate_portal_db');", "define('DB_NAME', '$db_name');", $config_content);
                                    $config_content = str_replace("define('DB_USER', 'root');", "define('DB_USER', '$db_user');", $config_content);
                                    $config_content = str_replace("define('DB_PASS', '');", "define('DB_PASS', '$db_pass');", $config_content);
                                    
                                    file_put_contents('config/database.php', $config_content);
                                    
                                    // Create lock file
                                    file_put_contents('INSTALLED.lock', date('Y-m-d H:i:s'));
                                    
                                    $success = true;
                                } else {
                                    $errors[] = "Could not read database schema file.";
                                }
                                
                            } catch (PDOException $e) {
                                $errors[] = "Database connection failed: " . $e->getMessage();
                            } catch (Exception $e) {
                                $errors[] = "Installation failed: " . $e->getMessage();
                            }
                            ?>
                            
                            <?php if ($success): ?>
                                <div class="alert alert-success">
                                    <h4><i class="fas fa-check-circle me-2"></i>Installation Successful!</h4>
                                    <p>Elevate Portal has been installed successfully.</p>
                                    <hr>
                                    <h6>Demo Accounts:</h6>
                                    <ul class="mb-0">
                                        <li><strong>Employer:</strong> admin_user / password</li>
                                        <li><strong>Job Seeker:</strong> john_seeker / password</li>
                                    </ul>
                                </div>
                                
                                <div class="d-grid gap-2">
                                    <a href="public/index.php" class="btn btn-primary btn-lg">
                                        <i class="fas fa-home me-2"></i>Go to Application
                                    </a>
                                    <a href="public/index.php?delete_installer=1" class="btn btn-outline-danger">
                                        <i class="fas fa-trash me-2"></i>Delete Installer (Recommended)
                                    </a>
                                </div>
                                
                            <?php else: ?>
                                <div class="alert alert-danger">
                                    <h4><i class="fas fa-exclamation-triangle me-2"></i>Installation Failed</h4>
                                    <ul class="mb-0">
                                        <?php foreach ($errors as $error): ?>
                                            <li><?php echo htmlspecialchars($error); ?></li>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                                <a href="install.php" class="btn btn-primary">Try Again</a>
                            <?php endif; ?>
                            
                        <?php else: ?>
                            <!-- Installation Form -->
                            <div class="mb-4">
                                <h5>System Requirements Check</h5>
                                <div class="row">
                                    <div class="col-md-6">
                                        <ul class="list-group list-group-flush">
                                            <li class="list-group-item d-flex justify-content-between">
                                                PHP Version
                                                <span class="badge bg-<?php echo version_compare(PHP_VERSION, '8.0.0', '>=') ? 'success' : 'danger'; ?>">
                                                    <?php echo PHP_VERSION; ?>
                                                </span>
                                            </li>
                                            <li class="list-group-item d-flex justify-content-between">
                                                PDO Extension
                                                <span class="badge bg-<?php echo extension_loaded('pdo') ? 'success' : 'danger'; ?>">
                                                    <?php echo extension_loaded('pdo') ? 'Available' : 'Missing'; ?>
                                                </span>
                                            </li>
                                            <li class="list-group-item d-flex justify-content-between">
                                                PDO MySQL
                                                <span class="badge bg-<?php echo extension_loaded('pdo_mysql') ? 'success' : 'danger'; ?>">
                                                    <?php echo extension_loaded('pdo_mysql') ? 'Available' : 'Missing'; ?>
                                                </span>
                                            </li>
                                        </ul>
                                    </div>
                                    <div class="col-md-6">
                                        <ul class="list-group list-group-flush">
                                            <li class="list-group-item d-flex justify-content-between">
                                                Config Writable
                                                <span class="badge bg-<?php echo is_writable('config/database.php') ? 'success' : 'danger'; ?>">
                                                    <?php echo is_writable('config/database.php') ? 'Yes' : 'No'; ?>
                                                </span>
                                            </li>
                                            <li class="list-group-item d-flex justify-content-between">
                                                Uploads Directory
                                                <span class="badge bg-<?php echo is_writable('uploads') ? 'success' : 'danger'; ?>">
                                                    <?php echo is_writable('uploads') ? 'Writable' : 'Not Writable'; ?>
                                                </span>
                                            </li>
                                            <li class="list-group-item d-flex justify-content-between">
                                                Schema File
                                                <span class="badge bg-<?php echo file_exists('database/schema.sql') ? 'success' : 'danger'; ?>">
                                                    <?php echo file_exists('database/schema.sql') ? 'Found' : 'Missing'; ?>
                                                </span>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            
                            <form method="POST">
                                <h5 class="mb-3">Database Configuration</h5>
                                
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="db_host" class="form-label">Database Host</label>
                                        <input type="text" class="form-control" id="db_host" name="db_host" 
                                               value="localhost" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="db_name" class="form-label">Database Name</label>
                                        <input type="text" class="form-control" id="db_name" name="db_name" 
                                               value="elevate_portal_db" required>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="db_user" class="form-label">Database Username</label>
                                        <input type="text" class="form-control" id="db_user" name="db_user" 
                                               value="root" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="db_pass" class="form-label">Database Password</label>
                                        <input type="password" class="form-control" id="db_pass" name="db_pass" 
                                               placeholder="Leave empty for XAMPP default">
                                    </div>
                                </div>
                                
                                <div class="alert alert-info">
                                    <h6><i class="fas fa-info-circle me-2"></i>Installation Notes:</h6>
                                    <ul class="mb-0">
                                        <li>This will create the database if it doesn't exist</li>
                                        <li>Sample data and demo accounts will be created</li>
                                        <li>Configuration file will be updated automatically</li>
                                    </ul>
                                </div>
                                
                                <div class="d-grid">
                                    <button type="submit" class="btn btn-primary btn-lg">
                                        <i class="fas fa-download me-2"></i>Install Elevate Portal
                                    </button>
                                </div>
                            </form>
                        <?php endif; ?>
                        
                    </div>
                </div>
                
                <div class="text-center mt-4">
                    <small class="text-muted">
                        Elevate Portal v1.0 - Job Portal Application
                    </small>
                </div>
            </div>
        </div>
    </div>
</body>
</html>

<?php
// Handle installer deletion
if (isset($_GET['delete_installer']) && $_GET['delete_installer'] == '1') {
    if (file_exists('INSTALLED.lock')) {
        unlink(__FILE__);
        header('Location: public/index.php');
        exit;
    }
}
?>
