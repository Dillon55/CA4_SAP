<?php
session_start();
require 'config.php';

if (!isset($_SESSION['username'])) {
    echo "You must be logged in to view reviews.";
    exit;
}

$username = $_SESSION['username'];
$role = $_SESSION['role']; // Retrieve the user's role from the session

// Display all restaurant reviews
$result = $db->query("SELECT * FROM reviews");

while ($row = $result->fetchArray()) {
    // Display review details
    echo "<h2>" . $row['game_name'] . "</h2>"; // Vulnerable to XSS (no sanitization)
    echo "<p><strong>Review:</strong> " . $row['review'] . "</p>"; // Vulnerable to XSS
    echo "<p><strong>Recommend:</strong> " . $row['recommend'] . "</p>";
    echo "<p><strong>By:</strong> " . $row['customer'] . "</p>";

    // Allow customers to edit only their own reviews
    if ($role === 'customer' && $row['customer'] === $username) {
        echo "<form method='GET' action='edit_review.php' style='display:inline;'>
                <input type='hidden' name='review_id' value='" . $row['id'] . "'>
                <input type='submit' value='Edit'>
              </form>";
    }

    // Allow admin to delete any review
    if ($role === 'admin') {
        echo "<form method='POST' action='delete_review.php' style='display:inline;'>
                <input type='hidden' name='review_id' value='" . $row['id'] . "'>
                <input type='submit' value='Delete' onclick='return confirm(\"Are you sure you want to delete this review?\");'>
              </form>";
    }

    echo "<hr>";
}
?>
