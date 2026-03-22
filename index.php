<?php
// Hide PHP errors from browser output — errors go to error_log only.
// This prevents PHP warnings from corrupting JSON responses.
ini_set('display_errors', 0);
ini_set('log_errors', 1);
error_reporting(E_ALL);

// Use separate session cookies for admin vs teacher to prevent cross-contamination.
// Admin routes all contain "/admin"; teacher routes use "/user" or "/".
session_name(strpos($_SERVER['REQUEST_URI'] ?? '/', '/admin') !== false ? 'ewgs_admin' : 'ewgs_teacher');
session_start();

// Load app config first (defines BASE constant)
require_once __DIR__ . '/config/app.php';

// Session timeout: 30 minutes of inactivity
$sessionTimeout = 1800;
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity']) > $sessionTimeout) {
    $wasAdmin   = isset($_SESSION['logged_in']);
    $wasTeacher = isset($_SESSION['teacher_logged_in']);
    session_unset();
    session_destroy();
    // Each session is now isolated — redirect to the correct login for this session type
    header('Location: ' . ($wasAdmin ? BASE . '/admin?expired=1' : BASE . '/?expired=1'));
    exit;
}
if (isset($_SESSION['logged_in']) || isset($_SESSION['teacher_logged_in'])) {
    $_SESSION['last_activity'] = time();
}

// Composer autoloader (PhpSpreadsheet etc.)
require_once __DIR__ . '/vendor/autoload.php';

// Autoload classes
spl_autoload_register(function ($class) {
    $paths = [
        __DIR__ . '/core/' . $class . '.php',
        __DIR__ . '/controllers/' . $class . '.php',
        __DIR__ . '/models/' . $class . '.php',
        __DIR__ . '/middleware/' . $class . '.php',
    ];

    foreach ($paths as $path) {
        if (file_exists($path)) {
            require_once $path;
            return;
        }
    }
});

// Load config
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/config/mail.php';

// Ensure teacher table has required columns (one-time migration).
// Uses SHOW COLUMNS to skip the ALTER when the column already exists —
// avoids table locks and works on both MySQL and MariaDB.
try {
    $conn = getConnection();
    $r = $conn->query("SHOW COLUMNS FROM tbl_teacher LIKE 'must_change_password'");
    if ($r && $r->num_rows === 0) {
        $conn->query("ALTER TABLE tbl_teacher ADD COLUMN must_change_password TINYINT(1) NOT NULL DEFAULT 1");
    }
    $r = $conn->query("SHOW COLUMNS FROM tbl_teacher LIKE 'status'");
    if ($r && $r->num_rows === 0) {
        $conn->query("ALTER TABLE tbl_teacher ADD COLUMN status ENUM('Active','Inactive') NOT NULL DEFAULT 'Active'");
    }
} catch (Throwable $e) {
    error_log('EWGS: ALTER TABLE migration failed: ' . $e->getMessage());
}

// Load helpers
foreach (glob(__DIR__ . '/helpers/*.php') as $helper) {
    require_once $helper;
}

// Initialize router
$router = new Router();

// Load routes
require_once __DIR__ . '/routes/web.php';

// Run the router
$router->dispatch();