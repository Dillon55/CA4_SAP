<?php
session_start();
require 'config.php';

function secureSession() {
    ini_set('session.cookie_httponly', 1);
    ini_set('session.cookie_secure', 1); 
    ini_set('session.use_strict_mode', 1);
    session_regenerate_id(true);
}

// Security headers
header("X-Content-Type-Options: nosniff");
header("X-Frame-Options: DENY");
header("Content-Security-Policy: default-src 'self';");
header("Strict-Transport-Security: max-age=31536000; includeSubDomains");

if (!isset($_SESSION['username'])) {
    echo "You must be logged in to view reviews.";
    exit;
}

$username = $_SESSION['username'];
$role = $_SESSION['role']; // Retrieve the user's role from the session


$stmt = $db->prepare("SELECT * FROM reviews");
$result = $stmt->execute();

while ($row = $result->fetchArray()) {
    
    $game_name = htmlspecialchars($row['game_name'], ENT_QUOTES, 'UTF-8');
    $review = htmlspecialchars($row['review'], ENT_QUOTES, 'UTF-8');
    $recommend = htmlspecialchars($row['recommend'], ENT_QUOTES, 'UTF-8');
    $customer = htmlspecialchars($row['customer'], ENT_QUOTES, 'UTF-8');
    
  
    echo "<h2>$game_name</h2>";
    echo "<p><strong>Review:</strong> $review</p>";
    echo "<p><strong>Recommend:</strong> $recommend</p>";
    echo "<p><strong>By:</strong> $customer</p>";

   
    if ($role === 'customer' && $row['customer'] === $username) {
        echo "<form method='GET' action='edit_review.php' style='display:inline;'>
            <input type='hidden' name='csrf_token' value='" . htmlspecialchars($_SESSION['csrf_token']) . "'>
            <input type='hidden' name='review_id' value='" . (int)$row['id'] . "'>
            <input type='submit' value='Edit'>
        </form>";
    }

   
    if ($role === 'admin') {
        echo "<form method='POST' action='delete_review.php' style='display:inline;'>
            <input type='hidden' name='csrf_token' value='" . htmlspecialchars($_SESSION['csrf_token']) . "'>
            <input type='hidden' name='review_id' value='" . (int)$row['id'] . "'>
            <input type='submit' value='Delete' onclick='return confirm(\"Are you sure you want to delete this review?\");'>
        </form>";
    }

    echo "<hr>";
}
?>
