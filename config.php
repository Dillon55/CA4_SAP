<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
class MyDB extends SQLite3 {
    function __construct() {
        $this->open('game_review.db');
    }
}

// Security headers
header(header: "X-Content-Type-Options: nosniff");
header("X-Frame-Options: DENY");
header("Content-Security-Policy: default-src 'self';");
header("Strict-Transport-Security: max-age=31536000; includeSubDomains");

// Initialize database
$db = new MyDB();
if (!$db) {
    die("Database error: " . $db->lastErrorMsg());
}


// Create `users` table if it doesn't exist
$db->exec("
    CREATE TABLE IF NOT EXISTS users (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        username TEXT NOT NULL UNIQUE,
        password TEXT NOT NULL,
        role TEXT NOT NULL
    )
");

// Create `reviews` table if it doesn't exist
$db->exec("
    CREATE TABLE IF NOT EXISTS reviews (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    game_name TEXT NOT NULL,
    review TEXT NOT NULL,
    recommend TEXT NOT NULL CHECK (recommend IN ('Yes', 'No')),
    customer TEXT NOT NULL,
    FOREIGN KEY (customer) REFERENCES users(username) ON DELETE CASCADE
)

");

// Insert admin account if it doesn't already exist
$admin_username = 'admin';
$admin_password = password_hash('password', PASSWORD_DEFAULT);
$admin_role = 'admin';

// Check if admin already exists
$result = $db->query("SELECT * FROM users WHERE username = '$admin_username'");
if (!$result->fetchArray()) {
    $db->exec("INSERT INTO users (username, password, role) VALUES ('$admin_username', '$admin_password', '$admin_role')");
}

// Generate CSRF token
function generateCsrfToken() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
}

// Verify CSRF token
function verifyCsrfToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

// Logging function
function logAction($message) {
    $logfile = 'app.log';
    $timestamp = date('Y-m-d H:i:s');
    $entry = "[$timestamp] $message" . PHP_EOL;
    file_put_contents($logfile, $entry, FILE_APPEND);
}

generateCsrfToken();
?>
