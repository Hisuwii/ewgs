<?php
session_start();

// Session timeout: 30 minutes of inactivity
$sessionTimeout = 1800;
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity']) > $sessionTimeout) {
    $wasAdmin   = isset($_SESSION['logged_in']);
    $wasTeacher = isset($_SESSION['teacher_logged_in']);
    session_unset();
    session_destroy();
    // Teacher takes priority: if a teacher session was active, always send to teacher login
    header('Location: ' . ($wasTeacher ? '/ewgs/?expired=1' : ($wasAdmin ? '/ewgs/admin?expired=1' : '/ewgs/?expired=1')));
    exit;
}
if (isset($_SESSION['logged_in']) || isset($_SESSION['teacher_logged_in'])) {
    $_SESSION['last_activity'] = time();
}

// Composer autoloader (PhpSpreadsheet etc.)
require_once 'vendor/autoload.php';

// Autoload classes
spl_autoload_register(function ($class) {
    $paths = [
        'core/' . $class . '.php',
        'controllers/' . $class . '.php',
        'models/' . $class . '.php',
        'middleware/' . $class . '.php'
    ];
    
    foreach ($paths as $path) {
        if (file_exists($path)) {
            require_once $path;
            return;
        }
    }
});

// Load config
require_once 'config/database.php';
require_once 'config/mail.php';

// Ensure teacher table has the must_change_password flag column
getConnection()->query("ALTER TABLE tbl_teacher ADD COLUMN IF NOT EXISTS must_change_password TINYINT(1) NOT NULL DEFAULT 1");

// Load helpers
foreach (glob('helpers/*.php') as $helper) {
    require_once $helper;
}

// Initialize router
$router = new Router();

// Load routes
require_once 'routes/web.php';

// Run the router
$router->dispatch();