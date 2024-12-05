<?php

ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_secure', 1);
ini_set('session.use_strict_mode', 1);

session_start();
require 'config.php';

// Security headers
header("X-Content-Type-Options: nosniff");
header("X-Frame-Options: DENY");
header("Content-Security-Policy: default-src 'self';");
header("Strict-Transport-Security: max-age=31536000; includeSubDomains");

// Function to enhance session security
function secureSession() {
    session_regenerate_id(true); 
}


secureSession();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || !verifyCsrfToken($_POST['csrf_token'])) {
        echo "Invalid CSRF token!";
        exit;
    }
    // Sanitize user input
    $username = htmlspecialchars($_POST['username'], ENT_QUOTES, 'UTF-8');
    $password = $_POST['password'];

    // Retrieve user from database
    $stmt = $db->prepare("SELECT * FROM users WHERE username = :username");
    $stmt->bindValue(':username', $username, SQLITE3_TEXT);
    $result = $stmt->execute();
    $user = $result->fetchArray(SQLITE3_ASSOC);

    if ($user) {
        // Verify hashed password
        if (password_verify($password, $user['password'])) {
            secureSession();
            $_SESSION['username'] = $username;
            $_SESSION['role'] = $user['role'];
            logAction("User '$username' logged in successfully.");
            header("Location: menu.php");
            exit;
        } else {
            logAction("Failed login attempt for user '$username': incorrect password.");
            echo "Invalid login credentials."; 
        }
    } else {
        logAction("Failed login attempt with unknown username: '$username'.");
        echo "Invalid login credentials."; 
    }
}
?>
<title>Login</title>
<h1>Login</h1>
<form method="POST">
<input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
    Username: <input type="text" name="username" required><br>
    Password: <input type="password" name="password" required><br>
    <input type="submit" value="Login">
</form>
