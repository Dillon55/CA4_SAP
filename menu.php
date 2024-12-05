<?php

ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_secure', 1); 
ini_set('session.use_strict_mode', 1);

session_start();
require 'config.php';

// Function to enhance session security
function secureSession() {
    session_regenerate_id(true); 
}


secureSession();
?>

<!DOCTYPE html>
<html>
<head>
    <title>game Review System</title>
</head>
<body>
    <h1>Welcome to the game review</h1>

    

    
    <?php if (isset($_SESSION['username'])): ?>
        <p>Hello, <?php echo htmlspecialchars($_SESSION['username']); ?>! You are logged in.</p>
        <ul>
            <li><a href="add_review.php">Add Review</a></li>
            <li><a href="view_review.php">View Reviews</a></li>
            <li><a href="logout.php">Logout</a></li>
        </ul>
        <?php if ($_SESSION['role'] === 'admin'): ?>
    <a href="view_logs.php">View Logs</a><br>
<?php endif; ?>
    <?php else: ?>
        <ul>
            <li><a href="register.php">Register</a></li>
            <li><a href="login.php">Login</a></li>
            <li><a href="view_review.php">View Reviews</a></li>
        </ul>
    <?php endif; ?>
</body>
</html>
