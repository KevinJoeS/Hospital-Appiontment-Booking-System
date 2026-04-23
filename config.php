<?php
/**
 * Hospital Appointment Booking System
 * Database Configuration File
 * 
 * This file handles all database connections and configuration
 * for the Hospital Appointment Management System
 */

// ============================================
// DATABASE CONFIGURATION
// ============================================

// Database credentials
define('DB_HOST', 'localhost:3307');      // Database host with port
define('DB_USER', 'root');                // Database user
define('DB_PASSWORD', '');                // Database password (empty for local dev)
define('DB_NAME', 'hospital_db');         // Database name

// ============================================
// CONNECTION SETTINGS
// ============================================

// Enable error reporting (disable in production)
define('DEBUG_MODE', true);

// Character set for database connection
define('DB_CHARSET', 'utf8mb4');

// ============================================
// CREATE DATABASE CONNECTION
// ============================================

try {
    // Create connection using improved method
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
    
    // Set character set
    if (!$conn->set_charset(DB_CHARSET)) {
        throw new Exception("Error setting charset: " . $conn->error);
    }
    
    // Check connection
    if ($conn->connect_error) {
        throw new Exception("Connection failed: " . $conn->connect_error);
    }
    
    // Connection successful message (for testing)
    if (DEBUG_MODE && isset($_GET['test_connection'])) {
        echo "<div style='background: #10b981; color: white; padding: 20px; border-radius: 8px; margin: 20px;'>";
        echo '<img src="/Hospital-system/images/icons/check.svg" alt="Check" class="emoji-icon"> Database connection successful!<br>';
        echo "Host: " . DB_HOST . "<br>";
        echo "Database: " . DB_NAME . "<br>";
        echo "Charset: " . DB_CHARSET;
        echo "</div>";
    }
    
} catch (Exception $e) {
    // Error handling
    if (DEBUG_MODE) {
        // Development: Show detailed error
        die("<div style='background: #ef4444; color: white; padding: 20px; border-radius: 8px; margin: 20px; font-family: Arial;'>
            <h2><img src='/Hospital-system/images/icons/error.svg' alt='Error' class='emoji-icon'> Database Connection Error</h2>
            <p><strong>Error:</strong> " . htmlspecialchars($e->getMessage()) . "</p>
            <p><strong>File:</strong> " . __FILE__ . "</p>
            <p><strong>Time:</strong> " . date('Y-m-d H:i:s') . "</p>
            <hr>
            <h3>Troubleshooting Tips:</h3>
            <ul>
                <li>Check if MySQL service is running</li>
                <li>Verify database host: <code>" . DB_HOST . "</code></li>
                <li>Check database name: <code>" . DB_NAME . "</code></li>
                <li>Verify database user credentials</li>
                <li>Ensure database exists</li>
                <li>Check user permissions</li>
            </ul>
        </div>");
    } else {
        // Production: Show generic message
        die("<div style='background: #ef4444; color: white; padding: 20px; border-radius: 8px; margin: 20px;'>
            <h2>Database Connection Error</h2>
            <p>Unable to connect to the database. Please contact the administrator.</p>
        </div>");
    }
}

// ============================================
// HELPER FUNCTIONS
// ============================================

/**
 * Escape user input for database queries
 * NOTE: Use prepared statements in production instead
 * 
 * @param string $input User input to escape
 * @return string Escaped input
 */
function sanitize_input($input) {
    global $conn;
    return $conn->real_escape_string(trim($input));
}

/**
 * Execute a database query
 * 
 * @param string $sql SQL query
 * @return mysqli_result|bool Result or false on error
 */
function execute_query($sql) {
    global $conn;
    return $conn->query($sql);
}

/**
 * Get last inserted ID
 * 
 * @return int Last inserted ID
 */
function get_last_insert_id() {
    global $conn;
    return $conn->insert_id;
}

/**
 * Get error message
 * 
 * @return string Error message
 */
function get_db_error() {
    global $conn;
    return $conn->error;
}

/**
 * Close database connection
 */
function close_db_connection() {
    global $conn;
    if ($conn) {
        $conn->close();
    }
}

// ============================================
// ERROR LOGGING
// ============================================

/**
 * Log database errors to file
 * 
 * @param string $error Error message
 */
function log_error($error) {
    $log_file = __DIR__ . '/logs/error.log';
    
    // Create logs directory if it doesn't exist
    if (!file_exists(__DIR__ . '/logs')) {
        mkdir(__DIR__ . '/logs', 0755, true);
    }
    
    $timestamp = date('Y-m-d H:i:s');
    $log_message = "[$timestamp] $error\n";
    
    file_put_contents($log_file, $log_message, FILE_APPEND);
}

// ============================================
// SECURITY SETTINGS
// ============================================

// Session configuration
//ini_set('session.use_strict_mode', 1);
//ini_set('session.cookie_httponly', 1);

// Set PHP reporting based on DEBUG_MODE
if (DEBUG_MODE) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(E_ALL);
    ini_set('display_errors', 0);
    ini_set('log_errors', 1);
}

// ============================================
// ENVIRONMENT VARIABLES (Optional)
// ============================================

/**
 * Load environment variables from .env file (if using)
 * Uncomment if you have a .env file
 */

/*
if (file_exists(__DIR__ . '/.env')) {
    $env = parse_ini_file(__DIR__ . '/.env');
    define('DB_HOST', $env['DB_HOST']);
    define('DB_USER', $env['DB_USER']);
    define('DB_PASSWORD', $env['DB_PASSWORD']);
    define('DB_NAME', $env['DB_NAME']);
}
*/

// ============================================
// APPLICATION CONSTANTS
// ============================================

// Application settings
define('APP_NAME', 'Joe Medical Center');
define('APP_VERSION', '1.0.0');
define('APP_URL', 'http://localhost');

// Session timeout (in seconds)
define('SESSION_TIMEOUT', 3600); // 1 hour

// Pagination
define('ITEMS_PER_PAGE', 10);

// Upload settings
define('MAX_UPLOAD_SIZE', 5242880); // 5MB
define('ALLOWED_EXTENSIONS', ['jpg', 'jpeg', 'png', 'pdf', 'doc', 'docx']);

// ============================================
// TIMEZONE SETTING
// ============================================

date_default_timezone_set('UTC');

// ============================================
// END OF CONFIGURATION
// ============================================

?>
