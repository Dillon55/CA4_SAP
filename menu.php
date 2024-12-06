<?php
session_start();
require 'config.php';
?>

<!DOCTYPE html>
<html>
<head>
    <title>Game Review System</title>
</head>
<body>
    <h1>Welcome to the game Review </h1>
    
    <?php if (isset($_SESSION['username'])): ?>
        <p>Hello, <?php echo htmlspecialchars($_SESSION['username']); ?>! You are logged in.</p>
        <ul>
            <li><a href="add_review.php">Add Review</a></li>
            <li><a href="view_review.php">View Reviews</a></li>
            <li><a href="logout.php">Logout</a></li>
        </ul>
    <?php else: ?>
        <ul>
            <li><a href="register.php">Register</a></li>
            <li><a href="login.php">Login</a></li>
            <li><a href="view_review.php">View Reviews</a></li>
        </ul>
    <?php endif; ?>
</body>
</html>
