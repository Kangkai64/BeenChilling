<?php

// ============================================================================
// Application Settings
// ============================================================================

// Application
define('APP_NAME', 'BeenChilling');
define('APP_VERSION', '1.0.0');
define('APP_ENV', 'development'); // development, testing, production
define('APP_URL', 'http://localhost/BeenChilling');
define('APP_TIMEZONE', 'Asia/Kuala_Lumpur');

// Directories
define('APP_ROOT', __DIR__);
define('APP_PUBLIC', APP_ROOT . '/public');
define('APP_UPLOADS', APP_PUBLIC . '/uploads');
define('APP_VIEWS', APP_ROOT . '/app/views');
define('APP_CACHE', APP_ROOT . '/cache');

// ============================================================================
// Database Settings
// ============================================================================

define('DB_HOST', 'localhost');
define('DB_NAME', 'beenchilling');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');
define('DB_COLLATION', 'utf8mb4_unicode_ci');

// ============================================================================
// Session Settings
// ============================================================================

define('SESSION_NAME', 'BEENCHILLING_SESSION');
define('SESSION_LIFETIME', 1800); // 30 minutes
define('SESSION_PATH', '/');
define('SESSION_DOMAIN', '');
define('SESSION_SECURE', true);
define('SESSION_HTTPONLY', true);
define('SESSION_SAMESITE', 'Strict');

// ============================================================================
// Security Settings
// ============================================================================

define('CSRF_TOKEN_LENGTH', 32);
define('PASSWORD_HASH_ALGO', PASSWORD_BCRYPT);
define('PASSWORD_HASH_OPTIONS', [
    'cost' => 12
]);

// File upload settings
define('MAX_FILE_SIZE', 5 * 1024 * 1024); // 5MB
define('ALLOWED_FILE_TYPES', [
    'image/jpeg',
    'image/png',
    'image/gif'
]);

// ============================================================================
// Error Reporting
// ============================================================================

if (APP_ENV === 'development') {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}

ini_set('log_errors', 1);
ini_set('error_log', APP_ROOT . '/logs/error.log');

// ============================================================================
// Helper Functions
// ============================================================================

function config($key) {
    return defined($key) ? constant($key) : null;
}

function is_development() {
    return config('APP_ENV') === 'development';
}

function is_production() {
    return config('APP_ENV') === 'production';
}

function asset($path) {
    return config('APP_URL') . '/public/' . ltrim($path, '/');
}

function url($path = '') {
    return config('APP_URL') . '/' . ltrim($path, '/');
}

function redirect($path = '') {
    header('Location: ' . url($path));
    exit();
} 