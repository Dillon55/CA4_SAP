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


function secureSession() {
    session_regenerate_id(true); 
}


secureSession();

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
    echo "You must be logged in to add a review!";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || !verifyCsrfToken($_POST['csrf_token'])) {
        echo "Invalid CSRF token!";
        exit;
    }

    // Validate and sanitize user input to protect against XSS and SQL Injection
    $game_name = htmlspecialchars($_POST['game_name'], ENT_QUOTES, 'UTF-8'); 
    $review = htmlspecialchars($_POST['review'], ENT_QUOTES, 'UTF-8'); 
    $recommend = htmlspecialchars($_POST['recommend'], ENT_QUOTES, 'UTF-8'); 

    if (!in_array($recommend, ['Yes', 'No'])) {
        echo "Invalid recommendation value!";
        exit;
    }

    // Prepared statement to prevent SQL Injection using SQLite3
    $stmt = $db->prepare("INSERT INTO reviews (game_name, review, recommend, customer) VALUES (:game_name, :review, :recommend, :customer)");
    $stmt->bindValue(':game_name', $game_name, SQLITE3_TEXT);
    $stmt->bindValue(':review', $review, SQLITE3_TEXT);
    $stmt->bindValue(':recommend', $recommend, SQLITE3_TEXT);
    $stmt->bindValue(':customer', $_SESSION['username'], SQLITE3_TEXT);

    // Execute the prepared statement
    if ($stmt->execute()) {
        echo "Review added! You can return to <a href='menu.php'>menu</a>.";
        exit;
    } else {
        echo "Error adding review.";
    }
}
?>

<h1>Add a Game Review</h1>
<form method="POST">
   
    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">

    Game Name: <input type="text" name="game_name" required><br>
    What did you like or dislike about the game?: <textarea name="review" required></textarea><br>
    Would you recommend?: 
    <label><input type="radio" name="recommend" value="Yes" required> Yes</label>
    <label><input type="radio" name="recommend" value="No" required> No</label><br>
    <input type="submit" value="Submit Review">
</form>
