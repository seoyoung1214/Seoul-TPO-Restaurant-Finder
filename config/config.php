<?php
/**
 * Database Configuration File
 * Team: team12
 */

// Database configuration constants
define('DB_HOST', 'localhost');
define('DB_NAME', 'team12');
define('DB_USER', 'team12');
define('DB_PASS', 'team12');
define('DB_CHARSET', 'utf8mb4');

// Application settings
define('APP_NAME', 'Seoul TPO Restaurant Finder');
define('APP_URL', 'http://localhost/team12/');

// Session configuration
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);

// Error reporting (set to 0 in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Timezone setting
date_default_timezone_set('Asia/Seoul');
?>
