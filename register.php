<?php
session_start();
require 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username']; // No input sanitization (vulnerable)
    $password = $_POST['password']; // Plaintext password storage
    $role = 'customer'; // Default role for registrants

    try {
        // Begin a transaction to avoid locks
        $db->exec("BEGIN");

        // Insert user into the database (vulnerable to SQL injection)
        $sql = "INSERT INTO users (username, password, role) VALUES ('$username', '$password', '$role')";
        $db->exec($sql);

        // Commit the transaction
        $db->exec("COMMIT");

        
        echo "Account created successfully! You can now <a href='login.php'>login</a>.";
        echo "SQL Query: " . $sql . "<br>";  // This will print the query to the page

    } catch (Exception $e) {
        // Rollback on error and handle database lock
        $db->exec("ROLLBACK");
        echo "Error: " . $e->getMessage();
    } finally {
        // Explicitly close the database connection
        $db->close();
    }
}
?>

<h1>Register</h1>
<form method="POST">
    Username: <input type="text" name="username" required><br>
    Password: <input type="text" name="password" ><br>
    <input type="submit" value="Register">
</form>
