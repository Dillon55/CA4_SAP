<?php
session_start();
require 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username']; // No input sanitization
    $password = $_POST['password']; // Plaintext password comparison

    // Vulnerable SQL query with username and password fields
    $sql = "SELECT * FROM users WHERE username = '$username' AND password = '$password'";
    $result = $db->query($sql);

    if ($row = $result->fetchArray()) {
        // If SQL query returns a row, login is successful
        $_SESSION['username'] = $username;
        $_SESSION['role'] = $row['role']; // Set role in session

        // Redirect based on role
        if ($row['role'] === 'admin') {
            header("Location: menu.php"); // Admin dashboard
        } else {
            header("Location: menu.php"); // Customer dashboard
        }
        exit;
    } else {
        echo "Invalid username or password!";
    }
}
?>

<h1>Login</h1>
<form method="POST">
    Username: <input type="text" name="username" required><br>
    Password: <input type="password" name="password" required><br>
    <input type="submit" value="Login">
</form>
