<?php
class MyDB extends SQLite3 {
    function __construct() {
        $this->open('game_review1.db'); // Database file name
    }
}

$db = new MyDB();

if (!$db) {
    echo $db->lastErrorMsg();
    exit;
} else {
    echo "Database opened successfully.\n";
}

// Create users table
$db->exec("
    CREATE TABLE IF NOT EXISTS users (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        username TEXT NOT NULL UNIQUE,
        password TEXT NOT NULL,
        role TEXT NOT NULL
    )
");

// Create reviews table
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

// Insert admin account only if it doesn't exist
$admin_username = 'admin';
$admin_password = 'password'; // Plaintext for vulnerability purposes
$admin_role = 'admin';

// Check if admin already exists 
$result = $db->query("SELECT * FROM users WHERE username = '$admin_username'");
if (!$result->fetchArray()) {
    // Insert hardcoded admin credentials
    $sql = "INSERT INTO users (username, password, role) VALUES ('$admin_username', '$admin_password', '$admin_role')";
    $db->exec($sql); 
    echo "Admin account created successfully.\n";
}
?>
