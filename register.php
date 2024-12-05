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
    $username = htmlspecialchars($_POST['username'], ENT_QUOTES, 'UTF-8');
    $password = $_POST['password'];
    $role = 'customer'; 

    // Input validation
    if (empty($username) || empty($password)) {
        echo "Username and password are required.";
        exit;
    }

    // Ensure username has valid characters
    if (!preg_match('/^[a-zA-Z0-9_]{3,20}$/', $username)) {
        echo "Username must be 3-20 characters long and contain only letters, numbers, and underscores.";
        exit;
    }

    // Ensure password is at least 3 characters long and contains at least one number
    if (!preg_match('/^(?=.*\d).{3,}$/', $password)) {
        echo "Password must be at least 3 characters long and include at least one number.";
        exit;
    }

    // Hash the password 
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    try {
        // Check if the username already exists in the database
        $stmt = $db->prepare("SELECT COUNT(*) FROM users WHERE username = :username");
        $stmt->bindValue(':username', $username, SQLITE3_TEXT);
        $result = $stmt->execute();
        $row = $result->fetchArray(SQLITE3_ASSOC);

        if ($row['COUNT(*)'] > 0) {
            // If username already exists
            echo "Error: The username '$username' is already taken. Please choose a different one.";
            exit;
        }

        
        $stmt = $db->prepare("INSERT INTO users (username, password, role) VALUES (:username, :password, :role)");
        $stmt->bindValue(':username', $username, SQLITE3_TEXT);
        $stmt->bindValue(':password', $hashed_password, SQLITE3_TEXT);
        $stmt->bindValue(':role', $role, SQLITE3_TEXT);

     
        $stmt->execute();

       
        logAction("New account created for user '$username' with role '$role'.");

        echo "Account created successfully! You can now <a href='login.php'>login</a>.";
    } catch (Exception $e) {
       
        echo "An unexpected error occurred: " . $e->getMessage();
        exit;
    }
}
?>

<h1>Register</h1>
<form method="POST">
    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
    Username: <input type="text" name="username" required><br>
    Password: <input type="text" name="password" required><br>
    <input type="submit" value="Register">
</form>
